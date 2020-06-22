<?php 
require('connection.php');
include('inc/header.php');
?>
    <a href="batch_student-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
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
            WHERE batch_student.deleted != '1' 
            ORDER BY batch.name ASC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $subject_id = $row['id'];
        ?>
            <tr>
                <td><?=$row['name']?></td>
                <td><?=$row['batch_name']?></td>
                <td>
                    <a href="batch_student-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <!-- <a href="batch_student-add.php?badge_id=<?=$row['id']?>" class="btn btn-secondary btn-sm"><i class="fa fa-plus" aria-hidden="true"></i> View Section</a> -->
                    <!-- <a href="delete.php?tbl=subject&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a> -->
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

