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
    var ainmationLength = 1500;
    var sliderForm = new Glide(formSlider, {
        type: 'slider',
        focusAt: 'center',
        dragDistance: false,
        swipeThreshold: false,
        dragThreshold: false,
        keyboard: false,
        animationDuration: ainmationLength/2,
    })
    sliderForm.mount();
    
    // animation on slide changes
    var index = 0
    var colorCover = document.getElementById("color_cover")
    var colours = ["#00E300","#00D2E2","#FE5C01","#00E300","#00D2E2","#FE5C01"]
    var next_btns = document.querySelectorAll('#next_btn');
    for (var i = 0; i < next_btns.length; i++){
        next_btns[i].addEventListener('click', function(){
            colorCover.classList.add("slideAnimation")
            colorCover.style.background =`linear-gradient(to left, ${colours[index++]} 50%, transparent 50%)`;
            colorCover.style.backgroundSize = '200% 100%';
            setTimeout(function(){
                colorCover.classList.remove("slideAnimation")
            }, ainmationLength);
            
            setTimeout(function(){
                sliderForm.go('>');
            }, ainmationLength/3);
        });
    };

    var back_btns = document.querySelectorAll('#back_btn');
    for (var j = 0; j < back_btns.length; j++){
        back_btns[j].addEventListener('click', function(){
            colorCover.classList.add("slideAnimationReverse")
            colorCover.style.background =`linear-gradient(to left, ${colours[--index]} 50%, transparent 50%)`;
            colorCover.style.backgroundSize = '200% 100%';
            setTimeout(function(){
                colorCover.classList.remove("slideAnimationReverse")
            }, ainmationLength);
            setTimeout(function(){
                sliderForm.go('<');
            }, ainmationLength/3);
            
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


    // personal details validation
    input_fields.forEach(input => {
        input.addEventListener('change', function(){
            
            if(name.value == '' || address1.value == '' || city.value == '' || postcode.value == '' || country.value == '' || phone.value <= 5){
                detailsStatus.style.color = '#E30000';
                document.getElementById('sub-status').innerHTML = 'Missing';
            }else{
                detailsStatus.style.color = '#00E300';
                document.getElementById('sub-status').innerHTML = 'Completed';
            }

        })
    });

    // last slide buttons
    var details_link = document.getElementById('details_link')
    details_link.addEventListener('click', function(){
        colorCover.classList.add("slideAnimationReverse")
        setTimeout(function(){
            colorCover.classList.remove("slideAnimationReverse")
        }, ainmationLength);
        setTimeout(function(){
            sliderForm.go('=0');
        }, ainmationLength/3);
        index = 0;
    });

    var files_link = document.getElementById('files_link')
    files_link.addEventListener('click', function(){
        colorCover.classList.add("slideAnimationReverse")
        setTimeout(function(){
            colorCover.classList.remove("slideAnimationReverse")
        }, ainmationLength);
        setTimeout(function(){
            sliderForm.go('=4')
        }, ainmationLength/3);
    });

    // file validation for last slide 
    file_fields.forEach(file => {
        file.addEventListener('input', function(){
            console.log(file)
            if(doc1.files.length == 0 || doc2.files.length == 0 || doc3.files.length == 0){
                filesStatus.style.color = '#E30000';
                document.getElementById('file-status').innerHTML = 'Missing'
            }else{
                filesStatus.style.color = '#00E300';
                document.getElementById('file-status').innerHTML = 'Completed'
            }
            
        })
    });

    // Event listeners for inputs to enable buttons
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

}
    





