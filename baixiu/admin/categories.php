<?php
 
 require_once('../functions.php');
 baixiu_get_current_user();
 
 /**
  * 添加数据
  */
 function add_category()
 {
     global $error_message;
     if (empty($_POST['name'])) {
         $error_message='请填写分类名称';
         return;
     }
     if (empty($_POST['slug'])) {
         $error_message='请填写别名';
         return;
     }
     $name=$_POST['name'];
     $slug=$_POST['slug'];
     $rows=baixiu_execute("insert into categories values(null,'{$slug}','{$name}');");
     if ($rows<=0) {
         $error_message='添加失败';
         return;
     }
 }
 
 //判断是否为需要编辑的数据
 if(!empty($_GET['id'])){
 	 //客户端通过URL传递了一个ID
 	 $id=$_GET['id'];
 	 $current_edit_category=baixiu_fetch_one("select * from categories where id='{$id}';");
 }
 
 /**
  * 编辑数据
  */
 function edit_category(){
	 global $current_edit_category;
	 $id=$_GET['id'];
	 $name=empty($_POST['name'])?$current_edit_category['name']:$_POST['name'];
	 $current_edit_category['name']=$name;
	 $slug=empty($_POST['slug'])?$current_edit_category['slug']:$_POST['slug'];
	 $current_edit_category['slug']=$slug;
	 $rows=baixiu_execute("update categories set slug='{$slug}',name='{$name}' where id='{$id}';");
	 if ($rows<=0) {
	     $error_message='编辑失败';
	     return;
	 }
 }
 
 //添加操作
 if ($_SERVER['REQUEST_METHOD']==='POST') {
     if(empty($_GET['id'])){
		 add_category();
	 }else{
		 edit_category();
	 }
 }
 
 
 //查询全部的分类数据
 $categories=baixiu_fetch_all('select * from categories;');
 
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include('inc/navbar.php'); ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>分类目录</h1>
      </div>
	  	 <!-- 有错误信息时展示 -->
     <?php if (isset($error_message)): ?>
		<div class="alert alert-danger">
			<strong>错误！</strong><?php echo $error_message; ?>
		</div>
	<?php endif; ?>
      <div class="row">
        <div class="col-md-4">
            <?php if(isset($current_edit_category)): ?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $current_edit_category['id']; ?>" method="post">
					<h2>编辑<?php echo $current_edit_category['name']; ?></h2>
					<div class="form-group">
					<label for="name">名称</label>
					<input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name']; ?>">
					</div>
					<div class="form-group">
					<label for="slug">别名</label>
					<input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug']; ?>">
					</div>
					<div class="form-group">
					<button class="btn btn-primary" type="submit">保存</button>
					</div>
			<?php else: ?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
					<h2>添加新分类目录</h2>
					<div class="form-group">
					<label for="name">名称</label>
					<input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
					</div>
					<div class="form-group">
					<label for="slug">别名</label>
					<input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
					</div>
					<div class="form-group">
					<button class="btn btn-primary" type="submit">添加</button>
					</div>
			<?php endif; ?>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action" style="height: 30px;">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
				<?php foreach ($categories as $item): ?>
					<tr>
					  <td class="text-center">
						  <input type="checkbox" data-id='<?php echo $item['id']; ?>'>
					  </td>
					  <td><?php echo $item['name']; ?></td>
					  <td><?php echo $item['slug'] ?></td>
					  <td class="text-center">
					    <a href="/admin/categories.php?id=<?php echo $item['id']; ?>" class="btn btn-info btn-xs">编辑</a>
					    <a href="/admin/category-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
					  </td>
					</tr>
				<?php endforeach; ?>           
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
	
	<?php $current_page='categories'; ?>
	<?php include('inc/sidebar.php'); ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
	  $(function(){
	  		var arr=[];
	  		var check_all=$('table thead tr th input');
	  		var check=$('table tbody tr td input');
	  		check_all.on('click',function(){
	  			var checked=$(this).prop('checked');
	  			check.prop('checked',checked);
	  			if(checked){
	  				$('.page-action>a').show();
	  				for(var i=0;i<check.length;i++){
	  					arr.push(check.eq(i).data('id'));
	  				}
	  			}else{
	  				$('.page-action>a').hide();
	  				arr=[];
	  			}
	  			$('.page-action>a').attr('href','/admin/category-delete.php?id='+arr);
	  		});			
	  		check.on('click',function(){
	  			var input_num=$('table tbody tr td input:checked');
	  			var allLength=check.length;
	  			var checkLength=input_num.length;
	  			if(checkLength>=1){
	  				$('.page-action>a').show();
	  			}else{
	  				$('.page-action>a').hide();
	  			}
	  			if(checkLength===allLength){
	  				check_all.prop('checked',true);
	  			}else{
	  				check_all.prop('checked',false);
	  			}
	  			for(var i=0;i<checkLength;i++){
	  				arr.push(input_num.eq(i).data('id'));
	  			}
	  			$('.page-action>a').attr('href','/admin/category-delete.php?id='+arr);
	  			arr=[];
	  		});
	  		
	  		var checkLength=$('table tbody tr td input:checked').length;
	  		if(checkLength>=1){
	  			$('.page-action>a').show();
	  		}else{
	  			$('.page-action>a').hide();
	  		}
	  });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
