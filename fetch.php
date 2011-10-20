<?php
define('LOGIN',false);
define('NUM_SCHEDULES',1);
define('USERNAME','switchboard');
define('PASSWORD','switchboard');
define('SCHEDULES_PATH','/home/www/intranet.dbrl.org/www/app/peoplewhat/');
//define('SCHEDULES_PATH','/home/www/intranet.dbrl.org/www/app/workbench/peoplewhat/');
define('COOKIEFILE','/home/www/intranet.dbrl.org/www/app/peoplewhat/cookies.txt');
define('REPORT_URL','http://schedule.dbrl.org/reports/schedule.asp?selectedreporttype=2&reporttype=2&selectedstaffid=142&orgid=9&rotationorgid=0&rotationid=0&dispname=1&dispabsences=1&dispshifts=1');

$librarians = array(
    'Angela S',
    'Betsy C',
    'Brandy S',
    'Hilary A',
    'Hollis S',
    'Judy P',
    'Kirk H',
    'Lauren W',
    'Nina S',
    'Patricia M',
    'Sally A',
    'Sarah H',
    'Seth S',
    'Svetlana G'
);

$dates = array();

for ( $i = 0; $i < NUM_SCHEDULES; $i++ ):
    $dates[] = date('n/j/Y', strtotime($i.' days'));
endfor;

//var_dump ($dates);
//exit;

$crap_to_delete = array(
  "/<span class='details_date'>.+<br \/><\/span>\n/",
  "/<span class='details_time'>.+<br \/><\/span>\n/",
  "/<span class='details_orgcode'>PS<\/span>\n/",
  "/<span class='details_bull'> \* <\/span>\n/",
  "/<span class='details_orgname'>Public Services<\/span>\n/",
  "/<span class='details_orgs'> <br \/><\/span>\n/",
  "/<span class='details_orgcode'><br \/>PS<\/span>\n/",
  "/<span class='details_role'>.+<br \/><\/span>\n/",
  "/<span class='details_orgname'><br \/>Public Services<\/span>/",
  "/\n<span class='details_unscheduled'>\n<span class='details_staffname'>\*No Staff Scheduled\*<br \/><\/span><\/span>/",
  "/<span class='details_shifts'>\n/",
  "/:00/",
  "/ACES . /"
);


function get_schedule( $date ){
    // Parse cookie file to find session ID
    $cookie = file_get_contents(COOKIEFILE);
    $bits = explode("\t",$cookie);
    $cookie_name = rtrim($bits[5]);
    $session_id = rtrim($bits[6]);

    //echo '['.$session_id.']';

    // Fetch the report using session ID

    //$url = 'http://schedule.dbrl.org/reports/schedule.asp?selectedreporttype=2&reporttype=2&startdate=10%2F14%2F2011&enddate=10%2F11%2F2011&selectedstaffid=142&orgid=9&rotationorgid=0&rotationid=0&dispname=1&dispabsences=1&dispshifts=1';
    //$url = REPORT_URL . '&startdate=10%2F14%2F2011&enddate=10%2F11%2F2011';
    $url = REPORT_URL . "&startdate={$date}&enddate={$date}";

    //var_dump ($url);

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


function extract_table_html ( &$html, $i ) {
    global $crap_to_delete, $librarians;
   // extract the main schedule table
    $start = strpos($html, '<table id="reporttable"');
    $stop = strpos($html, '<div style="display:none; border:#000 solid 1px;" id="pleasewait">');
    $table = substr($html,$start, $stop-$start);


    $table = str_replace('<span',"\n<span",$table);
    $table = str_replace('<tr',"\n\n<tr",$table);
    $table = str_replace('<td',"\n<td",$table);

    $table = preg_replace($crap_to_delete,'',$table);

    // more cleanup
    $table = str_replace('</span></span>','</span>',$table);

    $table = preg_replace("/\n<span class='details_staffname'>(\w+), (\w+)<\/span>/e","'$2 '.substr('$1',0,1)",$table);
    $table = str_replace('></td>','>&nbsp;</td>', $table);

    // set appropriate <thead>
    $table = str_replace('<tbody>','<thead>', $table);
    $table = preg_replace("/PM<\/td>\s+<\/tr>/", "PM</td>\n</tr>\n</thead>\n\n<tbody>\n", $table);

    //give each table unique ID
    $table = str_replace('id="reporttable','id="reporttable'.$i, $table);

    // remove empty rows
    $table = preg_replace("/<tr>\n<td .+>.+\n<\/td>\n(<td .+>&nbsp;<\/td>\n?)+<\/tr>/",'',$table);

    // mark the librarians
    foreach ( $librarians as $awesome ):
        $table = preg_replace("/<td colspan='4' class='details_shifts'>($awesome)<\/td>/","<td colspan='4' class='details_shifts librarian'>$1</td>",$table);
    endforeach;

    return $table;
}

function write_table ( $file, &$table ) {
    static $file_mode = 'w';

    if (!$fh = fopen(SCHEDULES_PATH.$file, $file_mode)) {
         echo "Cannot open file ({$file})";
         exit;
    }

    // Write $somecontent to our opened file.
    if (fwrite($fh, $table) === FALSE) {
        echo "Cannot write to file ({$file})";
        exit;
    }

    fclose($fh);

    //$file_mode = 'a';
}

// Login to set the cookie

if ( LOGIN ):
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://schedule.dbrl.org/login.asp?staffaction=signin&email='.USERNAME.'&password='.PASSWORD);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIEFILE);
    //curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIEFILE);

    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
endif;






//$html = get_schedule( '10/14/2011' );
//$table = extract_table_html($html);
//write_table('schedule1.html', $table);

?>
<html>
<body>

<h3>Fetching tables&hellip;</h3>
<?php $i = 0;
foreach ($dates as $d):
    echo "<p>{$d}</p>";
    $html = get_schedule( $d );
    $table = extract_table_html($html, $i);
    write_table('schedule'.$i++.'.html', $table);
endforeach;
?>


</body>
</html>
