<!DOCTYPE html>
<head>
    <title><?php echo $output_title?></title>
</head>
<body>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

<h1>Welcome to the Backup Utility</h1>
    <p>Specify an Amazon S3 bucket name, a folder name, and the directory you'd like to back up.</p>

Bucket Name: <select name="Bucket">
        <?php
            foreach ($Buckets as $Bucket)
            {
            print('<option value=\"' . $Bucket['Name'] . '\">' . $Bucket['Name'] . '</option>');
            }
        ?>
        </select>
<br><br>
Bucket Folder Name:
<input type="text" name="BucketFolder">
<br><br>
Directory:
<input type="text" name="Directory">
<br><br>
    <input type="submit" name="submit" value="Back Up Directory">

    </form>
</body>
</html>