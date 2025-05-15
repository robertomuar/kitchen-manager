$(function() {
  const $loginBg = $('#login-modal-backdrop');
  
  function openModal() {
    $('.modal-backdrop').removeClass('show');
    $loginBg.addClass('show');
  }
  
  // Botón de cabecera
  $('#open-login-modal').on('click', e => {
    e.preventDefault();
    openModal();
  });
  
  // Cambio entre login y registro
  $('#open-register-modal-inline').on('click', function(e) {
    e.preventDefault();
    $('#login-modal-backdrop').removeClass('show');
    $('#register-modal-backdrop').addClass('show');
  });
  
  $('#open-login-modal-inline').on('click', function(e) {
    e.preventDefault();
    $('#register-modal-backdrop').removeClass('show');
    $('#login-modal-backdrop').addClass('show');
  });
  
  $('#open-register-modal').on('click', function(e) {
    e.preventDefault();
    $('#register-modal-backdrop').addClass('show');
  });
  
  // Cerrar modales
  $('.modal-close').on('click', function() {
    $(this).closest('.modal-backdrop').removeClass('show');
  });
  
  $('.modal-backdrop').on('click', function(e) {
    if (e.target === this) $(this).removeClass('show');
  });
  
  // Intercepta SOLO enlaces con clase auth-required
  $(document).on('click', 'a.auth-required', function(e) {
    e.preventDefault();
    openModal();
  });
});