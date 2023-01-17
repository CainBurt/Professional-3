import Glide from '@glidejs/glide'

var sliders = document.querySelectorAll('#team-sliders');
if(sliders){
    for (var i = 0; i < sliders.length; i++) {
        var glide = new Glide(sliders[i], {
            type: 'carousel',
            perView: 8,
            focusAt: 'center',
            autoplay: 4000 + Math.floor(Math.random() * 2000),
            breakpoints: {
            2000:{
                perView: 7,
            },
            1700:{
                perView: 6,
            },
            1400:{
                perView: 5,
            },
            1100:{
                perView: 3,
            },
            800: {
                perView: 3
            },
            480: {
                perView: 1
            }
            }
        })
        glide.mount()
    }
}


