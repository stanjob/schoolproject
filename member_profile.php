<?php (!isset($_SESSION))? session_start(): ""; ?>
<!-- import the database -->
<?php require_once('Connections/conn_db.php');?>
<!-- import common PHP function -->
<?php require_once("php_lib.php"); ?>
<!-- login path setting -->
 <?php
(!isset($_SESSION))? session_start() :"";

//if the user not logged in, redirect to login
if(!isset($_SESSION['login'])){
  echo "<script>alert('請先登入會員')</script>";
  $sPath="member_login.php?sPath=member_profile.php";
  header(sprintf("Location:%s",$sPath));
}
// get member info
$emailid=$_SESSION['emailid'];

$sql="SELECT m.*,a.mobile,a.myzip,a.address From member AS m LEFT JOIN addbook AS a ON m.emailid=a.emailid AND a.setdefault=1 WHERE m.emailid=?";
$sth=$link->prepare($sql);
$sth->execute([$emailid]);
$row=$sth->fetch(PDO::FETCH_ASSOC);
 ?>




<!doctype html>
<html lang="en">
  <!-- head setup -->
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project01</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="./website_p01.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.2.1/css/all.css">
    <!-- AOS plugin -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Lightbox css plugin -->
    <link rel="stylesheet" href="./css/jquery.lightbox-0.5.css">
  </head>

  <body class="position-relative" style="background-image:radial-gradient(rgba(250,200,100,0.5),rgba(255,255,255,0.8));">
  <!-- whole page absolute item -->
   <?php require_once("./page_deco.php") ?>

<div class="wrapper container-fluid">
<section id="header" style="position: sticky;top:0; z-index:3;" >
  <!-- navigation -->
  <?php require_once("navbar.php") ?>
</section>

<!-- send form and register the data to database -->
<?php

if(isset($_POST['formct']) && $_POST['formct']=='update'){
  $cname=$_POST['cname'];
  $tssn=$_POST['tssn'];
  $birthday=$_POST['birthday'];
  $mobile=$_POST['mobile'];
  $myZip=$_POST['myZip'];
  $address=$_POST['address'];
  $imgname=$_POST['uploadname']==''?'avatar.svg': $_POST['uploadname'];
try{
  // start a transction
  $link->beginTransaction();

  // get the old image filename from DB
  $sql_old="SELECT imgname FROM member WHERE emailid=?";
  $sth_old=$link->prepare($sql_old);
  $sth_old->execute([$emailid]);
  $old=$sth_old->fetch(PDO::FETCH_ASSOC);
  $oldImg=$old['imgname'];
  
  // new file uploaded and delete old one if not default
  if($imgname!==$oldImg && $oldImg!='avatar.svg' && file_exists("uploads/".$oldImg)){
    unlink("uploads/".$oldImg);
  }

  // update member basic info
  $sql1="UPDATE member SET cname=?, tssn=?, birthday=?, imgname=? WHERE emailid=?";
  $stmt1 = $link->prepare($sql1);
  if (!$stmt1->execute([$cname, $tssn, $birthday, $imgname, $emailid])) {
      throw new Exception("會員基本資料更新失敗");
    }
  // update address info in addbook
  $sql2="UPDATE addbook SET cname=?, mobile=?, myZip=?, address=? WHERE emailid=? AND setdefault=1";
  $stmt2 = $link->prepare($sql2);
  if (!$stmt2->execute([$cname, $mobile, $myZip, $address, $emailid])) {
    throw new Exception("地址資料更新失敗");
  }
  // commit all if success
  $link->commit();

  //update session
  $_SESSION['cname']=$cname;
  $_SESSION['imgname']=$imgname;

  echo "<script>alert('會員資料已成功更新');location.href='member_profile.php';</script>";
  
} catch(Exception $e){
  $link->rollBack();
  $errorMsg=addslashes($e->getMessage());
  echo "<script>alert('更新失敗：{$errorMsg}'); location.href='member_profile.php';</script>";
}
}
?>

