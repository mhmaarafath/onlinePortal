<?php 
require('connection.php');

$student_id = $_COOKIE['auth'];
$course_id = $_COOKIE['course_id'];

if(isset($_COOKIE['finish'])){
    $exam_id = $_COOKIE['exam'];
    mysqli_query($connect,"UPDATE exam SET status = 'completed' WHERE id = '$exam_id'");
    setcookie("exam","", -3600);
    setcookie("finish","", -3600);
    setcookie("completed","", -3600);
    setcookie("exam_type","", -3600);
    setcookie("last_answer_id","", -3600);

    $marks = round((mysqli_fetch_array(mysqli_query($connect,"SELECT SUM(remark) AS marks FROM answer WHERE exam_id = '$exam_id'"))['marks']/mysqli_num_rows(mysqli_query($connect,"SELECT * FROM answer WHERE exam_id = '$exam_id'")))*100);
    $marks = sprintf('%02d', $marks)."%";
    setcookie("sweet_alert_marks",$marks, 0);
    header("Location: dashboard.php");
}

if(isset($_POST['exam'])){
    $course_id = $_COOKIE['course_id'] ? $_COOKIE['course_id'] : $_POST['course_id'];
    $subject_id = $_POST['subject_id'];
    $exam_type = $_POST['exam_type'];
    $section_id = $_POST['section_id'];
    
    if($exam_type == "practice"){
        mysqli_query($connect,"INSERT INTO exam (student_id, subject_id, exam_type, attempt, start_time) VALUES ('$student_id', '$subject_id', '$exam_type', '$section_id', SYSDATE())");
        $exam_id= mysqli_insert_id($connect);
        mysqli_query($connect,"UPDATE exam SET status = 'completed' WHERE id = '$exam_id'");
        
        $sql4="SELECT * FROM question WHERE deleted != '1' AND section_id = '$section_id' AND exam_type = '$exam_type' ORDER BY RAND()";
        $query4 = mysqli_query($connect,$sql4);
        while($row4 = mysqli_fetch_array($query4)){
            $question_id = $row4['id'];
            mysqli_query($connect,"INSERT INTO answer (exam_id, question_id) VALUES ('$exam_id', '$question_id')");
        }
        setcookie("exam", $exam_id, 0);
        setcookie("exam_type", "practice", 0);
        header("Location: exam.php");    

    } else {
        if($course_id != "" && $subject_id !="" && $exam_type !=""){
            $sql_time = "SELECT 
                exam.*, 
                subject.minute 
                FROM exam 
                INNER JOIN subject ON exam.subject_id = subject.id 
                WHERE 
                exam.subject_id = '$subject_id' AND 
                exam.student_id = '$student_id' AND 
                exam.exam_type = '$exam_type' AND
                exam.status = 'following'";
            $query_time = mysqli_query($connect, $sql_time);
            if(mysqli_num_rows($query_time) > 0){
                $row_time = mysqli_fetch_array($query_time);
                $exam_id = $row_time['id'];
                setcookie("exam", $exam_id, 0);
                $remain_time = strtotime($row_time['start_time']) + $row_time['minute'] - time();
    
                if($remain_time > 0 ){
                    header("Location: exam.php");                            
                } else {
                    setcookie("finish","1","0");
                    header("Location: dashboard.php");                            
                }
            } else {
                $sql_pending = "SELECT 
                exam.*
                FROM exam 
                INNER JOIN subject ON exam.subject_id = subject.id 
                WHERE 
                exam.subject_id = '$subject_id' AND 
                exam.student_id = '$student_id' AND 
                exam.exam_type = '$exam_type' AND
                exam.status = 'pending'";
                $query_pending = mysqli_query($connect, $sql_pending);
                if(mysqli_num_rows($query_pending) > 0){
                    setcookie("sweet_alert","pending", 0);
                    header("Location: dashboard.php");                            
                } else {
                    $sql_approved = "SELECT 
                    exam.*
                    FROM exam 
                    INNER JOIN subject ON exam.subject_id = subject.id 
                    WHERE 
                    exam.subject_id = '$subject_id' AND 
                    exam.student_id = '$student_id' AND 
                    exam.exam_type = '$exam_type' AND
                    exam.status = 'approved'
                    LIMIT 1";
                    $query_approved = mysqli_query($connect, $sql_approved);
                    if(mysqli_num_rows($query_approved) > 0){
                        $exam_id = mysqli_fetch_array($query_approved)['id'];
                        mysqli_query($connect, "UPDATE exam SET status = 'following', start_time = SYSDATE() WHERE id = '$exam_id'");
                        setcookie("exam", $exam_id, 0);
                        header("Location: exam.php");  
                    } else {
                        $attempt = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM exam WHERE student_id = '$student_id' AND subject_id = '$subject_id' AND exam_type = '$exam_type'")) + 1;
                        mysqli_query($connect,"INSERT INTO exam (student_id, subject_id, exam_type, attempt, start_time) VALUES ('$student_id', '$subject_id', '$exam_type', '$attempt', SYSDATE())");
                        $exam_id= mysqli_insert_id($connect);
            
                        $sql = "SELECT * FROM section WHERE subject_id = '$subject_id' AND deleted != '1'";
                        $query = mysqli_query($connect, $sql);
                        while($row = mysqli_fetch_array($query)){
                            $section_id = $row['id'];
                            $noq = $row['noq'];
                
                            $sql1="SELECT * FROM question WHERE deleted != '1' AND section_id = '$section_id' AND exam_type = '$exam_type' ORDER BY RAND() LIMIT $noq";
                            $query1 = mysqli_query($connect,$sql1);
                            while($row1 = mysqli_fetch_array($query1)){
                                $question_id = $row1['id'];
                                mysqli_query($connect,"INSERT INTO answer (exam_id, question_id) VALUES ('$exam_id', '$question_id')");
                            }
                        }
                        setcookie("sweet_alert","pending", 0);
                        header("Location: dashboard.php");                                
                    }
                }
  
            }
        }  
    }
}


