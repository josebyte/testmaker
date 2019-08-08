<?php

$target_dir = "upload/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
echo $target_file;

function upload($target_file){
  $uploadOk = false;
  $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

echo "entra";
echo $fileType;

  if ($fileType === 'docx') {
      $uploadOk = true;
  }

  echo $uploadOk;
  return $uploadOk;
}



$uploaded = upload($target_file);

if ($uploaded) {
  $dir = 'upload/uncompressed/';
  $docxUncompressedFile = $dir . 'word/document.xml';

  // get the absolute path to $file
  $path = pathinfo(realpath($target_file), PATHINFO_DIRNAME);

  echo "<br>Path:";
  echo $path;
  echo "<br>target_file:";
  echo $target_file;
  $zip = new ZipArchive;
  $res = $zip->open($target_file);
  if ($res === TRUE) {
    $zip->extractTo($dir);
    $zip->close();

    //echo "WOOT! $file extracted to $path";

    $xmldata = simplexml_load_file($docxUncompressedFile) or die("Failed to load");
    echo $xmldata;
    foreach($xmldata->children() as $line) {
     echo $line->firstname . ", ";
    }

  } else {
    echo "Doh! I couldn't open $file";
  }
}


?>
