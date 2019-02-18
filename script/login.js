function login() {
    var username= $('#usname').val();
    var password= $('#pawrd').val();
    if( ((username=='') && (password=='')) ){
        alert('Both Fields Required');
        return 0;
    }
    if (password=='') {
        alert('Both Fields Required');
        return 0;
    }
    if (username==''){
        alert('Both Fields Required');
        return 0;
    }else{
        var DataArr={
            'action' : 'userLogged',
            'Username' : username,
            'Paswword' : password
        }

        //console.log(DataArr);

        $.ajax({
            url: "index.php",
            type: "POST",
            data: DataArr,
            dataType : "JSON",
            cache : false,
            success: function (data) {

                if (data.result == 21) {
                    alert("LoggedIn Successfully");
                    window.location.href = "dashboard.php";
                }
                else {
                     alert("Error.!!");
                }
            },
        });
    }



}
//
function registerUser() {
    var uname=$('#username').val();
    var pass=$('#pass1').val();
    var pass2=$('#pass2').val();
    var email=$('#email').val();
    var mobile=$('#mobile').val();

    if(pass != pass2 ){
        alert("Password Not matched");
    }
    var DataArr={
        'action' : 'regisUser',
        'Username' : uname,
        'Paswword' : pass2,
        'email' : email,
        'Mobile' : mobile
    };
   //console.log(DataArr);

   $.ajax({
        url: "register.php",
        type: "POST",
        data: DataArr,
        dataType : "JSON",
        cache : false,
        success: function (data) {
            if (data.result == 500) {
               alert("User Created Successfully");
                window.location.reload();
            }
            else {
               alert("Error.!!");
            }
        },
    });


}