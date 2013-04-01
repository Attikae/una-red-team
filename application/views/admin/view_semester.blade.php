<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />

  <title>UNA CLASS SCHEDULER</title>

  {{ HTML::script("js/jquery.js") }}
  {{ HTML::style("css/view_semester.css") }}
  {{ HTML::script("js/view_semester.js") }}
</head>

<body>
  <div id="logo_div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo_img"));
    ?>
  </div>

  <div id="main_div">


    <?php
      // Can take the following out. Just playing around with passing data. 
      echo "Currently viewing " . $semester 
    ?>
  </div>
</body>

</html>
