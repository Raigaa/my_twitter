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

        $("#DeleteAccountForm").submit(function(event) {
            event.preventDefault();

            if (userData) { 
                let formDeleteAccount = new FormData();
                formDeleteAccount.append("id", userData.user_id);
                formDeleteAccount.append("deleteAccount", "delete");

                $.ajax({
                    type: "POST",
                    url: "../../controller/DeleteAcc.php",
                    data: formDeleteAccount,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function (response) {
                        console.log("Response:", response);
                        if (response.status === "success") {
                            alert("Account deleted successfully!");
                            window.location.href = "./login.html";
                        } else {
                            alert("Account deletion error.");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText);
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
