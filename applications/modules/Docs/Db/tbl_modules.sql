-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Set 05, 2019 alle 11:20
-- Versione del server: 5.7.27-0ubuntu0.18.04.1
-- Versione PHP: 7.2.19-0ubuntu0.18.04.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `2019_websync_framework_sito354`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `tbl_modules`
--

CREATE TABLE `tbl_modules` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `content` text,
  `code_menu` text NOT NULL,
  `ordering` int(8) NOT NULL DEFAULT '0',
  `section` int(1) NOT NULL,
  `help_small` varchar(255) NOT NULL,
  `help` text NOT NULL,
  `active` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tbl_modules`
--

INSERT INTO `tbl_modules` (`id`, `name`, `label`, `alias`, `content`, `code_menu`, `ordering`, `section`, `help_small`, `help`, `active`) VALUES
(1, 'users', 'Users', 'user', 'Il modulo che gestisce gli utenti', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-users fa-fw\\\"><\\/i>\",\"label\":\"%LABEL%\"}', 2, 0, 'Aiuto breve del modulo users', '<p>Aiuto del modulo users</p>', 1),
(2, 'levels', 'User levels', 'levels', 'Il modulo che gestisce i livelli utente', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-users fa-fw\\\"><\\/i>\",\"label\":\"%LABEL%\"}', 3, 0, 'aiuto breve livelli utente', '<p>aiuto livelli utente</p>', 1),
(3, 'home', 'Home', 'home', 'La pagina home di ogni utente', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-home fa-fw\\\"><\\/i>\",\"label\":\"%LABEL%\"}', 1, 0, 'Aiuto breve modulo Home', '<p>Aiuto modulo Home</p>', 1),
(4, 'menu', 'Menu', 'menu', 'Il modulo che gestisce il menu del sito', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-bars fa-fw\\\" ><\\/i>\",\"label\":\"%LABEL%\"}', 5, 0, '', '', 1),
(5, 'pages', 'Pages', 'pages', 'Il modulo che gestisce le pagine dinamiche del sito', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-file-text-o fa-fw\\\" ><\\/i>\",\"label\":\"%LABEL%\"}', 4, 0, '', '', 1),
(6, 'pagetemplates', 'Page templates', 'templates', 'Il modulo che gestisce i template delle pagine del sito', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-desktop fa-fw\\\"><\\/i>\",\"label\":\"%LABEL%\"}', 1, 2, 'aiuto breve template', '<p>aiuto template</p>', 1),
(7, 'tools', 'Strumenti', 'tools', 'La sezione strumenti del sito', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-cog fa-fw\\\" ><\\/i>\",\"label\":\"%LABEL%\"}', 2, 2, 'Aiuto breve strumenti', '<p>Aiuto strumenti</p>', 0),
(8, 'modules', 'Moduli', 'modules', 'Il modulo per installare e configurare i moduli del sito', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-cogs fa-fw\\\" ><\\/i>\",\"label\":\"%LABEL%\"}', 1, 3, 'Aiuto breve del modulo Moduli', '<p>Aiuto completo del modulo Moduli</p>', 1),
(9, 'help', 'Admin - Help', 'help', 'Il modulo che gestisce l\'aiuto generale della amministrazione', '{\"name\":\"%NAME%\",\"icon\":\"<i class=\\\"fa fa-question fa-fw\\\"><\\/i>\",\"label\":\"%LABEL%\"}', 2, 3, '', '', 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `tbl_modules`
--
ALTER TABLE `tbl_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `active` (`active`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `tbl_modules`
--
ALTER TABLE `tbl_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
