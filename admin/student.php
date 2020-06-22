<?php 
require('connection.php');

if(isset($_POST["upload"])){
    if($_FILES['product_file']['name']){
        $filename = explode(".", $_FILES['product_file']['name']);
        if(end($filename) == "csv"){
            $handle = fopen($_FILES['product_file']['tmp_name'], "r");
            while($data = fgetcsv($handle)){
                $register_number = mysqli_real_escape_string($connect, $data[0]);
                if(!mysqli_num_rows(mysqli_query($connect, "SELECT * FROM student WHERE register_number = '$register_number'"))){
                    $sql4 ="INSERT student (register_number) VALUES ('$register_number')";
                    mysqli_query($connect, $sql4);
                }
            }
            fclose($handle);
            header("Location: student.php");    
        }else{
            $message = '<label class="text-danger">Please Select CSV File only</label>';
        }
    }else{
        $message = '<label class="text-danger">Please Select File</label>';
    }
}

include('inc/header.php');
?>
    <form class="form-inline mb-3" method="post" enctype='multipart/form-data'>
        <input type="submit" name="upload" class="btn btn-info mr-2" value="Upload" />
        <?=$message?>
        <input type="file" name="product_file" /></p>
    </form>

    <a href="student-add.php" class="btn btn-primary mb-3" id="add">Single</a>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Register Number</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        require('connection.php');
        $sql="SELECT * FROM student WHERE deleted != '1' ORDER BY register_number DESC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
        ?>
            <tr>
                <td><?=$row['register_number']?></td>
                <td><?=$row['email']?></td>
                <td>
                    <a href="student-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <!-- <a href="student-add.php?student_id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Add Student</a> -->
                    <!-- <a href="delete.php?tbl=course&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a> -->
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

