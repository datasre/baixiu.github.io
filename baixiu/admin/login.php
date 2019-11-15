<?php

//开始session
session_start();

//载入配置文件
require_once('../config.php');

 function login()
 {
     global $error_message;
     if (empty($_POST['email'])) {
         $error_message='请输入邮箱!';
         return;
     }
     if (empty($_POST['password'])) {
         $error_message='请输入密码!';
         return;
     }
     $email=$_POST['email'];
     $password=$_POST['password'];
     
     $connection=mysqli_connect(BAIXIU_DB_HOST, BAIXIU_DB_USER, BAIXIU_DB_PASS, BAIXIU_DB_NAME);
     if (!$connection) {
         exit('<h1>数据库连接失败</h1>');
     }
        
     $query=mysqli_query($connection, "select * from users where email='{$email}' limit 1;");
     if (!$query) {
         $error_message='登陆失败，请重试!';
         return;
     }
     $user=mysqli_fetch_assoc($query);
     if (!$user) {
         $error_message='邮箱和密码不匹配!';
         return;
     }
     //md5进行加密(单纯的md5已经不安全了)   一般密码是加密存储的
     if ($user['password']===md5($password)) {
         //存一个登陆标识
		 //为了后面可以直接拿到用户信息,这里直接将用户信息放在session中
         $_SESSION['current_login_user']=$user;
         mysqli_free_result($query);
         mysqli_close($connection);
         header('Location:/admin/');
     } else {
         mysqli_free_result($query);
         mysqli_close($connection);
         $error_message='邮箱和密码不匹配!';
     }
 }
 
 if ($_SERVER['REQUEST_METHOD']==='POST') {
     login();
 }
 
 //退出功能
 if(isset($_GET['action'])&&$_GET['action']==='logout'){
	 //删除session
	 unset($_SESSION['current_login_user']);
 }
 
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
	  <!-- 可以通过在form上添加novalidate去取消浏览器自带的校验功能 -->
	  <!-- 可以通过在form上添加autocomplete去取消浏览器的自动完成功能 -->
    <form class="login-wrap<?php echo isset($error_message)?' shake animated':''; ?>" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" novalidate="novalidate" autocomplete="off" >
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)): ?>
		<div class="alert alert-danger">
		  <strong>错误！</strong> <?php echo $error_message; ?>
		</div>
	  <?php endif;?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" value="<?php echo empty($_POST['email'])?'':$_POST['email']; ?>" name="email" type="email" class="form-control" placeholder="邮箱" autofocus>
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
	  <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
	  //利用ajax拿到服务端的数据
	  //目标:在用户输入自己的邮箱过后,页面上展示这个邮箱对应的头像
	  $(function($){
		  var emailFormat=/^\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
		  $('#email').on('blur',function(){
			  var val=$(this).val();
			  // console.log(val);
			  if(!val||!emailFormat.test(val)) return;
			  $.get('/admin/api/avatar.php',{email:val},function(res){
			  		// console.log(res);
					if(!res) return;
			  		$('.avatar').attr('src',res);
			  });
			  var val2=$(this).val();
		  });
	  });
  </script>
</body>
</html>
