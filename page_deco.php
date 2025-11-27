<div class="position-fixed carticon rounded-circle bg-white shadow" style="top:70%; right:80%; width:100px; height:100px; z-index:5;" >
    <!-- find quantity for cart -->
    <?php
    $SQLstring="SELECT * FROM cart WHERE orderid is NULL AND ip='".$_SERVER['REMOTE_ADDR']."'";
    $cart_rs=$link->query($SQLstring);
    ?>
    <!-- the cart -->
    <a href="./product_cart.php" ><img src="./images/assets/carticon.png" style="width: 100%; height:auto;" alt="" class="position-relative"></a><span class="badge text-bg-danger position-absolute rounded-circle" style="left:70%; font-size:1.5em"><?php echo ($cart_rs) ? $cart_rs->rowCount() :''; ?></span>
</div>
<!-- Floating chat/contact box -->
<div class="floating-box">
    <div class="chat-toggle" id="chatToggle">
    <i class="fa-solid fa-comments me-1"></i><span style="font-size: 1em;">聯絡我們</span>
    </div>
    <div class="chat-panel shadow-lg" id="chatPanel" style="display:none;">
    <h5 class="mb-3 text-center">聯絡我們</h5>
    <div class="d-flex flex-column align-items-center">
        <a href="https://line.me/ti/p/xxxx" target="_blank" class="btn btn-success mb-2 w-75">
        <i class="fa-brands fa-line me-2"></i> LINE
        </a>
        <a href="https://facebook.com/xxxx" target="_blank" class="btn btn-primary mb-2 w-75">
        <i class="fa-brands fa-facebook me-2"></i> Facebook
        </a>
        <a href="mailto:info@yourmail.com" class="btn btn-warning text-dark w-75">
        <i class="fa-solid fa-envelope me-2"></i> Email
        </a>
    </div>
    </div>
</div>