document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("pdpForm").addEventListener("submit", function (event) {
        event.preventDefault();
  
        var profilePictureFile = document.getElementById("profile_picture").files[0];

        var profilePictureForm = new FormData();
        profilePictureForm.append("profilePictureFile", profilePictureFile);
        
        $.ajax({
            type: "POST",
            url: "../../controller/UpdateImage.php",
            data: profilePictureForm,
            cache: false,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
                alert("An error occurred during profile update.");
            },
        });
    });
});
