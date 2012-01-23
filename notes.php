<?php
/**
 * User: npauley
 * Date: 1/20/12
 * Time: 12:08 PM
 */
require_once "header.inc.php";


?>
</head>
<body>
    <div id="container">
    <div class="entry">

    </div>
    <form action="" method="post" class="notes">
        <fieldset>
            <label for="date">Date</label>
            <input type="text" name="date" id="date" size="10" /><br />
            <label for="weekend">Off for Weekend</label>
            <textarea name="weekend" id="weekend" rows="2"></textarea><br />
            <label for="vacations">Vacations & Other</label>
            <textarea name="vacations" id="vacations" rows="2"></textarea><br />
            <label for="changes">Schedules Changes</label>
            <textarea name="weekend" id="changes" rows="2"></textarea><br />
            <input type="submit" name="submit" value="Submit" />
        </fieldset>
    </form>
    <div id="footer">
        Powered by <em>Schedule Magic!</em>&trade;
    </div>
  </div>
</body>
</html>