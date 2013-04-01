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
  <div id="logo-div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo-img"));
    ?>
  </div>

  <div id="main-div">

    <div id="menu-div">
      <div class="menu-btn" id="versions-btn">
        <div class="option-txt">schedule versions</div>
      </div>

      <div class="menu-btn" id="input-btn">
        <div class="option-txt">input files</div>
      </div>

      <div class="menu-btn" id="pref-btn">
        <div class="option-txt">preferences</div>
      </div>
    </div>

    <!--//////////////////////////////////////-->
    <div id="welcome-div" class="container">
      <div id="welcome-txt">
        Welcome to the administrator control panel.
      </div>
    </div>

    <!--//////////////////////////////////////-->
    <div id="versions-div" class="container">
    </div>

    <!--//////////////////////////////////////-->
    <div id="input-div" class="container">

      <!-- --------------------------------- -->
      <div id="class-time-btn" class="file-btn">
        class times
      </div>

      <div id="class-time-div" class="file-div">
        <textarea></textarea>
      </div>

      <!-- --------------------------------- -->
      <div id="room-btn" class="file-btn">
        available rooms
      </div>

      <div id="room-div" class="file-div">
        <textarea></textarea>
      </div>

      <!-- --------------------------------- -->
      <div id="course-btn" class="file-btn">
        courses to schedule
      </div>

      <div id="course-div" class="file-div">
        <textarea></textarea>
      </div>

      <!-- --------------------------------- -->
      <div id="conflict-btn" class="file-btn">
        conflict times
      </div>

      <div id="conflict-div" class="file-div">
        <textarea></textarea>
      </div>

      <!-- --------------------------------- -->
      <div id="prereq-btn" class="file-btn">
        prerequisites
      </div>

      <div id="prereq-div" class="file-div">
        <textarea></textarea>
      </div>

      <!-- --------------------------------- -->
      <div id="faculty-btn" class="file-btn">
        faculty members
      </div>

      <div id="faculty-div" class="file-div">
        <textarea></textarea>
      </div>

    </div>

    <!--//////////////////////////////////////-->
    <div id="pref-div" class="container">





    </div>

  </div>

    <?php
      // Can take the following out. Just playing around with passing data. 
      echo "Currently viewing " . $semester 
    ?>
</body>

</html>
