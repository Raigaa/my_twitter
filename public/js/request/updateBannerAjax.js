document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("ppBannerForm").addEventListener("submit", function (event) {
        event.preventDefault();
  
        var profileBannerFile = document.getElementById("profile_banner").files[0];

        var profileBannerForm = new FormData();
        profileBannerForm.append("profileBannerFile", profileBannerFile); 
        
        $.ajax({
            type: "POST",
            url: "../../controller/UpdateImage.php",
            data: profileBannerForm,
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