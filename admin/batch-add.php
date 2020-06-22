<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM batch WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $course_id = ($_GET['course_id']) ? $_GET['course_id'] : $_POST["course_id"];
    $location = ($_GET['course_id']) ? "batch-add.php?course_id=$course_id" : "batch.php";
    if(isset($_GET['id'])){
        $sql = "UPDATE batch SET name ='$name', course_id = '$course_id' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO batch (name, course_id) VALUES ('$name','$course_id')";
    }
    mysqli_query($connect, $sql);
    header("Location: $location");
}
?>
<?php include('inc/header.php');?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Batch</div>
        <div class="card-body">
            <?php
                require('connection.php');
                $course_id = ($_GET['course_id']) ? $_GET['course_id'] : $row['course_id'];
                $disabled = ($_GET['course_id']) ? "disabled" : "";
            ?>
            <select <?=$disabled?> name="course_id" class="form-control mb-2" required>
                    <option value="">Select Course</option>
                <?php 
                    $sql1="SELECT * FROM course WHERE deleted != '1'";
                    $query1=mysqli_query($connect,$sql1); 
                    while($row1= mysqli_fetch_array($query1)){
                        $selected = ($course_id == $row1['id']) ? "selected" : "";
                ?>
                        <option <?=$selected?> value="<?=$row1['id']?>"><?=$row1['name']?></option>
                <?php
                    }
                ?>
            </select>            
            <input type="text" name="name" value="<?=$row['name']?>" placeholder="Batch Name" class="form-control mb-2" required>
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Update" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>
<?php
if($_GET['course_id']){
    $course_id = $_GET['course_id'];
?>
<table class="table table-striped table-hover mt-2">
    <thead>
        <tr>
            <th>Name</th>
            <th>Course</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    require('connection.php');
    $sql="SELECT 
        batch.*,
        course.name AS course_name 
        FROM batch 
        INNER JOIN course ON batch.course_id = course.id
        WHERE batch.deleted != '1' AND batch.course_id = '$course_id'
        ORDER BY batch.id DESC";
    $query=mysqli_query($connect,$sql); 
    while($row= mysqli_fetch_array($query)){
    ?>
        <tr>
            <td><?=$row['name']?></td>
            <td><?=$row['course_name']?></td>
            <td>
                <a href="batch-add.php?course_id=<?=$course_id?>&id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="batch_student-add.php?batch_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Batch Student</a>
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
