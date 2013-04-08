<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">

  <title>UNA CLASS SCHEDULER</title>

  {{ HTML::script("js/jquery.js") }}
  {{ HTML::style("css/login.css") }}
  {{ HTML::style("js/login.js" ) }}
</head>

<body>
  <div id="logo_div">
    <a href="index">
      <?php echo HTML::image( "img/LOGOBIG.jpg",
                              "UNA",
                              array("id" => "logo_img") );
      ?>
    </a>
  </div>

  <div id="login_div">

    <form method="POST" action="{{ URL::to("home/login") }}" id="login_form">
      <?php
        if(isset($message))
        {
          echo "<p>" . $message['error'] . "</p>";
          echo 'username: <input id="username" name="username" type="text"
                value="' . $message['username'] . '"><br>';
        }
        else
        {
          echo 'username: <input id="username" name="username" type="text"><br>';
        }
      ?>

      password: <input id="password" name="password" type="password"><br>

      <button id="submit" type="submit">submit</button>
    </form>
  </div>
</body>

</html>
