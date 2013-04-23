<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />

  <title>UNA CLASS SCHEDULER</title>

  {{ HTML::script("js/jquery.js") }}
  {{ HTML::style("css/faculty_view_semester.css") }}
  {{ HTML::script("js/faculty_view_semester.js") }}
</head>

<body>
  <iframe id="file-submit-iframe" name='file-submit-iframe' style='display: none;'></iframe>.
  <div id="logo-div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo-img"));
    ?>
  </div>

  <div id="top-menu">
    <a id="home-lnk" href="faculty_index">home</a>
    <br>
    
  </div>

  <div id="semester-title">
    <?php
      // Can take the following out. Just playing around with passing data. 
      echo "Currently viewing " . Session::get('semester'); 
    ?>
  </div>

  <?php echo '<input id="faculty_schedule_id" type="hidden" value="' . Session::get('faculty_schedule_id') . '"></input>'; ?> 

  <div id="main-div">

    <div id="menu-div">
      <div class="menu-btn" id="versions-btn">
        <div class="option-txt">schedule versions</div>
      </div>

      <div class="menu-btn" id="pref-btn">
        <div class="option-txt">preferences</div>
      </div>
    </div>

    <!--////////////////////////////////////////////////////////////-->
    <div id="welcome-div" class="container">
      <div id="welcome-txt">
        Welcome to the semester control panel.
      </div>
    </div>

    <!--////////////////////////////////////////////////////////////-->
    <div id="versions-div" class="container">

      <table id="versions-list">

        <?php

          if(empty($versions))
          {
            echo "<tr><th>No versions currently exist.</th></tr>";
          }
          else
          {
            foreach($versions as $version)
            {
              echo "<tr><td><span id='". $version->id ."' class='version-lnk'>"
                   . $version->name
                   . "</span></td></tr>";
            }
          }

        ?>
      </table>
    </div>
    <!--////////////////////////////////////////////////////////////-->
    <div id="pref-div" class="container">

      <h1 id="pref-title">course preferences</h1>

      <table id="course-prefs-table">
        <thead>
          <tr>
            <th>Course</th>
            <th>Morning</th>
            <th>Midday</th>
            <th>Late Aft.</th>
            <th># Day Sect.</th>
            <th># Night Sect.</th>
            <th># Internet Sect.</th>
          </tr>
        </thead>
        <tbody>

        <?php 

        if ($courses != "")
        {
          foreach ($courses as $course)
          {
            echo "<tr id='" . $course->id . "'>";
            echo "<td>" . $course->course . "</td>";
            echo "<td><input class='morning' type='checkbox'></input></td>";
            echo "<td><input class='midday' type='checkbox'></input></td>";
            echo "<td><input class= 'late-aft' type='checkbox'></input></td>";
            echo "<td><select class='day-sections'>";
            for($i = 0; $i <= $course->day_sections; $i++)
            {
              echo "<option>" . $i . "</option>";
            }
            echo "</select></td>";
            echo "<td><select class='night-sections'>";
            for($i = 0; $i <= $course->night_sections; $i++)
            {
              echo "<option>" . $i . "</option>";
            }
            echo "</select></td>";
            echo "<td><select class='internet-sections'>";
            for($i = 0; $i <= $course->internet_sections; $i++)
            {
              echo "<option>" . $i . "</option>";
            }
            echo "</select></td>";
            echo "</tr>"; 
          }
        }
        else{
          echo "<h2>Administrator has not enabled course preference" .
               "submissions for this semester</h2>";
        }
        
        ?>
        </tbody>
      </table>


      <div id="pref-btn-div">
        <button id="pref-submit-btn">submit</button>
        <button id="pref-reset-btn">reset</button>
      </div>
    </div>
  </div>

    
</body>

</html>

