<?php
require_once 'data/init.php';

$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (stripos($user_agent, 'MSIE 6.0') !== false && stripos($user_agent, 'MSIE 8.0') === false && stripos($user_agent, 'MSIE 7.0') === false) {
    if (!isset($HTTP_COOKIE_VARS["ie"])) {
        setcookie("ie", "yes", time() + 60 * 60 * 24 * 360);
        header("Location: /ie6/ie6.html");
    }
}

function getOptions($db) {

    global $hostname_ISANdb;
    global $database_ISANdb;
    global $username_ISANdb;
    global $password_ISANdb;
    $mysqli = new mysqli($hostname_ISANdb, $username_ISANdb, $password_ISANdb, $database_ISANdb);

    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }
    $mysqli->query("SET NAMES 'UTF8'");

    echo '<option value="0" selected>Не выбрано</option>';

    if ($db == "person") {
        if ($stmt = $mysqli->prepare("SELECT id, firstName, lastName, patronymicName FROM `$db` WHERE 1 ORDER BY lastName")) {
            $stmt->execute();
            $stmt->bind_result($id, $fname, $lname, $pname);

            while ($stmt->fetch()) {
                $lname = mb_convert_case($lname, MB_CASE_TITLE, "UTF-8");
                $result .= "<option value=\"$id\">$lname $fname $pname</option>";
            }
            $mysqli->close();
            echo $result;
            return;
        }
        else
            echo $stmt->error;
    }

    if ($result = $mysqli->query("SELECT id, name FROM $db WHERE 1")) {
        /* fetch associative array */
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
        }
        $result->close();
    }
    $mysqli->close();
}
?>
<?php require_once 'data/check.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="css/template.css" type="text/css" />
        <link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.5.custom.css" type="text/css" />
        <script src="js/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="js/jquery.form.js" type="text/javascript"></script>
        <script src="js/jquery-ui-1.8.5.custom.min.js" type="text/javascript"></script>
        <script src="js/jquery.cookie.js" type="text/javascript"></script>
        <script src="js/js.js" type="text/javascript"></script>
        <title>База данных</title>
    </head>

    <body>
        <div id="statusBar">
            <ul>
                <li><a href="#">Просмотр</a></li>
                <li><a href="#">Добавление</a></li>
                <li><a href="#">Редактирование</a></li>
                <li id="response"><span class="wait"><img alt="Ожидание" title="Ожидание" src="images/bullet_yellow.png" />Ожидание ввода</span></li>
                <li id="liexit"><button id='exit'>Выход</button></li>
            </ul>
        </div>

        <div id="pwrap" class="draggable">
            <div class="close"><img src="images/close.png" alt="Закрыть" title="Закрыть" /></div>
            <form id="pform" class="smallform" action="submitter.php" method="post">
                <h2>Добавить должность</h2>
                <div><label for="pname">Название</label><input type="text" id="pname" name="name" /></div>
                <div><input id="psubmb" name="submb" class="clear bsubmit" value="Добавить" type="submit" /></div>
                <div class="hidden"><input type="hidden" name="submitter" value="position" /></div>
            </form>
        </div>

        <div id="dwrap" class="draggable">
            <div class="close"><img src="images/close.png" alt="Закрыть" title="Закрыть" /></div>
            <form id="dform" class="smallform" action="submitter.php" method="post">
                <h2>Добавить отдел</h2>
                <div><label for="dname">Название</label><input id="dname" name="name" /></div>
                <div><label for="chiefId">Глава отдела</label><select id="chiefId" name="chiefId" ><?php getOptions("person") ?></select>
                    <img alt="Обновить" title="Обновить" class="add" id="refchief" src="images/refresh.png" /></div>
                <div><label for="asChiefId">Зам. главы отдела</label><select id="asChiefId" name="asChiefId" ><?php getOptions("person") ?></select>
                    <img alt="Обновить" title="Обновить" class="add" id="refaschief" src="images/refresh.png" /></div>
                <div><label for="area">Описание</label><textarea id="area" name="area" cols="40" rows="8"></textarea></div>
                <div class="clear">&nbsp;</div>
                <div><label for="url">URL</label><input type="text" id="url" name="url" value="" size="256" /></div>
                <div><input id="dsubmb" name="submb" class="clear bsubmit" value="Добавить" type="submit" /></div>
                <div class="hidden"><input type="hidden" name="submitter" value="department" /></div>
            </form>
        </div>

        <div id="lwrap" class="draggable">
            <div class="close"><img src="images/close.png" alt="Закрыть" title="Закрыть" /></div>
            <form id="lform" class="smallform" action="submitter.php" method="post">
                <h2>Добавить лабораторию</h2>
                <div><label for="lname">Название</label><input type="text" id="lname" name="name" /></div>
                <div><label for="lchiefId">Глава лаборатории</label><select id="lchiefId" name="chiefId" ><?php getOptions("person") ?></select>
                    <img alt="Обновить" title="Обновить" class="add" id="reflchief" src="images/refresh.png" /></div>
                <div><label for="ldepartmentId">Отдел</label><select id="ldepartmentId" name="departmentId" ><?php getOptions("department") ?></select>
                    <img alt="Обновить" title="Обновить" class="add" id="refldep" src="images/refresh.png" /></div>
                <div><label for="larea">Описание</label><textarea id="larea" name="area" cols="40" rows="8"></textarea></div>
                <div class="clear">&nbsp;</div>
                <div><label for="lurl">URL</label><input type="text" id="lurl" name="url" value="" size="256" /></div>
                <div><input id="lsubmb" name="submb" class="clear bsubmit" value="Добавить" type="submit" /></div>
                <div class="hidden"><input type="hidden" name="submitter" value="lab" /></div>
            </form>
        </div>

        <div id="wrapper">
            <div id="submitfor">
                <label for="submitfor">Заполняется для:</label>
                <select name="submitfor" id="personSelect">
