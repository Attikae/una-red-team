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

  <?php echo '<input id="schedule_id" type="hidden" value="' . Session::get('schedule_id') . '"></input>'; ?> 

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

      <div id="pref-version-div">
        <form id="pref-version-form" action="POST">
          Choose schedule version:
          <select id="pref-version-select">
            <option>blablabla</option>
          </select>
        </form>
      </div>

      <div class="course-div">
        <div class="course-txt">
          CS 155
        </div>

        <div class="checkbox-grp">
          <div class="checkbox-txt">
            morning
            <input type="checkbox"></input>
          </div>

          <div class="checkbox-txt">
            afternoon
            <input type="checkbox"></input>
          </div>

          <div class="checkbox-txt">
            evening
            <input type="checkbox"></input>
          </div>
        </div>
      </div>
    
      <div class="course-div">
        <div class="course-txt">
          CS 245
        </div>

        <div class="checkbox-grp">
          <div class="checkbox-txt">
            morning
            <input type="checkbox"></input>
          </div>

          <div class="checkbox-txt">
            afternoon
            <input type="checkbox"></input>
          </div>

          <div class="checkbox-txt">
            evening
            <input type="checkbox"></input>
          </div>
        </div>
      </div>

      <div class="course-div">
        <div class="course-txt">
          CS 255
        </div>

        <div class="checkbox-grp">
          <div class="checkbox-txt">
            morning
            <input type="checkbox"></input>
          </div>

          <div class="checkbox-txt">
            afternoon
            <input type="checkbox"></input>
          </div>

          <div class="checkbox-txt">
            evening
            <input type="checkbox"></input>
          </div>
        </div>
      </div>

      <div class="course-div">
        <div class="course-txt">
          CS 355
        </div>

        <div class="checkbox-grp">
          <div class="checkbox-txt">
	    morning
	    <input type="checkbox"></input>
          </div>

            
          <div class="checkbox-txt">
	    afternoon
            <input type="checkbox"></input>
          </div>
        

          <div class="checkbox-txt">
	    evening
            <input type="checkbox"></input>
          </div>
            
        </div>
      </div>

      <div id="pref-btn-div">
        <button id="pref-submit-btn">submit</button>
        <button id="pref-reset-btn">reset</button>
      </div>
    </div>
  </div>

    
</body>

</html>

