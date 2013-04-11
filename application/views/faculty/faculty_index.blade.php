<!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>UNA CLASS SCHEDULER</title>

  {{ HTML::script("js/jquery.js") }}
  {{ HTML::style("css/faculty_index.css") }}
  {{ HTML::script("js/faculty_index.js") }}
</head>
<body>
  <div id="logo_div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo_img"));
    ?>
  </div>

  <div id="main_div">

    <form method="POST" 
          action="{{ URL::to("faculty/faculty_index") }}" 
          id="semester-form">

      <select id="semester-select" name="semester-select">
        <option value="default">
          select semester...
        </option>

        <?php
          foreach ($schedules as $schedule)
          {
            echo "<option value=" . $schedule->id . ">" . $schedule->name . " " . $schedule->year . "</option>";
          } 
        ?>

      </select>

      <button type="submit" id="submit">
        submit
      </button>

    </form>

    <div id="links">
      <?php echo HTML::link( "home/changepw",
                             "change password",
                             array("class" => "link"));
      ?>

      <br> <!-- LINE BREAK BETWEEN LINKS -->

      <?php echo HTML::link( "home/index",
                             "logout",
                             array("class" => "link"));
      ?>
    </div>
  </div>
 
</body>
</html>
