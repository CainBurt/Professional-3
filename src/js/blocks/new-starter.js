import Glide from '@glidejs/glide'

var slider = document.getElementById('start-slider');
// for (var i = 0; i < sliders.length; i++) {
    var glide = new Glide(slider, {
        type: 'carousel',
        perView: 8,
        focusAt: 'center',
        autoplay: 2000,
    })
    glide.mount()


