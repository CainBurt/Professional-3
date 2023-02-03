pill = document.getElementById("pill-checkbox");
pillSwitch = document.querySelector(".switch");
employeeName = document.getElementById("employee_name")
handbookName = document.getElementById("handbook_name")
if(pill){
    pill.addEventListener('change', function(){
        console.log(data.current_user.data.display_name)
        console.log(data.current_page)
        console.log("CLICKED")
        pillSwitch.classList.toggle("checked");
        if(employeeName){
            employeeName.value = data.current_user.data.display_name
        }
        
        if(handbookName){
            handbookName.value = data.current_page
        }
        
    })
}



