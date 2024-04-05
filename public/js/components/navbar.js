import TweetCard from "./TweetCard.js";

let tweetCardManager;

document.addEventListener("DOMContentLoaded", function () {
  var navbar = document.createElement("nav");
  navbar.innerHTML = `
    <nav-bar class="bg-white shadow">
      <ul class="flex items-center justify-between p-5">
        <div class="flex items-center">
          <label for="logo" class="font-bold text-xl mr-3">BARK</label>
          <img id="logo" src="../public/images/dog-placeholder-logo.png" class="h-10 w-10">
        </div>
        <li><a href="../view/user-profile.html" class="text-blue-500 hover:text-blue-800">Profile</a></li>
        <li><a href="../view/timeline.html" class="text-blue-500 hover:text-blue-800">Timeline</a></li>
        <li><a href="../view/messagerie-privée.php" class="text-blue-500 hover:text-blue-800">Messages</a></li>
        <form method="post" id="search-form" class="flex items-center">
          <input type="search" placeholder="Search" id="search-bar" class="border p-2 rounded">
          <input type="submit" value="Search" id="submit-search" class="bg-blue-500 text-white p-2 rounded ml-2">
        </form>
      </ul>
    </nav-bar>
  `;
  document.body.prepend(navbar);

  tweetCardManager = new TweetCard("card-container");

  displayFollowedTweet();

  document
    .getElementById("search-form")
    .addEventListener("submit", function (event) {
      event.preventDefault();

      let searchValue = document.getElementById("search-bar").value;

      let searchFormData = new FormData();
      searchFormData.append("search", searchValue);

      $.ajax({
        type: "POST",
        url: "../../controller/GetBarks.php",
        data: searchFormData,
        cache: false,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (response) {
          if (response.message === "Search results") {
            tweetCardManager.clearTweets();
            tweetCardManager.processResponse(response);
          } else {
            alert("Unexpected response message.");
          }
        },
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          alert("An error occurred during search.");
        },
      });
    });
});

function displayFollowedTweet() {

  fetch("../../controller/GetFollowedTweet.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {

      const tweetList = Object.values(data.followUserTweets);

      let tweetCardContainer = document.getElementById("card-container");
      while (tweetCardContainer.firstChild) {
        tweetCardContainer.removeChild(tweetCardContainer.firstChild);
      }

      tweetCardManager.processResponse({ followUserTweets: tweetList });
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
}