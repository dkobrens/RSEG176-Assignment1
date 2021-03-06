#!/usr/bin/env php
<?php
/**
 * Created by PhpStorm.
 * User: Heather
 * Date: 2/8/2015
 * Time: 10:58 PM
 */

error_reporting(E_ALL);

require_once('..\book.php');
require_once('assignment1_utility.php');

use Aws\S3\S3Client;
use Aws\S3\Enum\CannedAcl;

// Make sure that some arguments were supplied
if ($argc < 4)
{
    exit("Usage: " . $argv[0] . " bucket bucketfolder directory...\n");
}

// Get Bucket argument
$Bucket = ($argv[1] == '-') ? BOOK_BUCKET : $argv[1];

// Create the S3 client
try
{
    $S3 = S3Client::factory(array('key'    => AWS_PUBLIC_KEY,
        'secret' => AWS_SECRET_KEY,
        'region' => BUCKET_REGION));
}
catch (Exception $e)
{
    print("Error creating client:\n");
    print($e->getMessage());
}

$BucketFolder = $argv[2];

// Upload each file in the directory
for ($i = 3; $i < $argc; $i++)
{
    $Directory        = $argv[$i];
    print ("Backing up directory: " . $Directory . " to bucket: " . $Bucket . " under folder: " . $BucketFolder);

    // Check if bucket exists
    if( !$S3->doesBucketExist($Bucket))
    {
        // Create bucket if it doesn't exist, after checking for valid name
        if( !$S3->isValidBucketName($Bucket))
        {
            print ("Error: bucket name is invalid.");
            exit(0);
        } else
        {
            print ("Creating bucket: " . $Bucket);
            $S3->createBucket(array('Bucket' => $Bucket));
        }
    } else
    {
        print ("Bucket exists, proceeding with backup.");
    }

    $keyPrefix = $BucketFolder;
    $options = array(
        'debug'       => true
    );
    // Upload the directory to the bucket
    $S3->uploadDirectory($Directory, $Bucket, $keyPrefix);
    //UploadDirectory($S3, $Bucket, $Directory);
}

print ("Done");
exit(0);
?>

