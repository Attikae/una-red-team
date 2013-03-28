<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>UNA CLASS SCHEDULER</title>
  <meta name="viewport" content="width=device-width">

  {{ HTML::script('js/jquery.js') }}
  {{ HTML::style('css/login.css') }}
  {{ HTML::style('js/login.js' ) }}
</head>

<body>
  <div id="logo_div">
    <?php echo HTML::image( 'img/LOGOBIG.jpg',
			    'UNA',
			    array('id' => 'logo_img') ); 
    ?> 
  </div>

  <div id="login_div">
    <form method="POST" action="{{ URL::to('home/login') }}" id="login_form">
      username: <input type="text"><br>
      password: <input type="password"><br>

      <button class="login_btn" id="submit" type="submit">submit</button>
      <button class="login_btn" id="cancel">cancel</button>
    </form>
  </div>
</body>

</html>
