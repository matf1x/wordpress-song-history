CREATE TABLE `songHistory` (
  `id` int(10) NOT NULL,
  `trackGuid` varchar(36) NOT NULL,
  `start` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `songLibrary` (
  `trackGuid` varchar(36) NOT NULL,
  `artist` text NOT NULL,
  `title` text NOT NULL,
  `cover` longtext NOT NULL,
  `spotify` text,
  `apple` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `songHistory`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `songLibrary`
  ADD PRIMARY KEY (`trackGuid`);

ALTER TABLE `songHistory`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;