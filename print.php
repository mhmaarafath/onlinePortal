<?php
include('connection.php');
require('fpdf/fpdf.php');
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','',11);

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql="SELECT 
        exam.*,
        subject.name AS subject_name,
        course.name AS course_name
        FROM exam
        INNER JOIN subject ON exam.subject_id = subject.id
        INNER JOIN course ON subject.course_id = course.id
        WHERE exam.id = '$id'"; 

    $sql1="SELECT 
        answer.*,
        section.name AS section_name,
        SUM(answer.remark) AS marks,
        count(answer.question_id) AS noq
        FROM answer
        INNER JOIN question ON answer.question_id = question.id
        INNER JOIN section ON question.section_id = section.id
        WHERE answer.exam_id = '$id'
        GROUP BY section.id"; 

    $marks = round((mysqli_fetch_array(mysqli_query($connect,"SELECT SUM(remark) AS marks FROM answer WHERE exam_id = '$id'"))['marks']
            /
            mysqli_num_rows(mysqli_query($connect,"SELECT * FROM answer WHERE exam_id = '$id'"))
            )*100);

        $pdf->Ln();
        $pdf->Image('admin/img/logo-admin.png',10,10,-300);
    $query = mysqli_query($connect, $sql);
    $query1 = mysqli_query($connect, $sql1);
    while($row = mysqli_fetch_array($query)){
        $pdf->Ln();        
        $pdf->Cell(10,20,"",0);        
        $pdf->Ln();
        $pdf->Cell(10,10,"Exam ID : ".$row['id'],0);
        $pdf->Ln();
        $pdf->Cell(10,10,"Student ID : ".$row['student_id'],0);
        $pdf->Ln();
        $pdf->Cell(10,10,"Course : ".$row['course_name'],0);
        $pdf->Ln();
        $pdf->Cell(10,10,"Subject : ".$row['subject_name'],0);
        $pdf->Ln();
        $pdf->Cell(10,10,"Attempt : ".$row['attempt'],0);
        $pdf->Ln();
        $pdf->Cell(10,10,"Marks : ".$marks."%",0);
    }

    $pdf->SetFont('Arial','B',11);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Cell(150,10,"SECTION",0);
    $pdf->Cell(10,10,"MARKS",0);


    $pdf->SetFont('Arial','',11);
    while($row1 = mysqli_fetch_array($query1)){
        $correct = round(($row1['marks']/$row1['noq'])*100);
        $pdf->Ln();
        $pdf->Cell(150,10,$row1['section_name'],0);
        $pdf->Cell(10,10,$correct."%",0);
    }
}
$pdf->Output("I","Result.pdf");
?>