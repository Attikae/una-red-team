<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>UNA CLASS SCHEDULER</title>

{{ HTML::script("js/jquery.js") }}
{{ HTML::style("css/changepw.css") }}
{{ HTML::script("js/changepw.js") }}
</head>
<body>
  <div id="logo_div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo_img") );
    ?>
  </div>

  <div id="main-div">
    <form id="pw-form" 
          method="POST"
          action="{{ URL::to("home/login") }}">
      old pasword: <input id="old-pw" name="old-pw" type="text"><br>
      new password: <input id="new-pw" name="new-pw" type="text"><br>
      repeat new password: <input id="rpt-new-pw" name="rpt-new-pw" type="text">
      <button id="submit" type="submit">submit</button>
    </form>
    <button id="cancel">cancel</button>
  </div>
</body>
</html>
