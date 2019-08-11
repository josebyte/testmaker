<?php
ini_set('max_execution_time', 0); // 0 = Unlimited

require_once('./lib/simple_html_dom.php');

class DocxConversion{

    private $filename;

    public function __construct($filePath) {
        $this->filename = $filePath;
    }

    public function read_docx(){
        $zip = new ZipArchive;
        if (true === $zip->open($this->filename)) {
            // If successful, search for the data file in the archive
            if (($index = $zip->locateName("word/document.xml")) !== false) {
                // Index found! Now read it to a string
                $text = $zip->getFromIndex($index);
                $html = new simple_html_dom();
                $html->load($text);

                //var_dump($html);
            }
        }

        $zip->close();
        //var_dump($dom->root->first_child()->children[1]->nodes);
        return $html->root->first_child()->children[1];
    }

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

             //unzip
            $docObj = new DocxConversion("upload/" . $filename);
            $node = $docObj->read_docx();

        /*
                for ($i = 0; $i < count($node->nodes); $i++) {
                    $node_child = $node->nodes[$i];
                    print_r($node_child);
                }
        */

            echo "termina";


        } else{
            echo "Error: There was a problem uploading your file. Please try again.";
        }

}
?>