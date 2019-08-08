<?php
// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if file was uploaded without errors
    if(isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0){

        $filename = $_FILES["photo"]["name"];
        $filetype = $_FILES["photo"]["type"];
        $filesize = $_FILES["photo"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");

            // Check whether file exists before uploading it
            if(file_exists("upload/" . $filename)){
                echo $filename . " is already exists.";
            } else{
                move_uploaded_file($_FILES["photo"]["tmp_name"], "upload/" . $filename);
                echo "Your file was uploaded successfully.";

                 //unzip
                $dir = "./upload/temp/";
                $target_file = "upload/" . $filename;
                $docxUncompressedFile = $dir.'word/document.xml';

                // get the absolute path to $file
                $path = pathinfo(realpath($target_file), PATHINFO_DIRNAME);
                $zip = new ZipArchive;
                $res = $zip->open($target_file);
                if ($res === TRUE) {
                    $zip->extractTo($dir);
                    $zip->close();

                    echo $docxUncompressedFile;
                    $xmldata = simplexml_load_file($docxUncompressedFile) or die("Failed to load");
                    echo $xmldata;
                    foreach($xmldata->children() as $line) {
                        echo $line->firstname . ", ";
                    }

                } else {
                    echo "Doh! I couldn't open $file";
                }


            }
        } else{
            echo "Error: There was a problem uploading your file. Please try again.";
        }

}
?>