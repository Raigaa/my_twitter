$(document).ready(function () {
    $("#submit-form").click(function (event) {
        event.preventDefault();

        var email = $("#email").val();
        var password = $("#password").val();

        $.ajax({
            type: "POST",
            url: "../../controller/LoginForm.php",
            data: { email: email, password: password },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    window.location.href = "./timeline.html";
                } else {
                    alert("Incorrect password or email, or this account is deactivated.");
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
                alert("An error has occurred during connection.");
            }
        });
    });
});
