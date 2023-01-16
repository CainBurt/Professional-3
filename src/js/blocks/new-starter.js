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
    
    var input_fields = document.querySelectorAll(".wpcf7-text")
    var name = document.getElementById("name")
    var address1 = document.getElementById("address1")
    var city = document.getElementById("city")
    var postcode = document.getElementById("postcode")
    var country = document.getElementById("country")
    var detailsStatus = document.getElementById("details_status")

    var file_fields = document.querySelectorAll(".wpcf7-file")
    var doc1 = document.getElementById("file1")
    var doc2 = document.getElementById("file2")
    var doc3 = document.getElementById("file3")
    var filesStatus = document.getElementById("files_status")



    input_fields.forEach(input => {
        input.addEventListener('change', function(){
            if(name.value == '' || address1.value == '' || city == '' || postcode == '' || country == ''){
                // console.log("some fields are empty")
                detailsStatus.style.color = 'red';
                document.getElementById('sub-status').innerHTML = 'Missing'
            }else{
                detailsStatus.style.color = 'Green';
                document.getElementById('sub-status').innerHTML = 'Completed'
            }
        })
    })

    var details_link = document.getElementById('details_link')
    details_link.addEventListener('click', function(){
        sliderForm.go('=1')
    })

    file_fields.forEach(file => {
        file.addEventListener('input', function(){
            console.log(file)
            if(doc1.files.length == 0 || doc2.files.length == 0 || doc3.files.length == 0){
                // console.log("some files are empty")
                filesStatus.style.color = 'red';
                document.getElementById('file-status').innerHTML = 'Missing'
            }else{
                filesStatus.style.color = 'Green';
                document.getElementById('file-status').innerHTML = 'Completed'
            }
            
        })
    })

    var files_link = document.getElementById('files_link')
    files_link.addEventListener('click', function(){
        sliderForm.go('=4')
    })
}
    





