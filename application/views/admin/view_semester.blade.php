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
  <iframe id="file-submit-iframe" name='file-submit-iframe' style='display: none;'></iframe>.
  <div id="logo-div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo-img"));
    ?>
  </div>

  <div id="top-menu">
    <a id="home-lnk" href="admin_index">home</a>
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

      <div class="menu-btn" id="input-btn">
        <div class="option-txt">input files</div>
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
      <div id="new-version-btn">new version</div>


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
                   . "</span></td><td><a class='delete-version-lnk' href=''>delete</a></td></tr>";
            }
          }


        ?>
        <!-- <a class="version-lnk" href="">f2012_a</a>
        <a class="version-lnk" href="">f2012_b</a>
        <a class="version-lnk" href="">f2012_c</a> -->
      </table>

      <!---<div id="option-list">
        <a class='delete-version-lnk' href=''>delete</a>
        <a class='delete-version-lnk' href=''>delete</a>
        <a class='delete-version-lnk' href=''>delete</a>
      </div> -->
    </div>

    <!--////////////////////////////////////////////////////////////-->
    <div id="input-div" class="container">

      <!-- --------------------------------- -->
      <div id="class-time-btn" class="file-btn">
        class times
      </div>

      <div id="class-time-div" class="file-div">

        <textarea><?php echo $text['class_times'] ?></textarea>
        <form method='POST' action="{{ URL::to("admin/file_upload") }}" enctype='multipart/form-data' target='file-submit-iframe'>
          <input name="input-type" type="hidden" value="class-times"/>
          <input id="fileToUpload" type="file" size="45" name="fileToUpload" class="input"/>
          <button class="button upload">Upload</button>
          <button class="save-btn">Use</button>
        </form>
      </div>

      <!-- --------------------------------- -->
      <div id="room-btn" class="file-btn">
        available rooms
      </div>

      <div id="room-div" class="file-div">
        <textarea><?php echo $text['available_rooms'] ?></textarea>
        <form method='POST' action="{{ URL::to("admin/file_upload") }}" enctype='multipart/form-data' target='file-submit-iframe'>
          <input name="input-type" type="hidden" value="available-rooms"/>
          <input id="fileToUpload" type="file" size="45" name="fileToUpload" class="input"/>
          <button class="button upload">Upload</button>
          <button class="save-btn">Use</button>
        </form>
      </div>

      <!-- --------------------------------- -->
      <div id="course-btn" class="file-btn">
        courses to schedule
      </div>

      <div id="course-div" class="file-div">
        <textarea><?php echo $text['courses_to_schedule'] ?></textarea>
        <form method='POST' action="{{ URL::to("admin/file_upload") }}" enctype='multipart/form-data' target='file-submit-iframe'>
          <input name="input-type" type="hidden" value="courses-to-schedule"/>
          <input id="fileToUpload" type="file" size="45" name="fileToUpload" class="input"/>
          <button class="button upload">Upload</button>
          <button class="save-btn">Use</button>
        </form>
      </div>

      <!-- --------------------------------- -->
      <div id="conflict-btn" class="file-btn">
        conflict times
      </div>

      <div id="conflict-div" class="file-div">
        
        <textarea><?php echo $text['conflict_times'] ?></textarea>
        <form method='POST' action="{{ URL::to("admin/file_upload") }}" enctype='multipart/form-data' target='file-submit-iframe'>
          <input name="input-type" type="hidden" value="conflict-times"/>
          <input id="fileToUpload" type="file" size="45" name="fileToUpload" class="input"/>
          <button class="button upload">Upload</button>
          <button class="save-btn">Use</button>
        </form>
      </div>

      <!-- --------------------------------- -->
      <div id="prereq-btn" class="file-btn">
        prerequisites
      </div>

      <div id="prereq-div" class="file-div">
        <textarea><?php echo $text['prerequisites'] ?></textarea>
        <form method='POST' action="{{ URL::to("admin/file_upload") }}" enctype='multipart/form-data' target='file-submit-iframe'>
          <input name="input-type" type="hidden" value="prerequisites"/>
          <input id="fileToUpload" type="file" size="45" name="fileToUpload" class="input"/>
          <button class="button upload">Upload</button>
          <button class="save-btn">Use</button>
        </form>
      </div>

      <!-- --------------------------------- -->
      <div id="faculty-btn" class="file-btn">
        faculty members
      </div>

      <div id="faculty-div" class="file-div">
        <textarea><?php echo $text['faculty_members'] ?></textarea>
        <form method='POST' action="{{ URL::to("admin/file_upload") }}" enctype='multipart/form-data' target='file-submit-iframe'>
          <input name="input-type" type="hidden" value="faculty-members"/>
          <input id="fileToUpload" type="file" size="45" name="fileToUpload" class="input"/>
          <button class="button upload">Upload</button>
          <button class="save-btn">Use</button>
        </form>
      </div>

    </div>

    <!--////////////////////////////////////////////////////////////-->
    <div id="pref-div" class="container">

    <h1 id="pref-title">course preferences</h1>

    <div id="pref-version-div">
      <form id="pref-version-form" action="POST">
        Choose schedule version:
        <select id="pref-version-select" name="">
          <option>
            blablabla
          </option>
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
