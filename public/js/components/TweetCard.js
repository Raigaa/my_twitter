export default class TweetCard {
  constructor(containerId) {
    this.cardContainer = document.getElementById(containerId);
    this.cardContainer.innerHTML = "";
  }

  processResponse(response) {
    if (response.profiles && Object.keys(response.profiles).length > 0) {
      this.createProfileCards(response.profiles);
    }

    if (response.hashtags_mentions) {
      for (let hashtag in response.hashtags_mentions) {
        if (Array.isArray(response.hashtags_mentions[hashtag])) {
          this.createTweetCards(response.hashtags_mentions[hashtag]);
        }
      }
    }

    if (response.wholeMessage) {
      this.createTweetCards(response.wholeMessage);
    }

    if (response.followingTweets) {
      for (let user in response.followingTweets) {
        if (Array.isArray(response.followingTweets[user])) {
          this.createTweetCards(response.followingTweets[user]);
        }
      }
    }

    if (response.followUserTweets) {
      this.createTweetCards(response.followUserTweets);
    }

    if (
      response.hashtags_mentions &&
      response.hashtags_mentions.error &&
      response.profiles &&
      response.profiles.error &&
      (!response.wholeMessage || response.wholeMessage.length === 0) &&
      (!response.followingTweets ||
        Object.keys(response.followingTweets).length === 0)
    ) {
      alert("No Barks found for this search.");
    }
  }

  createProfileCards(profiles) {
    let profileContainer = document.createElement("div");
    profileContainer.classList.add("profile-container");

    for (let username in profiles) {
      if (Array.isArray(profiles[username])) {
        profiles[username].forEach((profile) => {
          if (profile) {
            let profileCard = this.profileContent(profile);
            if (profileCard) {
              profileContainer.appendChild(profileCard);
            }
          }
        });
      }
    }

    this.cardContainer.prepend(profileContainer);
  }

  profileContent(profile) {
    if (profile) {
      let card = document.createElement("div");
      card.classList.add("profile-card");
      this.appendStyles();

      let profileInfo = document.createElement("div");
      profileInfo.classList.add("profile-info");

      let username = document.createElement("p");
      username.textContent = "Username: " + profile.username;
      username.classList.add("profile-username");
      profileInfo.appendChild(username);

      if (profile.bio !== undefined) {
        let bio = document.createElement("p");
        bio.textContent = "Bio: " + profile.bio;
        bio.classList.add("profile-bio");
        profileInfo.appendChild(bio);
      } else {
        
        console.error("No bio found for user: ", profile.username);

      }

      card.appendChild(profileInfo);

      let followButton;
      if (profile.status === 1) {
        followButton = this.createUnfollowButton(
          profile.username,
          profile.user_id
        );
      } else {
        followButton = this.createFollowButton(
          profile.username,
          profile.user_id
        );
      }
      card.appendChild(followButton);

      return card;
    } else {
      console.error("Aucun profil trouvé dans le tableau :", profile);
      return null;
    }
  }

  createUnfollowButton(username, userId) {
    let button = document.createElement("button");
    button.textContent = "Unfollow " + username;
    button.classList.add("default-button", "unfollow-button");
    button.setAttribute("data-user-id", userId);
    button.addEventListener("click", () => {
      button.classList.toggle("clicked-button");
      if (button.classList.contains("clicked-button")) {
        this.unfollowUser(username, userId);
        button.textContent = "Follow " + username;
      } else {
        this.followUser(username, userId);
        button.textContent = "Unfollow " + username;
      }
    });
    return button;
  }

  createTweetCards(tweetList) {
    if (Array.isArray(tweetList) && tweetList.length > 0) {
      tweetList.forEach((tweet) => {
        let tweetCard = this.tweetContent(tweet);
        this.cardContainer.appendChild(tweetCard);
      });
    } else {
      console.error("No tweets to display.");
    }
  }

  tweetContent(tweet) {
    let card = document.createElement("div");
    card.classList.add("tweet-card");
    this.appendStyles();

    let author = document.createElement("p");
    author.textContent = "Author: " + tweet.username;
    author.classList.add("tweet-author");
    card.appendChild(author);

    let content = document.createElement("p");
    content.textContent = tweet.message;
    content.classList.add("tweet-content");
    card.appendChild(content);

    if (tweet.media) {
      let mediaContainer = document.createElement("div");
      mediaContainer.classList.add("media-container");
      let media = document.createElement("img");
      media.src = tweet.media;
      media.classList.add("tweet-media");
      mediaContainer.appendChild(media);
      card.appendChild(mediaContainer);
    }

    let button = document.createElement("button");
    button.textContent = "View Author";
    button.classList.add("default-button", "view-author-button");
    card.appendChild(button);

    button.addEventListener("click", () => {
  
      this.createAuthorModal(tweet); 
    });

    return card;
  }

  createFollowButton(username, userId) {
    let button = document.createElement("button");
    button.textContent = "Follow";
    button.classList.add("default-button", "follow-button");
    button.setAttribute("data-user-id", userId);
    button.addEventListener("click", () => {
      button.classList.toggle("clicked-button");
      if (button.classList.contains("clicked-button")) {
        this.followUser(username, userId);
        button.textContent = "Unfollow " + username;
      } else {
        this.unfollowUser(username, userId);
        button.textContent = "Follow";
      }
    });
    return button;
  }

  appendStyles() {
    let style = document.createElement("style");
    style.textContent = `
        .tweet-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            position: relative;
        }

        .tweet-author {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .tweet-content {
            margin-bottom: 10px;
        }

        .media-container {
            text-align: center;
            margin-bottom: 10px;
        }

        .tweet-media {
            max-width: 100%;
            max-height: 200px;
            border-radius: 5px;
        }

        .default-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .default-button:hover {
            background-color: #0056b3;
        }

        .clicked-button {
            background-color: #dc3545;
        }

        .follow-button,
        .unfollow-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .unfollow-button {
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .unfollow-button:hover {
            background-color: #c82333;
        }

        .profile-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            position: relative;
        }
  
        .profile-username {
            font-weight: bold;
            margin-bottom: 5px;
        }
    `;
    document.head.appendChild(style);
  }

  followUser(username, userId) {
    let followData = {
      username: username,
      userId: userId,
    };

    this.followAjax(followData);
  }

  unfollowUser(username, userId) {
    let unfollowData = {
      username: username,
      userId: userId,
    };

    this.unfollowUserAjax(unfollowData);
  }

  followAjax(followData) {
    $.ajax({
      url: "../../controller/FollowAjax.php",
      type: "POST",
      data: { followData: followData },
      dataType: "json",
      success: function (response) {
      },
      error: function (error) {
        console.error("Erreur lors de la requête :", error);
      },
    });
  }

  unfollowUserAjax(unfollowData) {
    $.ajax({
      url: "../../controller/FollowAjax.php",
      type: "POST",
      dataType: "json",
      data: { unfollowData: unfollowData },
      success: function (response) {
      },
      error: function (error) {
        console.error("Erreur lors de la requête :", error);
      },
    });
  }

  clearTweets() {
    this.cardContainer.innerHTML = "";
  }

  createAuthorModal(authorData) {
    let modal = document.createElement("div");
    modal.classList.add(
      "author-modal",
      "fixed",
      "top-1/2",
      "left-1/2",
      "transform",
      "-translate-x-1/2",
      "-translate-y-1/2",
      "bg-white",
      "p-5",
      "border",
      "border-gray-300",
      "rounded-md",
      "shadow-lg",
      "z-50"
    );

    let profilePicture = document.createElement("img");
    profilePicture.src =
      authorData.profile_picture || "../public/images/profil-pic.jpg";
    profilePicture.classList.add("w-24", "h-24", "rounded-full", "mb-4");
    modal.appendChild(profilePicture);

    let username = document.createElement("p");
    username.textContent = "Username: " + authorData.username;
    username.classList.add("text-lg", "font-bold", "mb-4");
    modal.appendChild(username);

    let bio = document.createElement("p");
    bio.textContent = "Bio: " + authorData.bio || "No bio yet";
    bio.classList.add("text-base", "mb-2");
    modal.appendChild(bio);

    let location = document.createElement("p");
    location.textContent =
      "Location: " + authorData.localisation || "No location yet";
    location.classList.add("text-base", "mb-2");
    modal.appendChild(location);

    let closeButton = document.createElement("button");
    closeButton.textContent = "Close";
    closeButton.classList.add(
      "close-button",
      "absolute",
      "top-2",
      "right-2",
      "bg-red-600",
      "text-white",
      "border-0",
      "rounded-md",
      "px-2",
      "py-1",
      "cursor-pointer",
      "hover:bg-red-500"
    );
    closeButton.addEventListener("click", (event) => {
      event.stopPropagation();
      document.body.removeChild(modal);
    });
    modal.appendChild(closeButton);

    document.body.appendChild(modal);
  }
}
