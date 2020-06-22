</div>
</body>
</html>


<script>
$(document).ready(function(){
    
    var question_type = $('[name="question_type"]').val();
    $("[name='noa']").show();
    if(question_type == "short") {
        $("[name='noa']").hide();
    }

    $('[name="question_type"]').change(function() {
        var question_type = $(this).val();
        $("[name='noa']").show();
        if(question_type == "short") {
            $("[name='noa']").hide();
        }
    }); 


    $("[name='submit']").click(function() {
        $("[name='correct_answer[]']").prop('required',false);
        checked = $("input:checkbox:checked").length;
        if(!checked) {
            $("[name='correct_answer[]']").prop('required',true);
        }
    });


    $('table').DataTable({
        stateSave: true
    });
});
</script>
