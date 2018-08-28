<?php
require_once 'DataProvider.php';

$DataProvider = new DataProvider('API.json');
$DataProvider->setAPIConfig('API.json');
$DataProvider->setMongoDB('localhost', 27017);
$data = @rawurldecode($_GET["data"]);

echo $DataProvider->Query($data);
?>
