<?php

define('TESTING',false);

require_once 'config.php';

$dates = array();

// override to fetch 12 days worth of schedules for manager review
$num_schedules = ( isset($_GET['extra']) && $_GET['extra'] = 1 ) ? 12 : NUM_SCHEDULES;

for ( $i = 0; $i < $num_schedules; $i++ ):
    $dates[] = date('m/d/Y', strtotime($i.' days'));
endfor;

//var_dump ($dates);

$crap_to_delete = array(
  "/<span class='details_date'>.+<br \/><\/span>\n/",
  "/<span class='details_time'>.+<br \/><\/span>\n/",
  "/<span class='details_orgcode'>.+<\/span>\n/",
  "/<span class='details_bull'> \* <\/span>\n/",
  "/<span class='details_orgs'> <br \/><\/span>\n/",
  "/<span class='details_orgcode'><br \/>PS<\/span>\n/",
  "/<span class='details_role'>.+<br \/><\/span>\n/",
  "/<span class='details_orgname'>.+<\/span>\n*/",
  "/<span class='details_staffname'>\*No Staff Scheduled\*<br \/><\/span>/",
  "/\n<span class='details_unscheduled'>\n<span class='details_staffname'>\*No Staff Scheduled\*<br \/><\/span><\/span>/",
  "/<span class='details_shifts'>\n/",
  "/:00/",
  "/ACES . /"
);

/**
 * @param $date
 * @return string
 */
function get_schedule( $date, $dept_id ){

    static $cookie_name, $session_id;

    // Parse cookie file to find session ID; save it for later
    if (!isset($session_id)) {
        $cookie = file_get_contents(COOKIEFILE);
        $bits = explode("\t",$cookie);
        $cookie_name = rtrim($bits[5]);
        $session_id = rtrim($bits[6]);
    }

    $url = SCHEDULE_FETCH_URL . "&orgid={$dept_id}&startdate={$date}&enddate={$date}";

    if (TESTING) {
        var_dump ($url);
    }

    $opts = array(
      'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
                  "Cookie: {$cookie_name}={$session_id}; path=/; domain=schedule.dbrl.org\r\n"
      )
    );

    $context = stream_context_create($opts);

    // Open the file using the HTTP headers set above
    return file_get_contents($url, false, $context);
}


/**
 * @param $html
 * @param $i
 * @return mixed|string
 */
function extract_table_html ( &$html, $i) {
    global $crap_to_delete, $librarians;

    // extract the main schedule table
    $start = strpos($html, '<table id="reporttable"');
    $stop = strpos($html, '<div style="display:none; border:#000 solid 1px;" id="pleasewait">');
    $table = substr($html,$start, $stop-$start);


    $table = str_replace('<span',"\n<span",$table);
    $table = str_replace('<tr',"\n\n<tr",$table);
    $table = str_replace('<td',"\n<td",$table);


    ///write_table( SCHEDULES_PATH . $dept_id .'__'.$i.'.html', $table);

    $table = preg_replace($crap_to_delete,'',$table);

    // more cleanup
    $table = str_replace('</span></span>','</span>',$table);
    // "What is an I-Project?" haha!
    $table = str_replace('I-Project','Project',$table);

    // change LastName, FirstName to FirstName L(ast initial)
    $table = preg_replace("/\n<span class='details_staffname'>(\w+[ -\w]*), (\w+)<\/span>/e","'$2 '.substr('$1',0,1)",$table);
    // normalize empty td cells
    $table = str_replace('></td>','>&nbsp;</td>', $table);

    // set appropriate <thead>
    $table = str_replace('<tbody>','<thead>', $table);
    $table = preg_replace("/PM<\/td>\s+<\/tr>/", "PM</td>\n</tr>\n</thead>\n\n<tbody>\n", $table);

    //give each table unique ID
    $table = str_replace('id="reporttable','id="reporttable'.$i, $table);

    // remove empty rows
    $table = preg_replace("/<span class='details_unscheduled'>\n<\/span>\n?/",'',$table);
    $table = preg_replace("/<tr>\n<td .+>.+\n<\/td>\n(<td .+>\n?&nbsp;<\/td>\n?)+<\/tr>/",'',$table);

    // mark the librarians
    foreach ( $librarians as $awesome ):
        $table = preg_replace("/<td colspan='4' class='details_shifts'>($awesome)<\/td>/","<td colspan='4' class='details_shifts librarian'>$1</td>",$table);
    endforeach;

    return $table;
}


/**
 * @param $file
 * @param $table
 * @return void
 */
function write_table ( $file, &$table ) {
    static $file_mode = 'w';

    if (!$fh = fopen($file, $file_mode)) {
         echo "Cannot open file ({$file})";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($fh, $table) === FALSE) {
        echo "Cannot write to file ({$file})";
        exit;
    }

    fclose($fh);
}


/**
 * @return void
 * @description Login to set the cookie
 */
function peoplewhere_login(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, LOGIN_URL . USERNAME . '&password=' . PASSWORD);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEFILE);
    //curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEFILE);

    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

}


?>
<html>
<body>

<h3>Fetching tables&hellip;</h3>
<?php

peoplewhere_login();

foreach ($departments as $dept_id => $dept):

    $i = 0;
    foreach ($dates as $day):
        echo "<p>{$dept} ({$dept_id}) - {$day}</p>\n";
        $html = get_schedule( $day, $dept_id );

        if (TESTING) { echo '<p>'.strlen($html)."</p>\n"; }

        //write_table( SCHEDULES_PATH . $dept_id .'__'.$i.'.html', $html);

        $table = extract_table_html($html, $i);

        write_table( SCHEDULES_PATH . $dept_id .'_'.$i++.'.html', $table);

    endforeach;

endforeach;
?>
<p>Done.</p>

</body>
</html>
