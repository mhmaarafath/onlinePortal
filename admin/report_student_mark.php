<?php 
require('connection.php');
include('inc/header.php');
?>
        <table class="table table-responsive table-striped table-hover">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Register Number</th>
                    <th>Subject</th>
                    <th>Course</th>
                    <th>Time</th>
                    <th>Type</th>
                    <th>Attempt</th>
                    <th>Marks</th>
                </tr>
            </thead>
            <tbody>
            <?php
            require('connection.php');
            $sql3="SELECT 
            exam.*,
            subject.name AS subject_name,
            course.name AS course_name,
            student.register_number
            FROM exam
            INNER JOIN subject ON exam.subject_id = subject.id
            INNER JOIN course ON subject.course_id = course.id 
            INNER JOIN student ON exam.student_id = student.id 
            WHERE exam.exam_type = 'mock'
            ORDER BY exam.start_time DESC";
            $query3=mysqli_query($connect,$sql3);
            while($row3= mysqli_fetch_array($query3)){
                $exam_id = $row3['id'];
            ?>
                <tr>
                    <td><?=$row3['id']?></td>
                    <td><?=$row3['register_number']?></td>
                    <td><?=$row3['subject_name']?></td>
                    <td><?=$row3['course_name']?></td>
                    <td><?=$row3['start_time']?></td>
                    <td><?=strtoupper($row3['exam_type'])?></td>
                    <td><?=$row3['attempt']?></td>
                    <td class="text-right">
                        <?php
                            if($row3['status'] == "completed"){
                            echo(
                                    round((
                                        mysqli_fetch_array(mysqli_query($connect,"SELECT SUM(remark) AS marks FROM answer WHERE exam_id = '$exam_id'"))['marks']
                                        /
                                        mysqli_num_rows(mysqli_query($connect,"SELECT * FROM answer WHERE exam_id = '$exam_id'"))
                                    )
                                    *100)
                                );
                        ?>
                        % <a target="_new" class="badge badge-info" href="../print.php?id=<?=$exam_id?>">Print</a>
                        <?php
                            } else{
                        ?>
                                <span class="badge badge-secondary"><?=ucfirst($row3['status'])?></span>
                        <?php
                            }
                        ?>

                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>



<?php include('inc/footer.php');?>


