<?php
require('connection.php');

if(!isset($_GET['course_id'])){
    header("Location: index.php");
}

if(isset($_POST['login'])){
    $course_id = $_GET['course_id'];
    $register_number = $_POST['register_number'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM student WHERE register_number = '$register_number' AND password = '$password'";
    $query = mysqli_query($connect, $sql);

    if(mysqli_num_rows($query) > 0){
        $student_id = mysqli_fetch_array($query)['id'];
        setcookie("auth", $student_id, 0);
        setcookie("course_id", $course_id, 0);
        setcookie("sweet_alert","valid", 0);
        header("Location: dashboard.php");        
    } else {
        setcookie("sweet_alert","invalid", 0);
        header("Location: login.php?course_id=$course_id");        
    }
}

include('inc/header.php');
?>

    <div class="fixed-top header">
        <?=strtoupper("Achievers Online Portal")?>
    </div>
    <div class="container">
        <div class="row min-vh-100 justify-content-center align-items-center">
            <div class="col-12 col-md-6 text-right d-none d-sm-block" style="border-right:3px solid #ed1c24">
                <img class="img-fluid" src="admin/img/login_banner.png" alt="" sizes="">
            </div>
            <div class="col-12 col-md-6">
                <form action="" method="post">
                    <div class="card">
                        <div class="card-header h3 text-center"><?=strtoupper("System Login")?></div>
                        <div class="card-body mt-3">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                                </div>
                                <input type="text" name="register_number" placeholder="Registration Number" class="form-control">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-key" aria-hidden="true"></i></span>
                                </div>
                                <input type="password" name="password" placeholder="Password" class="form-control">
                            </div>
                        
                        </div>
                        <div class="card-footer">
                            <input type="submit" name="login" value="Login" class="btn btn-primary d-block w-100 btn-lg">
                            <div class="text-center mt-2">
                                <p>Don't have an Account</p>
                                <a href="register.php" class="text-warning"><b>Register</b></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="fixed-bottom footer">
        <?=strtoupper("The largest college for CIMA and ACCA")?>
    </div>        

<?php 
include('inc/footer.php');
?>        
