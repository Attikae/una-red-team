<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />

  <title>UNA CLASS SCHEDULER</title>

  {{ HTML::script("js/jquery.js") }}
  {{ HTML::style("css/add_semester.css") }}
  {{ HTML::script("js/add_semester.js") }}
</head>

<body>
  <div id="logo_div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo_img"));
    ?>
  </div>

  <div id="main_div">
    <p>add semester</p>
    <form method="post" action="">

      <select id="select-season">
        <option>Spring</option>
        <option>Summer</option>
        <option>Fall</option>
      </select>

      <select id="select-year">
        <!-- FILLED BY JAVASCRIPT -->
      </select>

      <button>submit</button>
      <br>
      <button>cancel</button>

    </form>
  </div>
</body>

</html>
