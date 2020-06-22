<?php
require('connection.php');

$sql = "SELECT * FROM course WHERE deleted !='1'";
$query = mysqli_query($connect, $sql);


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
                    <div class="card-header h3 text-center"><?=strtoupper("Courses")?></div>
                    <div class="card-body mt-3">
                        <?php
                        while($row = mysqli_fetch_array($query)){
                        $course_id = $row['id'];
                        ?>
                        <a class = "btn btn-info btn-lg mt-2" href="login.php?course_id=<?=$course_id?>"><?=$row['name']?></a>
                        <?php
                        }
                        ?>                    
                    </div>
                    <div class="card-footer">
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
