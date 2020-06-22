<?php 
require('connection.php');
if(isset($_COOKIE['exam'])){
    $exam_id = $_COOKIE['exam'];
    $row_time = mysqli_fetch_array(mysqli_query($connect,"SELECT exam.start_time, subject.minute FROM exam INNER JOIN subject ON exam.subject_id = subject.id WHERE exam.id = '$exam_id'"));
 
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        if($exam_id == mysqli_fetch_array(mysqli_query($connect, "SELECT * FROM answer WHERE id = '$id'"))['exam_id']){
        $sql = "SELECT 
                answer.*,
                question.question_type,
                question.name AS question_name,
                question.noa,
                question.list_answer,
                question.correct_answer
                FROM answer
                INNER JOIN question ON answer.question_id = question.id
                WHERE answer.id = '$id'";
        } else {
            header("Location: exam.php");   
        }
    }elseif(isset($_COOKIE['completed'])){
        $completed = $_COOKIE['completed'];
        $sql = "SELECT 
                answer.*,
                question.question_type,
                question.name AS question_name,
                question.noa,
                question.list_answer,
                question.correct_answer
                FROM answer
                INNER JOIN question ON answer.question_id = question.id
                WHERE answer.exam_id = '$exam_id' 
                ORDER BY answer.id LIMIT $completed,1";
    } else {
        $sql = "SELECT 
                answer.*,
                question.question_type,
                question.name AS question_name,
                question.noa,
                question.list_answer,
                question.correct_answer
                FROM answer
                INNER JOIN question ON answer.question_id = question.id
                WHERE answer.exam_id = '$exam_id' AND remark IS NULL LIMIT 1";
    }

    $query = mysqli_query($connect,$sql);

    $row = mysqli_fetch_array($query);
    setcookie("previous", "0", -3600);
    $answer_id = $row['id'];
    $question_id = $row['question_id'];
    $question_type = $row['question_type'];
    $list_answer = explode("|", $row['list_answer']);
    $name = explode("|", $row['name']);
    $noa = $row['noa'];

    $empty_questions = mysqli_num_rows(mysqli_query($connect,"SELECT * FROM answer WHERE exam_id = '$exam_id' AND remark IS NULL"));
    $total_questions = mysqli_num_rows(mysqli_query($connect,"SELECT * FROM answer WHERE exam_id = '$exam_id'"));


    if(isset($_POST['finish'])){
        setcookie("sweet_alert","finish", 0);
        header("Location: exam.php");
    }

    if(isset($_POST['previous'])){
        if($_COOKIE['last_answer_id']){
            $last_answer_id = $_COOKIE['last_answer_id'];
            header("Location: exam.php?id=$last_answer_id");
        }
    }

    if(isset($_POST['submit']) || isset($_POST['flag'])|| isset($_POST['skip'])){

        if(isset($_POST['submit']) || isset($_POST['flag'])){
            $answer = implode("|", $_POST["answer"]);

            if(isset($_POST['submit'])){
                $remark = ($row['correct_answer'] == $answer) ? "1" : "0";
            } elseif(isset($_POST['flag'])){
                $remark = "F";
            }

        } elseif(isset($_POST['skip'])){
            $answer = "";
            $remark = "S";
        }
        
        mysqli_query($connect,"UPDATE answer SET name = '$answer', remark = '$remark' WHERE id = '$answer_id'");
        setcookie("last_answer_id", $answer_id, 0);
        $empty_questions = mysqli_num_rows(mysqli_query($connect,"SELECT * FROM answer WHERE exam_id = '$exam_id' AND remark IS NULL"));
    
        if($empty_questions == 0){
            if(!isset($_COOKIE['completed'])){
                setcookie("sweet_alert","review", 0);
                setcookie("completed","0", 0);
            } else {
                $completed =  ($_COOKIE['completed'] + 1) % $total_questions;
                setcookie("completed",$completed, 0);
            }
            header("Location: exam.php"); 
        } else {
            header("Location: exam.php"); 
        }
    
    }

} else {
    header("Location: dashboard.php");
}

