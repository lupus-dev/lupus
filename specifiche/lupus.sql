SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `achievements` (
  `id_achievement` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `achievement_name` varchar(50) NOT NULL,
  `unlock_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `chat` (
  `id_chat` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `id_user_from` int(11) NOT NULL,
  `dest` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `text` varchar(200) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `event` (
  `id_event` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `event_code` int(11) NOT NULL,
  `event_data` varchar(500) DEFAULT NULL,
  `day` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `game` (
  `id_game` int(11) NOT NULL,
  `id_room` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `game_name` varchar(10) NOT NULL,
  `game_descr` varchar(100) NOT NULL,
  `num_players` int(11) NOT NULL,
  `gen_info` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `notification` (
  `id_notification` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` varchar(200) NOT NULL,
  `link` varchar(200) DEFAULT NULL,
  `hidden` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `player` (
  `id_role` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `role` varchar(45) NOT NULL,
  `status` int(11) NOT NULL,
  `data` varchar(500) NOT NULL,
  `chat_info` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `room` (
  `id_room` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `room_name` varchar(10) NOT NULL,
  `room_descr` varchar(45) NOT NULL,
  `private` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `room_acl` (
  `id_room` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  `karma` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `surname` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vote` (
  `id_vote` int(11) NOT NULL,
  `id_game` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `day` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id_achievement`),
  ADD UNIQUE KEY `id_user_2` (`id_user`,`achievement_name`),
  ADD KEY `id_user` (`id_user`);

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

ALTER TABLE `notification`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `id_user` (`id_user`);

ALTER TABLE `player`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `id_game` (`id_game`,`id_user`) USING BTREE,
  ADD KEY `id_user` (`id_user`);

ALTER TABLE `room`
  ADD PRIMARY KEY (`id_room`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `room_name` (`room_name`);

ALTER TABLE `room_acl`
  ADD PRIMARY KEY (`id_room`,`id_user`),
  ADD KEY `id_user` (`id_user`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `vote`
  ADD PRIMARY KEY (`id_vote`),
  ADD KEY `id_game` (`id_game`,`id_user`),
  ADD KEY `id_user` (`id_user`);


ALTER TABLE `achievements`
  MODIFY `id_achievement` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `chat`
  MODIFY `id_chat` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `event`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `game`
  MODIFY `id_game` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `notification`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `player`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `room`
  MODIFY `id_room` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vote`
  MODIFY `id_vote` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`id_game`) REFERENCES `game` (`id_game`),
  ADD CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`id_user_from`) REFERENCES `user` (`id_user`);

ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`id_game`) REFERENCES `game` (`id_game`);

ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`id_room`) REFERENCES `room` (`id_room`);

ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE `player`
  ADD CONSTRAINT `player_ibfk_1` FOREIGN KEY (`id_game`) REFERENCES `game` (`id_game`),
  ADD CONSTRAINT `player_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `user` (`id_user`);

ALTER TABLE `room_acl`
  ADD CONSTRAINT `room_acl_ibfk_1` FOREIGN KEY (`id_room`) REFERENCES `room` (`id_room`),
  ADD CONSTRAINT `room_acl_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

ALTER TABLE `vote`
  ADD CONSTRAINT `vote_ibfk_1` FOREIGN KEY (`id_game`) REFERENCES `game` (`id_game`),
  ADD CONSTRAINT `vote_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