<section id="productcontent">
  <div class="container-fluid">
      <div class="row align-items-start g-0 d-flex flex-row" style="height:200vh;">
          <div class="col-md-3" >
            <!-- sidebar -->
              <?php require_once("./sidebar.php") ?>
          </div>
          <div class="col-md-9" >
            <!-- Member register form -->
            <div class="shadow-sm rounded-3 ms-3" style="background-color:rgba(255,255,255,0.8);" >
              <div class="row mt-3">
                <div class="col-12 text-center">
                  <h1 class="mt-5">會員資料編輯</h1>
                  
                  <hr class="my-5">
                </div>
              </div>
              <div class="row">
                <div class="col-8 offset-2 pb-5">
                  <form action="member_profile.php" method="POST" name="profile" id="profile">
                    <div class="mb-4">
                      <label for="email" class="form-label mb-2">會員信箱</label>
                      <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>" readonly>
                    </div>
                    
                    <div class=" mb-3">
                      <label for="cname" class="form-label mb-2">姓名：</label>
                      <input type="text" name="cname" id="cname" class="form-control" value="<?php echo htmlspecialchars($row['cname']); ?>">
                    </div>
                    <div class=" mb-3">
                      <label for="tssn" class="form-label mb-2">身分證字號：</label>
                      <input type="text" name="tssn" id="tssn" class="form-control" value="<?php echo htmlspecialchars($row['tssn']); ?>">
                    </div>
                    <div class=" mb-3">
                      <label for="pw2" class="form-label mb-2">生日：</label>
                      <input type="text" name="birthday" id="birthday" onfocus="(this.type='date')" class="form-control" value="<?php echo htmlspecialchars($row['birthday']); ?>">
                    </div>
                    <hr>
                    <h5>以下為預設收件人及付款人聯繫方式，如有需要請更新。</h5>
                    <hr>
                    <div class=" mb-3">
                      <label for="mobile" class="form-label mb-2">連絡電話：</label>
                      <input type="text" name="mobile" id="mobile"  class="form-control" value="<?php echo htmlspecialchars($row['mobile']); ?>">
                    </div>
                    <div class="mb-3">
                      <label for="myZip" class="form-label mb-2">郵遞區號</label>
                      <input type="text" name="myZip" id="myZip"  class="form-control" value="<?php echo htmlspecialchars($row['myzip']); ?>">
                    </div>
                    <div class="mb-3">
                      <label for="address" class="form-label mb-2">聯絡地址：</label>
                      <input type="text" name="address" id="address"  class="form-control" value="<?php echo htmlspecialchars($row['address']); ?>">
                    </div>
                    
                    <div class="mb-3">
                      <label for="fileToUpload" class="form-label">會員頭像：</label>
                      <input type="file" name="fileToUpload" id="fileToUpload" class="form-control" title="請上傳圖片" accept="image/x-png,image/jpeg,image/git,image/jpg"/>
                      
                      <button class="btn btn-warning mt-3" id="uploadForm" name="uploadForm">開始上傳</button>
  
                      <!-- bootstrap progress bar -->
                       <div id="progress-div01" class="progress" style="width:100%;display:none;">
                        <div id="progress-bar01" class="progress-bar progress-bar" role="progressbar" style="width:0%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">0%
  
                        </div>
                       </div>
  
                       <input type="hidden" name="uploadname" id="uploadname" value=""/>
                       <img src="" alt="photo" name="showimg" id="showimg" style="display:none;" class="img-fluid">

                    </div>
<!-- Show existing member photo -->
<?php
$photo = ($row['imgname'] == "" || $row['imgname'] == "avatar.svg") 
         ? "avatar.svg" 
         : $row['imgname'];
?>
<img src="uploads/<?php echo $photo; ?>" 
     id="showimg" 
     class="img-fluid mt-3" 
     style="max-width:200px; <?php echo ($row['imgname'] ? '' : 'display:none;'); ?>">
                    <div class="input-group mb-3">
                    <div class="input-group">
                      <input type="hidden" name="captcha" id="captcha" value="">
                      <a href="javascript:void(0);" title="按我更新認證" onclick="getCaptcha();">
                        <canvas id="can"></canvas>
                      </a>
                    </div> 
                    </div>
                    <input type="text" name="recaptcha" id="recaptcha" class="form-control" placeholder="請輸入驗證碼">
                    <input type="hidden" name="formct" id="formct" value="update">
                
                      <button type="submit" class="btn btn-success me-2 mt-3">更新資料</button>
                      <button type="button" class="btn btn-danger text-white me-2 mt-3"  data-bs-toggle="modal" data-bs-target="#exampleModal">變更會員密碼</button>
           
  
                  </form>
                </div>
              </div>
            </div>

