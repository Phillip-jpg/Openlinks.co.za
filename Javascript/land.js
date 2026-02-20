jQuery(document).ready(function($){
    'use strict';
  
    $.Scrollax();
  });




  document.addEventListener( 'DOMContentLoaded', function () {
    new Splide( '.splide', {
        autoplay    : true,
        slide: true,
        rewind: true,
        drag: true,
        cover     : true,
        height    : '35rem',
        breakpoints: {
            640: {
                height: '6rem'
            }
        }
    }  ).mount();
} );

setInterval(()=>{
    $(".btn").toggleClass("pulse");
}, 1500);