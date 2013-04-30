$(document).ready(function(){
  
  appendResetDelete();
  setTableHeader();
});


/**
@method 
@param 
@author
*/
function appendResetDelete(){
  
  // Append the reset password and delete options to every
  // row in the user account table

  reset_password = "<td><a href=''>reset password</a></td>";
  delete_user = "<td><a href=''>delete</a></td>";
  $(".entry").append( reset_password + delete_user );
};


/**
@method 
@param 
@author
*/
function setTableHeader(){
  
  // Dynamically sets the table header size
  // based on the table's width

  $("#table-header").width( $("#faculty-table").width() );
};
