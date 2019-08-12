<?php

ini_set('max_execution_time', 0); // 0 = Unlimited

require_once('./lib/simple_html_dom.php');
require_once('./db.php');

function readDocx($filePath) {
    // Create new ZIP archive
    $zip = new ZipArchive;
    $dataFile = 'word/document.xml';
    // Open received archive file
    if (true === $zip->open($filePath)) {
        // If done, search for the data file in the archive
        if (($index = $zip->locateName($dataFile)) !== false) {
            // If found, read it to the string
            $zip_entry = $zip->getFromIndex($index);
            $zip->close();

            $xml = DOMDocument::loadXML($zip_entry, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            $nodes = $xml->firstChild->firstChild;

            return  $nodes;
        }
        $zip->close();
    }
    // In case of failure return empty string
    return "";
}














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

            move_uploaded_file($_FILES["photo"]["tmp_name"], "upload/" . $filename);
            echo "Your file was uploaded successfully.";

             //unzi
            $nodes = readDocx("upload/" . $filename);

            //parse nodes

            $question_id = 0;

            foreach ($nodes->childNodes AS $item) {
                $node_value = trim($item->nodeValue);

                if (empty($node_value)) {
                    continue;
                }

                //if is question:
                $re = '/^\d+\./m';
                preg_match_all($re, $node_value, $matches, PREG_SET_ORDER, 0);
                if ($matches) {

                } else {
                    $re = '/^.+\)/m';

                }



                print $item->nodeName . " = " . $item->nodeValue . "<br>";
            }

            print_r($nodes);



            echo "termina";


        } else{
            echo "Error: There was a problem uploading your file. Please try again.";
        }

}
?>