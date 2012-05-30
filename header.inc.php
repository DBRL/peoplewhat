<?php
/**
 * Created by JetBrains PhpStorm.
 * User: npauley
 * Date: 1/20/12
 * Time: 12:21 PM
 * To change this template use File | Settings | File Templates.
 */
define('VERSION','20120201');
require_once 'config.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>DBRL Schedule</title>
  <link rel="stylesheet" href="style.css?<?=VERSION?>" />
  <link rel="stylesheet" href="print.css?<?=VERSION?>" media="print" />
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
  <script type="text/javascript">
    if (typeof jQuery == 'undefined')
    {
        document.write("<script src='//files.dbrl.org/js/jquery/jquery-1.7.min.js'><"+"/script>");
    }
  </script>
  <script src="jquery.floatheader.min.js"></script>
  <script src="init.js?<?=VERSION?>"></script>
