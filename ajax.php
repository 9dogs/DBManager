<?php

require_once 'data/init.php';

$request = $_GET["q"];
if ($request)
    echo loadData($request);

function loadData($request) {

    global $hostname_ISANdb;
    global $database_ISANdb;
    global $username_ISANdb;
    global $password_ISANdb;

    $mysqli = new mysqli($hostname_ISANdb, $username_ISANdb, $password_ISANdb, $database_ISANdb);

    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }

    $mysqli->query("SET NAMES 'UTF8'");

    if ($request == "person") {
        if ($stmt = $mysqli->prepare("SELECT id, firstName, lastName, patrName FROM `$request` WHERE 1")) {
            $stmt->execute();
            $stmt->bind_result($id, $fname, $lname, $pname);

            while ($stmt->fetch()) {
                $result .= "<option value=\"$id\">$lname $fname $pname</option>";
            }
            $mysqli->close();
            return $result;
        }
    }

    if ($stmt = $mysqli->prepare("SELECT id, name FROM `$request` WHERE 1")) {
        $stmt->execute();
        $stmt->bind_result($id, $name);

        $result = "<option value=\"0\">Не выбрано</option>";
        while ($stmt->fetch()) {
            $result .= "<option value=\"$id\">$name</option>";
        }
        $mysqli->close();
        return $result;
    }
}
