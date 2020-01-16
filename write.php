<?php 

header('Content-Type: application/json');
// $json_get = json_encode($data);
$myfile = fopen("epum.json", "w") or die("Unable to open file!");


$json = $_POST["json"];
error_log(print_r($_POST["json"]));

$json_string = json_encode($_POST["QueryListView"], JSON_UNESCAPED_UNICODE);
fwrite($myfile, $json_string);
fclose($myfile);

?>