// resources/js/overlay.js
$(function() {
  // Selectores genéricos
  const $loginBg = $('#login-modal-backdrop');
  const $regBg   = $('#register-modal-backdrop');

  // Triggers
  const $openLogin       = $('#open-login-modal');
  const $openReg         = $('#open-register-modal');
  const $openRegInline   = $('#open-register-modal-inline');
  const $openLoginInline = $('#open-login-modal-inline');

  // Close buttons
  const $closeBtns = $('.modal-backdrop .modal-close');

  // Abrir un modal
  function openModal($bg) {
    $('.modal-backdrop').removeClass('show'); // cierra cualquier otro
    $bg.addClass('show');
  }
  // Cerrar un modal
  function closeModal($bg) {
    $bg.removeClass('show');
  }

  // Handlers
  $openLogin.on('click', e => { e.preventDefault(); openModal($loginBg); });
  $openReg.on('click',   e => { e.preventDefault(); openModal($regBg); });

  $openRegInline.on('click', e => {
    e.preventDefault();
    $loginBg.removeClass('show');
    $regBg.addClass('show');
  });
  $('#open-login-modal-inline').on('click', e => {
    e.preventDefault();
    $regBg.removeClass('show');
    $loginBg.addClass('show');
  });

  $('.modal-close').on('click', function() {
    $(this).closest('.modal-backdrop').removeClass('show');
  });
  $('.modal-backdrop').on('click', function(e) {
    if (e.target === this) $(this).removeClass('show');
  });
});
