<?php

require_once 'data/init.php';
if(isset($_POST["submitter"])) submitData($_POST["submitter"]);

function checkData($data) {

}

function submitData($submitto) {
    
    global $hostname_ISANdb;
    global $database_ISANdb;
    global $username_ISANdb;
    global $password_ISANdb;

    $mysqli = new mysqli($hostname_ISANdb,$username_ISANdb,$password_ISANdb,$database_ISANdb);
    if ($mysqli->connect_error) {
        echo('<span class="error"><img alt="Ошибка" title="Ошибка" src="images/bullet_red.png" />Ошибка соединения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error . '</span>');
        exit(1);
    }
    $mysqli->query("SET NAMES 'UTF8'");

    $exFields = array("submb", "submitter"); //Поля-исключения, которые не будут добавляться в базу
    $reqFields = array("firstName","lastName","patrName"); //Поля, которые должны быть не пустыми
    $dateFields = array("birthDate"); //Поля, которые трактуются как даты

    foreach ($reqFields as $field)
        if( !isset($_POST[$field]) or ($_POST[$field] == null) ) {
            echo "<span class='error'><img alt='Ошибка' title='Ошибка' src='images/bullet_red.png' />Заполните необходимое поле: $field.</span>";
            exit(2);
        }

    foreach ($dateFields as $field)
        if( ($timestamp = strtotime($_POST[$field])) === false ) {
            echo "<span class='error'><img alt='Ошибка' title='Ошибка' src='images/bullet_red.png' />Неверно введена дата: $field.</span>";
            exit(2);
        }
        else { $_POST[$field] = date('Y-m-d', $timestamp);  }

    foreach ($_POST as $key => &$value) {
        $value = $mysqli->real_escape_string($value);
        if( ($value === "") or ($value == '0') ) $value = "NULL";
        else $value = "'".$value."'";
    }

    $query = "INSERT INTO $submitto SET ";
    $data = "";
    foreach ($_POST as $key => $value)
        if (!in_array($key, $exFields))
            $data .= "$key=$value, ";
    $data = rtrim($data, ", ");
    $query .= $data;
    if( $mysqli->query($query) ) {
        $status = $mysqli->affected_rows;
        $id = $mysqli->insert_id;
        echo "<span class='success'><img alt='Успех' title='Успех' src='images/bullet_green.png' />Добавлена $status запись, id=$id</span>";
    }
    else echo "<span class='error'><img alt='Ошибка' title='Ошибка' src='images/bullet_red.png' />Ошибка добавления. Запрос: $query.</span>";
    $mysqli->close();
}

//function submitData($submitto) {
//
//    $mysqli = new mysqli('localhost','jedi','sanmp15','isan');
//    if ($mysqli->connect_error) {
//        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
//    }
//    $mysqli->query("SET NAMES 'UTF8'");
//
//    switch ($submitto) {
//        case 'position': {
//                if ( $_POST['pname'] !== "" ) {
//                    if ($stmt = $mysqli->prepare("INSERT INTO position SET name=?")) {
//                        $stmt->bind_param('s', $_POST['pname']);
//                        $stmt->execute();
//                        $stmt->close();
//                        $mysqli->close();
//                        echo "Данные добавлены";
//                    }
//                    break;
//                }
//            }
//
//        case 'person': {
//
//                if ( $_POST['name'] !== "" ) {
//                    foreach ($_POST as &$s) {
//                        if ( ($s == "0") or ($s === "") ) $s = null;
//                    }
//                    $mySqlDate = date("Y-m-d", $_POST["birthDate"]);
//
//                    if ($stmt = $mysqli->prepare("INSERT INTO person SET name=?, birthDate=?, birthDateVisible=?,
//                        posId=?, posDate=?, departmentId=?, labId=?, isInTU=?, isInJSC=?, isInSC=?, isInDC=?,  photoURL=?, homeURL=?, visible=?,
//                        biography=?, contactId=NULL")) {
//                        $stmt->bind_param('ssiisiiiiiissis', $_POST['name'], $_POST['birthDate'], $_POST['birthDateVisible'], $_POST['posId'], $_POST['posDate'], $_POST['departmentId'],
//                                $_POST['labId'], $_POST['isInTU'], $_POST['isInJSC'], $_POST['isInSC'], $_POST['isInDC'], $_POST['photoURL'], $_POST['homeURL'], $_POST['visible'],
//                                $_POST['biography']);
//                        $stmt->execute();
//                        $stmt->close();
//                        $mysqli->close();
//                        echo "Данные добавлены";
//                    }
//                    break;
//                }
//            }
//
//        case 'department': {
//                if ( $_POST['dname'] !== "" ) {
//                    foreach ($_POST as &$s) {
//                        if ( ($s == "0") or ($s === "") ) $s = null;
//                    }
//                    if ($stmt = $mysqli->prepare("INSERT INTO department SET name=?, chiefId=?, asChiefId=?, area=?, url=?")) {
//                        $stmt->bind_param('siiss', $_POST['dname'], $_POST['chiefId'], $_POST['asChiefId'], $_POST['area'], $_POST['url']);
//                        $stmt->execute();
//                        $stmt->close();
//                        $mysqli->close();
//                        echo "Данные добавлены";
//                    }
//                    break;
//                }
//            }
//
//        case 'lab': {
//                if ( $_POST['lname'] !== "" ) {
//                    foreach ($_POST as &$s) {
//                        if ( ($s == "0") or ($s === "") ) $s = null;
//                    }
//                    if ($stmt = $mysqli->prepare("INSERT INTO lab SET name=?, chiefId=?, departmentId=?, area=?, url=?")) {
//                        $stmt->bind_param('siiss', $_POST['lname'], $_POST['lchiefId'], $_POST['ldepartmentId'], $_POST['larea'], $_POST['lurl']);
//                        $stmt->execute();
//                        $stmt->close();
//                        $mysqli->close();
//                    }
//                    break;
//                }
//            }
//    }
//}
