<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>DBRL Schedule</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="print.css" media="print" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.js"></script>
  <script src="jquery.floatheader.min.js"></script>

  <script>

      $(function() {

         // auto highlight current hour when on today's schedule
         var update_date = function(){
             //console.log('!');
             var date = new Date();
             $("#reporttable0 thead td:nth-child("+(date.getHours()-5)+")").addClass("highlight");
             return setTimeout(update_date, 5*60*1000)
         }
         var timeout = update_date();


         // add search box
         var add_search = function() {
             $('<input name="q" id="q" size="4" maxlength="5"/>').appendTo("thead tr:nth-child(2) td:first").focus();
             //$("thead tr").eq(1).find("td").eq(0).html('<input name="q" id="q" size="6" />').focus();
         }
         add_search();

         var highlight_desks = function(){
            var current = "";
            var rowClass = "even";
            $("tbody tr").find("td:first").each(function() {
              $this = $(this);
              if ( $this.text() != current ) {
                rowClass = ( rowClass == "even" ) ? "" : "even";
                $this.parent().addClass(rowClass);
                current = $this.text();
              } else {
                $this.parent().addClass(rowClass);
                $this.text("");
              }
            });
         }
         highlight_desks();

         $.ajaxSetup ({
    		cache: false
	      });

         // lock the container height
         $("#schedule").height($("#schedule").height());

         var $floatHeader = $("#reporttable0").floatHeader();

         $("#list a").click(function(e) {
             e.preventDefault();
             $that = $(this);
             var id = $that.attr("rel");

             $("#schedule").fadeOut(200, function() {

                 $.ajax({
                  url: 'schedule' + id + '.html',
                  success: function(data) {
                     $("#schedule").html(data);
                  },
                  complete: function(data) {

                     $("#list li").removeClass("active").eq(id).addClass("active");
                     $("#schedule").fadeIn(300);
                     add_search();
                     highlight_desks();
                     $( "#reporttable" + id ).floatHeader();

                     if ( id == 0 ) {
                        timeout = update_date();
                     } else {
                        clearTimeout(timeout);
                     }

                  },
                  dataType: "html"
                });

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
          <li class="active"><a href="#" rel="0">Today</a></li>
          <?php
            for ($i = 1; $i < 10 ; $i++ ):
                echo '<li><a href="#'.$i.'" rel="'.$i.'">' . date('l', strtotime($i.' days')) . '</a></li>';
            endfor;
           ?>
        </ul>
        </div>
    </div>
    <div id="main" role="main">
       <div id="schedule">
        <?php echo file_get_contents('/home/www/intranet.dbrl.org/www/app/peoplewhat/schedule0.html'); ?>
       </div>
    </div>
    <div id="footer" style="display: none;">
        Powered by <em>Schedule Magic!</em>&trade;
    </div>
  </div>
</body>
</html>
