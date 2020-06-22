<?php 
require('connection.php');
include('inc/header.php');
?>
    <a href="batch-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
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
            WHERE batch.deleted != '1' 
            ORDER BY course.name ASC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $batch_id = $row['id'];
        ?>
            <tr>
                <td><?=$row['name']?></td>
                <td><?=$row['course_name']?></td>
                <td>
                    <a href="batch-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="batch_student-add.php?batch_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Student</a>
                    <!-- <a href="delete.php?tbl=subject&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a> -->
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

