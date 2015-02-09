<?php
/**
 * Created by PhpStorm.
 * User: Heather
 * Date: 2/9/2015
 * Time: 12:13 AM
 */
/*
 * UploadObject -
 *
 *	Upload the given data to the indicated bucket name and key.
 *	Return true on success, false on error.
 */

function UploadObject($S3, $Bucket, $Key, $Data,
                      $ACL = CannedAcl::PRIVATE_ACCESS, $ContentType = "text/plain")
{
    $Try   = 1;
    $Sleep = 1;

    // Try to do the upload
    do
    {
        try
        {
            $Model = $S3->PutObject(array('Bucket'      => $Bucket,
                'Key'         => $Key,
                'Body'        => $Data,
                'ACL'         => $ACL,
                'ContentType' => $ContentType));

            return true;
        }
        catch (Exception $e)	//FIX should be more fine-grained?
        {
            print("Retry, sleep ${Sleep} - " . $e->getMessage() . "\n");
            sleep($Sleep);
            $Sleep *= 2;
        }
    }
    while (++$Try < 6);

    return false;
}

// Heather added
function UploadDirectory($S3, $Bucket, $Key, $Data,
                         $ACL = CannedAcl::PRIVATE_ACCESS)
{
    /*$dir = new DirectoryIterator(dirname(__FILE__));
    foreach ($dir as $fileinfo) {
      if (!$fileinfo->isDot()) {
        var_dump($fileinfo->getFilename());

        $ContentType = mime_content_type($fileinfo->getFilename());
        var_dump($ContentType);

        if ($ContentType == "directory") {
          UploadDirectory($S3, $Bucket, $Key)
        }
      }
        $Data        = file_get_contents($fileinfo->getFilename());
    */
}
?>