<?php
   define('VERSION','105');
   require_once 'config.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>DBRL Schedule</title>
  <link rel="stylesheet" href="style.css?<?=VERSION?>" />
  <link rel="stylesheet" href="print.css?<?=VERSION?>" media="print" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.js"></script>
  <script src="jquery.floatheader.min.js"></script>

  <script>
      var  staff = {"Amanda B":573, "Diana B":14, "Elinor B":7, "Jim S":100, "Judy H":540, "Marlene G":473, "Melanie H":217, "Melissa C":18, "Rebecca B":556, "David M":526, "Joe N":548, "Johnny J":54, "Madeline R":94, "Mike K":497, "Russ N":396, "Ryan G":591, "William B":180, "Betsy C":21, "Doyne M":69, "Scott H":487, "Amanda G":604, "Amber S":426, "Amy Z":528, "Angie R":605, "Anna H":618, "Ashley A":613, "Bronwyn L":552, "Cameron V":586, "Chris S":105, "Christine M":455, "Dave K":619, "Debbie H":585, "Doreen M":542, "Elizabeth H":614, "Emily S":601, "Hala F":567, "Joleen K":476, "Jordan S":314, "Karen M":623, "Kathy W":553, "Katie H":504, "Kim S":606, "Kim M":284, "Kris A":621, "Lindsey A":603, "Liz L":593, "Mary Ann H":41, "Matt S":463, "Matt W":617, "Melissa M":616, "Michelle F":188, "Nancy C":602, "Nathan T":308, "Pam H":530, "Patrick F":598, "Paul G":600, "Peggy P":568, "Rita H":608, "Roberta L":527, "Roddrick E":554, "Sara M":622, "Sarah G":472, "Shana J":599, "Sharon H":360, "Sheryl B":261, "Shirley D":25, "Stephanie Clarisse H":620, "Tawanda C":20, "Tim P":612, "Wayne P":87, "Wendy R":317, "Jay J":367, "Mike F":590, "Mike M":78, "Stephanie H":299, "Carolyn C":17, "Deb J":55, "Eric S":96, "Frances B":16, "Heather P":124, "Karen N":81, "R. Otter B":312, "Jenny M":67, "Joanne W":594, "Mitzi S":559, "Nathan P":85, "Veronica M":577, "Zack G":468, "Aaron B":560, "Aimee L":150, "Althea H":46, "Amanda F":375, "Amy W":558, "Amy H":486, "Amy L":61, "Angela S":80, "Anita G":386, "Barb T":195, "Barbara B":8, "Bette S":494, "Brad W":495, "Brandy S":543, "Carren S":440, "Chris S":483, "Christina J":145, "Colleen B":544, "Dana B":517, "Dirk B":445, "Elaine S":281, "Elf N":507, "Elizabeth P":597, "Frank S":395, "Gloria B":5, "Gwen G":587, "Hilary A":171, "Hollis S":103, "Ida F":290, "Jessi M":534, "Jim C":313, "Jim H":377, "Jordan R":580, "Judy P":410, "Kate P":89, "Kirk H":500, "Lauren W":388, "Lindsey E":446, "Lindsey S":340, "Lucius B":330, "Maria C":22, "Mary G":37, "Melissa S":162, "Nancy L":68, "Nina S":95, "Patricia M":74, "Robert K":222, "Robin D":581, "Sally B":301, "Sally A":2, "Sarah H":52, "Sarah E":609, "Seth S":610, "Shash L":325, "Stephanie H":611, "Stephanie T":374, "Steve D":28, "Svetlana G":39, "Terri H":533, "Wendy B":579, "Dawn O":83, "Idenia T":109, "Lisa M":73, "Pat K":60, "Rikki W":112, "Ronda M":75, "Sara Frances D":515, "Stephanie B":12, "Letitia D": 26};

      $(function() {

         // auto highlight current hour when on today's schedule
         var update_time = function(){
            var date = new Date();
            var hours = date.getHours();
            var ampm = "AM";
            if ( hours >= 12 ) {
               ampm = "PM";
            }
            if ( hours > 12 ) {
                hours = hours - 12;
            }

            $("thead td").removeClass("highlight");
            var re = new RegExp("^"+hours+" "+ampm,"i");

            $("thead td").filter(function() {
               return $(this).text().match(re);
            }).addClass("highlight");

            return setTimeout(update_time, 5*60*1000);
         };
         var timeout = update_time();


         // add search box
         var add_search = function() {
             var $cell = $("thead tr:nth-child(2) td:first").html("");
             $('<input name="q" id="q" maxlength="10"/>').appendTo($cell).focus();
             //$("thead tr").eq(1).find("td").eq(0).html('<input name="q" id="q" size="6" />').focus();
         };
         add_search();

         var add_note = function(label, forname) {
             $('<tr class="desk"><td class="category" colspan="4">'+label+'</td><td colspan="56"><input type="text" name="'+forname+'" class="notes" /></td></tr>')
               .appendTo(".reporttable");
         };
         var add_notes = function(){
            add_note('Off for Weekend','off');
            add_note('Vacations &amp; Other','vacations');
            add_note('Schedules Changes','changes');
         };
         add_notes();

         $("#photos").click(function() {
             var re;
             $.each(staff,function(index,val) {

                re = new RegExp("^"+index,"i");
                //console.log(index);

                $("tbody td.details_shifts").filter(function() {
                   name = $(this).text();
                   return name.match(re);
                }).html("<img src='http://intranet.dbrl.org/dir/staff/photos/"+val+"_sm.jpg' height='80' title='"+index+"' />");

             });
         });

         // removed redundant desk labels and add row highlighting
         var highlight_desks = function(){
            var current = "";
            var rowClass = "even";
            $("tbody tr").find("td:first").each(function() {
              $this = $(this);
              if ( $this.text() != current ) {
                rowClass = ( rowClass == "even" ) ? "" : "even";
                $this.parent().addClass(rowClass + " desk");
                current = $this.text();
              } else {
                $this.parent().addClass(rowClass);
                $this.text("");
              }
            });
         };
         highlight_desks();

         $.ajaxSetup ({
    		cache: false
	      });

         $("#email").focus(function() { if ( $(this).val() == "Username" ) { $(this).val("");  } });
         $("#email").blur(function() { if ( !$(this).val() ) { $(this).val("Username");  } });

         $("#department").change( function() {
             var dept_id = $(this).val();
             console.log('dept_id: ' + dept_id);
             update_schedule(dept_id, 0);

         });

         // lock the container height
         $("#schedule").height($("#schedule").height());

         var $floatHeader = $("#reporttable0").floatHeader();


         var update_schedule = function( dept_id, schedule_id ) {

             $("#schedule").fadeOut(200, function() {

                 $.ajax({
                  url: 'schedules/' + dept_id + '_' + schedule_id + '.html',
                  success: function(data) {
                     $("#schedule").html(data);
                  },
                  complete: function(data) {

                     $("#list li").removeClass("active").eq(schedule_id).addClass("active");
                     $("#schedule").fadeIn(300);
                     add_search();
                     add_notes();
                     highlight_desks();
                     $( "#reporttable" + schedule_id ).floatHeader();

                     if ( id == 0 ) {
                        timeout = update_time();
                     } else {
                        clearTimeout(timeout);
                     }

                  },
                  dataType: "html"
                });

             });

         };


         $("#list a").click(function(e) {
             e.preventDefault();
             $that = $(this);
             var schedule_id = $that.attr("rel");

             update_schedule( $("#department").val(), schedule_id );

         });

         $("#q").live('keyup', function(e){
              $this = $(this);
              search_text = $this.val();

              if ( search_text.length == 0 ) {
                  $(".match").removeClass("match");
                  $("tbody tr").stop(true, true).fadeTo(400,1);
              }

              if ( search_text.length  >= 3 && search_text.length  <= 7 ) {

                  $("tr, td").removeClass("match");

                 /* if ( !$.browser.msie ) {
                    $("tbody tr").stop(true, true).fadeIn();
                  }*/
                  
                  var re = new RegExp("^"+search_text,"i");

                  $("tbody td.details_shifts").filter(function() {
                     return $(this).text().match(re);
                   }).addClass("match");

                  /*if ( !$.browser.msie ) {
                    $("tbody tr:not(.match)").stop(true, true).fadeTo(500,0.3);
                  }*/
              }

         });

      });
  </script>
