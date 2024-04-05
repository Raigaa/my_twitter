<?php

require_once "Database.php";

class Bark
{
    private $db;
    private static $instance;

    private function __construct($database)
    {
        $this->db = $database;
        session_start();
    }

    public static function getInstance($database)
    {
        if (!isset (self::$instance)) {
            self::$instance = new self($database);
        }
        return self::$instance;
    }

    public function getBarks()
    {
        $userIdToIgnore = $_SESSION['user_id'] ?? null;
        if ($userIdToIgnore === null) {
            return [];
        }

        $connection = $this->db->getConnection();
        if (!$connection) {
            return [];
        }

        try {
            $query = "SELECT * FROM tweet WHERE user_id != ?";
            $statement = $connection->prepare($query);
            $statement->execute([$userIdToIgnore]);

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
            return [];
        }
    }

    function getLoggedInUserBarks()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId === null) {
            return [];
        }

        $connection = $this->db->getConnection();
        if (!$connection) {
            return [];
        }

        try {
            $query = "SELECT tweet.*, users.username 
                      FROM tweet 
                      INNER JOIN users ON tweet.user_id = users.id 
                      WHERE tweet.user_id = ?";
            $statement = $connection->prepare($query);
            $statement->execute([$userId]);

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
            return [];
        }
    }


    public function parseMessage($message)
    {
        $foundBarks = [
            'profiles' => [],
            'hashtags_mentions' => [],
            'wholeMessage' => [],
            'followingTweets' => []
        ];

        // find hashtags
        preg_match_all("/#(\w+)/", $message, $hashtags);
        $foundHashtags = $hashtags[1];
        if (!empty ($foundHashtags)) {
            foreach ($foundHashtags as $hashtag) {
                $barks = $this->getBarksWithHashtag($hashtag);
                if (!isset ($foundBarks['hashtags_mentions'][$hashtag])) {
                    $foundBarks['hashtags_mentions'][$hashtag] = $barks;
                }
            }
        }

        // find mentions
        preg_match_all("/@(\w+)/", $message, $mentions);
        $foundMentions = $mentions[1];
        if (!empty ($foundMentions)) {
            foreach ($foundMentions as $mention) {
                $barks = $this->getBarksWithMention($mention);
                if (!isset ($foundBarks['hashtags_mentions'][$mention])) {
                    $foundBarks['hashtags_mentions'][$mention] = $barks;
                }
                $users = $this->getUsersByUsername($mention);
                if (!isset ($foundBarks['profiles'][$mention])) {
                    $foundBarks['profiles'][$mention] = $users;
                }

                // Check if the current user follows the mentioned user
                foreach ($users as $user) {
                    if ($user['status'] == 1) {
                        // If following, fetch all tweets of the mentioned user
                        $followingTweets = $this->getAllTweetsOfUser($user['user_id']);
                        if (!isset ($foundBarks['followingTweets'][$mention])) {
                            $foundBarks['followingTweets'][$mention] = $followingTweets;
                        }
                    }
                }
            }
        }

        // if both empty find in whole message
        if (empty ($foundHashtags) && empty ($foundMentions)) {
            $barks = $this->searchInWholeMessage($message);
            $foundBarks['wholeMessage'] = $barks;
        }

        return $foundBarks;
    }



    public function getAllTweetsOfUser($userId)
    {
        $connection = $this->db->getConnection();
        if (!$connection) {
            return [];
        }

        try {
            $query = "SELECT tweet.*, users.username
                      FROM tweet 
                      JOIN users ON tweet.user_id = users.id 
                      WHERE tweet.user_id = ?";
            $statement = $connection->prepare($query);
            $statement->execute([$userId]);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "PDOException: " . $e->getMessage();
            return [];
        }
    }


    public function getUsersByUsername($username)
    {
        $connection = $this->db->getConnection();
        if (!$connection) {
            return [];
        }

        $username = $username . '%';
        $loggedInUserId = $_SESSION['user_id'];

        try {
            $query = "SELECT users.*, IF(followers.follower_id IS NOT NULL, 1, 0) AS status, users_preferences.*
                FROM users 
                LEFT JOIN users_preferences ON users.id = users_preferences.user_id
                LEFT JOIN followers ON users.id = followers.following_id AND followers.follower_id = ?
                WHERE users.username LIKE ? AND users.id != ? AND users.isDeleted != 1";

            $statement = $connection->prepare($query);
            $statement->execute([$loggedInUserId, $username, $loggedInUserId]);

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "PDOException: " . $e->getMessage();
            return [];
        }
    }

    public function getBarksWithHashtag($hashtag)
    {
        $connection = $this->db->getConnection();
        if (!$connection) {
            return [];
        }

        try {
            $loggedInUserId = $_SESSION['user_id'];
            $query = "SELECT tweet.*, users.username, users.isDeleted, users.id, followers.*, users_preferences.*
                  FROM tweet
                  JOIN users ON tweet.user_id = users.id
                  JOIN followers ON tweet.user_id = followers.following_id AND followers.follower_id = ?
                  JOIN users_preferences ON users.id = users_preferences.user_id
                  WHERE tweet.message LIKE ? AND users.id != ? AND users.isDeleted != 1";

            $statement = $connection->prepare($query);
            $statement->execute([$loggedInUserId, "%#$hashtag%", $loggedInUserId]);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
            return [];
        }
    }

    public function getBarksWithMention($mention)
    {
        $connection = $this->db->getConnection();
        if (!$connection) {
            return [];
        }

        try {
            $loggedInUserId = $_SESSION['user_id'];
            $query = "SELECT tweet.*, users.username, users.isDeleted, users.id, followers.*, users_preferences.*
                  FROM tweet
                  JOIN users ON tweet.user_id = users.id
                  JOIN followers ON tweet.user_id = followers.following_id AND followers.follower_id = ?
                  JOIN users_preferences ON users.id = users_preferences.user_id
                  WHERE tweet.message LIKE ? AND users.id != ? AND users.isDeleted != 1";

            $statement = $connection->prepare($query);
            $statement->execute([$loggedInUserId, "%@$mention%", $loggedInUserId]);
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
            return [];
        }
    }


    public function searchInWholeMessage($message)
    {
        $connection = $this->db->getConnection();
        if (!$connection) {
            return [];
        }

        try {
            $loggedInUserId = $_SESSION['user_id'];
            $query = "SELECT tweet.*, users.username, users.isDeleted, users_preferences.*
                  FROM tweet
                  JOIN users ON tweet.user_id = users.id
                  LEFT JOIN users_preferences ON users.id = users_preferences.user_id
                  LEFT JOIN followers ON users.id = followers.following_id AND followers.follower_id = ?
                  WHERE tweet.message LIKE ? AND tweet.user_id != ? 
                  AND users.isDeleted != 1 
                  AND followers.follower_id = ?";

            $statement = $connection->prepare($query);

            $messageParam = "%$message%";
            $statement->execute([$loggedInUserId, $messageParam, $loggedInUserId, $loggedInUserId]);

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "PDOException: " . $e->getMessage();
            return [];
        }
    }

    public function newBark($id, $bark)
    {
        try {
            $pdo = $this->db->getConnection();

            $query = "INSERT INTO tweet (user_id, message) VALUES (:user_id, :message)";
            $statement = $pdo->prepare($query);

            $statement->execute(array(':user_id' => $id, ':message' => $bark));

            return true;

        } catch (PDOException $e) {
            error_log("Error inserting bark: " . $e->getMessage());
            return false;
        }
    }

    public function followUser($followerId, $followingId)
    {
        try {
            $pdo = $this->db->getConnection();

            $query = "INSERT INTO followers (follower_id, following_id) VALUES (:follower_id, :following_id)";
            $statement = $pdo->prepare($query);

            $statement->execute(array(':follower_id' => $followerId, ':following_id' => $followingId));

            return true;

        } catch (PDOException $e) {
            error_log("Error deleting bark: " . $e->getMessage());
            return false;
        }
    }

    public function unfollowUser($followerId, $followingId)
    {
        try {
            $pdo = $this->db->getConnection();

            $query = "DELETE FROM followers WHERE follower_id = :follower_id AND following_id = :following_id";
            $statement = $pdo->prepare($query);

            $statement->execute(array(':follower_id' => $followerId, ':following_id' => $followingId));

            if ($statement->rowCount() > 0) {
                return true;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            error_log("Error unfollowing user: " . $e->getMessage());
            return false;
        }
    }

    function deleteBark($userId, $barkId)
    {
        $connection = $this->db->getConnection();
        if (!$connection) {
            return false;
        }

        try {
            $query = "DELETE FROM tweet WHERE user_id = ? AND id = ?";
            $statement = $connection->prepare($query);
            $statement->execute([$userId, $barkId]);

            if ($statement->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
            return false;
        }
    }

    public function getFollowingTweets($loggedInUserId)
    {
        $connection = $this->db->getConnection();
        if (!$connection) {
            return [];
        }

        try {
            $query = "SELECT tweet.*, users.username, users_preferences.*
                  FROM tweet
                  JOIN users ON tweet.user_id = users.id
                  JOIN followers ON tweet.user_id = followers.following_id AND followers.follower_id = ?
                    JOIN users_preferences ON users.id = users_preferences.user_id
                  WHERE users.id != ? AND users.isDeleted != 1";
            $statement = $connection->prepare($query);
            $statement->execute([$loggedInUserId, $loggedInUserId]);

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException $e) {
            echo "PDOException: " . $e->getMessage();
            return [];
        }
    }
}
