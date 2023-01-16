import Glide from '@glidejs/glide'

var slider = document.getElementById('start-slider');
// for (var i = 0; i < sliders.length; i++) {
if(slider){
    var glide = new Glide(slider, {
        type: 'carousel',
        perView: 9,
        focusAt: 'center',
        autoplay: 2000,
        breakpoints:{
            1700:{
                perView: 6,
            },
        }
    })
    glide.mount()
}

var formSlider = document.getElementById('new_starter_form');
if(formSlider){
    var sliderForm = new Glide(formSlider, {
        type: 'slider',
        focusAt: 'center',
        dragDistance: false,
        swipeThreshold: false,
    })
    sliderForm.mount()

    var next_btns = document.querySelectorAll('#next_btn')
    for (var i = 0; i < next_btns.length; i++){
        next_btns[i].addEventListener('click', function(){
            sliderForm.go('>')
        })
    }

    var back_btns = document.querySelectorAll('#back_btn')
    for (var j = 0; j < back_btns.length; j++){
        back_btns[j].addEventListener('click', function(){
            sliderForm.go('<')
        })
    }
    
}



