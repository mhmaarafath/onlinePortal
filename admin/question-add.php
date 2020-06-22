<?php
require('connection.php');
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM question WHERE id='$id'";
    $query = mysqli_query($connect,$sql); 
    $row = mysqli_fetch_array($query);
    $list_answer = explode("|",  $row["list_answer"]);
    $correct_answer = explode("|",  $row["correct_answer"]);
}

if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $exam_type = $_POST["exam_type"];
    $question_type = $_POST["question_type"];
    $noa = ($_POST["noa"] == "" || $_POST["question_type"] == "short") ? "1" : $_POST["noa"];
    
    $list_answer = implode("|", $_POST["list_answer"]);
    $correct_answer = ($_POST["question_type"] == "short") ? $list_answer : implode("|", $_POST["correct_answer"]);
    $section_id = (isset($_GET['section_id'])) ? $_GET['section_id'] : $_POST["section_id"];
    $user_id = $_COOKIE['admin'];
    
    if($_GET['answer'] == "edit"){
        $sql = "UPDATE question SET name ='$name', exam_type = '$exam_type', user_id = '$user_id', list_answer = '$list_answer', correct_answer = '$correct_answer' WHERE id='$id'";
        mysqli_query($connect, $sql);
        $location = "question-add.php?section_id=$section_id";
    } else {
        if(isset($_GET['id'])){
            $sql = "UPDATE question SET section_id = '$section_id', question_type = '$question_type', noa = '$noa', user_id = '$user_id'  WHERE id='$id'";
            mysqli_query($connect, $sql);
        } else {
            $sql = "INSERT INTO question (name, section_id, exam_type, question_type, noa, list_answer, correct_answer, user_id) VALUES ('', '$section_id', '', '$question_type', '$noa', '', '', '$user_id')";            
            mysqli_query($connect, $sql);
            $id = mysqli_insert_id($connect);
        }
        $location = "question-add.php?section_id=$section_id&id=$id&answer=edit";
    }

    header("Location: $location");
}
?>
<?php include('inc/header.php');?>

<form action="" method="POST" class="col-md-6 offset-md-3">
    <div class="card">
        <div class="card-header">Question</div>
        <div class="card-body">
            <?php
                require('connection.php');
                $section_id = ($_GET['section_id']) ? $_GET['section_id'] : $row['section_id'];
                $disabled = ($_GET['answer'] == "edit") ? "disabled" : "";
                $readonly = ($_GET['answer'] == "edit") ? "readonly" : "";
                $exam_type = $row['exam_type'];
                $question_type = $row['question_type'];
            ?>
            <select disabled name="section_id" class="form-control mb-2" required>
                    <option value="">Select Section</option>
                <?php 
                    $sql1="SELECT * FROM section WHERE deleted != '1'";
                    $query1=mysqli_query($connect,$sql1); 
                    while($row1= mysqli_fetch_array($query1)){
                        $selected = ($section_id == $row1['id']) ? "selected" : "";
                ?>
                        <option <?=$selected?> value="<?=$row1['id']?>"><?=$row1['name']?></option>
                <?php
                    }
                ?>
            </select>            
            <select <?=$disabled?> name="question_type" class="form-control mb-2" required>
                    <option value="">Select Question Type</option>
                    <option <?=($question_type == "single")? "selected" : ""?> value="single">MCQ</option>
                    <option <?=($question_type == "multiple")? "selected" : ""?> value="multiple">Multiple</option>
                    <option <?=($question_type == "short")? "selected" : ""?> value="short">Short Answer</option>
            </select>            
            <input <?=$disabled?> type="number" name ="noa" value="<?=$row['noa']?>" placeholder="Number of Answers" class="form-control mb-2 mt-2">
            <?php 
            if($_GET['answer'] == "edit"){

            ?>

                <select name="exam_type" class="form-control mb-2" required>
                        <option value="">Select Exam Type</option>
                        <option <?=($exam_type == "practice")? "selected" : ""?> value="practice">Practice Exam</option>
                        <option <?=($exam_type == "mock")? "selected" : ""?> value="mock">Mock Exam</option>
                </select>            
                <textarea rows="5" id="question" name="name" placeholder="Question" class="form-control mb-2" required><?=$row['name']?></textarea>
                <div>
                    <?php
                    for($x = 0; $x < $row['noa']; $x++){
                        if($row['question_type'] == "single"){
                    ?>
                            <div class="form-check">
                                <input required class="form-check-input" type="radio" name="correct_answer[]" id="exampleRadios1" value="mc-<?=($x+1)?>" <?=(in_array("mc-".($x+1), $correct_answer))? "checked" : ""?>>
                                <input required type="text" name="list_answer[]" class="form-control" value="<?=$list_answer[$x]?>">
                            </div>
                    <?php
                        } elseif ($row['question_type'] == "multiple"){
                    ?>
                            <div class="form-check">
                                <input type="checkbox" name="correct_answer[]" value="ml-<?=($x+1)?>" class="form-check-input" <?=(in_array("ml-".($x+1), $correct_answer))? "checked" : ""?>>
                                <input required type="text" name="list_answer[]" class="form-control" value="<?=$list_answer[$x]?>">
                            </div>
                    <?php
                        } else {
                    ?>
                            <div class="form-group">
                                <label>Answer:</label>
                                <input required type="number" name="list_answer[]" class="form-control" value="<?=$list_answer[$x]?>">
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            <?php
            }
            ?>

        </div>

        <div class="card-footer">
            <?php if($_GET['answer'] == "edit"){?>
                <a href="question-add.php?section_id=<?=$section_id?>&id=<?=$id?>" class="btn btn-warning mt-2">Back</a>
            <?php } ?>
            <input type="submit" name="submit" value="<?=($_GET['answer'] == "edit") ? "Update":"Add Answers"?>" class="btn btn-primary mt-2">  
        </div>
    </div>
</form>

<?php
if($_GET['section_id']){
    $section_id = $_GET['section_id'];
?>
<table class="table table-striped table-hover mt-2">
    <thead>
        <tr>
            <th>Name</th>
            <th>Exam Type</th>
            <th>Question Type</th>
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
        WHERE question.deleted != '1' AND question.section_id = '$section_id'
        ORDER BY question.id DESC";
    $query=mysqli_query($connect,$sql); 
    ?>
    <?php    
    while($row= mysqli_fetch_array($query)){
    ?>
        <tr>
            <td><?=$row['name']?></td>
            <td><?=$row['exam_type']?></td>
            <td><?=$row['question_type']?></td>
            <td><?=$row['section_name']?></td>
            <td><?=$row['subject_name']?></td>
            <td><?=$row['course_name']?></td>
            <td>
                <a href="question-add.php?section_id=<?=$section_id?>&id=<?=$row['id']?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete.php?tbl=question&id=<?=$row['id']?>&section_id=<?=$section_id?>" class="btn btn-danger btn-sm">Delete</a>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<div class="alert alert-primary text-right">
    <?=mysqli_num_rows($query)?> 
    Questions from
    <?php
        echo(mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM section WHERE id = '$section_id'"))['noq']);
    ?>
    (Minimum)
</div>
<?php
}
?>


<?php include('inc/footer.php');?>
