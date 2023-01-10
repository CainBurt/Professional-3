import Glide from '@glidejs/glide'

var sliders = document.querySelectorAll('.glide');
for (var i = 0; i < sliders.length; i++) {
    var glide = new Glide(sliders[i], {
        type: 'carousel',
        perView: 6,
        focusAt: 'center',
        breakpoints: {
        800: {
            perView: 2
        },
        480: {
            perView: 1
        }
        }
    })
    glide.mount()
}



