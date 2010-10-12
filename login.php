<? 
// Страница авторизации 

# Функция для генерации случайной строки 
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
} 

if(isset($_POST['submit'])) {
    # Соединямся с БД
    $mysqli = new mysqli('localhost', 'jedi', 'sanmp15', 'isan');
    if ($mysqli->connect_error) {
        die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }
    $mysqli->query("SET NAMES 'UTF8'");
    # Вытаскиваем из БД запись, у которой логин равняеться введенному
    $query = $mysqli->query("SELECT user_id, user_password FROM users WHERE user_login='".$mysqli->real_escape_string($_POST['login'])."' LIMIT 1");
        $data = $query->fetch_assoc();

        # Сравниваем пароли
        if($data['user_password'] === md5(md5($_POST['password']))) {
            # Генерируем случайное число и шифруем его
            $hash = md5(generateCode(10));

            if(!@$_POST['not_attach_ip']) {
                # Если пользователя выбрал привязку к IP
                # Переводим IP в строку
                $insip = ", user_ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
            }

            # Записываем в БД новый хеш авторизации и IP
            $mysqli->query("UPDATE users SET user_hash='".$hash."' ".$insip." WHERE user_id='".$data['user_id']."'");

            # Ставим куки
            setcookie("id", $data['user_id'], time()+60*60*24*30);
            setcookie("hash", $hash, time()+60*60*24*30);

            # Переадресовываем браузер на страницу проверки нашего скрипта
            header("Location: index.php");
            exit();
        }
        else {
            echo "<div id='errorBox'>Неправильный логин/пароль</div>";
        }
    $mysqli->close();
} 
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="css/template.css" type="text/css" />
        <link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.8.4.custom.css" type="text/css" />
        <script src="js/jquery-1.4.2.js" type="text/javascript"></script>
        <script src="js/jquery.form.js" type="text/javascript"></script>
        <script src="js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
        <script src="js/jquery.validate.js" type="text/javascript"></script>
        <title>База данных | Вход</title>
    </head>
    <body>
        <div id="loginwrap">
            <form action="<?php $_SERVER['PHP_SELF'] ?>" id="login" method="POST">
                <div><h2>Войти</h2></div>
                <div><label for="login">Логин</label><input type="text" value="" size="32" id="login" name="login" /></div>
                <div><label for="password">Пароль</label><input id="password" type="password" name="password" /></div>
                <div><label for="not_attach_ip">Не прикреплять к IP</label><input type="checkbox" class="wauto" id="not_attach_ip" name="not_attach_ip" /></div>
                <div><input name="submit" type="submit" class="wauto" value="Войти" /></div>
            </form>
        </div>
    </body>
</html>
