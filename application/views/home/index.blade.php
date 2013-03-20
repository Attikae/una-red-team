<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Laravel: A Framework For Web Artisans</title>
	<meta name="viewport" content="width=device-width">
	{{ HTML::style('css/index.css') }}
	{{ HTML::style('css/ui.css') }}
	{{ HTML::script('js/jquery.js') }}
	{{ HTML::script('js/index.js') }}

</head>
<body>
	<div id="logo_div">
			<?php echo HTML::image('img/LOGOBIG.jpg', 'UNA', array('id' => 'logo_img') ); ?> 
		</div>
		
		<div id="main_div">
			<button id="sched_btn" type="submit">
				view schedule
			</button>

			<?php echo HTML::link('home/login', 'Faculty Login', array('id' => 'login_lnk')); ?>

		</div>
		
	
	</div>
</body>
</html>
