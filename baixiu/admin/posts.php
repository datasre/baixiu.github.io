<?php
 
 require_once('../functions.php');
 baixiu_get_current_user();
 
 //处理分页参数
$page=empty($_GET['page'])?1:(int)$_GET['page'];
$size=10;

//接收筛选参数
$where='1=1';
if(isset($_GET['category'])&&$_GET['category']!='all'){
	$where.=' and posts.category_id='.$_GET['category'];
}
if(isset($_GET['postsStatus'])&&$_GET['postsStatus']!=-1){
	$where.=" and posts.`status`='{$_GET['postsStatus']}'";
}

$tol=''; 
if(isset($_GET['category'])){
	$tol.='&category='.$_GET['category'];
}
if(isset($_GET['postsStatus'])){
	$tol.="&postsStatus=".$_GET['postsStatus'];
}
// var_dump($tol);

 $total_count=baixiu_fetch_one("select
 count(1) as num
 from posts
 inner join categories on posts.category_id=categories.id
 inner join users on posts.user_id=users.id
   where {$where};");
 $total_page=(int)ceil($total_count['num']/$size);//最大页码(seil向上取整得到的是float类型)
 if ($page>$total_page) {
     header("Location:/admin/posts.php?page=".$total_page.$tol);
 }
 if ($page<1) {
     header("Location:/admin/posts.php?page=1".$tol);
 }
 
$offset=($page-1)*$size;
 
 // $posts=baixiu_fetch_all("select * from posts");
 
 //联合查询
 $posts=baixiu_fetch_all("select
 posts.id,
 posts.title,
 categories.name,
 posts.created,
 posts.`status`,
 users.nickname
 from posts
 inner join categories on posts.category_id=categories.id
 inner join users on posts.user_id=users.id
  where {$where}
  order by posts.created desc
  limit {$offset},{$size};");
  
  
  //处理分页页码
  
  $visiables=5;
  $region=(int)floor(($visiables-1)/2);//左右区间
  $begin=$page-$region;//开始页码
  $end=$page+$region;//结束页码
  if ($begin<1) {
      $begin=1;
      $end=$visiables;
  }
  
  
  if ($end>$total_page) {
      $end=$total_page;
      $begin=$end-($visiables-1);
      if ($begin<1) {
          $begin=1;
      }
  }
  

  
  

/**
 * 转换状态显示
 * @param {Object} $status
 */
 function convert_status($status)
 {
     $dict=array(
        'published'=>'已发布',
        'drafted'=>'草稿',
        'trashed'=>'回收站'
     );
     return isset($status)?$dict[$status]:'未知';
 }
 
 function convert_date($created)
 {
     date_default_timezone_set('UTC');
     $timetamp=strtotime($created);
     return date('Y年m月d日 <b\r> H:i:s', $timetamp);
 }
 
 // function get_category($category_id){
    //  $name=baixiu_fetch_one("select name from categories where id={$category_id};");
    //  return $name['name'];
 // }
 //
 // function get_user($user_id){
 // 	 $nickname=baixiu_fetch_one("select nickname from users where id={$user_id};");
 // 	 return $nickname['nickname'];
 // }
 
 $categories=baixiu_fetch_all('select * from categories; ');
 
 $posts_status=baixiu_fetch_all('select * from posts group by status;');
 
 
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="/admin/post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action" style="height: 30px;">
        <!-- show when multiple checked -->
			<a class="btn btn-danger btn-sm" href="javascript:;" style="display: none;">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
          <select name="category" class="form-control input-sm">
			  <option value="all">所有分类</option>
			<?php foreach ($categories as $item): ?>
				<option value="<?php echo $item['id']; ?>" <?php echo isset($_GET['category'])&&$_GET['category']==$item['id']?'selected':''; ?> ><?php echo $item['name']; ?></option>
            <?php endforeach; ?>
          </select>
          <select name="postsStatus" class="form-control input-sm">
            <option value="-1">所有状态</option>
			<?php foreach ($posts_status as $item): ?>
				<option value="<?php echo $item['status']; ?>" <?php echo isset($_GET['postsStatus'])&&$_GET['postsStatus']==$item['status']?'selected':''; ?>  ><?php echo convert_status($item['status']); ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
		  <?php if ($page-1>0): ?>
		   <li><a href="?page=1<?php echo $tol; ?>">第一页</a></li>
		  <?php endif; ?>
          <?php if ($page-1>0): ?>
		   <li><a href="?page=<?php echo $page-1 ?><?php echo $tol; ?>">上一页</a></li>
		  <?php endif; ?>
		  
		  <?php if ($begin > 1): ?>
			<li class="disabled"><span>···</span></li>
		  <?php endif; ?>
		  
          <?php for ($i=$begin;$i<=$end;$i++): ?>
			<li class=<?php echo $i===$page?'active':''; ?>>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $i; ?><?php echo $tol; ?>"><?php echo $i ?></a>
			</li>
		  <?php endfor; ?>
		  
		  <?php if ($end < $total_page): ?>
		  	<li class="disabled"><span>···</span></li>
		  <?php endif; ?>
		  
		  <?php if ($page+1<=$total_page): ?>
           <li><a href="?page=<?php echo $page+1 ?> <?php echo $tol; ?>">下一页</a></li>
		  <?php endif; ?>
		  <?php if ($page+1<=$total_page): ?>
		   <li><a href="?page=<?php echo $total_page ?> <?php echo $tol; ?>">最后页</a></li>
		  <?php endif; ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
			<tr>
			  <td class="text-center"><input type="checkbox" data-id='<?php echo $item['id']; ?>'></td>
			  <td><?php echo $item['title']; ?></td>
			  <td><?php echo $item['nickname']; ?></td>
			  <td><?php echo $item['name']; ?></td>
			  <td class="text-center"><?php echo convert_date($item['created']); ?></td>
			  <td class="text-center"><?php echo convert_status($item['status']); ?></td>
			  <td class="text-center">
			    <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
			    <a href="/admin/posts-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
			  </td>
			</tr>
		  <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

	<?php $current_page='posts'; ?>
	<?php include('inc/sidebar.php'); ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
	  $(function(){
		  var deleteAll=$('.page-action>a');
		  var allElection=$('thead input');
		  var singleElection=$('tbody input');
		  var checkedAll=$(singleElection).length;
		  var arr=[];
		  allElection.on('click',function(){
			  var checked=allElection.prop('checked');
			  singleElection.prop('checked',checked);
			  if(checked){
				  deleteAll.show();
				  for(var i=0;i<checkedAll;i++){
						var num=singleElection.eq(i).data('id');
				  		arr.push(num);
				  }
			  }else{
				  deleteAll.hide();
				  arr=[];
			  }
			  deleteAll.attr('href','/admin/posts-delete.php?id='+arr);
		  });
		  singleElection.on('click',function(){
			 var checkedCount=$('tbody input:checked');
			 if(checkedCount.length==checkedAll){
				 allElection.prop('checked',true);
			 }else{
				 allElection.prop('checked',false);
			 }
			 if(checkedCount.length>0){
				 deleteAll.show();
			 }else{
				 deleteAll.hide();
			 }
			 for(var i=0;i<checkedCount.length;i++){
				 var num=checkedCount.eq(i).data('id');
				 arr.push(num);
			 }
			 deleteAll.attr('href','/admin/posts-delete.php?id='+arr);
			 arr=[];
		  });
		  if($('tbody input:checked').length>0){
		  	deleteAll.show();
		  }else{
		  	deleteAll.hide();
		  }
	  });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
