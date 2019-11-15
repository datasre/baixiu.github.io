<?php
require_once('../../functions.php');

if(empty($_GET['id'])){
	exit('没有必要参数');
}
$id=$_GET['id'];
if(strstr($id,'[')){
	$id=str_replace('[','',$id);
	$id=str_replace(']','',$id);
}

$sql=sprintf("delete from comments where id in ($id)");
$rows=baixiu_execute($sql);
header('Content-Type:application/json');
echo json_encode($rows>0);