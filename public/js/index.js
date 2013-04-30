$(document).ready(function(){

  $("#sched_btn").on("click", function(){
    $("#schedule-outer-container").show();
  });

  $("#close-outer-container").on("click", function(){
    $("#schedule-outer-container").hide();
  })

  $("#view-schedule").on("click", ajaxDisplayPublishedSchedule);


});


function ajaxDisplayPublishedSchedule()
{

  var select = $("#published-schedule-select");
  var scheduleId = select.val();


  if(scheduleId == 0)
  {
    return false;
  }

  $.ajax({
    url: "display_published_output",
    dataType: "json",
    type: "POST",
    data: {
        schedule_id : scheduleId
    },
    success: function(data) {

      $("#schedule-container").html(data.html);

      $("#active-schedule-label").text("Viewing: " + 
                                 select.find(":selected").text());
      
    }
  });

}