<!-- Modal for password change -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel"><i class="fas fa-user-look me-2"></i> 會員密碼變更頁面</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="changePW" id="changePW" name="changePW">
          <div class="mb-3">
            <label for="PWOld" class="form-label">請輸入舊密碼</label>
            <input type="password" class="form-control" id="PWOld" name="PWOld" placeholder="Current Password" v-model="PWOld">
          </div>
          <div class="mb-3">
            <label for="PWNew1" class="form-label">請輸入新密碼</label>
            <input type="password" class="form-control" id="PWNew1" name="PWNew1" placeholder="New Password" v-model="PWNew1">
          </div>
          <div class="mb-3">
            <label for="PWNew2" class="form-label">請再確認新密碼</label>
            <input type="password" class="form-control" id="PWNew2" name="PWNew2" placeholder="Vertify Password" v-model="PWNew2">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="mClose" name="mClose" type="button" class="btn btn-secondary" data-bs-dismiss="modal">離開</button>
        <button type="button" class="btn btn-primary" onclick="savePW();">儲存密碼</button>
      </div>
    </div>
  </div>
</div>


             
          </div>
      </div>
  </div>
</section>

<section id="footer" >
<?php
require_once('./footer.php')
?>
</section>
<!-- loading page while loading -->
 <div id="loading" name="loading" style="display:none;position:fixed;width:100%;height:100%;top:0;left:0;background-color:rgba(255,255,255,0.5);z-index:10;"><i class="fas fa-spinner fa-spin fa-5x fa-fw" style="position:absolute;top:50%;left:50%;"></i></div>


      
</div>
</body>
</html>

    
<!--plugin section  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="./js/jslib.js"></script>
<script src="./js/commlib.js"></script>
<script src="./js/jquery.validate.js"></script>

<!-- init or function section -->
<script>
    AOS.init();
</script>
<script>
// chating box
document.getElementById("chatToggle").addEventListener("click", function() {
  const panel = document.getElementById("chatPanel");
  panel.style.display = panel.style.display === "block" ? "none" : "block";
});

</script>


