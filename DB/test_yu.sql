-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 10 juin 2024 à 17:43
-- Version du serveur :  10.5.23-MariaDB-0+deb11u1
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `test_yu`
--

-- --------------------------------------------------------

--
-- Structure de la table `matche`
--

CREATE TABLE `matche` (
  `match_id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) NOT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `isFinish` tinyint(1) NOT NULL DEFAULT 0,
  `callJudge` tinyint(1) NOT NULL DEFAULT 0,
  `num_table` int(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `matche`
--

INSERT INTO `matche` (`match_id`, `round_id`, `player1_id`, `player2_id`, `winner_id`, `isFinish`, `callJudge`, `num_table`) VALUES
(1, 1, 1, 2, 1, 1, 0, 1),
(2, 1, 3, 4, 3, 1, 0, 2),
(3, 1, 5, 6, 5, 1, 0, 3),
(4, 2, 1, 3, NULL, 0, 1, 1),
(5, 2, 5, 2, 2, 1, 0, 2),
(6, 2, 4, 6, 6, 1, 0, 3);

-- --------------------------------------------------------

--
-- Structure de la table `round`
--

CREATE TABLE `round` (
  `round_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `round_number` int(11) NOT NULL,
  `round_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `round`
--

INSERT INTO `round` (`round_id`, `tournament_id`, `round_number`, `round_date`) VALUES
(1, 1, 1, '2024-05-27'),
(2, 1, 2, '2024-05-27');

-- --------------------------------------------------------

--
-- Structure de la table `tournament`
--

CREATE TABLE `tournament` (
  `tournament_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `tournament`
--

INSERT INTO `tournament` (`tournament_id`, `name`, `date`, `status`) VALUES
(1, 'WCQ Yu-Gi-Oh', '2024-05-28', 'en cours'),
(2, 'Pokemon YCS', '2024-06-27', 'en cours');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Zerzeusse', 'ggg', 'Gregory', '2024-05-27 08:41:47'),
(2, 'Theo', 'eee', 'test', '2024-05-27 08:41:47'),
(3, 'p3', 'p3', 'p3', '2024-05-27 17:14:17'),
(4, 'p4', 'p4', 'p4', '2024-05-27 17:14:17'),
(5, 'p5', 'p5', 'p5', '2024-05-27 17:14:17'),
(6, 'p6', 'p6', 'p6', '2024-05-27 17:14:18');

-- --------------------------------------------------------

--
-- Structure de la table `userscore`
--

CREATE TABLE `userscore` (
  `user_score_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `userscore`
--

INSERT INTO `userscore` (`user_score_id`, `user_id`, `tournament_id`, `score`) VALUES
(1, 1, 1, 3),
(2, 2, 1, 2),
(3, 3, 1, 3),
(4, 4, 1, 0),
(5, 5, 1, 6),
(6, 6, 1, 0),
(9, 1, 2, 0),
(11, 1, 2, 0),
(12, 1, 2, 0);

-- --------------------------------------------------------

--
-- Structure de la table `usertournament`
--

CREATE TABLE `usertournament` (
  `user_tournament_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `usertournament`
--

INSERT INTO `usertournament` (`user_tournament_id`, `user_id`, `tournament_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(9, 1, 2),
(11, 1, 2),
(12, 1, 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `matche`
--
ALTER TABLE `matche`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `round_id` (`round_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`),
  ADD KEY `winner_id` (`winner_id`);

--
-- Index pour la table `round`
--
ALTER TABLE `round`
  ADD PRIMARY KEY (`round_id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Index pour la table `tournament`
--
ALTER TABLE `tournament`
  ADD PRIMARY KEY (`tournament_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Index pour la table `userscore`
--
ALTER TABLE `userscore`
  ADD PRIMARY KEY (`user_score_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- Index pour la table `usertournament`
--
ALTER TABLE `usertournament`
  ADD PRIMARY KEY (`user_tournament_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tournament_id` (`tournament_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `matche`
--
ALTER TABLE `matche`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `round`
--
ALTER TABLE `round`
  MODIFY `round_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `tournament`
--
ALTER TABLE `tournament`
  MODIFY `tournament_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `userscore`
--
ALTER TABLE `userscore`
  MODIFY `user_score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `usertournament`
--
ALTER TABLE `usertournament`
  MODIFY `user_tournament_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `matche`
--
ALTER TABLE `matche`
  ADD CONSTRAINT `matche_ibfk_1` FOREIGN KEY (`round_id`) REFERENCES `round` (`round_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matche_ibfk_2` FOREIGN KEY (`player1_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `matche_ibfk_3` FOREIGN KEY (`player2_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `matche_ibfk_4` FOREIGN KEY (`winner_id`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `round`
--
ALTER TABLE `round`
  ADD CONSTRAINT `round_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`tournament_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `userscore`
--
ALTER TABLE `userscore`
  ADD CONSTRAINT `userscore_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `userscore_ibfk_2` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`tournament_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `usertournament`
--
ALTER TABLE `usertournament`
  ADD CONSTRAINT `usertournament_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usertournament_ibfk_2` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`tournament_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
