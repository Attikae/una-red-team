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

  <div id="schedule-outer-container">
    <span id="active-schedule-label"></span>
    <div id="button-container">
      <div id="schedule-select-container">
        <select id="published-schedule-select">
          <?php
            if(! empty($schedules))
            {
              echo "<option value='0'>Select schedule</option>";
              foreach ($schedules as $schedule) {
                echo "<option value='" . $schedule->id . "'>" .
                      $schedule->name . " " . $schedule->year . "</option>";
              }
            }
            else
            {
              echo "<option value='0'>No published schedules exist</option>";
            }
          ?>
        </select>
        <button id="view-schedule">View</button>
      </div>
    </div>
    <button id="close-outer-container">Close</button>
    <div id="schedule-inner-container">
      <div id="schedule-container" class="output-container">
      </div>
    </div>
  </div>


</body>

</html>
