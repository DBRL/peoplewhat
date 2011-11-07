<?php
/**
 * User: npauley
 * Date: 11/7/11
 * Time: 3:38 PM
 */
 
///home/www/intranet.dbrl.org/www/app/workbench/peoplewhat/

define('NUM_SCHEDULES',3);
define('USERNAME','npauley');
define('PASSWORD','npauley');

define('APP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR );
define('SCHEDULES_PATH', APP_PATH . 'schedules' . DIRECTORY_SEPARATOR);


define('COOKIEFILE', APP_PATH . 'cookies.txt');
define('LOGIN_URL','http://schedule.dbrl.org/login.asp?staffaction=signin&email=');
define('REPORT_URL','http://schedule.dbrl.org/reports/schedule.asp?selectedreporttype=2&reporttype=2&selectedstaffid=142&orgid=9&rotationorgid=0&rotationid=0&dispname=1&dispabsences=1&dispshifts=1');

define('BASE_SCHEDULE_URL','http://schedule.dbrl.org/reports/schedule.asp?selectedreporttype=2&reporttype=2&selectedstaffid=142&rotationorgid=0&rotationid=0&dispname=1&dispabsences=1&dispshifts=1');

// Organization IDs
//&orgid=9
$departments = array(
    3 => 'Callaway County Public Library',
    4 => 'Southern Boone County',
    5 => 'Outreach',
    7 => 'Circulation - Front Desk',
    8 => 'Circulation - Shelving',
    9 => 'Public Services',
    10 => 'Maintenance',
    11 => 'Regional Services'
);

$librarians = array( 'Angela S', 'Betsy C', 'Brandy S', 'Hilary A', 'Hollis S', 'Judy P', 'Kirk H',
    'Lauren W', 'Nina S', 'Patricia M', 'Sally A', 'Sarah H', 'Seth S', 'Svetlana G' );