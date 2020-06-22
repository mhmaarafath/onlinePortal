<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM batch_student WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $batch_id = ($_GET['batch_id']) ? $_GET['batch_id'] : $_POST["batch_id"];
    $location = ($_GET['batch_id']) ? "batch_student-add.php?batch_id=$batch_id" : "batch_student.php";
    $student_id = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM student WHERE register_number = '$name' LIMIT 1"))['id'];
    if(isset($_GET['id'])){
        $sql = "UPDATE batch_student SET name ='$name', student_id = '$student_id', batch_id = '$batch_id' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO batch_student (name, batch_id, student_id) VALUES ('$name','$batch_id', $student_id)";
    }
    mysqli_query($connect, $sql);
    header("Location: $location");
}

if(isset($_POST["upload"])){
    $batch_id = $_GET['batch_id'];
    if($_FILES['product_file']['name']){
        $filename = explode(".", $_FILES['product_file']['name']);
        if(end($filename) == "csv"){
            $handle = fopen($_FILES['product_file']['tmp_name'], "r");
            while($data = fgetcsv($handle)){
                $register_number = mysqli_real_escape_string($connect, $data[0]);
                if(!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM batch_student WHERE name = '$register_number' AND batch_id = '$batch_id'"))){
                    $student_id = mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM student WHERE register_number = '$register_number' LIMIT 1"))['id'];
                    $sql4 ="INSERT batch_student (batch_id, name, student_id) VALUES ('$batch_id', '$register_number', '$student_id')";
                    mysqli_query($connect, $sql4);
                }
            }
            fclose($handle);
            header("Location: batch_student-add.php?batch_id=$batch_id");    
        }else{
            $message = '<label class="text-danger">Please Select CSV File only</label>';
        }
    }else{
        $message = '<label class="text-danger">Please Select File</label>';
    }
}

?>
<?php include('inc/header.php');?>
<?php
if($_GET['batch_id']){
?>
<form class="form-inline" method="post" enctype='multipart/form-data'>
    <input type="submit" name="upload" class="btn btn-info mr-2" value="Upload" />
        <?=$message?>
    <input type="file" name="product_file" /></p>
</form>
<?php
}
?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Batch</div>
        <div class="card-body">
            <?php
                require('connection.php');
                $batch_id = ($_GET['batch_id']) ? $_GET['batch_id'] : $row['batch_id'];
                $disabled = ($_GET['batch_id']) ? "disabled" : "";
            ?>
            <select <?=$disabled?> name="batch_id" class="form-control mb-2" required>
                    <option value="">Select Batch</option>
                <?php 
                    $sql1="SELECT * FROM batch WHERE deleted != '1'";
                    $query1=mysqli_query($connect,$sql1); 
                    while($row1= mysqli_fetch_array($query1)){
                        $selected = ($batch_id == $row1['id']) ? "selected" : "";
                ?>
                        <option <?=$selected?> value="<?=$row1['id']?>"><?=$row1['name']?></option>
                <?php
                    }
                ?>
            </select>            
            <input type="text" name="name" value="<?=$row['name']?>" placeholder="Student ID" class="form-control mb-2" required>
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Update" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>

<?php
if($_GET['batch_id']){
    $batch_id = $_GET['batch_id'];
?>
<table class="table table-striped table-hover mt-2">
    <thead>
        <tr>
            <th>Name</th>
            <th>Batch</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    require('connection.php');
    $sql="SELECT 
        batch_student.*,
        batch.name AS batch_name 
        FROM batch_student 
        INNER JOIN batch ON batch_student.batch_id = batch.id
        WHERE batch_student.deleted != '1' AND batch_student.batch_id = '$batch_id'
        ORDER BY batch_student.id DESC";
    $query=mysqli_query($connect,$sql); 
    while($row= mysqli_fetch_array($query)){
    ?>
        <tr>
            <td><?=$row['name']?></td>
            <td><?=$row['batch_name']?></td>
            <td>
                <a href="batch_student-add.php?batch_id=<?=$batch_id?>&id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                <!-- <a href="batch_student-add.php?batch_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Batch Student</a> -->
                <!-- <a href="delete.php?tbl=subject&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a> -->
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<?php
}
?>


<?php include('inc/footer.php');?>
