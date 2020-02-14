<?php
$path = $_POST['path'];
$text = $_POST['text'];
$path = "trans/".$path;
$file_name = basename ($path);
$real_path = str_replace('/'.$file_name,'',$path);
if ( ! file_exists( $real_path ) ) {
    mkdir( $real_path, 0777, true );
}

$myfile = fopen($path, "a") or die("Unable to open file!");
fwrite($myfile, $text);
fclose($myfile);

//file_put_contents("trans/".$path, $text);
