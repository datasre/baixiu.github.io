<?php
if(empty($_FILES['avatar'])){
	exit('必须上传图片');
}
$avatar=$_FILES['avatar'];
// var_dump($avatar);
if($avatar['error']!=UPLOAD_ERR_OK){
	exit('上传文件失败');
}
if($avatar['size']>20*1024*1024){
	exit('上传文件过大');
}
$allow_type_image=array('image/png','image/jpeg','image/gif');
if(!in_array($avatar['type'],$allow_type_image)){
	exit('这是不支持的文件格式');
}
$temp_url=$avatar['tmp_name'];
$ext=pathinfo($avatar['name'],PATHINFO_EXTENSION);// 扩展名
$target_url='../../static/uploads/img-'.uniqid().'.'.$ext;
if(!move_uploaded_file($temp_url,$target_url)){
	exit('文件上传失败');
}

echo substr($target_url,5);


