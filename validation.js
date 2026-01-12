function validateRegistration(){
    var form = document.forms["regForm"];
    var pass = form["password"].value;
    var cpass = form["confirm_password"].value;
    var phone = form["phone"].value;

    if(pass.length < 6){
        alert("Password must be at least 6 characters!");
        return false;
    }
    if(pass !== cpass){
        alert("Passwords do not match!");
        return false;
    }
    if(!/^\d{11}$/.test(phone)){
        alert("Phone number must be exactly 11 digits!");
        return false;
    }
    return true;
}
