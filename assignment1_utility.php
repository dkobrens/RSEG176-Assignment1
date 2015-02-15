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

use Aws\S3\S3Client;
use Aws\S3\Enum\CannedAcl;

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
function UploadDirectory($S3, $Bucket, $Directory)
{

    $dir = new DirectoryIterator($Directory);
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            var_dump($fileinfo->getFilename());

            //$ContentType = mime_content_type($fileinfo->getFilename());
            $ContentType = GuessType($fileinfo->getFilename());
            var_dump($ContentType);

            try {
                if ($ContentType === 'directory') {
                    UploadDirectory($S3, $Bucket, $fileinfo->getPath() . "\\" . $fileinfo->getFilename());
                } else {
                    $Data = file_get_contents($fileinfo->getFilename());

                    if ($Data === FALSE) {
                        print ("Error uploading file/directory: " . $fileinfo->getFilename());
                    } else {
                        if (UploadObject($S3, $Bucket, $fileinfo->getPath() . "\\" . $fileinfo->getFilename(),
                                $Data, CannedAcl::PUBLIC_READ, $ContentType)) {
                            print("Uploaded file " . $fileinfo->getFilename() .
                                " to Bucket '{$Bucket}'\n");
                        } else {
                            exit("Could not " .
                                "upload file " . $fileinfo->getFilename() .
                                " to Bucket '{$Bucket}'\n");
                        }
                    }
                }
            }
            catch (UnexpectedValueException $e) {
                print ("Error: Could not read directory or was not a directory: " . $fileinfo->getFilename());
            }

        }
    }
}

/*
 * GuessType -
 *
 *	Make a simple guess as to the file's content type,
 *	and return a MIME type.
 */

function GuessType($File)
{
    $Info = pathinfo($File, PATHINFO_EXTENSION);

    switch (strtolower($Info))
    {
        case "jpg":
        case "jpeg":
            return "image/jpeg";

        case "png":
            return "image/png";

        case "gif":
            return "image/gif";

        case "htm":
        case "html":
            return "text/html";

        case "txt":
            return "text/plain";

        case "php":
            return "text/plain";

        case "":
            return "directory";

        default:
            return "text/plain";
    }
}

/*
 * SendHeader - Generate page header for a web page,
 *		complete with a very simple style sheet.
 *		No return value.
 */

function SendHeader($Title)
{
    print("<html>\n");
    print("<head>\n");
    print("<title>${Title}</title>\n");
    print("<link rel=\"stylesheet\" type=\"text/css\" href=\"book.css\" />\n");
    print("</head>\n");
    print("<body>\n");
}

/*
 * SendParagraph - Generate text in a P tag for a web page.
 *		   No return value.
 */
function SendParagraph($Text)
{
    print("<p>${Text}</p>\n");
}

/*
 * SendFooter - Generate page footer for a web page. No
 *		return value.
 */

function SendFooter()
{
    print("</body>\n");
    print("</html>\n");
}
?>