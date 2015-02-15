-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 06. Oktober 2012 um 17:11
-- Server Version: 5.5.22
-- PHP-Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `db_exportdata`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tb_addresses`
--

CREATE TABLE IF NOT EXISTS `tb_addresses` (
  `address_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `letter_address` varchar(255) NOT NULL,
  PRIMARY KEY (`address_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `tb_addresses`
--

INSERT INTO `tb_addresses` (`address_id`, `letter_address`) VALUES
(1, 'Frau Jeanine Muster<br/>Beispielstrasse 00<br/>0000 Stadt'),
(2, 'Signora Gianna Esempio<br/>Via Esempio 00<br/>0000 Città '),
(3, 'Mister Jack Example<br/>Example Road 00<br/>0000 City'),
(4, 'Madame Jacqueline Exemple<br/>Avenue Exemple 00<br/>0000 Ville');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tb_letters`
--

CREATE TABLE IF NOT EXISTS `tb_letters` (
  `letter_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL DEFAULT '0',
  `letter_date` varchar(255) NOT NULL,
  `address_id` int(11) NOT NULL DEFAULT '0',
  `letter_email` varchar(255) NOT NULL,
  `letter_concerns` varchar(255) NOT NULL,
  `letter_dear` varchar(255) NOT NULL,
  `letter_content` text NOT NULL,
  `letter_greetings` varchar(255) NOT NULL,
  `letter_ownames` varchar(255) NOT NULL DEFAULT '0',
  `letter_attachments` varchar(255) NOT NULL DEFAULT '0',
  `letter_url` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`letter_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `tb_letters`
--

INSERT INTO `tb_letters` (`letter_id`, `sender_id`, `letter_date`, `address_id`, `letter_email`, `letter_concerns`, `letter_dear`, `letter_content`, `letter_greetings`, `letter_ownames`, `letter_attachments`, `letter_url`) VALUES
(1, 1, 'Stadt,<br/>den 01.10.2012', 1, 'test@testproject.ch', 'Fast Export nach Windows Word Document', 'Sehr geehrte Frau Muster', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. üöäèéà?()/&%ç*"+.Punkt.', 'Mit freundlichen Grüssen', 'Vorname Name', 'Beilagen erwà¤hnt', 'www.testproject.ch'),
(2, 2, 'Città ,<br/>il 02.10.2012', 2, 'test@testedit.ch', 'Working Export da Windows Word Document', 'Caro Signora Esempio', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. üöäèéà?()/&%ç*"+.Punkt.', 'Distinti saluti', 'Prenome Cognome', 'Allegato', 'www.testedit.ch'),
(3, 1, 'City,<br/>03.10.2012', 3, 'test@testproject.ch', 'Test Export To Windows Word Document', 'Dear Mister Example', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. üöäèéà?()/&%ç*"+.Punkt.', 'With my best regards', 'Firstname Lastname', 'Enclosure', 'www.testproject.ch'),
(4, 4, 'Ville,<br/>le 03.10.2012', 4, 'test@testedit.ch', 'Working Contact Export à  Windows Word Document', 'Chère Madame Exemple', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. üöäèéà?()/&%ç*"+.Punkt.', 'Avec mes meilleures salutations', 'Prénom Nom de famille', 'Suppléments mentionés', 'www.testedit.ch');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tb_orders`
--

CREATE TABLE IF NOT EXISTS `tb_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '0',
  `singleprice` double NOT NULL DEFAULT '0',
  `orderdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

--
-- Daten für Tabelle `tb_orders`
--

INSERT INTO `tb_orders` (`id`, `firstname`, `lastname`, `email`, `number`, `singleprice`, `orderdate`) VALUES
(1, 'Hans', 'Tester', 'hans@test.de', 4, 17.5, '2003-06-12 00:00:00'),
(2, 'Claude', 'Marbeland', 'claude@marbeland.ch', 12, 14.5, '2012-09-20 14:15:32'),
(3, 'Claudita', 'Marblita', 'claudita@marblita.ch', 2, 22.6, '2012-09-21 14:16:42'),
(4, 'Claudiae', 'Rominae', 'claudiae@rominae.ch', 24, 45.9, '2012-09-22 14:21:51'),
(5, 'Claude', 'Benseler', 'claude@benseler.ch', 13, 56, '2012-09-22 14:41:59'),
(6, 'Clanga', 'van Glossing', 'clanga@vanglossing.nl', 43, 18.9, '2012-09-22 14:46:32'),
(7, 'Clara', 'Oberhofen', 'clara@oberhofen.ch', 12, 14.8, '2012-09-22 15:21:52'),
(8, 'Ratzke', 'Ramires', 'info@ramirez.es', 19, 116.55, '2012-09-22 17:39:29'),
(9, 'Henri', 'de Montagne', 'henri@demontagne.fr', 21, 45.7, '2012-09-23 17:41:13'),
(10, 'Mimi', 'Rogers', 'mini@rogers.ch', 76, 88.9, '2012-09-23 17:49:13'),
(31, 'Clara', 'Oberhofen', 'clara@oberhofen.ch', 12, 14.8, '2012-09-22 15:21:52'),
(32, 'Ratzke', 'Ramires', 'info@ramirez.ch', 19, 116.55, '2012-09-22 17:39:29'),
(33, 'Hendrik', 'van Bergen', 'hendrik@vanbergen.nl', 21, 45.7, '2012-09-23 17:41:13'),
(34, 'Mimi', 'Rogers', 'mini@rogers.ch', 76, 88.9, '2012-09-23 17:49:13');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tb_reports`
--

CREATE TABLE IF NOT EXISTS `tb_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL DEFAULT '0',
  `mission_id` int(11) NOT NULL DEFAULT '0',
  `mission_priorite` tinyint(4) NOT NULL DEFAULT '0',
  `mission_accepted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `report_title` varchar(255) NOT NULL,
  `report` text NOT NULL,
  `report_links` varchar(255) NOT NULL,
  `report_path` varchar(255) NOT NULL,
  `report_sendto` int(11) NOT NULL DEFAULT '0',
  `first_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`report_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `tb_reports`
--

INSERT INTO `tb_reports` (`report_id`, `member_id`, `mission_id`, `mission_priorite`, `mission_accepted`, `report_title`, `report`, `report_links`, `report_path`, `report_sendto`, `first_created`, `last_created`) VALUES
(1, 12, 4, 1, '2012-06-30 00:00:00', 'My latest report', 'Lorem ipsum dolor sit amet, consectetuer adipiscing eliät, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autemö vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan etü iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.\r\nLorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.', 'http://www.myreport1.ch/|http://www.myreport2.ch/|http://www.myreport3.ch/|http://www.myreport4.ch/', 'c:/xampp/htdocs/CreateDocuments/|c:/xampp/htdocs/CreateDocuments/MyCreateDocsHandler/createxml', 1, '2012-06-30 00:00:00', '2012-07-02 00:00:00'),
(2, 5, 6, 1, '2012-07-01 00:00:00', 'My further report', 'Lorem ipsum dolor sit amet, consectetuer adipiscing eliät, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.&#139;br/&#155;Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autemä vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan etü iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.&#139;br/&#155;Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.', 'http://www.myfreport1.ch/|http://www.myfreport2.ch/|http://www.myfreport3.ch/|http://www.myfreport4.ch/', 'c:/xampp/htdocs/CreateDocuments', 0, '2012-07-02 00:00:00', '0000-00-00 00:00:00'),
(3, 9, 7, 2, '2012-07-02 00:00:00', 'My newest report', 'Text follows', 'http://www.mynreport1.ch/|http://www.myneport2.ch/|http://www.mynreport3.ch/|http://www.mynreport4.ch/', 'c:/xampp/htdocs/CreateDocuments/MyCreateDocsHandler/docs', 0, '2012-07-02 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tb_sender`
--

CREATE TABLE IF NOT EXISTS `tb_sender` (
  `sender_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `letter_sender` varchar(255) NOT NULL,
  PRIMARY KEY (`sender_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `tb_sender`
--

INSERT INTO `tb_sender` (`sender_id`, `letter_sender`) VALUES
(1, 'TEST!project<br/>Hans Muster<br/>Beispielstrasse 00<br/>0000 Stadt<br/>Tel. +41 44 322 47 74<br/>Mobil 076 730 57 83'),
(2, 'Great Test Portal<br/>c/o MyCompany<br/>MyFirstname MyLastname<br/>Example Street 00<br/>0000 City<br/>Tel. +44 44 555 77 99<br/>Mobil 066 333 77 88'),
(3, 'MyFirstname MyLastname</br/>Examples Street 00<br/>0000 City<br/>Tel. +49 12 222 77 74<br/>Mobil 099 736 87 89'),
(4, 'MonPrénom NomdeFamille<br/>Avenue Exemple 00<br/>0000 Ville<br/>Tel. +49 12 222 77 74<br/>Mobil 099 736 87 89');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tb_serialletter`
--

CREATE TABLE IF NOT EXISTS `tb_serialletter` (
  `serial_id` int(11) NOT NULL AUTO_INCREMENT,
  `letter_content` text NOT NULL,
  PRIMARY KEY (`serial_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `tb_serialletter`
--

INSERT INTO `tb_serialletter` (`serial_id`, `letter_content`) VALUES
(1, 'Start Serial Letter. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Üüöä?()/&%çç*"+.End Serial Letter');

