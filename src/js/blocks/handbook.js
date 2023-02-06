form = document.querySelector(".handbook-form")
pill = document.getElementById("pill-checkbox");
pillSwitch = document.querySelector(".switch");
employeeName = document.getElementById("employee_name")
handbookName = document.getElementById("handbook_name")
window.addEventListener('load', function(){
    if(data.current_meta.read_handbook[0] == 'Yes'){
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







