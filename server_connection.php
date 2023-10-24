<?php
//for testing
$serverName = "192.185.204.64";

$connectionString = [
	"Database"=>"islanter_iwats",
	"UID"=>"islanter_iwatsuser",
	"PWD"=>"3@37bjTe"
];
/*$connectionString = [
	"Database"=>"islanter_test",
	"UID"=>"islanter_iwatsuser",
	"PWD"=>"3@37bjTe"
]; */
$conn = sqlsrv_connect($serverName, $connectionString);




?>