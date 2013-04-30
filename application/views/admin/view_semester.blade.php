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
  <iframe id="file-submit-iframe" name='file-submit-iframe' style='display: none;'></iframe>
  
  <button id="fill-prefs">Fill Prefs</button>
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
                   . "</span></td><td><span class='delete-version-lnk'>delete</span></td></tr>";
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
  </div>

    <!--////////////////////////////////////////////////////////////-->
 

  <div id="schedule-output-container">
    <span id="active-container-label">Viewing: By Seniority</span>
    <div id="button-container">
      <div id="top-buttons-container">
        <button id="show-seniority">By Seniority</button>
        <button id="show-sumbission">By Order of Submission</button>
        <!-- <button id="hide-schedule-output">Hide Schedule</button> -->
      </div>
      </br>
      <div id="bottom-buttons-container">
        <button id="show-by-room">By Room</button>
        <button id="show-by-class-name">By Class Name</button>
        <button id="show-by-faculty">By Faculty</button>
        <button id="show-by-time">By Time</button>
        <button id="show-not-scheduled">Not Scheduled</button>
      </div>
    </div>
    <button id="publish-btn">Publish</button>
    <button id="hide-schedule-output">Close</button>
    <div id="schedule-inner-container">
      <div id="seniority-container" class="output-container">
        Seniority
      </div>
      <div id="submission-container" class="output-container">
        Sumbissions
      </div>
    </div>
  </div>

  <div id="schedule-container-overlay"></div>

  <div id="schedule-edit-container">
    <input id="edit-course-id" type="hidden"/>
    <input id="edit-output-version-id" type="hidden"/>
    <input id="edit-priority-flag" type="hidden"/>
    <input id="edit-course-duration" type="hidden"/>
    <input id="edit-class-size" type="hidden"/>
    <input id="edit-course-type" type="hidden"/>
    <span id="course-label">CS TEST</span>
    </br></br>
    <table id="schedule-edit-table">
        <td>
          <div id="start-hour-label">Start Hour</div>
        </td>
        <td>
          <select id="start-hour-select">
            <?php
              for ($i=7; $i < 19 ; $i++) { 
                if($i < 10)
                {
                  echo "<option val='" . $i . "'>0" . $i . "</option>";
                }
                else
                {
                  echo "<option val='" . $i . "'>" . $i . "</option>";
                } 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          <div id="start-minute-label">Start Minute</div>
        </td>
        <td>
          <select id="start-minute-select">
            <?php
              for ($i=0; $i < 60 ; $i++) {
                if($i < 10)
                {
                  echo "<option value='" . $i . "'>0" . $i . "</option>";
                }
                else
                {
                  echo "<option value='" . $i . "'>" . $i . "</option>";
                } 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>
          <div id="duration-label">Duration</div>
        </td>
        <td>
          <select id="duration-select">
            <?php
              for($i=5; $i < 301; $i+=5)
              {
                echo "<option value='" . $i . "'>" . $i . "</option>";
              }
            ?>
          </select>
        </td>
      </tr>
    </table>
    </br>
    <table "schedule-edit-days-table">
      <caption>Days</caption>
      <tr><td>M</td><td>T</td><td>W</td><td>R</td><td>F</td><td>S</td></tr>
      <tr>
        <td><input id="monday-checkbox" class="day-checkbox" type="checkbox"/></td>
        <td><input id="tuesday-checkbox" class="day-checkbox" type="checkbox"/></td>
        <td><input id="wednesday-checkbox" class="day-checkbox" type="checkbox"/></td>
        <td><input id="thursday-checkbox" class="day-checkbox" type="checkbox"/></td>
        <td><input id="friday-checkbox" class="day-checkbox" type="checkbox"/></td>
        <td><input id="saturday-checkbox" class="day-checkbox" type="checkbox"/></td>
      </tr>
    </table>
    </br>
    <table>
      <tr>
        <td>Faculty</td>
        <td>
          <select id="faculty-select">
          </select>
        </td>
      </tr>
      <tr>
        <td>Room</td>
        <td>
          <select id="room-select">
          </select>
        </td>
      </tr>
    </table>
    <button id="edit-submit">Save</button>
    <button id="edit-close">Cancel</button>
  </div>

    
</body>

</html>