<script>
  // 亂數產生驗證內容
  function getCaptcha(){
    var inputTxt=document.getElementById("captcha");
    //can為canvas的ID名稱
    // 150=影像寬,50=影像高,blue是影像背景顏色
    // white=文字顏色,28px=文字大小，5=認證碼長度
    inputTxt.value=captchaCode("can",150,50,"blue","white","28px",5);
  }
  // 取得元素ID
  function getId(el){
    return document.getElementById(el);
  }
  // 圖示上傳處理
  $("#uploadForm").click(function(e){
    var fileName=$('#fileToUpload').val();
    var idxDot=fileName.lastIndexOf(".")+1;
    let extFile=fileName.substr(idxDot,fileName.length).toLowerCase();
    if(extFile=="jpg" || extFile=="jpeg" || extFile=="png" || extFile=="gif"){
      $('#progress-div01').css("display","flex");
      let file1=getId("fileToUpload").files[0];
      let formdata=new FormData();
      formdata.append("file1",file1);
      let ajax=new XMLHttpRequest();
      ajax.upload.addEventListener("progress", progressHandler, false);
      ajax.addEventListener("load", completeHandler,false);
      ajax.addEventListener("error",errorHandler,false);
      ajax.addEventListener("abort",abortHandler,false);
      ajax.open("POST", "file_upload_parser.php");
      ajax.send(formdata);
      return false
    }else{
      alert('目前只支援jpg,jpeg,png,gif檔案格式上傳!');
    }
  });
  // 上傳過程顯示百分比
  function progressHandler(event){
    let percent=Math.round((event.loaded/event.total)*100);
    $("#progress-bar01").css("width",percent+"%");
    $("#progress-bar01").html(percent+"%");
  }
  // 上傳完成處理顯示圖片
  function completeHandler(event){
    let data=JSON.parse(event.target.responseText);
    if(data.success=='true'){
      $('#uploadname').val(data.fileName);
      $('#showimg').attr({
        'src':'uploads/'+data.fileName,
        'style':'display:block;'
      });
    }else{
      alert(data.error);
    }
  }
  // Upload failed:上傳發生錯誤處理
  function errorHandler(event){
    alert("Upload Failed:上傳發生錯誤");
  }
  // Upload Aborted:上傳作業取消處理
  function abortHandler(event){
    alert("Uload Aborted:上傳作業取消");
  }
  // 自訂身分證格式驗證
  jQuery.validator.addMethod("tssn",function(value,element,param){
    var tssn=/^[a-zA-Z]{1}[1-2]{1}[0-9]{8}$/;
    return this.optional(element) || (tssn.test(value));
  });
  // 自訂手機格式驗證
  jQuery.validator.addMethod("checkphone",function(value,element,param){
    var checkphone=/^[0]{1}[9]{1}[0-9]{8}$/;
    return this.optional(element) || (checkphone.test(value));
  });
  // 自訂郵遞區號驗證
  jQuery.validator.addMethod("checkMyTown",function(value,element,param){
    return (value !== "");
  });
  // 啟動驗證功能
  $(function(){
    getCaptcha();
  })
  // 驗證form #reg表單
  $('#profile').validate({
    rules:{

      cname:{
        required:true,
      },
      tssn:{
        required:false,
        tssn:true,
      },
      birthday:{
        required:true,
      },
      mobile:{
        required:true,
        checkphone:true,
      },
      address:{
        required:true,
      },
      recaptcha:{
        required:true,
        equalTo:'#captcha',
      },
    },
    messages:{

      cname:{
        required:'使用者名稱不得為空白',
      },
      tssn:{
        required:'身份證ID不得為空白',
        tssn:'身份證ID格式有誤',
      },
      birthday:{
        required:'生日不得為空白',
      },
      mobile:{
        required:'手機號碼不得為空白',
        checkphone:'手機號碼格式有誤',
      },
      address:{
        required:'地址不得為空白',
      },
      recaptcha:{
        required:'驗證碼不得為空白！',
        equalTo:'驗證碼需相同！',
      },
    },
    
  });
  $("#changePW").validate({
    errorClass:"text-danger", 
    rules: {
        PWOld: { required: true },
        PWNew1: { required: true, minlength: 6 },
        PWNew2: { required: true, equalTo: "#PWNew1" }
    },
    messages: {
        PWOld: { required: "請輸入舊密碼" },
        PWNew1: {
            required: "請輸入新密碼",
            minlength: "新密碼至少 6 個字元"
        },
        PWNew2: {
            required: "請再次輸入新密碼",
            equalTo: "兩次輸入的新密碼不相同"
        }
    }
});
</script>
<script>
  // jQuery version of savePW()
function savePW() {

    // Validate change password form
    let valid = $("#changePW").valid();

    if (valid) {

        $.ajax({
            url: "reqchangePW.php",
            type: "GET",
            data: {
                emailid: "<?php echo $_SESSION['emailid']; ?>", // PHP session email
                PWNew1: MD5($("#PWNew1").val())
            },
            success: function(res) {
                let data = {};

                // If response is JSON string → convert
                try {
                    data = typeof res === "string" ? JSON.parse(res) : res;
                } catch (e) {
                    alert("Server response error");
                    return;
                }

                if (data.c === true) {

                    // Reset form validation
                    $("#changePW").validate().resetForm();

                    // Clear fields
                    $("#PWOld").val("");
                    $("#PWNew1").val("");
                    $("#PWNew2").val("");

                    // Close modal
                    $("#mClose").click();

                    alert(data.m);
                    setTimeout(function() {
                        $("#exampleModal").modal("hide");
                    }, 100);
                } else {
                    alert(data.m || "變更密碼失敗");
                }
            },
            error: function(err) {
                alert("AJAX ERROR: " + err.statusText);
            }
        });

    }
}


</script>


