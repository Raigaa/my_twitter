$(document).ready(function() {
  fetch("../../controller/UserPreferences.php")
      .then((response) => {
          if (!response.ok) {
              throw new Error("Network response was not ok");
          }
          return response.json();
      })
      .then((data) => {
          $("#bio").val(data.bio);
          $("#localisation").val(data.localisation);
          $("#website").val(data.website);
      })
      .catch(error => {
          console.error('Error fetching user preferences:', error);
      });

  $("form#publicFormData").submit(function(event) {
      event.preventDefault();

      var bio = $("#bio").val().trim() || null;
      var localisation = $("#localisation").val().trim() || null;
      var website = $("#website").val().trim() || null;

      if (bio && bio.length > 100) {
          alert("Character limit reached for bio. (100 max)");
          return;
      }

      let regexUrl = /^https:\/\/[a-z0-9.-]+(\/[a-z0-9.-\/]+)?$/i;

      if (website && !regexUrl.test(website)) {
          alert("Invalid website URL. (https://www.example.com)");
          return;
      }

      var formPublicData = new FormData();

      formPublicData.append("bio", bio);
      formPublicData.append("localisation", localisation);
      formPublicData.append("website", website);


      $.ajax({
          type: "POST",
          url: "../../controller/UpdatePublicData.php",
          data: formPublicData,
          cache: false,
          processData: false,
          contentType: false,
          dataType: "json",
          success: function (response) {
              if (response.status === "success") {
                  alert("Profile updated successfully!");
              } else {
                  alert("Profile update error.");
              }
          },
          error: function (xhr, status, error) {
              console.log(xhr.responseText);
              alert("An error occurred during profile update.");
          },
      });
  });
});
