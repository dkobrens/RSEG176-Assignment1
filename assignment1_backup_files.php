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
if ($argc < 3)
{
    exit("Usage: " . $argv[0] . " bucket directories...\n");
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

// Upload each file in the directory
for ($i = 2; $i < $argc; $i++)
{
    $Directory        = $argv[$i];

    $dir = new DirectoryIterator(dirname(__FILE__));
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            var_dump($fileinfo->getFilename());

            $ContentType = mime_content_type($fileinfo->getFilename());
            var_dump($ContentType);


            $Data = file_get_contents($fileinfo->getFilename());

            if ($Data === FALSE)
            {
                print ("Error uploading file/directory: " . $fileinfo->getFilename() );
            }
            else {
                if (UploadObject($S3, $Bucket, $fileinfo->getFilename(), $Data, CannedAcl::PUBLIC_READ,
                    $ContentType))
                {
                    print("Uploaded file '${File}' " .
                        "to Bucket '{$Bucket}'\n");
                }
                else
                {
                    exit("Could not "             .
                        "upload file '${File}' " .
                        "to Bucket '{$Bucket}'\n");
                }
            }

        }
    }

}
exit(0);
?>

