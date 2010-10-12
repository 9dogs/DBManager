<? 
// Скрипт проверки 

# Соединямся с БД 
$mysqli = new mysqli('localhost', 'jedi', 'sanmp15', 'isan');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
$mysqli->query("SET NAMES 'UTF8'");

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
    $query = $mysqli->query("SELECT *,INET_NTOA(user_ip) FROM users WHERE user_id = '".intval($_COOKIE['id'])."' LIMIT 1");
        $userdata = $query->fetch_assoc();

        if( ($userdata['user_hash'] !== $_COOKIE['hash']) or ($userdata['user_id'] !== $_COOKIE['id']) ) {
            //or ( ($userdata['user_ip'] !== $_SERVER['REMOTE_ADDR']) and ($userdata['user_ip'] !== "0") ) ) {
            setcookie("id", "", time() - 3600*24*30*12, "/");
            setcookie("hash", "", time() - 3600*24*30*12, "/");
            header("Location: login.php");
        }
        //else echo "<div id='greeting'><p><span>Здравствуйте, ".$userdata['user_login'].".</span><button id='exit'>Выход</button></p></div>";
    $query->close();
}
else header("Location: login.php");
$mysqli->close();
?> 