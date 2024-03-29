<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />

  <title>UNA CLASS SCHEDULER</title>

  {{ HTML::script("js/jquery.js") }}
  {{ HTML::style("css/view_faculty.css") }}
  {{ HTML::script("js/view_faculty.js") }}
</head>

<body>
  <div id="logo-div">
    <?php echo HTML::image( "img/LOGOBIG.jpg",
                            "UNA",
                            array("id" => "logo-img"));
    ?>
  </div>


  <div id="table-header">
    <a href="admin_index" class='link-class 'id="back-lnk">back</a>
    <p id="table-title">faculty accounts</p>
  </div>

  <table id="faculty-table">

    <?php

      if(isset($users))
      {
        foreach ($users as $user) {
          echo  "<tr id='" . $user->id . "'> " .
                  "<td>" . $user->email . "</td>" .
                  "<td><span class='unlock link-class'>unlock</span></td>" .
                  "<td><span class='delete link-class'>delete</span></td>" .
                "</tr>";
        }
      }
      else if(isset($message))
      {
        echo "<tr><td>" . $mesage . "</td></tr>";
      }

    ?>

    <!-- <tr>
      <th>username</th>
      <th>password</th>
      <th colspan="2">actions</th>
    </tr>
    
    <tr class="entry">
      <td>assalinas@una.edu</td>
      <td>aEZfvQPu!</td>
    </tr>
    
    <tr class="entry">
      <td>dmthomas@una.edu</td>
      <td>Hbm76R.dd</td>
    </tr>

    <tr class="entry">
      <td>rvbrown@una.edu</td>
      <td>buan,AQ39</td>
    </tr> -->

  </table>
</body>

</html>

