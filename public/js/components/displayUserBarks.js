document.addEventListener("DOMContentLoaded", function () {
  fetch("../../controller/LoggedInUserBarks.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      data.sort((a, b) => b.id - a.id);

      let tweetContainer = document.getElementById("tweetContainer");

      data.forEach((tweet) => {
        let tweetCard = createTweetCard(tweet);
        tweetContainer.appendChild(tweetCard);
      });
    })
    .catch((error) => {
      console.error("Fetch error:", error);
    });

  function createTweetCard(tweet) {
    let card = document.createElement("div");
    card.classList.add("tweet-card");

    let author = document.createElement("p");
    author.textContent = tweet.username;
    author.classList.add("tweet-author");
    card.appendChild(author);

    let content = document.createElement("p");
    content.textContent = tweet.message;
    content.classList.add("tweet-content");
    card.appendChild(content);

    let deleteButton = document.createElement("button");
    deleteButton.textContent = "Delete";
    deleteButton.style.backgroundColor = "#dc3545"; 
    deleteButton.style.color = "#fff";
    deleteButton.style.padding = "8px 16px"; 
    deleteButton.style.border = "none";
    deleteButton.style.borderRadius = "4px"; 
    deleteButton.style.cursor = "pointer";
    deleteButton.style.marginRight = "10px"; 

    deleteButton.addEventListener("click", function () {
      if (window.confirm("Are you sure you want to delete this tweet?")) {
        deleteTweet(tweet.id);
      }
    });

    card.appendChild(deleteButton);

    if (tweet.media) {
      let media = document.createElement("img");
      media.src = tweet.media;
      media.classList.add("tweet-media");
      card.appendChild(media);
    }

    return card;
  }

  function deleteTweet(tweetId) {
    let deleteBarkData = new FormData();
    deleteBarkData.append("tweetId", tweetId);

    $.ajax({
      type: "POST",
      url: "../../controller/DeleteBark.php",
      data: deleteBarkData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.message === "Bark deleted") {
          window.location.reload();
        } else {
          alert("Unexpected response message");
        }
      },
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      },
    });
  }
});
