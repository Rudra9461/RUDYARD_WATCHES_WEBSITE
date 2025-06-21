// sign_in form //
$(document).ready(function(){
  $('#myform').on('sumbit_btn',function(){
    var fname =$('#fname').val();
    var lname =$('#lname').val();
    var contact =$('#contact').val();
    var email =$('#email').val();
    var password =$('#password').val();
    var address =$('#address').val();
    var pincode =$('#pin_code').val();

    if(sname ==''){
      $('#sname_error').text('Please enter first name ');
      return false;
    }else if (lname ==''){
      $('#roll_error').text('Please enter last name');
      return false;
    }else if (contact ==''){
      $('#contact_error').text('Please enter contact number');
      return false;
    }else if (email==''){
      $('#email_error').text('Please enter email');
      return false;
    }else if (password ==''){
      $('#password_error').text('Please enter password');
      return false;;
    }else if (address ==''){
      $('#address_error').text('Please enter address');
      return false;
    }else if (pincode ==''){
      $('#pin_code').text('Please enter pincode');
      return false;
    } else {
      $('#Callback').click(function() {
    $('#sumbit_btn').hide(5000, function() {
      alert('Thank your task is completed');
    });
      });
  };

