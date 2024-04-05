document.addEventListener("DOMContentLoaded", function () {

  getUserPreferences();
  getUserData();
  document.querySelectorAll("[data-target]").forEach(function (button) {
    button.addEventListener("click", function () {
      let target = document.querySelector(this.getAttribute("data-target"));
      showModal(target);
    });
  });
});

function showModal(target) {
  target.classList.add("show");

  target.addEventListener("click", function (event) {
    if (!event.target.closest(".modal-content")) {
      hideModal(target);
    }
  });

  document.addEventListener("keyup", function (event) {
    if (event.key === "Escape") {
      getUserPreferences();
      getUserData();
      hideModal(target);
    }
  });

  target.querySelectorAll(".popin-dismiss").forEach(function (button) {
    button.addEventListener("click", function () {
      getUserPreferences();
      getUserData();
      hideModal(target);
    });
  });
}

function hideModal(target) {
  target.classList.remove("show");
}

function getUserPreferences() {
  fetch("../../controller/UserPreferences.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      let bioElement = document.getElementById("bio-fields");
      let localisationElement = document.getElementById("localisation-fields");
      let websiteElement = document.getElementById("website-fields");
      let ppUser = document.getElementById("ppUser");
      let bannerUser = document.getElementById("bannerUser");

      if (bioElement && localisationElement && websiteElement && ppUser && bannerUser) {
        bioElement.textContent = data.bio || "No bio yet";
        localisationElement.textContent = data.localisation || "No location yet";
        websiteElement.textContent = data.website || "No website yet";
        ppUser.src = data.profile_picture || "../public/images/profil-pic.jpg";
        bannerUser.src = data.profile_banner || "../public/images/default-banner.jpg";
      }
    })
    .catch((error) => {
      console.error("Error fetching user preferences:", error);
    });
}


function getUserData(){
  fetch("../../controller/ProfileData.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      let usernameElement = document.getElementById("username-profile");

      if (usernameElement) {
        usernameElement.textContent = data.username || "No username yet";
      }
    })
    .catch((error) => {
      console.error("Error fetching user data:", error);
    });
}