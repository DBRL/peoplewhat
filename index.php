<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title></title>
  <link rel="stylesheet" href="style.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js"></script>
    <script src="jquery.floatheader.min.js"></script>
  <script>

      $(function() {

         //$("table").hide();
         //$("table").eq(0).show();

         $("#list li").eq(0).addClass("active");

         // add search box
         $('<input name="q" id="q" size="6" />').appendTo("thead tr:nth-child(2) td:first").focus();
         //$("thead tr").eq(1).find("td").eq(0).html('<input name="q" id="q" size="6" />').focus();

         var date = new Date();
         $("#reporttable0 thead td:nth-child("+(date.getHours()-5)+")").addClass("highlight");

         $.ajaxSetup ({
    		cache: false
	      });

         var $floatHeader = $("#reporttable0").floatHeader();

         // fix the table width
         ///$(".reporttable").width($(".reporttable").width());

         $("#list a").click(function(e) {
             e.preventDefault();
             $that = $(this);
             var id = $that.attr("rel");

             $("#main .reporttable").fadeOut(400);

             $("#main").load('schedule' + id + '.html', function() {
                 $("#reporttable0 thead td:nth-child("+(date.getHours()-5)+")").addClass("highlight");
                 $('<input name="q" id="q" size="6" />').appendTo("thead tr:nth-child(2) td:first").focus();
                 $("#list li").removeClass("active").eq(id).addClass("active");
                 ///$(".reporttable").width($(".reporttable").width());
                 $( "#reporttable" + id ).floatHeader();
             });

             //console.log($that.attr("rel"));
             //$activeTable = $( "#reporttable" + $that.attr("rel") );
             //console.log($activeTable);
            /* $("table:visible").fadeOut(400, function() {
                 $(".floatHeader").remove();
                 $activeTable.fadeIn(800, function(){
                  // $floatHeader.fhInit();
                  $( "#reporttable" + $that.attr("rel") ).floatHeader();
                 });

             });*/

         });

         $("#q").live('keyup', function(){
              $this = $(this);
              search_text = $this.val();

              if ( search_text.length < 3 ) {
                  $(".match").removeClass("match");
                  $("tbody tr").stop(true, true).fadeTo(400,1);

              }

              if ( search_text.length  >= 3 ) {
                  $(".match").removeClass("match");
                  $("tbody tr").stop(true, true).fadeIn();
                  
                  var re = new RegExp("^"+search_text,"i");
                  $("tbody td.details_shifts").filter(function() {
                    return $(this).text().match(re);
                   }).addClass("match").parent().addClass("match");

                  //$("tbody tr").not(".match").stop(true, true).fadeOut(600);
                  $("tbody tr").not(".match").stop(true, true).fadeTo(600,0.3);
              }

         });

      });
  </script>
</head>
<body>
    <div id="container">
    <header>
        <h1>DBRL Public Services Schedule</h1>
        <p><em>This is a work in progress. Best viewed in a &ldquo;proper&rdquo; browser (IE 9+, Firefox, Chrome, etc.)</em></p>
        <div id="nav">
        <ul id="list">
          <li><a href="#" rel="0">Today</a></li>
          <?php
            for ($i = 1; $i < 10 ; $i++ ):
                echo '<li><a href="#'.$i.'" rel="'.$i.'">' . date('l', strtotime($i.' days')) . '</a></li>';
            endfor;
           ?>
        </ul>
        </div>
    </header>
    <div id="main" role="main">
        <?php echo file_get_contents('/home/www/intranet.dbrl.org/www/app/peoplewhat/schedule0.html'); ?>
    </div>
    <footer>
        PeopleWhat&trade; <em>Express</em>
    </footer>
  </div>
</body>
</html>
