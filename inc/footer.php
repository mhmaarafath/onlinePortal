</div>
</body>
</html>
<script>
$(document).ready(function(){

    $('[name="register_number"]').tooltip({'trigger':'focus', 'title': 'Check the Rececipt for Registration Number'});

    $('[name="section_id"]').hide();
    $('[name="exam_type"]').change(function(){
      ($(this).val() == "practice")? $('[name="section_id"]').show() : $('[name="section_id"]').hide();
    });

    var course_id = $('[name="course_id"]').val();  
      $.ajax({  
          url:"load_data.php",  
          method:"POST",  
          data:{course_id:course_id},  
          success:function(data){  
                $('[name="subject_id"]').html(data);  
          }  
      }); 

    $('[name="course_id"], [name="subject_id"], [name="exam_type"]').change(function() {
      var subject_id = $('[name="subject_id"]').val();  
      var exam_type = $('[name="exam_type"]').val();
      $.ajax({  
          url:"load_data.php",  
          method:"POST",  
          data:{subject_id:subject_id, exam_type:exam_type},  
          success:function(data){  
                $('[name="section_id"]').html(data);  
          }  
      }); 
    }); 
});


function sec2time(timeInSeconds) {
    var pad = function(num, size) { return ('000' + num).slice(size * -1); },
    time = parseFloat(timeInSeconds).toFixed(3),
    hours = Math.floor(time / 60 / 60),
    minutes = Math.floor(time / 60) % 60,
    seconds = Math.floor(time - minutes * 60),
    milliseconds = time.slice(-3);

    return pad(hours, 2) + ':' + pad(minutes, 2) + ':' + pad(seconds, 2);
}

 </script>

<?php
if(!isset($_COOKIE['exam_type'])){
?>
  <script>
    var count = document.getElementById('time').value;
    interval = setInterval(function(){
      document.getElementById('time').value=count;
      document.getElementById('count').innerHTML=sec2time(count);
      count--;
      if (count <= 0){
        clearInterval(interval);
        document.getElementById('count').innerHTML='00:00:00';
        document.cookie = "sweet_alert=time";
        document.cookie = "finish=1";
        window.location.replace("dashboard.php");
      }
    }, 1000);
  </script>
<?php
} else {
  ?>
  <script>
    document.getElementById('count').innerHTML='Practice Exam';
  </script>
<?php
}
?>

<script>
window.onload = function () {
 
 var chart = new CanvasJS.Chart("chartContainer", {
   animationEnabled: true,
   exportEnabled: true,
   theme: "light2", // "light1", "light2", "dark1", "dark2"
   title:{
     text: "Past Exams Summery"
   },
   data: [{
     type: "column", //change type to bar, line, area, pie, etc
     //indexLabel: "{y}", //Shows y value on all Data Points
     indexLabelFontColor: "#5A5757",
     indexLabelPlacement: "outside",   
     dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
   }]
 });
 chart.render();
  
 }
</script>
