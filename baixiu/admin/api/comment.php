<?php

require_once('../../functions.php');

$page=empty($_GET['page'])?1:intval($_GET['page']);
$length=10;


$total_count=baixiu_fetch_one("select count(1) as num 
from comments
inner join posts on comments.post_id=posts.id;")['num'];


$totalPage=(int)ceil($total_count/$length);
$page=$page>$totalPage?$totalPage:$page;
$offset=$length*($page-1);

$sql=sprintf("SELECT comments.*,posts.title
from comments
INNER join posts on comments.post_id=posts.id
ORDER BY comments.created DESC
limit %d,%d;",$offset,$length);
$comm=baixiu_fetch_all($sql);
header('Content-type:application/json');
$json=json_encode(array(
'comm'=>$comm,
'totalPage'=>$totalPage
));
echo $json;