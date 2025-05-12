// home.js

$(function(){
  // Cierra el menú móvil al hacer click
  $('.navbar-nav .nav-link').on('click', function(){
    $('.navbar-collapse').collapse('hide');
  });

  // Smooth scroll a la sección de características
  $('a[href="#features"]').on('click', function(e){
    e.preventDefault();
    $('html, body').animate({
      scrollTop: $('#features').offset().top
    }, 600);
  });

  // Detectar brillo de la imagen de fondo y ajustar color de texto
  var hero = $('.hero');
  var bg = hero.css('background-image');
  var matches = bg.match(/url\(["']?(.+?)["']?\)/);
  if (matches && matches[1]) {
    var url = matches[1];
    var img = new Image();
    img.src = url;
    img.onload = function() {
      // Reducimos la imagen a 100×100 para análisis ligero
      var canvas = document.createElement('canvas');
      var ctx = canvas.getContext('2d');
      var w = 100, h = 100;
      canvas.width = w;
      canvas.height = h;
      ctx.drawImage(img, 0, 0, w, h);
      var data = ctx.getImageData(0, 0, w, h).data;
      var colorSum = 0;
      for (var i = 0; i < data.length; i += 4) {
        // Luminancia perceptual
        colorSum += data[i] * 0.299 + data[i+1] * 0.587 + data[i+2] * 0.114;
      }
      var brightness = colorSum / (w * h);
      // <128 → imagen oscura → texto claro
      if (brightness < 128) {
        hero.addClass('light-text');
      } else {
        hero.addClass('dark-text');
      }
    };
    img.onerror = function() {
      // Fallback por defecto
      hero.addClass('dark-text');
    };
  }
});
