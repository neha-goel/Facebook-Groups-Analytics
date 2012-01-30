<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once '../../FbStats.php';
include_once '../config.php';
include_once 'helper.php';

if (!session_id())
    session_start();

$fb = new FbStats($config);

$feedParams = array(
 'sourceId' => $sourceId,
 'limit' => 200,
 'since' => 'last+Year',
);

//get Groups
try {
    $groups = $fb->getInfo('me/groups');
} catch (Exception $e) {
    echo '<div class="alert-message error">' . $e->getMessage() . '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Facebook Groups Analytics</title>
        <link href="../css/bootstrap.min.css" rel="stylesheet" />
        <link href="../css/custom.css" rel="stylesheet" />
		
		<script src="jquery-1.7.1.min.js"></script>
		<script src="jquery.ui.core.js"></script>
		<script src="jquery.ui.widget.js"></script>
		<script src="jquery.ui.datepicker.js"></script>
		<script src="stats.js"></script>
		
		<link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css">
    </head>

    <body>
        <div class="topbar">
            <div class="fill">
                <div class="container">
                    <a class="brand" href="#">Facebook Group Analytics</a>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="content">
                <div class="page-header">
                    <h1>Facebook Group Analytics Example</h1>
                </div>
                <div class="row">
                    <div class="span9">
                        <div class="row ml20">
                            <form id="frmStat" method="post">
                                <label>Select Group</label>
                                <select id="group" name="group">
                                    <option value=""> -- </option>
                                    <?php
                                    foreach ($groups['data'] as $key => $val) {
                                        echo '<option value="' . $val['id'] . '">' . $val['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <br/>

                                <label>Statistic</label>
                                <select id="stat" name="stat">
                                    <option value=""> -- </option>
                                    <?php
                                    foreach ($stats as $stat) {
                                        echo "<option value='{$stat}'>{$messages[$stat]}</option>";
                                    }
                                    ?>
                                </select>
                                
								<span id="more_options"></span><br/>
								
								<label>From</label>
                                <input type="text" name="from" id="from" value="<?php echo date('d M Y',strtotime('last '.date('l'))); ?>" />
                                <br/>
								
								<label>To</label>
                                <input type="text" name="to" id="to" value="<?php echo date('d M Y',strtotime('now')); ?>" />
                                <br/>
								
                                <label>No. of top users</label>
                                <input type="text" name="usersCount" id="usersCount" value="10" />
                                <br />

                                <input type="submit" name="show_result" id="show_result" value="Show Result" class="btn primary" />
                                &nbsp; &nbsp; <input type="submit" name="copy_result" id="copy_result" value="Result to copy & update" class="btn primary" />
                                <br/><br/>
                            </form>
                            <div id="result"></div>
                        </div>
                    </div>
                    <div class="span4">
                        <h3>Project Links</h3>
                        <ul>
                            <li><a href="https://github.com/rakeshtembhurne/Facebook-Groups-Analytics">Facebook Groups Analytics on Github</a></li>
                            <li><a href="https://www.facebook.com/groups/nagpurpug/">Nagpur PHP Users Group</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <footer>
                <p>&copy; Company 2011</p>
            </footer>
        </div> <!-- /container -->
    </body>
</html>