form = document.querySelector(".handbook-form-page")
pill = document.getElementById("handbook-pill-checkbox");
pillSwitch = document.querySelector(".handbook-switch");
employeeName = document.getElementById("employee_name")
handbookName = document.getElementById("handbook_name")

window.addEventListener('load', function(){
    
    if(data.current_meta.read_handbook == 'Yes'){
        if(pill){
            pillSwitch.classList.toggle("checked");
        }
    }
})
if(pill){
    
    pill.addEventListener('change', function(){
        pillSwitch.classList.toggle("checked");
        if(employeeName){
            employeeName.value = data.current_user.data.display_name
        }
        if(handbookName){
            handbookName.value = data.current_page
        }
        form.submit()
    })
}


formSecurity = document.querySelector(".security-form")
pillSecurity = document.getElementById("security-pill-checkbox");
pillSwitchSecurity = document.querySelector(".security-switch");
employeeNameSecurity = document.getElementById("employee_name")

window.addEventListener('load', function(){
    
    if(data.current_meta.read_security == 'Yes'){
        if(pillSecurity){
            pillSwitchSecurity.classList.toggle("checked");
        }
    }
})
if(pillSecurity){
    
    pillSecurity.addEventListener('change', function(){
        
        pillSwitchSecurity.classList.toggle("checked");
        if(employeeNameSecurity){
            employeeNameSecurity.value = data.current_user.data.display_name
        }
        
        formSecurity.submit()
    })
}







