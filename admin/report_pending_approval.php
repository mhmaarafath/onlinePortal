<?php 
require('connection.php');
if(isset($_GET['id'])){
    $exam_id = $_GET['id'];
    mysqli_query($connect, "UPDATE exam SET status = 'approved' WHERE id = '$exam_id'");
    header("Location: report_pending_approval.php");
}
include('inc/header.php');
?>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Batch</th>
                <th>Course</th>
                <th>Attempt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT 
        exam.*, 
        subject.name AS subject_name,
        student.register_number
        FROM exam 
        INNER JOIN subject ON exam.subject_id = subject.id 
        INNER JOIN student ON exam.student_id = student.id 
        WHERE 
        exam.status = 'pending'";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $exam_id = $row['id'];
        ?>
            <tr>
                <td><?=$row['subject_name']?></td>
                <td><?=$row['register_number']?></td>
                <td><?=$row['attempt']?></td>
                <td>
                    <a href="report_pending_approval.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Approve</a>
                </td>
                <td></td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>




<?php include('inc/footer.php');?>


