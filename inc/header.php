<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if(isset($_POST['logout'])){
    setcookie("auth","", time()-3600);
    setcookie("course_id","", time()-3600);
    setcookie("sweet_alert","logout", 0);
    header("Location: index.php");
}

if(!isset($_COOKIE['auth']) && !isset($_COOKIE['course_id']) && ((basename(strtok($_SERVER["REQUEST_URI"], '?')) != "login.php") && (basename($_SERVER['REQUEST_URI']) != "register.php") && (basename($_SERVER['REQUEST_URI']) != "index.php"))){
  header("Location: index.php");
}
?>

<?php 
    function sweet_alert($title, $text, $icon, $btn, $url){
        $cancel = ($_COOKIE['sweet_alert'] == "finish") ? "showCancelButton: 'true'" : "";
        $cookie = ($_COOKIE['sweet_alert'] == "finish") ? "document.cookie = \"finish=1\"": "";
        echo 
        "
        <script>
        document.cookie = 'sweet_alert= ; expires = Thu, 01 Jan 1970 00:00:00 GMT';
        document.cookie = 'sweet_alert_marks= ; expires = Thu, 01 Jan 1970 00:00:00 GMT';
        Swal.fire({
            title: '$title',
            text: '$text',
            icon: '$icon',
            confirmButtonText: '$btn',
            $cancel
        }).then((result) =>{
            if (result.value) {
                $cookie
                window.location.href = '$url';
            }
        })           
        </script>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon" href="admin/img/favicon.ico">
    <title>Achievers Online Portal</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    <link rel="stylesheet" href="style.css">

    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

</head>
<body>
<?php if(basename($_SERVER['REQUEST_URI']) != "index.php" && basename($_SERVER['REQUEST_URI']) != "register.php" && basename(strtok($_SERVER["REQUEST_URI"], '?')) != "login.php") {?>
<nav class="sticky-top navbar navbar-expand-lg py-1 navbar-light" style="background-color:#fd000a !important">
  <a class="navbar-brand" href="#">
    <img src="admin/img/logo.png" alt="Logo" style="width:200px;">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <div class="mx-auto mt-2 mt-sm-0" style="font-size:22px;color:#fff">
      <?=strtoupper("Achievers Online Portal")?>
    </div>
    <!-- <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" style="color:#fff" href="#"></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="subject.php">Subject</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="section.php">Section</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="question.php">Question</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Dropdown
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="#">Action</a>
          <a class="dropdown-item" href="#">Another action</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>
    </ul> -->
    <form action = "" method = "post" class="form-inline my-2 my-lg-0">
      <!-- <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search"> -->
      <input type = "submit" class="btn btn-dark btn-sm my-2 my-sm-0" name = "logout" value = "Log Out">
    </form>
  </div>
</nav>
<?php } ?>

<div class="container-fluid mt-2">
<?php 
  if(isset($_COOKIE['sweet_alert'])){
    $sweet_alert = $_COOKIE['sweet_alert'];
    if($sweet_alert == "invalid"){
      sweet_alert("Invalid Login", "", "error", "Try Again", "");
    } elseif ($sweet_alert == "valid") {
      sweet_alert("Welcome", "", "success", "Okay", "");
    } elseif ($sweet_alert == "register") {
      sweet_alert("Registered Successfully", "", "success", "Okay", "");
    } elseif ($sweet_alert == "logout"){
      sweet_alert("Logged Out", "", "info", "Bye", "");
    } elseif ($sweet_alert == "time"){
      sweet_alert("Time Out", "", "info", "View Results", "");
    } elseif ($sweet_alert == "review"){
      sweet_alert("Completed", "", "info", "Review Answers", "");
    } elseif ($sweet_alert == "finish"){
      sweet_alert("Confirm", "End & Submit the Exam", "question", "Complete", "dashboard.php");
    } elseif ($sweet_alert == "pending"){
      sweet_alert("Pending", "", "info", "Request Pending", "");
    } 
  }
  if(isset($_COOKIE['sweet_alert_marks'])){
    $marks = $_COOKIE['sweet_alert_marks'];
    sweet_alert("Results", $marks, "info", "Complete", "");
  }
?>
