<?php 
require('connection.php');
include('inc/header.php');
?>
    <a href="section-add.php" class="btn btn-primary mb-2" id="add">ADD</a>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Subject</th>
                <th>Course</th>
                <th>NOQ</th>
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
            WHERE section.deleted != '1' 
            ORDER BY section.name ASC";
        $query=mysqli_query($connect,$sql); 
        while($row= mysqli_fetch_array($query)){
            $section_id = $row['id'];
        ?>
            <tr>
                <td><?=$row['name']?></td>
                <td><?=$row['subject_name']?></td>
                <td><?=$row['course_name']?></td>
                <td>
                    <?php echo(mysqli_num_rows(mysqli_query($connect,"SELECT * FROM question WHERE deleted !='1' AND section_id = '$section_id'")))?> / 
                    <?=$row['noq']?>
                </td>
                <td>
                    <a href="section-add.php?id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="question-add.php?section_id=<?=$row['id']?>" class="btn btn-secondary btn-sm">Add Question</a>
                    <a href="delete.php?tbl=section&id=<?=$row['id']?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>


<?php include('inc/footer.php');?>

