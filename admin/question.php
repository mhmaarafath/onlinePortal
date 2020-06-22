<?php 
require('connection.php');
include('inc/header.php');
?>
    <a href="question-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Section</th>
                <th>Subject</th>
                <th>Course</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        require('connection.php');
        $sql="SELECT 
            question.*,
            section.name AS section_name, 
            subject.name AS subject_name, 
            course.name AS course_name 
            FROM question 
            INNER JOIN section ON question.section_id = section.id
            INNER JOIN subject ON section.subject_id = subject.id
            INNER JOIN course ON subject.course_id = course.id
            WHERE question.deleted != '1' 
            ORDER BY question.name ASC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
        ?>
            <tr>
                <td><?=$row['name']?></td>
                <td><?=$row['section_name']?></td>
                <td><?=$row['subject_name']?></td>
                <td><?=$row['course_name']?></td>
                <td>
                    <a href="question-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete.php?tbl=question&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

