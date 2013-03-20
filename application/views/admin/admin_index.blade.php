<!DOCTYPE html>

<!--
PHILLIP C CLARK
LOGIN.HTML
-->

<html lang='en'>
	<head>
		<meta charset="UTF-8" />
		<title>UNA CLASS SCHEDULER</title>

		{{ HTML::style('css/admin.css') }}
		{{ HTML::style('css/ui.css') }}
		{{ HTML::script('js/jquery.js') }}
		{{ HTML::script('js/admin.js') }}

	</head>
	<body>
		
		<div id="logo_div">
			<?php echo HTML::image('img/LOGOBIG.jpg', 'UNA', array('id' => 'logo_img') ); ?> 
		</div>
		
		<div id="main_div">
			<div id="menu_div">
				<div id="schedules_btn" class="btn menu-btn">
					<div class="btn-text menu-text">
						schedules
					</div>
				</div>
				<div id="faculty_btn" class="btn menu-btn">
					<div class="btn-text menu-text">
						faculty
					</div>
				</div>
			</div>
			
			<div id="content_div">
				
				<div id="schedules_div">
					
					<div id="add_btn" class="btn btn-text">
						+ add semester
					</div>
					
					<div id="add_window_html">
						<select class="add_select">
						  <option>Spring</option>
						  <option>Summer</option>
						  <option>Fall</option>
						</select>
						<select class="add_select">
						  <option>2013</option>
						  <option>2012</option>
						  <option>2011</option>
						</select>
						<div id="add_submit" class="btn">
							<div class="btn-text">
								submit
							</div>
						</div>
						<div id="add_cancel" class="btn">
							<div class="btn-text">
								cancel
							</div>
						</div>
					</div>
					
					
					<div id="semester_list">
						<li id="s2013-btn" class="btn semester-btn">
							<div class="btn-text semester-text">
								Spring 2013
							</div>
						</li>
						<div id="s2013-list" class="sched-versions">
							<a class="versions">s2013_ver1</a><br>
							<a class="versions">s2013_ver2</a><br>
							<a class="versions">s2013_ver3</a><br>
						</div>
						
						<li id="f2012-btn" class="btn semester-btn">
							<div class="btn-text semester-text">
								Fall 2012
							</div>
						</li>
						<div id="f2012-list" class="sched-versions">
							<a class="versions">f2012_ver1</a><br>
							<a class="versions">f2012_ver2</a><br>
							<a class="versions">f2012_ver3</a><br>
						</div>
						
					</div>
				</div>
				
				<div id="faculty_div">
					999999999999999999999999
				</div>
				
			</div>
		</div>
	</body>
</html>
