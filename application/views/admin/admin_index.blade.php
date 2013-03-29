<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />

  <title>UNA CLASS SCHEDULER</title>

  {{ HTML::script("js/jquery.js") }}
  {{ HTML::style("css/admin_index.css") }}
  {{ HTML::script("js/admin_index.js") }}
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
          action="" 
          id="semester-form">

      <select id="semester-select">
        <option>
          select semester...
        </option>

        <option value="f2013">
          Fall 2013
        </option>

        <option value="su2013">
          Summer 2013
        </option>

        <option value="sp2013">
          Spring 2013
        </option>

        <option value="f2012">
          Fall 2012
        </option>

        <option value="new">
          <--new semester -->
        </option>
      </select>

      <button type="submit" id="submit">
        submit
      </button>

    </form>

    <div id="links">
      <?php echo HTML::link( "admin/view_faculty",
                             "view/edit faculty",
                             array("id" => "faculty-lnk"));
      ?>

      <br> <!-- LINE BREAK BETWEEN LINKS -->

      <?php echo HTML::link( "home/index",
                             "logout",
                             array("id" => "logout-lnk"));
      ?>
    </div>
  </div>
</body>

</html>
