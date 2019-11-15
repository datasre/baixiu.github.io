<?php
 
 require_once('../functions.php');
 baixiu_get_current_user();
 
 ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn-agress btn btn-info btn-sm">批量批准</button>
          <button class="btn-refuse btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn-delete btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right"></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

	<?php $current_page='comments'; ?>
	<?php include('inc/sidebar.php'); ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script id="comment" type="text/x-jsrender">
	  {{for comments}}
		  <tr {{if status=='rejected'}} class="danger" {{else status=='held'}} class="warning"  {{/if}} data-id="{{:id}}">
			<td class="text-center"><input type="checkbox"></td>
			<td>{{:author}}</td>
			<td>{{:content}}</td>
			<td>{{:title}}</td>
			<td>{{:created}}</td>
			<td>{{:status}}</td>
			<td class="text-center">
				{{if status=='held'}}
				<a href="post-add.php" class="btn btn-info btn-xs">批准</a>
				<a href="javascript:;" class="btn btn-warning btn-xs">拒绝</a>
				{{/if}}
				<a href="javascript:;" class="btn-delete btn btn-danger btn-xs">删除</a>
			</td>
		  </tr>
	  {{/for}}
  </script>
  <script>
	  $(function($){
		 $(document).ajaxStart(function(){
			 NProgress.start();
		 });
		 
		 var currentPage=1;
		 
		 function loadPageData(page){
			 $.getJSON('/admin/api/comment.php',{page:page},function(res){
				 if(page>res.totalPage){
					 loadPageData(res.totalPage);
					 return;
				 }
				 $('.pagination').twbsPagination('destroy');
				 $('.pagination').twbsPagination({
				 	 totalPages:res.totalPage,
				 	 visiblePages: 5,
				 	 first:'首页',
				 	 last:'末页',
				 	 prev:'上一页',
				 	 next:'下一页',
					 startPage:page,
					 initiateStartPageClick:false,
				 	 onPageClick:function(event,page){
				 		loadPageData(page);
				 	 }
				 });				 
				 $(document).ajaxStop(function(){
				 	NProgress.done();
				 });
			 	 // console.log(res);
			 	 var html=$('#comment').render({comments:res.comm});
			 	 $('tbody').html(html);
				 currentPage=page;
			});
		 }
		 
		 loadPageData(currentPage);
		 // 删除功能
		 // 由于删除按钮是动态添加的,而且执行动态添加的按钮是在此之后执行的,过早注册不上
		 // 所以可以采用委托事件来进行事件绑定(事件冒泡)
		 $('tbody').on('click','.btn-delete',function(){
			 var $tr=$(this).parent().parent()
			 var id=$tr.data('id');
		 	$.get('/admin/api/comments-delete.php',{id:id},function(res){
				if(res){
					// $tr.remove();
					loadPageData(currentPage);
				}
		 	}); 
		 });
		 
		 $('.btn-batch .btn-delete').on('click',function(){
			 $.get('/admin/api/comments-delete.php',{},function(res){
				 console.log(res);
			 });
		 });
		 
	  });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
