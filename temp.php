<?php
$hostname_ISANdb = "localhost";
$database_ISANdb = "isan";
$username_ISANdb = "jedi";
$password_ISANdb = "sanmp15";

ini_set('max_execution_time', 300);


$mysqli = new mysqli($hostname_ISANdb, $username_ISANdb, $password_ISANdb, $database_ISANdb);

    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }
    $mysqli->query("SET NAMES 'UTF8'");

    $mysqli->query("SET foreign_key_checks=0;");
    $mysqli->query("LOAD DATA INFILE './personal1.txt' into table `person` FIELDS TERMINATED BY ';' ENCLOSED BY '" . "\"'");
    echo $mysqli->error;
?>