<?php
    function multiList01(){
    global $link;
    //列出產品類別第一層
    $SQLstring="SELECT * FROM pyclass WHERE level=1 ORDER BY sort";
    $pyclass01=$link->query($SQLstring);

    ?>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="./products_p01.php" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 20px; font-weight:600;">
        商品資訊
        </a>
        <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="./products_p01.php">所有商品</a></li>
        <li><hr class="dropdown-divider"></li>
        <?php while ($pyclass01_Rows= $pyclass01->fetch()){ ?>
        <li class="nav-item dropend">
            <a class="dropdown-item dropdown-toggle" href="#">
            <?php echo $pyclass01_Rows['cname']; ?>
            </a>
            <?php
            //list the second layer of the product according to the class
            $SQLstring=sprintf("SELECT * FROM pyclass WHERE level=2 AND uplink=%d ORDER BY sort",$pyclass01_Rows['classid']);
            $pyclass02=$link->query($SQLstring);
            ?>
            <ul class="dropdown-menu">
                <?php while($pyclass02_Rows=$pyclass02->fetch()){ ?>
                <li><a href="products_p01.php?classid=<?php echo $pyclass02_Rows['classid']; ?>" class="dropdown-item"><em class="fas <?php echo $pyclass02_Rows['fonticon']; ?> fa-fw"></em><?php echo $pyclass02_Rows['cname']; ?></a></li>
                <?php } ?>
            </ul>
        </li>
        <?php }?>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">優惠商品</a></li>
    </ul>
    </li>
<?php } ?>
<!-- 查詢推車商品數量 -->
<?php
$SQLstring="SELECT * FROM cart WHERE orderid is NULL AND ip='".$_SERVER['REMOTE_ADDR']."'";
$cart_rs=$link->query($SQLstring);
?>
<!-- navigation main part -->
<div class="top d-flex bg-warning p-0 m-0"> 
    <div class="topleft ms-auto w-60 d-flex justify-content-end p-0 m-0" style="width: 50%;">
    <img src="./images/assets/facebook.png" class="mx-3 mt-2" alt="" style="height: 30px;"> 粉絲團 
    <img src="images/assets/like.png" alt="" style="height: 30px;" class="mx-3 mt-2">
    </div> 

    <div class="topright  d-flex ms-auto w-39 p-0 m-0" style="width:50%;">
    

    <form class="d-flex p-0 m-0" role="search" name="search" action="products_p01.php" method="get">

        <!-- searching box -->
        <input class="form-control me-2 mt-1" type="search" placeholder="Search" aria-label="Search" name="search_name" id="search_name" value="<?php echo (isset($_GET['search_name']))?$_GET['search_name']:''; ?>" required>
        <button type="submit" class="btn p-0 border-0 bg-transparent mt-1"><i class="fa-solid fa-magnifying-glass mx-3 " style="font-size: 30px;"></i></button>
     </form>
        <!-- member -->
         <?php if (isset($_SESSION['login'])){ ?>
            <a class="nav-link p-0 m-0" href="javascript:void(0);" onclick="btn_confirmLink('是否確定登出?','logout.php')" style="font-size: 20px; max-height: 50px;"><i class="fa-solid fa-right-from-bracket ms-3 my-0 p-0" style="font-size:30px; line-height:50px;"></i></a>
        
        <?php } else { ?>
            <a class="nav-link p-0 m-0" href="./member_login.php" style="font-size: 20px;  max-height: 50px; "><i class="fa-solid fa-user ms-3 my-0 p-0 " style="font-size: 30px; line-height:50px; max-height:50px;"></i></a>
        <?php } ?>
        
        <!-- shopping cart -->
        <a href="product_cart.php" class="text-decoration-none text-black position-relative p-0 m-0" style="max-height: 50px;"><i class="fa-solid fa-cart-shopping mx-3 my-0 p-0 " style="font-size: 30px; max-height:50px; line-height:50px;"><span class="badge text-bg-danger position-absolute rounded-circle" style="left:20%; top:10%; height:20px; width:20px; font-size:10px;  "><?php echo($cart_rs) ?$cart_rs->rowCount() :''; ?></span></i></a>
        
    </div>
</div>

<nav class="navbar navbar-expand-lg bg-warning sticky-top" >
    
    
    <div class="container-fluid ms-auto">
    <a class="navbar-brand" href="./index_p01.php"><img src="./images/assets/logov122.png" style="width: 80px;" alt=""></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <!-- change the member icon when login -->
        <?php if (isset($_SESSION['login'])){ ?>
        <li class="nav-item">
            <a class="nav-link active" href="javascript:void(0);" onclick="btn_confirmLink('是否確定登出?','logout.php')" style="font-size: 20px; font-weight:600;">會員登出</a>
        </li>
        <?php } else { ?>
        <li class="nav-item">
            <a class="nav-link" href="./member_login.php" style="font-size: 20px; font-weight:600;">會員登入</a>
        </li>
        <?php } ?>
        <!-- dynamicly change the member image -->
        <?php if(isset($_SESSION['login'])) { ?>
        <li class="nav-item dropdown mx-2  ">
            <a class="nav-link dropdown-toggle member" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 20px; font-weight:600; ">
                <img src="./uploads/<?php echo($_SESSION['imgname']!='')? $_SESSION['imgname']:'avatar.svg'; ?>" width="40" height="40" class="rounded-circle me-2" alt="" style="vertical-align:middle;"><span class="" style="position:relative; top:-3.5px;">會員專區</span>
           
            </a>
            <div class="dropdown-menu">
            <a class="dropdown-item" href="./member_orderlist.php">訂單列表</a>
            <a class="dropdown-item" href="member_profile.php">編輯個人資訊</a>
            <a class="dropdown-item" href="#" onclick="btn_confirmLink('請確認是否登出','logout.php')">登出</a>
            </div>
        </li>
        <?php } ?>

        <!-- The dropdown component I am going to use -->
        <?php multiList01(); ?>

        
        <li class="nav-item">
            <a class="nav-link " style="font-size: 20px; font-weight:600;">關於我們</a>
        </li>
        </ul>
        
      
    </div>
    </div>
</nav>  
<style>
    .dropdown-toggle.member::after{
        position: relative;
        top: -3px;
    }
</style>
