document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("barkForm").addEventListener("submit", function (event) {
      event.preventDefault();
      
      let barkText = document.getElementById("tweet-input").value;
      
      if (barkText.trim() === "") {
          alert("Please at least write something.");
          return;
      }

      var barkFormData = new FormData();

      let motApresHashtag = barkText.match(/#(\w+)?/);
      
      let motApresArrobase = barkText.match(/@(\w+)?/);
      
      if (motApresHashtag) {
          let motAvecHashtag = "#" + motApresHashtag[1];
          barkFormData.append("hashtagWord", motAvecHashtag);
      }

      if (motApresArrobase) {
          let motAvecArrobase = "@" + motApresArrobase[1];
          barkFormData.append("Username", motAvecArrobase);
      }

      barkFormData.append("barkText", barkText);

      $.ajax({
          type: "POST",
          url: "../../controller/AddBark.php",
          data: barkFormData,
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
              alert("Une erreur s'est produite lors de la requete");
          },
      });
  });
});