$dataPoints = array();
$query_graph = mysqli_query($connect, "SELECT * FROM exam WHERE student_id = '$student_id' AND exam_type = 'mock' AND status = 'completed'");
while($row_graph= mysqli_fetch_array($query_graph)){
    $exam_id = $row_graph['id'];
    $marks = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM answer WHERE exam_id = '$exam_id' AND remark = '1'"));
    $noq = mysqli_num_rows(mysqli_query($connect, "SELECT * FROM answer WHERE exam_id = '$exam_id'"));
    $marks = $marks ? $marks : 0;
    $noq = $noq ? $noq : 1;
    $marks = $marks/$noq*100;
    array_push($dataPoints, array("x" => $exam_id, "y" => $marks));
}
 

include('inc/header.php');
?>

<div class="row">
    <div class="col-md-3">
        <form action="" method="post">
            <div class="card">
                <div class="card-header">
                    New Exam
                </div>
                <div class="card-body">
                    <div class="card-text">
                        <form action = "" method="post">
                            <select disabled="disabled" name="course_id" class="form-control mb-2">
                                    <option value="">Select Course</option>
                                <?php
                                    $sql2="SELECT * FROM course WHERE deleted != '1'";
                                    $query2=mysqli_query($connect,$sql2); 
                                    while($row2= mysqli_fetch_array($query2)){
                                        $selected = ($course_id == $row2['id']) ? "selected" : "";
                                ?>
                                        <option <?=$selected?> value="<?=$row2['id']?>"><?=$row2['name']?></option>
                                <?php
                                    }
                                ?>
                            </select> 
                            <select name="subject_id" class="form-control mb-2">
                                <option value="">Select Subject</option>
                            </select> 
                            <select name="exam_type" class="form-control mb-2">
                                <option value="">Exam Type</option>
                                <option value="mock">Mock Exam</option>
                                <option value="practice">Practice Questions</option>
                            </select> 
                            <select name="section_id" class="form-control mb-2">
                                <option value="">Select Section</option>
                            </select> 
                        </form>
                    
                    </div>
                </div>
                <div class="card-footer">
                    <input type="submit" name="exam" value="Enroll" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-9">

<div class="jumbotron mt-2 mt-sm-0">
    <h2 class="text-right">Student Dashboard</h2>
</div>
<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Mock</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Practice</a>
  </li>
</ul>

<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
        <!-- <div id="chartContainer" style="height: 370px; width: 100%;"></div> -->
        <table class="table table-responsive table-striped table-hover">
            <thead>
                <tr>
                    <th>Id</th>
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
            course.name AS course_name
            FROM exam
            INNER JOIN subject ON exam.subject_id = subject.id
            INNER JOIN course ON subject.course_id = course.id
            WHERE exam.student_id = '$student_id' 
            AND exam.exam_type = 'mock'
            ORDER BY exam.start_time DESC";
            $query3=mysqli_query($connect,$sql3);
            while($row3= mysqli_fetch_array($query3)){
                $exam_id = $row3['id'];
            ?>
                <tr>
                    <td><?=$row3['id']?></td>
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
                        % <a target="_new" class="badge badge-info" href="print.php?id=<?=$exam_id?>">Print</a>
                        <?php
                            } else{
                        ?>
                                <span class="badge badge-secondary"><?=ucfirst($row3['status'])?></span>
                        <?php
                            }
                        ?>

                    </td>
                    <td>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
  </div>
  <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Course</th>
                <th>Time</th>
                <th>Section</th>
                <th>Marks</th>
            </tr>
        </thead>
        <tbody>
        <?php
        require('connection.php');
        $sql5="SELECT 
        exam.*,
        subject.name AS subject_name,
        course.name AS course_name,
        section.name AS section_name
        FROM exam
        INNER JOIN subject ON exam.subject_id = subject.id
        INNER JOIN course ON subject.course_id = course.id
        INNER JOIN section ON exam.attempt = section.id
        WHERE exam.student_id = '$student_id' 
        AND exam.exam_type = 'practice'
        ORDER BY exam.start_time DESC";
        $query5=mysqli_query($connect,$sql5);
        while($row5= mysqli_fetch_array($query5)){
            $exam_id = $row5['id'];
        ?>
            <tr>
                <td><?=$row5['subject_name']?></td>
                <td><?=$row5['course_name']?></td>
                <td><?=$row5['start_time']?></td>
                <td><?=$row5['section_name']?></td>
                <td class="text-right">
                    <?php
                        if($row5['status'] == "completed"){
                        echo(
                                round((
                                    mysqli_fetch_array(mysqli_query($connect,"SELECT SUM(remark) AS marks FROM answer WHERE exam_id = '$exam_id'"))['marks']
                                    /
                                    mysqli_num_rows(mysqli_query($connect,"SELECT * FROM answer WHERE exam_id = '$exam_id'"))
                                )
                                *100)
                            );
                    ?>
                    %
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
  </div>
</div>




    </div>
</div>
</form>

<?php include('inc/footer.php');?>

