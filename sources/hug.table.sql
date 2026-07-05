CREATE TABLE `hug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `recipientKey` char(8) NOT NULL,
  `senderKey` char(12) NOT NULL,
  `viewCount` int(11) NOT NULL DEFAULT 0,
  `acceptCount` int(11) NOT NULL DEFAULT 0,
  `returnedCount` int(11) NOT NULL DEFAULT 0,
  `infoDisplayed` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipientKey` (`recipientKey`),
  UNIQUE KEY `senderKey` (`senderKey`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
