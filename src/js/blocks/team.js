import Glide from '@glidejs/glide'

var sliders = document.querySelectorAll('#team-sliders');
if(sliders){
    for (var i = 0; i < sliders.length; i++) {
        var glide = new Glide(sliders[i], {
            type: 'slider',
            perView: 8,
            // focusAt: 'center',
            autoplay: 30000 + Math.floor(Math.random() * 5000),
            rewind: false,
            breakpoints: {
            2000:{
                perView: 7,
            },
            1700:{
                perView: 5,
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


