<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Laravel: A Framework For Web Artisans</title>
  <meta name="viewport" content="width=device-width">

  {{ HTML::script('js/jquery.js') }}
  {{ HTML::style('css/index.css') }}
  {{ HTML::script('js/index.js') }}
</head>

<body>
  <div id="logo_div">
    <?php echo HTML::image( 'img/LOGOBIG.jpg', 
			    'UNA', 
			    array('id' => 'logo_img') ); 
    ?> 
  </div>

  <div id="main_div">
    <button id="sched_btn" type="submit">
      view schedule
    </button>

    <?php echo HTML::link('home/login', 'faculty login', array('id' => 'login_lnk')); ?>
  </div>
</body>

</html>
