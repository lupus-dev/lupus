SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `chat` (
  `id_chat` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `id_user_from` int(11) NOT NULL,
  `dest` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `text` varchar(200) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event` (
  `id_event` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `event_code` int(11) NOT NULL,
  `event_data` varchar(500) NOT NULL,
  `day` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `game` (
  `id_game` int(11) NOT NULL,
  `id_room` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `game_name` varchar(10) NOT NULL,
  `game_descr` varchar(100) NOT NULL,
  `num_players` int(11) NOT NULL,
  `gen_info` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `player` (
  `id_role` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `role` varchar(45) NOT NULL,
  `status` int(11) NOT NULL,
  `data` varchar(500) NOT NULL,
  `chat_info` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `room` (
  `id_room` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `room_name` varchar(10) NOT NULL,
  `room_descr` varchar(45) NOT NULL,
  `private` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(45) NOT NULL,
  `level` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `surname` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user` (`id_user`, `username`, `password`, `level`, `name`, `surname`) VALUES
(1, 'root', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 10, '', ''),
(2, 'user1', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 2, '', ''),
(3, 'user2', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 2, '', ''),
(4, 'user3', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 2, '', ''),
(5, 'user4', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 2, '', ''),
(6, 'user5', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 2, '', ''),
(7, 'user6', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 2, '', ''),
(8, 'user7', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 2, '', '');

CREATE TABLE `vote` (
  `id_vote` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `day` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `chat`
  ADD PRIMARY KEY (`id_chat`),
  ADD KEY `id_game` (`id_game`),
  ADD KEY `id_user_from` (`id_user_from`),
  ADD KEY `dest` (`dest`),
  ADD KEY `group` (`group`);

ALTER TABLE `event`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `id_game` (`id_game`);

ALTER TABLE `game`
  ADD PRIMARY KEY (`id_game`),
  ADD KEY `id_room` (`id_room`),
  ADD KEY `game_name` (`game_name`);

ALTER TABLE `player`
  ADD PRIMARY KEY (`id_role`),
  ADD KEY `id_game` (`id_game`,`id_user`);

ALTER TABLE `room`
  ADD PRIMARY KEY (`id_room`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `room_name` (`room_name`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `vote`
  ADD PRIMARY KEY (`id_vote`),
  ADD KEY `id_game` (`id_game`,`id_user`);


ALTER TABLE `chat`
  MODIFY `id_chat` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `event`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `game`
  MODIFY `id_game` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `player`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `room`
  MODIFY `id_room` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vote`
  MODIFY `id_vote` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
