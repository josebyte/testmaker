<?php

ini_set('max_execution_time', 0); // 0 = Unlimited

//require_once('./lib/simple_html_dom.php');
require_once ('./MysqliDb.php');

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






$NUM_ANSWERS = 4; //todo: preguntar por esta variable
$num_answers = $_POST["num_answers"];


// Check if the form was submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Check if file was uploaded without errors
    if(isset($_FILES["file"]) && $_FILES["file"]["error"] == 0){

        $filename = $_FILES["file"]["name"];
        $filetype = $_FILES["file"]["type"];
        $filesize = $_FILES["file"]["size"];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");

            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $filename);
            echo "<br>Documento subido al servidor.<br>";

             //unzip
            $nodes = readDocx("upload/" . $filename);

            //parse nodes
            $question_id = 0;
            $db = new MysqliDb ('mysql', 'root', 'root', 'test');
            $db->autoReconnect = false;

            $_answers = 0;
            foreach ($nodes->childNodes AS $item) {
                $node_value = trim($item->nodeValue);
                if (empty($node_value)) {
                    continue;
                }

                //if is question:
                $re = '/^\d+\./m';
                preg_match_all($re, $node_value, $matches, PREG_SET_ORDER, 0);
                if ($matches) {

                    if ($question_id !== 0  && intval($NUM_ANSWERS) !== $_answers) {
                        echo "<br>Error, la pregunta con identificador ".$question_id." tiene un número erroneo de respustas</br>";
                    }

                    //Insert question
                    $patrón = '/^\d+\./m';
                    $sustitución = '${1}1,$3';
                    $question_text = preg_replace($patrón, '', $node_value); //eliminar numeración del documento

                    $cols = Array ("id", "question");
                    $db->where ('question', $node_value);
                    $alredy_exist_question = $db->get ("questions", null, $cols);

                    if ($alredy_exist_question && count($alredy_exist_question) > 0){
                        echo "<br>Error, la pregunta ".$node_value." ya existe en la bd.</br>";
                    }

                    $new_question = Array (
                        "test_id" => 1, //TODO: METER UNA VARIABLE CON EL TEST_ID!!!
                        "question" => $question_text,
                    );
                    $question_id = $db->insert ('questions', $new_question);
                    $_answers = 0;
                } else {
                    //Insert answer
                    $re = '/^.+\)/m';
                    preg_match_all($re, $node_value, $matchesAnswer, PREG_SET_ORDER, 0);

                    if ($matchesAnswer) {
                        $_answers++;

                        $new = new DomDocument;
                        $new->appendChild($new->importNode($item, true));
                        $xpath = new DomXPath($new);
                        $highlight_ar = $xpath->query("//w:highlight");

                        $highlight = 0;
                        if($highlight_ar && count($highlight_ar) > 0){
                            $highlight = 1;
                        }

                        $new_answer = Array (
                            "answer" => $node_value,
                            "question_id" => $question_id,
                            "correct" => $highlight
                        );
                        $answer_id = $db->insert ('answers', $new_answer);
                    }

                }



                //print $item->nodeName . " = " . $item->nodeValue . "<br>";
            }

            //print_r($nodes);



            echo "<BR><BR>Documento importado satisfactoriamente!";


        } else{
            echo "Error: There was a problem uploading your file. Please try again.";
        }

}
?>