<?php
session_start();
require_once '../config.php';
require_once '../class/db_connection.php';
require_once '../logic/logic_user.php';

if($_POST['action']=='userLogged'){
    $logic_object = new logic_user();
    $logic_object->userLogged($_POST);
}else {
    require_once '../view/include/header.php';
    ?>

    <script type="text/javascript" src="../script/login.js"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"></div>
            <div class="col-md-4 col-md-4 col-sm-4 col-xs-12">
                <div class="text-center m-b-md custom-login">
                    &nbsp;
                    <h3>LOGIN </h3>
                </div>
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="form-group">
                            <label class="control-label" for="username">Username</label>
                            <input type="text" required placeholder="E-mail (xyz@abc.com)" id="usname" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="password">Password</label>
                            <input type="text" required placeholder="***********" id="pawrd" class="form-control">
                        </div>
                        &nbsp;
                        <button class="btn btn-success btn-block loginbtn" onclick="login()">Login</button>
                        &nbsp;
                        <a class="btn btn-default btn-block" href="register.php">Register</a>

                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"></div>
        </div>
    </div>

    <?php
    include '../view/include/footer.php';
}
?>