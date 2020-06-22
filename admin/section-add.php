<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM section WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
	$row = mysqli_fetch_array($query);
}

if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $noq = $_POST["noq"];
    $subject_id = (isset($_GET['subject_id'])) ? $_GET['subject_id'] : $_POST["subject_id"];
    $location = ($_GET['subject_id']) ? "section-add.php?subject_id=$subject_id" : "section.php";
    if(isset($_GET['id'])){
        $sql = "UPDATE section SET name ='$name', subject_id = '$subject_id', noq = '$noq' WHERE id='$id'";
    } else {
        $sql = "INSERT INTO section (name,subject_id, noq) VALUES ('$name','$subject_id', '$noq')";
    }
    mysqli_query($connect, $sql);
    header("Location: $location");
}
?>
<?php include('inc/header.php');?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Section</div>
        <div class="card-body">
            <?php
                require('connection.php');
                $subject_id = ($_GET['subject_id']) ? $_GET['subject_id'] : $row['subject_id'];
                $disabled = ($_GET['subject_id']) ? "disabled" : "";
            ?>
            <select <?=$disabled?> name="subject_id" class="form-control mb-2" required>
                    <option value="">Select Subject</option>
                <?php 
                    $sql1="SELECT * FROM subject WHERE deleted != '1'";
                    $query1=mysqli_query($connect,$sql1); 
                    while($row1= mysqli_fetch_array($query1)){
                        $selected = ($subject_id == $row1['id']) ? "selected" : "";
                ?>
                        <option <?=$selected?> value="<?=$row1['id']?>"><?=$row1['name']?></option>
                <?php
                    }
                ?>
            </select>            
            <input type="text" name="name" value="<?=$row['name']?>" placeholder="Section" class="form-control mb-2" required>
            <input type="number" name="noq" value="<?=$row['noq']?>" placeholder="NOQ" class="form-control mb-2" required>
        </div>
        <div class="card-footer">
            <input type="submit" name="submit" value="Update" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>

<?php
if($_GET['subject_id']){
    $subject_id = $_GET['subject_id'];
?>
<table class="table table-striped table-hover mt-2">
    <thead>
        <tr>
            <th>Name</th>
            <th>Subject</th>
            <th>Course</th>
            <th data-toggle="tooltip" title="Minimum Mock Questions">MOQ</th>
            <th data-toggle="tooltip" title="Mock Questions">Mock</th>
            <th data-toggle="tooltip" title="Practice Questions">Practice</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    require('connection.php');
    $sql="SELECT 
        section.*,
        subject.name AS subject_name,
        course.name AS course_name 
        FROM section 
        INNER JOIN subject ON section.subject_id = subject.id
        INNER JOIN course ON subject.course_id = course.id
        WHERE section.deleted != '1' AND section.subject_id = '$subject_id'
        ORDER BY section.id DESC";
    $query=mysqli_query($connect,$sql);
    while($row= mysqli_fetch_array($query)){
        $section_id = $row['id'];
    ?>
        <tr>
            <td><?=$row['name']?></td>
            <td><?=$row['subject_name']?></td>
            <td><?=$row['course_name']?></td>
            <td><?=$row['noq']?></td>
            <td>
                <?php echo(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM question WHERE deleted !='1' AND section_id = '$section_id' AND exam_type = 'mock'")))?>
            </td>
            <td>
                <?php echo(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM question WHERE deleted !='1' AND section_id = '$section_id' AND exam_type = 'practice'")))?>
            </td>
            <td>
                <a href="section-add.php?subject_id=<?=$subject_id?>&id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="question-add.php?section_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Question</a>
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
