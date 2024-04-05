$(document).ready(function() {
    let userData; 

    fetch("../../controller/UserPreferences.php")
    .then((response) => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then((data) => {
        userData = data;

        $("#logOutForm").submit(function(event) {
            event.preventDefault();

            if (userData) { 
                let formDeleteAccount = new FormData();
                formDeleteAccount.append("id", userData.user_id);
                formDeleteAccount.append("logout", "logout");


                $.ajax({
                    type: "POST",
                    url: "../../controller/Logout.php",
                    data: formDeleteAccount,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            alert(response.message);
                            window.location.href = "./login.html";
                        } else {
                            alert("Log out error.");
                        }
                    },
                    error: function (xhr, status, error) {
                    }
                });
            } else {
                console.error("User data is not available yet.");
            }
        });
    })
    .catch(error => {
        console.error('Error fetching user preferences:', error);
    });
});
