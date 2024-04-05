
$(document).ready(function () {

    $("#submit-form").click(function (event) { 
        event.preventDefault();

        var username = $("#username").val();
        var firstname = $("#firstname").val();
        var lastname = $("#lastname").val();
        var email = $("#email").val();
        var dob = $("#dob").val();
        var password = $("#password").val();
        var password_confirm = $("#confirm_password").val();

        if (password !== password_confirm) {
            $("#password_error").text("The passwords do not match.");
            alert("The passwords do not match.");
            return; 
        }

        var formData = {
            username: username,
            firstname: firstname,
            lastname: lastname,
            email: email,
            dob: dob,
            password: password,
        };  



        $.ajax({
            type: "POST",
            url: "../../controller/SignupForm.php",
            data: formData,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert("Successful registration!");
                    window.location.href = "./login.html";
                } else {
                    alert("Registration error.");
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
                alert("An error occurred during registration.");
            }
        });
    });

});
