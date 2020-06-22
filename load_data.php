<?php  
 require('connection.php');
 $output = '';  
 $output_section = '';
 if(isset($_POST["course_id"])){ 
    $course_id = $_POST["course_id"];
    $sql = "SELECT * FROM subject WHERE course_id = '$course_id' AND deleted != '1'";  
    $query = mysqli_query($connect, $sql); 
        $output .= '<option value="">Select Subject</option>';
    while($row = mysqli_fetch_array($query)){  
        $output .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
    }  
    echo $output;  
 } 
 if(isset($_POST["subject_id"]) && $_POST["exam_type"] == "practice"){ 
    $subject_id = $_POST["subject_id"];
    $sql1 = "SELECT * FROM section WHERE subject_id = '$subject_id' AND deleted != '1'";  
    $query1 = mysqli_query($connect, $sql1);  
        $output_section .= '<option value="">Select Section</option>';
    while($row1 = mysqli_fetch_array($query1)){  
        $output_section .= '<option value="'.$row1['id'].'">'.$row1['name'].'</option>';
    }  
    echo $output_section;  
} 
 ?>  