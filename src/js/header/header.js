window.onload = (event) => {
    const toggle = document.getElementById('dropdown')
    const dropdown = document.getElementById('dropdown_menu')
    const toggleIcon = document.getElementById('dropdown_icon')

    if(toggle){
        toggle.onclick = function(){
            if(dropdown.style.display === 'flex'){
                dropdown.style.display = 'none'
                toggleIcon.classList.toggle("fa-bars")
                toggleIcon.classList.toggle("fa-x")
            }else{
                dropdown.style.display = 'flex'
                toggleIcon.classList.toggle("fa-bars")
                toggleIcon.classList.toggle("fa-x")
            }
        }
    }
    
    var dropDownValue = document.getElementById("section");
    var yourHeight = 160;

    dropDownValue.onchange = function() {
    if (this.selectedIndex !== 0) {
        window.location.href = this.value;
        // var scrolledY = window.scrollY;
        // if(scrolledY){
        //     window.scroll(0, scrolledY - yourHeight)
        // }
        console.log(dropDownValue)
        // window.scrollBy(0, 70)
    }
    };
    

}
      