</head>
<body>
    <div id="container">
    <div id="topnav">
            <a href="/">Intranet</a> &nbsp;|&nbsp; <a href="http://www.dbrl.org/" target="_blank">DBRL.org</a>
            <select name="department" id="department">
        <?php
            foreach ( $departments as $dept_id => $dept ):
                echo "<option value='{$dept_id}'>{$dept}</option>";
            endforeach;
        ?>
            </select>
            <form action="http://schedule.dbrl.org/login.asp" method="post" name="login" id="login" target="_blank">
            <a href="http://schedule.dbrl.org/" target="_blank">PeopleWhere</a>&trade; Sign-in &nbsp;
    	    <input type="hidden" value="signin" name="staffaction">
			<input type="text" name="email" value="Username" size="8" id="email">
			<input type="password" name="password" size="8"> <input type="submit" value="Go">
		    </form>
           </div>
        </div>
    <div id="header">


        <h1>DBRL Public Services Schedule</h1>
        <div id="nav">
        <ul id="list">
          <li class="active"><a href="#" rel="0">Today</a></li>
          <?php
            for ($i = 1; $i < 10 ; $i++ ):
                echo '<li><a href="#'.$i.'" rel="'.$i.'">' . date('l', strtotime($i.' days')) . '</a></li>';
            endfor;
           ?>
        </ul>
        <button id="photos">Photos!</button>
        </div>
    </div>
    <div id="main" role="main">
       <div id="schedule">
        <?php echo file_get_contents( SCHEDULES_PATH . '9_0.html'); ?>
       </div>
    </div>

    <div id="footer">
        Powered by <em>Schedule Magic!</em>&trade;
    </div>
  </div>
</body>
</html>
