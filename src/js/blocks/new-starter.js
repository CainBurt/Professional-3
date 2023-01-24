import Glide from '@glidejs/glide'

var slider = document.getElementById('start-slider');
if(slider){
    var glide = new Glide(slider, {
        type: 'carousel',
        perView: 9,
        focusAt: 'center',
        autoplay: 1,
        animationDuration: 3000,
        animationTimingFunc: 'linear',
        swipeThreshold: false,
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
        dragThreshold: false,
    })
    sliderForm.mount();

    var next_btns = document.querySelectorAll('#next_btn');
    for (var i = 0; i < next_btns.length; i++){
        next_btns[i].addEventListener('click', function(){
            sliderForm.go('>');
        });
    };

    var back_btns = document.querySelectorAll('#back_btn');
    for (var j = 0; j < back_btns.length; j++){
        back_btns[j].addEventListener('click', function(){
            sliderForm.go('<');
        });
    };
    
    var input_fields = document.querySelectorAll(".wpcf7-text");
    var name = document.getElementById("name");
    var address1 = document.getElementById("address1");
    var city = document.getElementById("city");
    var postcode = document.getElementById("postcode");
    var country = document.getElementById("country");
    var dob = document.getElementById("dob")
    var phone = document.getElementById("phone_number")
    var detailsStatus = document.getElementById("details_status");

    var file_fields = document.querySelectorAll(".wpcf7-file");
    var doc1 = document.getElementById("file1");
    var doc1Text = document.getElementById("file1_text")
    var doc2 = document.getElementById("file2");
    var doc2Text = document.getElementById("file2_text")
    var doc3 = document.getElementById("file3");
    var doc3Text = document.getElementById("file3_text")
    var filesStatus = document.getElementById("files_status");



    input_fields.forEach(input => {
        input.addEventListener('change', function(){
            
            if(name.value == '' || address1.value == '' || city.value == '' || postcode.value == '' || country.value == ''){
                detailsStatus.style.color = '#E30000';
                document.getElementById('sub-status').innerHTML = 'Missing'             
            }else{
                detailsStatus.style.color = '#00E300';
                document.getElementById('sub-status').innerHTML = 'Completed'
            }

        })
    });

    var details_link = document.getElementById('details_link')
    details_link.addEventListener('click', function(){
        sliderForm.go('=0')
    });

    file_fields.forEach(file => {
        file.addEventListener('input', function(){
            console.log(file)
            if(doc1.files.length == 0 || doc2.files.length == 0 || doc3.files.length == 0){
                // console.log("some files are empty")
                filesStatus.style.color = '#E30000';
                document.getElementById('file-status').innerHTML = 'Missing'
            }else{
                filesStatus.style.color = '#00E300';
                document.getElementById('file-status').innerHTML = 'Completed'
            }
            
        })
    });

    var files_link = document.getElementById('files_link')
    files_link.addEventListener('click', function(){
        sliderForm.go('=4')
    });

    // Event listeners for buttons
    name.addEventListener('change', function(){
        var childBtn = name.parentElement.parentElement.querySelector('.next-btn')
        if(name.value == '' || name.value == null){
            childBtn.classList.add("disabled");
        }else{
            childBtn.classList.remove("disabled");
        }
    });
    
    [address1, city, postcode, country].forEach(function(element){
        element.addEventListener('change', function(){
            var childBtn = address1.parentElement.parentElement.querySelector('.next-btn');
            if(address1.value == '' || city.value == '' || postcode.value == '' || country.value == ''){
                childBtn.classList.add("disabled");
            }else{
                childBtn.classList.remove("disabled");
            }
        });
    });

    dob.addEventListener('change', function(){
        var childBtn = dob.parentElement.parentElement.querySelector('.next-btn')
        console.log(dob.value)
        if(dob.value == '' || dob.value == null){
            childBtn.classList.add("disabled");
        }else {
            childBtn.classList.remove("disabled");
        }
    });

    phone.addEventListener('change', function(){
        var childBtn = phone.parentElement.parentElement.parentElement.querySelector('.next-btn')
        console.log(phone.value.length)
        if(phone.value.length <= 5 || phone.value == null){
            childBtn.classList.add("disabled");
        }else {
            childBtn.classList.remove("disabled");
        }
    });

    // add file name into text field
    doc1.addEventListener('change', function(){
        doc1Text.value = doc1.value.split("\\").pop();
    });
    doc2.addEventListener('change', function(){
        doc2Text.value = doc2.value.split("\\").pop();
    });
    doc3.addEventListener('change', function(){
        doc3Text.value = doc3.value.split("\\").pop();
    });

    //next btn for file uploads
    // [doc1, doc2, doc3].forEach(function(element){
    //     element.addEventListener('change', function(){
    //         var childBtn = doc1.parentElement.parentElement.parentElement.querySelector('.next-btn');
    //         if(doc1.value == '' || doc2.value == '' || doc3.value == ''){
    //             childBtn.classList.add("disabled");
    //         }else{
    //             childBtn.classList.remove("disabled");
    //         }
    //     });
    // });

}
    





