<?php require_once "header.inc.php"; ?>
<script>

			if (typeof console == "undefined") { var console = { log: function() {} }; }

			var departments = <?php echo json_encode($departments); ?>;
			var today = new Date();
			var days_of_week = ["sunday","monday","tuesday","wednesday","thursday","friday","saturday"];
			var relative_days = {"monday":1, "tuesday":2, "wednesday":3,  "thursday":4, "friday":5, "saturday":6, "sunday":7};

			$(function() {
				// don't cache the schedules as they are updated each hour
				$.ajaxSetup ({
					cache: false
				});

				// cache the schedule notes
				var schedule_notes = {};
				$.ajax({
						url: "schedules/ps-notes.json",
						cache: false,
						dataType: 'json',
						success: function(data){
								 schedule_notes.current = data;
								 //console.log("notes: ",schedule_notes.current);
								 add_notes(0, days_of_week[today.getDay()]);
						}
				});
				$.ajax({
						url: "schedules/ps-notes-next.json",
						cache: false,
						dataType: 'json',
						success: function(data){
								 schedule_notes.next = data;
								 //console.log("notes: ",schedule_notes.next);
						}
				});

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
						 $('<input name="q" id="q" maxlength="15"/>').appendTo($cell).focus();
				};
				add_search();

				 // display schedule notes
				var add_notes = function(schedule_id, day){
                        var notes, this_that;
						// a little complex to describe
						if ( day == "today" ){
								day = days_of_week[today.getDay()];
						}

                        // do we need to display the current week or next?
						this_that = ( schedule_id >= relative_days[day] ) ? "next" : "current";

                        if ( schedule_notes[this_that] ) {
                            notes = schedule_notes[this_that][day];
                            $("#weekend").text(notes['weekend']).show();
                            $("#vacations").text(notes['vacations']).show();
                            $("#changes").text(notes['changes']).show();
                        }
				};


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

						// divide out the notes section
						$(".notes:eq(0)").css("border-top","3px solid");
				};
				highlight_desks();




				$("#email").focus(function() { if ( $(this).val() == "Username" ) { $(this).val("");  } });
				$("#email").blur(function() { if ( !$(this).val() ) { $(this).val("Username");  } });

				// Select a department
				$("#department").change(function() {
						var dept_id = $(this).val();
						//console.log('dept_id: ' + dept_id);
						update_schedule(dept_id, 0);
				});

				 // Initializations
				$("thead tr:nth-child(1) td:first").append(" &bull; " + departments["9"]);
				var $floatHeader = $("#reporttable0").floatHeader();

				// Fetch and display schedule
				var update_schedule = function( dept_id, schedule_id, day ) {

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
										 // if PS
										 if ( dept_id == 9 ) {
											 add_notes(schedule_id, day);
										 }
										 highlight_desks();
										 $("thead tr:nth-child(1) td:first").append(" &bull; " + departments[dept_id]);
										 $( "#reporttable" + schedule_id ).floatHeader();

										 if ( schedule_id == 0 ) {
												timeout = update_time();
										 } else {
												clearTimeout(timeout);
										 }

									},
									dataType: "html"
								});

						});

				 }; // update schedule

				 // Change schedule
				 $("#list a").click(function(e) {
						 e.preventDefault();
						 $that = $(this);
						 var schedule_id = $that.attr("rel");

						 update_schedule( $("#department").val(), schedule_id, $that.text().toLowerCase() );

				 });

				 // Live search
				 $("#q").live('keyup', function(e){
							$this = $(this);
							search_text = $this.val();

							if ( search_text.length == 0 ) {
									$(".match").removeClass("match");
									$("tbody tr").stop(true, true).fadeTo(400,1);
							}

							if ( search_text.length  >= 3 && search_text.length  <= 15 ) {

									$("tr, td").removeClass("match");
									
									//var re = new RegExp("^"+search_text,"i");
									var re = new RegExp(search_text,"ig");

									$("tbody td.details_shifts, .notes td").filter(function() {
										 return $(this).text().match(re);
									 }).addClass("match");

									if ( $(".match").length > 0 ) {
											$this.addClass("match");
									}

							}

				 });

			});
	</script>
</head>
<body>
<div id="container">
	<div id="topnav"> <a href="/">Intranet</a> &nbsp;|&nbsp; <a href="http://www.dbrl.org/" target="_blank">DBRL.org</a>
		<div id="dept-selector">
			<select name="department" id="department">
				<?php
						foreach ( $departments as $dept_id => $dept ):
								echo "<option value='{$dept_id}'>{$dept}</option>";
						endforeach;
				?>
			</select>
		</div>
		<form action="http://schedule.dbrl.org/login.asp" method="post" name="login" id="login" target="_blank">
			<a href="http://schedule.dbrl.org/" target="_blank">PeopleWhere</a>&trade; Sign-in &nbsp;
			<input type="hidden" value="signin" name="staffaction">
			<input type="text" name="email" value="Username" size="8" id="email">
			<input type="password" name="password" size="8">
			<input type="submit" value="Go">
		</form>
	</div>
</div>
<div id="header">
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
	<div id="schedule"> <?php echo file_get_contents( SCHEDULES_PATH . '9_0.html'); ?> </div>
</div>
<div id="footer"> Powered by <em>Schedule Magic!</em>&trade; </div>
</div>
</body>
</html>