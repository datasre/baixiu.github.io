<?php

/**
 * 根据用户邮箱获取用户头像
 */

 require_once('../../config.php');
 if(empty($_GET['email'])){
	 echo '/static/assets/img/default.png';
 }else{
	 $email=$_GET['email'];
	 $conn=mysqli_connect(BAIXIU_DB_HOST,BAIXIU_DB_USER,BAIXIU_DB_PASS,BAIXIU_DB_NAME);
	 if(!$conn){
	 	 exit('连接数据库失败');
	 }
	 $query=mysqli_query($conn,"select avatar from users where email='{$email}' limit 1;");
	 if(!$query){
	 	 exit('数据查询失败');
	 }
	 $user=mysqli_fetch_assoc($query);
	 if(empty($user)){
		 echo '/static/assets/img/default.png';
	 }else{
		echo $user['avatar'];
	 }
	 mysqli_free_result($query);
	 mysqli_close($conn);
 }
 
 ?>