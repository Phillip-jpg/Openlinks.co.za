document.addEventListener( 'DOMContentLoaded', function () {
    new Splide( '.splide', {
        autoplay    : true,
        slide: true,
        rewind: true,
        drag: true,
        cover     : true,
        height    : '13rem',
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