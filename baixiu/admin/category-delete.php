<?php
/**
 * 根据客户端传递过来的ID删除对应的数据
 */

require_once('../functions.php');

 if(empty($_GET['id'])){
	 exit('缺少必要参数');
 }
 $id=$_GET['id'];
// $data=explode(',',$id);
 //id=('1 or 1=1');   
 //sql注入  int('1 or 1=1')=1

$rows=baixiu_execute("delete from categories where id in ($id);");



//删除成功和失败都要返回界面
header('Location:/admin/categories.php');

