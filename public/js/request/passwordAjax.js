document.addEventListener("DOMContentLoaded", function handleProfileDataLoad() {
  fetch("../../controller/ProfileData.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      $(document).ready(function () {
        $("#submit-password-form").click(function handleSubmitPasswordForm(event) {
          event.preventDefault();

          const old_password = $("#oldPassword").val();

          const new_password_confirm = $("#confirmNewPassword").val();

          if (new_password !== new_password_confirm) {
            alert("The passwords don't match.");
            return; 
          }else if(old_password === new_password) {
            alert("Please choose a different password, this one has already been used.");
            return;
          }

          const formData = {
            id: data.id,
            old_password: old_password,
            new_password: new_password,
          };


          $.ajax({
            type: "POST",
            url: "../../controller/UpdatePassword.php",
            data: { formPassword: formData },
            dataType: "json",
            success: function handleSuccessResponse(response) {
              if (response === true || response === "true") { 
                  alert("Mot de passe modifié !");
                  window.location.href = "./login.html";
              } else {
                  alert("Error modifying password.");
              }
          },
          
            error: function handleErrorResponse(xhr, status, error) {
              console.log(xhr.responseText);
              alert("An error has occurred while changing the password.");
            },
          });
        });
      });
    })
    .catch((error) => {
      console.error("Error fetching profile data:", error);
      alert("An error occurred while retrieving profile data.");
    });
});
