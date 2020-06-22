<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM student WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $register_number = $_POST["register_number"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    if(isset($_GET['id'])){
        $sql = "UPDATE student SET register_number ='$register_number', email = '$email', password = '$password' WHERE id='$id'";
    } else {
        if(!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM student WHERE register_number = '$register_number'"))){
            $sql = "INSERT INTO student (register_number, email, password) VALUES ('$register_number', '$email', '$password')";
        }
    }
    mysqli_query($connect, $sql);
    header("Location: student.php");
}
?>
<?php include('inc/header.php');?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Student</div>
        <div class="card-body">
            <input type="text" name="register_number" value="<?=$row['register_number']?>" placeholder="Register Number" class="form-control mb-2">
            <input type="email" name="email" value="<?=$row['email']?>" placeholder="Email" class="form-control mb-2">
            <input type="password" name="password" value="<?=$row['password']?>" placeholder="Password" class="form-control mb-2">
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Update" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>

<?php include('inc/footer.php');?>
