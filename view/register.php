<?php
require_once '../config.php';
require_once '../logic/logic_user.php';

if($_POST['action']=='regisUser'){
    $logic_object = new logic_user();
    $logic_object->addNewUser($_POST);
}else {
    require_once  '../view/include/header.php';
    ?>

    <script type="text/javascript" src="../script/login.js"></script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"></div>
            <div class="col-md-6 col-md-6 col-sm-6 col-xs-12">
                <div class="text-center custom-login">
                    <h3>Registration</h3>
                    <p>Admin template with very clean and aesthetic style prepared for your next app. </p>
                </div>
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group col-lg-12">
                                <label>Username</label>
                                <input class="form-control" id="username">
                            </div>
                            <div class="form-group col-lg-6">
                                <label>Password</label>
                                <input type="password" class="form-control" id="pass1">
                            </div>
                            <div class="form-group col-lg-6">
                                <label>Repeat Password</label>
                                <input type="password" class="form-control" id="pass2">
                            </div>
                            <div class="form-group col-lg-6">
                                <label>Email Address</label>
                                <input type="email" class="form-control" id="email">
                            </div>
                            <div class="form-group col-lg-6">
                                <label>Mobile Number</label>
                                <input class="form-control" id="mobile">
                            </div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-success loginbtn" onclick="registerUser()">Register</button>
                            <a href="<?= $baseUrl ?>">
                                <button class="btn btn-default" >Login</button>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"></div>
        </div>

    </div>

    <?php
    include_once '../view/include/footer.php';
}
?>