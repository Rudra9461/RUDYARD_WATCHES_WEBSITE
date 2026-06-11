// Rudyard Watches — style.js

$(document).ready(function () {

  // ── FORM VALIDATION ─────────────────────────────────────
  $('#myform').on('submit', function (e) {
    var isValid = true;

    $('.rw-error').text('');
    $('.rw-input').removeClass('is-invalid is-valid');

    var fname    = $('#fname').val().trim();
    var lname    = $('#lname').val().trim();
    var contact  = $('#contact').val().trim();
    var email    = $('#email').val().trim();
    var password = $('#password').val().trim();
    var address  = $('#address').val().trim();
    var pincode  = $('#pin_code').val().trim();

    if (fname === '') {
      showError('fname', 'First name is required');
      isValid = false;
    } else { markValid('fname'); }

    if (lname === '') {
      showError('lname', 'Last name is required');
      isValid = false;
    } else { markValid('lname'); }

    if (contact === '') {
      showError('contact', 'Contact number is required');
      isValid = false;
    } else if (!/^\d{10}$/.test(contact)) {
      showError('contact', 'Enter a valid 10-digit number');
      isValid = false;
    } else { markValid('contact'); }

    if (email === '') {
      showError('email', 'Email address is required');
      isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showError('email', 'Enter a valid email address');
      isValid = false;
    } else { markValid('email'); }

    if (password === '') {
      showError('password', 'Password is required');
      isValid = false;
    } else if (password.length < 6) {
      showError('password', 'Password must be at least 6 characters');
      isValid = false;
    } else { markValid('password'); }

    if (address === '') {
      showError('address', 'Address is required');
      isValid = false;
    } else { markValid('address'); }

    if (pincode === '') {
      showError('pin_code', 'PIN code is required');
      isValid = false;
    } else if (!/^\d{6}$/.test(pincode)) {
      showError('pin_code', 'Enter a valid 6-digit PIN code');
      isValid = false;
    } else { markValid('pin_code'); }

    if (!isValid) {
      // Stop form from submitting to submit.php if JS validation fails
      e.preventDefault();
    } else {
      // JS passed — show loading state, let form submit normally to submit.php
      $('#submit_btn').prop('disabled', true).text('Registering…');
    }
  });

  // Live validation — clear error as user types
  $('.rw-input').on('input', function () {
    var id = this.id;
    $('#' + id + '_error').text('');
    $(this).removeClass('is-invalid');
    if (id === 'password') {
      updateStrength($(this).val());
    }
  });

  // ── HELPERS ──────────────────────────────────────────────
  function showError(fieldId, msg) {
    $('#' + fieldId + '_error').text(msg);
    $('#' + fieldId).addClass('is-invalid').removeClass('is-valid');
  }

  function markValid(fieldId) {
    $('#' + fieldId).addClass('is-valid').removeClass('is-invalid');
  }

  function updateStrength(pass) {
    var strength = 0;
    if (pass.length >= 8)          strength++;
    if (/[A-Z]/.test(pass))        strength++;
    if (/[0-9]/.test(pass))        strength++;
    if (/[^A-Za-z0-9]/.test(pass)) strength++;

    var el = $('#password_strength');
    if (pass.length === 0) { el.text('').css('color', ''); return; }

    var levels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
    var colors = ['', '#e05c5c', '#e0a84c', '#4c9de0', '#4caf87'];
    el.text('Strength: ' + levels[strength]).css('color', colors[strength]);
  }

  // ── TOAST NOTIFICATION ───────────────────────────────────
  function showToast(message, type) {
    var colors = { success: '#4caf87', error: '#e05c5c', info: '#4c9de0' };
    var toast = $('<div>').css({
      position: 'fixed', top: '24px', right: '24px', zIndex: 9999,
      background: colors[type] || colors.info,
      color: '#fff', padding: '14px 22px',
      borderRadius: '8px', fontFamily: 'Inter, sans-serif',
      fontSize: '14px', fontWeight: '500',
      boxShadow: '0 8px 30px rgba(0,0,0,0.3)',
      opacity: 0, transition: 'opacity 0.3s ease',
      cursor: 'pointer', maxWidth: '320px'
    }).text(message);
    $('body').append(toast);
    setTimeout(function () { toast.css('opacity', 1); }, 10);
    setTimeout(function () {
      toast.css('opacity', 0);
      setTimeout(function () { toast.remove(); }, 300);
    }, 4000);
    toast.on('click', function () { $(this).remove(); });
  }

});