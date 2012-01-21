<?php
require_once '../FbStats.php';
include_once 'config.php';

if (!session_id())
    session_start();

$fb = new FbStats($config);

//get Groups 
try {
    $groups = $fb->getGroups();
} catch (Exception $e) {
    echo '<div class="alert-message error">'.$e->getMessage().'</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Facebook Groups Analytics</title>
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Le styles -->
        <link href="bootstrap.min.css" rel="stylesheet">
        <style type="text/css">
            /* Override some defaults */
            html, body {
                background-color: #eee;
            }
            body {
                padding-top: 40px; /* 40px to make the container go all the way to the bottom of the topbar */
            }
            .container > footer p {
                text-align: center; /* center align it with the container */
            }
            .container {
                width: 820px; /* downsize our container to make the content feel a bit tighter and more cohesive. NOTE: this removes two full columns from the grid, meaning you only go to 14 columns and not 16. */
            }

            /* The white background content wrapper */
            .container > .content {
                background-color: #fff;
                padding: 20px;
                margin: 0 -20px; /* negative indent the amount of the padding to maintain the grid system */
                -webkit-border-radius: 0 0 6px 6px;
                -moz-border-radius: 0 0 6px 6px;
                border-radius: 0 0 6px 6px;
                -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
            }

            /* Page header tweaks */
            .page-header {
                background-color: #f5f5f5;
                padding: 20px 20px 10px;
                margin: -20px -20px 20px;
            }

            /* Give a quick and non-cross-browser friendly divider */
            .content .span4 {
                margin-left: 0;
                padding-left: 19px;
                border-left: 1px solid #eee;
            }

            .topbar .btn {
                border: 0;
            }
			
			label,input[type="text"],select,textarea{
				clear:both;
				display:block;
				font-weight:bold;
				text-align: left;
				width:350px;
			}
			
			.ml20{
				margin-left:20px;
			}
			
			.success{ 
				font-weight:bold;
				font-size:18px;
				color:#057135;
			}
			
			.error{ 
				color:#960B0B;
			}
        </style>
		<script src="jquery-1.7.1.min.js"></script>
		<script src="stats.js"></script>
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
							<label>Select Group</label>
							<select id="group">
								<option value="">Select Group</option>
								<?php 
									foreach( $groups['data'] as $key=>$val){
										echo '<option value="'.$val['id'].'">'.$val['name'].'</option>';
									}
								?>
							</select>
							<br/><label>Stats</label>
							<select id="stats">
								<option value="">Select</option>
								<option value="totalStatus">Top Posters</option>
								<option value="gotLikes">Most Liked Posts</option>
								<option value="gotComments">Most Commented Posts</option>
								<option value="totalPictures">Top Pictures Uploader</option>
							</select>
							<br/><input type="submit" name="show_result" id="show_result" value="Show Result" />
							<!--&nbsp; &nbsp; <input type="submit" name="show_result_publish" id="show_result_publish" value="Show & Publish Result" />-->
							<br/><br/><div id="result"></div>
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