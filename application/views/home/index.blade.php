<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">

  <title>UNA CLASS SCHEDULER</title>

  {{ HTML::script("js/jquery.js") }}
  {{ HTML::style("css/index.css") }}
  {{ HTML::script("js/index.js") }}
</head>

<body>
  <div id="logo_div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo_img") );
    ?>
  </div>

  <div id="main_div">
    <button id="sched_btn" type="submit">
      view schedule
    </button>

    <?php echo HTML::link( "home/login",
                           "login",
                           array("id" => "login_lnk"));
    ?>
  </div>
</body>

</html>
