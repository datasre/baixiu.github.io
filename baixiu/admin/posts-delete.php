<?php

require_once('../functions.php');

if(empty($_GET['id'])){
	exit('缺少必要参数');
}
$id=$_GET['id'];

// $_SERVER['HTTP_REFERER']
// http中的referer用来标识页面当前请求的来源
// var_dump($_SERVER['HTTP_REFERER']);
baixiu_execute("delete from posts where id in ($id);");
header('Location:'.$_SERVER['HTTP_REFERER']);