<?php 
require('connection.php');
include('inc/header.php');
?>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Batch</th>
                <th>Course</th>
                <th>Subject</th>
                <th>Attempted</th>
                <th>Total Students</th>

            </tr>
        </thead>
        <tbody>
        <?php
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
            $course_id = $row['course_id'];
            $sql1="SELECT * FROM subject WHERE course_id = '$course_id' AND subject.deleted != '1' ORDER BY name ASC"; 
            $query1=mysqli_query($connect,$sql1); 
            while($row1 = mysqli_fetch_array($query1)){
                $subject_id = $row1['id'];
                $sql2 = "SELECT 
                        batch_student.*
                        FROM batch_student
                        INNER JOIN (SELECT * FROM exam WHERE subject_id = '$subject_id' AND exam_type = 'mock' GROUP BY student_id) AS exam_table ON batch_student.student_id = exam_table.student_id
                        WHERE
                        batch_student.batch_id = '$batch_id' AND deleted != '1'";
                $number_students_enroll=mysqli_num_rows(mysqli_query($connect, $sql2));
                $number_students = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM batch_student WHERE batch_id = '$batch_id' AND deleted !='1'"));
        ?>
                <tr>
                    <td><?=$row['name']?></td>
                    <td><?=$row['course_name']?></td>
                    <td><?=$row1['name']?></td>
                    <td><?=$number_students_enroll?></td>
                    <td><?=$number_students?></td>
                </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>




<?php include('inc/footer.php');?>


