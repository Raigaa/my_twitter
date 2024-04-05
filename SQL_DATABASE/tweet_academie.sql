-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : lun. 26 fév. 2024 à 14:42
-- Version du serveur : 5.7.39
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tweet_academie`
--

-- --------------------------------------------------------

--
-- Structure de la table `followers`
--

CREATE TABLE `followers` (
  `id` int(10) UNSIGNED NOT NULL,
  `follower_id` int(10) UNSIGNED DEFAULT NULL,
  `following_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `hashtag`
--

CREATE TABLE `hashtag` (
  `id` int(10) UNSIGNED NOT NULL,
  `tweet_id` int(10) UNSIGNED DEFAULT NULL,
  `rtweet_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `path` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `tweet_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `retweets`
--

CREATE TABLE `retweets` (
  `id` int(10) UNSIGNED NOT NULL,
  `tweet_id` int(10) UNSIGNED NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(10) UNSIGNED NOT NULL,
  `references_tweet_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tweet`
--

CREATE TABLE `tweet` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `message` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Ce champs contiendras  du JSON ou du base 64',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tweet_comment`
--

CREATE TABLE `tweet_comment` (
  `id` int(10) UNSIGNED NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `message` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `tweet_id` int(10) UNSIGNED DEFAULT NULL,
  `rtweet_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `genre` enum('man','woman','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthdate` date NOT NULL,
  `isActive` tinyint(4) NOT NULL DEFAULT '1',
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `genre`, `email`, `birthdate`, `isActive`, `username`, `firstname`, `lastname`, `password_hash`, `isDeleted`, `created_at`, `updated_at`) VALUES
(1, NULL, 'test@test.com', '1999-09-19', 1, 'epitechTest', 'test', 'Test', 'epitech', 0, '2024-02-26 14:40:14', '2024-02-26 14:40:47');

--
-- Déclencheurs `users`
--
DELIMITER $$
CREATE TRIGGER `trigger_age_under_13` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    DECLARE age INT;
    SET age = YEAR(CURRENT_DATE()) - YEAR(NEW.birthdate);
    IF age < 13 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Users must be at least 13 years old.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `users_messages`
--

CREATE TABLE `users_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `recipient_id` int(10) UNSIGNED NOT NULL,
  `messages` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users_preferences`
--

CREATE TABLE `users_preferences` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `theme` tinyint(1) NOT NULL DEFAULT '0',
  `bio` longtext COLLATE utf8mb4_unicode_ci,
  `profile_picture` longtext COLLATE utf8mb4_unicode_ci,
  `profile_banner` longtext COLLATE utf8mb4_unicode_ci,
  `localisation` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `followers_users_id_fk2` (`following_id`),
  ADD KEY `followers_users_id_fk` (`follower_id`);

--
-- Index pour la table `hashtag`
--
ALTER TABLE `hashtag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hashtag_tweet_id_fk` (`tweet_id`),
  ADD KEY `hashtag_retweets_id_fk` (`rtweet_id`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_tweet_id_fk` (`tweet_id`),
  ADD KEY `media_users_id_fk` (`user_id`);

--
-- Index pour la table `retweets`
--
ALTER TABLE `retweets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `retweets_users_id_fk` (`user_id`);

--
-- Index pour la table `tweet`
--
ALTER TABLE `tweet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tweet_users_id_fk` (`user_id`);

--
-- Index pour la table `tweet_comment`
--
ALTER TABLE `tweet_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tweet_comment_tweet_id_fk` (`tweet_id`),
  ADD KEY `tweet_comment_user_fk` (`user_id`),
  ADD KEY `tweet_comment_retweets_id_fk` (`rtweet_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_unique_email` (`email`);

--
-- Index pour la table `users_messages`
--
ALTER TABLE `users_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_messages_users_id_fk` (`sender_id`),
  ADD KEY `users_messages_users_id_fk2` (`recipient_id`);

--
-- Index pour la table `users_preferences`
--
ALTER TABLE `users_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_preferences_users_id_fk` (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `hashtag`
--
ALTER TABLE `hashtag`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `retweets`
--
ALTER TABLE `retweets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tweet`
--
ALTER TABLE `tweet`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `tweet_comment`
--
ALTER TABLE `tweet_comment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `users_messages`
--
ALTER TABLE `users_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users_preferences`
--
ALTER TABLE `users_preferences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_users_id_fk` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `followers_users_id_fk2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `hashtag`
--
ALTER TABLE `hashtag`
  ADD CONSTRAINT `hashtag_retweets_id_fk` FOREIGN KEY (`rtweet_id`) REFERENCES `retweets` (`id`),
  ADD CONSTRAINT `hashtag_tweet_id_fk` FOREIGN KEY (`tweet_id`) REFERENCES `tweet` (`id`);

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_tweet_id_fk` FOREIGN KEY (`tweet_id`) REFERENCES `tweet` (`id`),
  ADD CONSTRAINT `media_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `retweets`
--
ALTER TABLE `retweets`
  ADD CONSTRAINT `retweets_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `tweet`
--
ALTER TABLE `tweet`
  ADD CONSTRAINT `tweet_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `tweet_comment`
--
ALTER TABLE `tweet_comment`
  ADD CONSTRAINT `tweet_comment_retweets_id_fk` FOREIGN KEY (`rtweet_id`) REFERENCES `retweets` (`id`),
  ADD CONSTRAINT `tweet_comment_tweet_id_fk` FOREIGN KEY (`tweet_id`) REFERENCES `tweet` (`id`),
  ADD CONSTRAINT `tweet_comment_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `users_messages`
--
ALTER TABLE `users_messages`
  ADD CONSTRAINT `users_messages_users_id_fk` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `users_messages_users_id_fk2` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `users_preferences`
--
ALTER TABLE `users_preferences`
  ADD CONSTRAINT `users_preferences_users_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
