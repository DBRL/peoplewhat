<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>DBRL Schedule</title>
  <link rel="stylesheet" href="style.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js"></script>
    <script src="jquery.floatheader.min.js"></script>
  <script>

      $(function() {

         //$("table").hide();
         //$("table").eq(0).show();

         $("#list li").eq(0).addClass("active");

         // add search box
         $('<input name="q" id="q" size="4" maxlength="5"/>').appendTo("thead tr:nth-child(2) td:first").focus();
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
                 $('<input name="q" id="q" size="4" maxlength="5" />').appendTo("thead tr:nth-child(2) td:first").focus();
                 $("#list li").removeClass("active").eq(id).addClass("active");
                 ///$(".reporttable").width($(".reporttable").width());
                 $( "#reporttable" + id ).floatHeader();
             });

         });

         $("#q").live('keyup', function(e){
              $this = $(this);
              search_text = $this.val();

              if ( search_text.length == 0 ) {
                  $(".match").removeClass("match");
                  $("tbody tr").stop(true, true).fadeTo(400,1);

              }

              if ( search_text.length  >= 3 && search_text.length  <= 5 ) {
                  // stop previous events
                  //e.stopImmediatePropagation();

                  $("tr, td").removeClass("match");
                  $("tbody tr").stop(true, true).fadeIn();
                  
                  var re = new RegExp("^"+search_text,"i");

                  $("tbody td.details_shifts").filter(function() {
                     return $(this).text().match(re);
                   }).addClass("match").parent().addClass("match");

                  //$("tbody tr:not(.match)").stop(true, true).fadeTo(500,0.3);
              }

         });

      });
  </script>
</head>
<body>
    <div id="container">
    <div id="header">
        <h1>DBRL Public Services Schedule</h1>
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
    </div>
    <div id="main" role="main">
        <?php echo file_get_contents('/home/www/intranet.dbrl.org/www/app/peoplewhat/schedule0.html'); ?>
    </div>
    <div id="footer">
        Powered by <em>Schedule Magic</em>&trade;
    </div>
  </div>
</body>
</html>
