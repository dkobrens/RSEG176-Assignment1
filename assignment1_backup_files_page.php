<?php
/**
 * Created by PhpStorm.
 * User: Heather
 * Date: 2/14/2015
 * Time: 4:24 PM
 */

error_reporting(E_ALL);

require_once('..\book.php');
require_once('assignment1_utility.php');

use Aws\S3\S3Client;
use Aws\S3\Enum\CannedAcl;

// Get parameters
//$Bucket = IsSet($_GET['Bucket']) ? $_GET['Bucket'] : BOOK_BUCKET;

// Set up page title
$PageTitle =
    "RSEG-176 Assignment 1: Team C";



// Generate page header and an explanatory paragraph
//SendHeader($PageTitle);
//SendParagraph($PageTitle);

// Create the S3 client
try {
    $S3 = S3Client::factory(array('key' => AWS_PUBLIC_KEY,
        'secret' => AWS_SECRET_KEY,
        'region' => BUCKET_REGION));
} catch (Exception $e) {
    print("Error creating client:\n");
    print($e->getMessage());
}

$BucketList = $S3->listBuckets();
$Buckets = $BucketList['Buckets'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $Bucket = $_POST["Bucket"];
    $BucketFolder = $_POST["BucketFolder"];
    $Directory = $_POST["Directory"];

    print ("Backing up directory: " . $Directory . " to bucket: " . $Bucket . " under folder: " . $BucketFolder);

    // Check if bucket exists
    if (!$S3->doesBucketExist($Bucket)) {
        // Create bucket if it doesn't exist, after checking for valid name
        if (!$S3->isValidBucketName($Bucket)) {
            print ("Error: bucket name is invalid.");
            exit(0);
        } else {
            print ("Creating bucket: " . $Bucket);
            $S3->createBucket(array('Bucket' => $Bucket));
        }
    } else {
        print ("Bucket exists, proceeding with backup.");
    }

    $keyPrefix = $BucketFolder;
    $options = array(
        'debug' => true
    );
    // Upload the directory to the bucket
    $S3->uploadDirectory($Directory, $Bucket, $keyPrefix);
    //UploadDirectory($S3, $Bucket, $Directory);
    print ("Done");
}

include 'backup_files.html.php';

exit(0);
?>



