<?php
/* Выбор марка, модель, год выпуска автомобиля с использованием технологии Ajax
Взято http://htmlweb.ru/ajax/example/automarka.php
Разрешается использование в любых своих разработках.
Размешение кода в открытом доступе разрешается только с сохранением активной ссылки на источник.
Все остальные права принадлежат Колесникову Дмитрию Геннадьевичу (kdg@aaanet.ru, ICQ 17754093).
*/

require_once($_SERVER['DOCUMENT_ROOT'].'/config.php');
define("db_prefix","pb_");

function sql($query) {
$res=mysql_query ( $query );
if(!$res)die("Запрос:\n".$query."\n");
return $res;
}

CREATE TABLE IF NOT EXISTS pb_marka (
  id        int(10) unsigned NOT NULL auto_increment,
  `name`    char(64) collate cp1251_bin NOT NULL,
  product    tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COLLATE=cp1251_bin;

CREATE TABLE IF NOT EXISTS pb_model (
  id        int(10) unsigned NOT NULL auto_increment,
  marka        int(10) unsigned NOT NULL,
  `name`    char(64) collate cp1251_bin NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COLLATE=cp1251_bin;

if(isset($_GET['product'])){
   $product=$_SESSION['product']=intval($_GET['product']); // 1-импортная
   echo '<option value=0 selected>Выберите марку</option>';
   $res = sql('SELECT * FROM '.db_prefix.'marka WHERE product='.$product.' ORDER by name');
   while($row = mysql_fetch_array($res))
    echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>\n";
   exit;    
}
elseif(isset($_GET['marka'])){
   $marka=$_SESSION['marka']=intval($_GET['marka']);
   $res = sql('SELECT * FROM '.db_prefix.'model WHERE marka='.$marka.' ORDER by name');
   /* todo if(mysql_num_rows($res)>1) */ echo '<option value=0 selected>Выберите модель</option>';
    while($row = mysql_fetch_array($res))
        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>\n";
   exit;    
}elseif(isset($_GET['model'])){
    $id_model=intval(param('model'));
    $res = sql('SELECT * FROM '.db_prefix.'model WHERE id='.$id_model.' LIMIT 1');
    if(!($row = mysql_fetch_array($res)))die;
    $model=$row['name'];
    $id_marka=intval($row['marka']);
    $res = sql('SELECT * FROM '.db_prefix.'marka WHERE id='.$id_marka.' LIMIT 1');
    if(!($row = mysql_fetch_array($res)))die;
    $marka=$row['name'];
    $product=intval($row['product']);

echo "
<div style='border: #C5D3DC 1px solid; padding: 10px; width: 97%;'>
производитель=<b>".($product==1?' импортное ТС':'отечественное ТС')."</b>
<br/>марка=<b>".$marka."</b>
<br/>модель=<b>".$model."</b>
</div>";
   mysql_close();
   die;    
}


$script=@$_SERVER['SCRIPT_URL']; if(!$script)$script=@$_SERVER['REQUEST_URI'];

$_SESSION['marka']=$_SESSION['model']=0;
$_SESSION['product']=1;
?>
<table border="0" cellspacing="0" cellpadding="5" align="center">
<tr>
<td width="250">Производитель транспортного средства (ТС) 
<td>
<input type="radio" CHECKED value="1" name="product"
    onClick="ajaxLoad('marka', '<?=$script?>?product='+this.value, '','',''); ">&nbsp;Иностранное ТС<br>
<input type="radio" value="2" name="product"
    onClick="ajaxLoad('marka', '<?=$script?>?product='+this.value, '','',''); ">&nbsp;Отечественное ТС<br>
<td width="250">&nbsp;

<tr>
<td colspan="3" class="blank">
<tr>
<td id="markat">Марка ТС
<td><select style="WIDTH: 200px; height:21" name="marka" id="marka" onLoad='this.focus = false;' 
onChange="getObj('model').disabled=''; ajaxLoad('model', '<?=$script?>?marka='+this.options[this.selectedIndex].value, '','','');">
    <option value=0 selected>Выберите марку</option>
<?
$res = sql('SELECT * FROM '.db_prefix.'marka WHERE product=1 ORDER by name');
while($row = mysql_fetch_array($res))
   echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>\n";
?>
</select>
<tr>
<td colspan="3" class="blank">
<tr>
<td id="modelt">Модель ТС
<td><select style="WIDTH: 200px; height:21" name="model" id="model" <?=(@$marka?'':'disabled="disabled"') ?>  
onChange="ajaxLoad('info', '<?=$script?>?model='+this.options[this.selectedIndex].value, '','','');" >
    <option value selected disabled="disabled">Выберите модель</option>
</select>
<tr>
<td colspan="3" class="blank">
<tr>
<td id="explt">Год выпуска ТС
<td><select style="WIDTH: 200px; height:21" name="expl">
    <option value selected>Выберите значение</option>
<? for($i=0;$i<=15;$i++) echo "<option value='".$i."'>".(intval(date("Y"))-$i)."</option>\n";
?>
</select>

</table>

<div id="info"></div>


<script language=JavaScript><!--
function ajaxLoad(obj,url,defMessage,post,callback){
  var ajaxObj;
  if (defMessage) document.getElementById(obj).innerHTML=defMessage;
  if(window.XMLHttpRequest){ 
      ajaxObj = new XMLHttpRequest(); 
  } else if(window.ActiveXObject){ 
      ajaxObj = new ActiveXObject("Microsoft.XMLHTTP");  
  } else { 
      return; 
  } 
  ajaxObj.open ((post?'POST':'GET'), url);
  if (post&&ajaxObj.setRequestHeader)
      ajaxObj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=windows-1251;");
 
  ajaxObj.onreadystatechange = ajaxCallBack(obj,ajaxObj,(callback?callback:null));
  ajaxObj.send(post); 
  return false;
  } 
function updateObj(obj, data, bold, blink){ 
   if(bold)data=data.bold();
   if(blink)data=data.blink();
   document.getElementById(obj).innerHTML = data; // упрощенный вариант, работает не во всех браузерах
  } 
function ajaxCallBack(obj, ajaxObj, callback){
return function(){
    if(ajaxObj.readyState == 4){
       if(callback) if(!callback(obj,ajaxObj))return;
       if (ajaxObj.status==200)
        updateObj(obj, ajaxObj.responseText);
       else updateObj(obj, ajaxObj.status+' '+ajaxObj.statusText,1,1);
    }
}}

//--></script>
        
?>