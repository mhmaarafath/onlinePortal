<?php
require('connection.php');
if(isset($_POST['register'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $reconfirm_password = $_POST['reconfirm_password'];
    $register_number = $_POST['register_number'];

    $message = "";
    
    if(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM student WHERE email = '$email'"))){
        $message.=" - Email Already Taken - ";
    }
    
    if($password != $reconfirm_password){
        $message.=" - Password Not Match - ";
    }
    
    if((mysqli_num_rows(mysqli_query($connect,"SELECT * FROM student WHERE register_number = '$register_number'"))) == 0){
        $message.=" - Invalid Registration Number - ";
    }

    if($message ==""){
        if((mysqli_num_rows(mysqli_query($connect,"SELECT * FROM student WHERE register_number = '$register_number' AND email != ''"))) > 0){
            $message.=" - Already Registered - ";
        } else {
            $student_id = mysqli_fetch_array(mysqli_query($connect,"SELECT * FROM student WHERE register_number = '$register_number'"))['id'];
            mysqli_query($connect, "UPDATE student SET email = '$email', password = '$password' WHERE id = '$student_id'");
            // setcookie("auth", $student_id, 0);
            setcookie("sweet_alert","register", 0);
            header("Location: index.php");            
        }
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
                    <div class="card-header h3 text-center"><?=strtoupper("User Registration")?></div>
                    <div class="card-body mt-3">
                        <?php
                        if($message !=""){
                        ?>
                            <div class="alert alert-warning"><?=$message?></div>
                        <?php
                        }
                        ?>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                            </div>
                            <input type="email" name="email" placeholder="Email" class="form-control">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-key" aria-hidden="true"></i></span>
                            </div>
                            <input type="password" name="password" placeholder="Password" class="form-control">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-key" aria-hidden="true"></i></span>
                            </div>
                            <input type="password" name="reconfirm_password" placeholder="Reconfirm Password" class="form-control">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                            </div>
                            <input type="text" name="register_number" placeholder="Registration Number" class="form-control">
                        </div>                   
                    </div>
                    <div class="card-footer">
                        <input type="submit" name="register" value="Register" class="btn btn-primary d-block w-100 btn-lg">
                        <div class="text-center mt-2">
                            <a href="index.php" class="text-warning"><b>Login</b></a>
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