include('inc/header.php');
?>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <h2 class="card-header" id="count">&nbsp</h2>
            <div class="card-body">
                <div class="card-title">Paper Summary</div>
                <div class="card-text">
                    <?php
                        $sql1 = "SELECT * FROM answer WHERE exam_id = '$exam_id'";
                        $query1 = mysqli_query($connect, $sql1);
                        $x = 1;
                        while($row1 = mysqli_fetch_array($query1)){
                            $id = $row1['id'];
                            if($row1['remark'] == "F"){
                                $btn = "warning";
                            } elseif ($row1['remark'] == "S"){
                                $btn = "danger";
                            }elseif ($row1['name'] == "" && $row1['remark'] != ""){
                                $btn = "danger";
                            } elseif ($row1['remark'] == ""){
                                $btn = "secondary";
                            } else {
                                $btn = "primary";
                            }
                    ?>
                            <a class="btn btn-<?=$btn?> mt-1 ml-1" href="?id=<?=$id?>"><?=sprintf('%02d', $x)?></a>
                    <?php
                            $x++;
                        }
                    ?>
                </div>
            </div>
            <div class="card-footer">
                Viewed <?=$total_questions-$empty_questions?> / <?=$total_questions?> (Total)
                <br><br>
                <form action="" method="post">
                    <input type="submit" name = "finish" value="Complete & Submit Exam" class="btn btn-success">
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card">
            <form action="" method="post">
                <div class="card-header overflow-auto">
                    <?=$row['question_name']?>
                    <?php
                    if($_COOKIE['exam_type']=="practice" && ($row['remark'] == "1" || $row['remark'] == "0")){
                    ?>
                        <div class="alert alert-success mt-2">
                            <div class="h6">Correct Answer</div>
                            <?php 
                                if($question_type != "short"){
                                    $correct_answers = explode("|", $row['correct_answer']);
                                    foreach($correct_answers as $num_correct_answers){
                                        $num_correct_answers = substr($num_correct_answers, -1); 
                                        echo $list_answer[$num_correct_answers-1];
                                        echo "<br>";
                                    }
                                } else {
                                    echo $list_answer[0];
                                }
                            ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="card-body">
                    <div class = "card-text">
                        <?php 
                        for($x = 0; $x < $noa; $x++){
                            if($question_type == "single"){
                                $checked = (in_array("mc-".($x+1),$name)) ? "checked =\"checked\"" : "";
                            ?>
                                <div class="custom-control custom-radio mb-2">
                                    <input <?=$checked?> id="customRadio<?=($x+1)?>" class="custom-control-input" type="radio" name="answer[]" value="mc-<?=($x+1)?>" class="form-check-input">
                                    <label for="customRadio<?=($x+1)?>" class="custom-control-label"><?=$list_answer[$x]?></label>
                                </div>
                            <?php
                            } elseif($question_type == "multiple"){
                                $checked = (in_array("ml-".($x+1),$name)) ? "checked =\"checked\"" : "";
                            ?>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input <?=$checked?> id="customCheck<?=($x+1)?>" class="custom-control-input" type="checkbox" name="answer[]" value="ml-<?=($x+1)?>">
                                    <label for="customCheck<?=($x+1)?>" class="custom-control-label"><?=$list_answer[$x]?></label>
                                </div>

                            <?php
                            } elseif($question_type == "short") {
                            ?>
                                <div class="form-group">
                                    <label>Answer:</label>
                                    <input class="form-control" type="number" name="answer[]" value="<?=$name[$x]?>">
                                </div>
                            <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="time" id="time" value="<?php echo(strtotime($row_time['start_time'])+$row_time['minute']-time())?>">                        
                    <input class="btn btn-primary" type="submit" name="previous" value="Previous">
                    <input class="btn btn-primary" type="submit" name="submit" value="Submit">
                    <input class="btn btn-warning" type="submit" name="flag" value="Flag">
                    <input class="btn btn-danger" type="submit" name="skip" value="Skip">
                </div>
            </form>
        </div>   
    </div>
</div>




<?php include('inc/footer.php');?>