<?php getOptions('person') ?>
                </select>
                <img src="images/lock.png" alt="Разблокировать" title="Разблокировать" id="lock" />
            </div>
            <div id="tabs">
                <ul>
                    <li><a href="#add"><span>Добавить</span></a></li>
                    <li><a href="#general"><span>Основное</span></a></li>
                    <li><a href="#positions"><span>Должности</span></a></li>
                    <li><a href="#addition"><span>Дополнительно</span></a></li>
                    <li><a href="#contacts"><span>Контакты</span></a></li>
                </ul>

                <div id="formwrap">
                    <h2>Добавить человека</h2>
                    <form id="form" action="submitter.php" method="post">
                        <div id="add">
                            <div><label for="firstName">Имя</label><input type="text" id="firstName" name="firstName" value="" size="32" /></div>
                            <div><label for="lastName">Фамилия</label><input type="text" id="lastName" name="lastName" value="" size="32" /></div>
                            <div><label for="patrName">Отчество</label><input type="text" id="patrName" name="patrName" value="" size="32" /></div>
                        </div>
                        <div id="general">
                            <div><label for="birthDate">Дата рождения</label> <input type="text" id="birthDate" name="birthDate" value="" size="32" /></div>
                            <div><label for="birthDateVisible">Отображать дату рождения</label> <input type="checkbox" class="wauto" id="birthDateVisible" name="birthDateVisible" value="1" checked="checked" /></div>
                            <div><label for="departmentId">Отдел</label> <select name="departmentId" id="departmentId"><?php getOptions("department"); ?></select>
                                <img alt="Добавить отдел" id="dshow" title="Добавить отдел" class="add" src="images/add.png" />
                                <img alt="Обновить" title="Обновить" class="add" id="refdep" src="images/refresh.png" /></div>
                            <div><label for="labId">Лаборатория</label> <select name="labId" id="labId"><?php getOptions("lab") ?></select>
                                <img alt="Добавить лабораторию" id="lshow" title="Добавить лабораторию" class="add" src="images/add.png" />
                                <img alt="Обновить" title="Обновить" class="add" id="reflab" src="images/refresh.png" /></div>
                        </div>
                        <div id="positions">
                            <div><label for="posId">Должность</label> <select name="posId" id="posId"><?php getOptions("position"); ?></select>
                                <img alt="Добавить должность" id="pshow" title="Добавить должность" class="add" src="images/add.png" />
                                <img alt="Обновить" title="Обновить" class="add" id="refpos" src="images/refresh.png" /></div>
                            <div><label for="posDate">Дата вступления в должность</label><input type="text" id="posDate" name="posDate" value="" size="32" /></div>
                            <div><label for="isInTU">Член профсоюза</label> <input type="checkbox" class="wauto" id="isInTU" name="isInTU" value="1" /></div>
                            <div><label for="isInJSC">Член совета молодых ученых</label> <input type="checkbox" class="wauto" id="isInJSC" name="isInJSC" value="1" /></div>
                            <div><label for="isInSC">Член ученого совета</label> <input type="checkbox" class="wauto" id="isInSC" name="isInSC" value="1" /></div>
                            <div><label for="isInDC">Член диссертационного совета</label> <input type="checkbox" class="wauto" id="isInDC" name="isInDC" value="1" /></div>
                            <div><label for="visible">Показывать</label> <input type="checkbox" class="wauto" name="visible" id="visible" value="1" checked="checked" /></div>
                        </div>
                        <div id="addition">
                            <div><label for="biography">Биография</label> <textarea name="biography" id="biography" rows="5" cols="47" ></textarea></div>
                            <div class="clear">&nbsp;</div>
                            <div><label for="homeURL">Домашняя страничка</label> <input type="text" name="homeURL" id="homeURL" value="" size="32" /></div>
                            <div><label for="photoURL">Ссылка на фото</label> <input type="text" name="photoURL" id="photoURL" value="" size="32" /></div>
                        </div>
                        <div id="contacts">
                            <div><label for="address">Адрес</label><textarea name="address" id="address" rows="5" cols="47" ></textarea></div>
                            <div class="clear">&nbsp;</div>
                            <div><label for="email">Email</label> <input type="text" name="email" id="email" value="" size="32" /></div>
                            <div><label for="workPN1">Рабочий телефон 1</label><input type="text" name="workPN1" id="workPN1" value="" size="32" /></div>
                            <div><label for="workPN2">Рабочий телефон 2</label><input type="text" name="workPN2" id="workPN2" value="" size="32" /></div>
                            <div><label for="fax1">Факс 1</label><input type="text" name="fax1" id="fax1" value="" size="32" /></div>
                            <div><label for="fax2">Факс 2</label><input type="text" name="fax2" id="fax2" value="" size="32" /></div>
                            <div><label for="intPN1">Внутренний телефон 1</label><input type="text" name="intPN1" id="intPN1" value="" size="32" /></div>
                            <div><label for="intPN2">Внутренний телефон 2</label><input type="text" name="intPN2" id="intPN2" value="" size="32" /></div>
                            <div><label for="mPN1">Мобильный телефон 1</label><input type="text" name="mPN1" id="mPN1" value="" size="32" /></div>
                            <div><label for="mPN2">Мобильный телефон 2</label><input type="text" name="mPN2" id="mPN2" value="" size="32" /></div>
                            <div><label for="hPN">Домашний телефон</label><input type="text" name="hPN" id="hPN" value="" size="32" /></div>
                        </div>
                        <!--<div><input id="submb" name="submb" class="clear wauto bsubmit" value="Добавить" type="submit" /></div>-->
                        <div class="hidden"><input type="hidden" name="submitter" value="person" /></div>
                    </form>
                </div>
            </div>
        </div>
        <p>&nbsp;</p>
    </body>
</html>
