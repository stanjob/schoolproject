<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json;charset=utf-8');

require_once('Connections/conn_db.php');
if(isset($_GET['emailid'])&& $_GET['emailid']!=''){
  $emailid=$_GET['emailid'];
  $PWNew1=$_GET['PWNew1'];
  $query=sprintf("UPDATE member SET pw1='%s' WHERE member.emailid='%d'",$PWNew1,$emailid);
  $result=$link->query($query);
  if($result){
    $retcode=array("c"=>"1","m"=>'謝謝您！會員密碼已經更新。');

  }else{
    $retcode=array("c"=>"0","m"=>'抱歉！資料無法寫入後台資料庫，請聯絡管理人員');
  }
  echo json_encode($retcode,JSON_UNESCAPED_UNICODE);
}
return;
?>