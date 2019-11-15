<?php
 
 require_once('config.php');
 /**
  * 封装共用的函数
  */
  session_start();
  
  
  
  /**
   * 获取当前登陆用户信息，如果没有获取到，则自动跳转到登陆页面
   */
 function baixiu_get_current_user()
 {
     if (empty($_SESSION['current_login_user'])) {
         // 没有当前登陆用户信息,意味着没有登陆
         header('Location:/admin/login.php');
         exit();
     }
     return $_SESSION['current_login_user'];
 }
 
 
 /**
  * 通过一个数据库查询获取数据
  * 获取多条数据   =>索引数组套关联数字
  */
 function baixiu_fetch_all($sql)
 {
     $connection=mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
     if (!$connection) {
         exit('数据库连接失败');
     }
     $query=mysqli_query($connection, $sql);
     if (!$query) {
         exit('查询失败');
     }
     while ($row=mysqli_fetch_assoc($query)) {
         $result[]=$row;
     }
     mysqli_free_result($query);
     mysqli_close($connection);
     return $result;
 }
 
 /**
  * 获取单条数据  =>关联数组
  * @param {Object} $sql
  */
 function baixiu_fetch_one($sql)
 {
     $res=baixiu_fetch_all($sql);
     return isset($res[0])?$res[0]:null;
 }
 
 /**
  * 执行一个增删改语句
  * @param {Object} $sql
  */
 function baixiu_execute($sql)
 {
     $connection=mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
     if (!$connection) {
         exit('数据库连接失败');
     }
     $query=mysqli_query($connection, $sql);
     if (!$query) {
         exit('查询失败');
     }
     $affected_rows=mysqli_affected_rows($connection);
	 mysqli_close($connection);
	 return $affected_rows;
 }
