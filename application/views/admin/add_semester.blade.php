
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
    <?php
      if(isset($message)){
        echo "<p>" . $message . "</p>";
      }
    ?>
    <p>add semester</p>
    <form method="post" action="{{ URL::to("admin/add_semester") }}">

      <select id="select-season" name="select-season">
        <option>Spring</option>
        <option>Summer</option>
        <option>Fall</option>
      </select>


      <select id="select-year" name="select-year">
        <!-- FILLED BY JAVASCRIPT -->
      </select>

      <button type="submit">submit</button>
      <br>
    </form>
    <a href="{{ URL::to("admin/admin_index") }}"><button>cancel</button></a>
  </div>
</body>

</html>
