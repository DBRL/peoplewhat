<?php
/**
 * User: npauley
 * Date: 1/20/12
 * Time: 12:08 PM
 */
require_once "header.inc.php";
$days_of_week =  array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');

?>
<script>
    var days_of_week = ["monday","tuesday","wednesday","thursday","friday","saturday","sunday"];
    $(function() {
        // get the current schedule notes files
        var schedule_notes = {};
         $.ajax({
            url: "schedules/ps-notes.json",
            cache: true,
            dataType: 'json',
            success: function(data){
                schedule_notes.current = data;
                //console.log("notes: ",schedule_notes.current);;
                add_notes();
            }
         });
        $.ajax({
            url: "schedules/ps-notes-next.json",
            cache: true,
            dataType: 'json',
            success: function(data){
                schedule_notes.next = data;
                //console.log("notes: ",schedule_notes.next);
            }
         });
        var add_notes = function () {
          for ( var i = 0; i < 7; i++ ) {
               var day = days_of_week[i];
               $("#weekend-"+day).val(schedule_notes.current[day].weekend);
               $("#vacations-"+day).val(schedule_notes.current[day].vacations);
               $("#changes-"+day).val(schedule_notes.current[day].changes);
          }
        };

    });
</script>
</head>
<body>
    <div id="container">
    <div class="entry">

    </div>
    <form action="" method="post" class="notes">
        <?php for ($i = 0; $i < 7; $i++): ?>
        <fieldset class="">
            <h3 class="date-<?=$days_of_week[$i]?>"><?=$days_of_week[$i]?></h3>
            <input name="weekend-<?=$days_of_week[$i]?>" id="weekend-<?=$days_of_week[$i]?>" placeholder="Weekend" />
            <input name="vacations-<?=$days_of_week[$i]?>" id="vacations-<?=$days_of_week[$i]?>" placeholder="Vacations" />
            <input name="changes-<?=$days_of_week[$i]?>" id="changes-<?=$days_of_week[$i]?>" placeholder="Changes" />
        </fieldset>
        <?php endfor; ?>
        <input type="submit" name="submit" value="Submit" />
    </form>
    <div id="footer">
        Powered by <em>Schedule Magic!</em>&trade;
    </div>
  </div>
</body>
</html>