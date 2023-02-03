-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Set 21, 2020 alle 15:02
-- Versione del server: 5.7.31-0ubuntu0.18.04.1
-- Versione PHP: 7.2.24-0ubuntu0.18.04.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phprojekt.altervista_frameworkapp130`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `app130_location_province`
--

CREATE TABLE `app130_location_province` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `targa` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` int(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dump dei dati per la tabella `app130_location_province`
--

INSERT INTO `app130_location_province` (`id`, `nome`, `targa`, `active`) VALUES
(1, 'Torino', 'TO', NULL),
(2, 'Vercelli', 'VC', NULL),
(3, 'Novara', 'NO', NULL),
(4, 'Cuneo', 'CN', NULL),
(5, 'Asti', 'AT', NULL),
(6, 'Alessandria', 'AL', NULL),
(96, 'Biella', 'BI', NULL),
(103, 'Verbano-Cusio-Ossola', 'VB', 1),
(7, 'Valle d\'Aosta', 'AO', 1),
(12, 'Varese', 'VA', 1),
(13, 'Como', 'CO', NULL),
(14, 'Sondrio', 'SO', NULL),
(15, 'Milano', 'MI', 1),
(16, 'Bergamo', 'BG', NULL),
(17, 'Brescia', 'BS', NULL),
(18, 'Pavia', 'PV', NULL),
(19, 'Cremona', 'CR', NULL),
(20, 'Mantova', 'MN', NULL),
(97, 'Lecco', 'LC', NULL),
(98, 'Lodi', 'LO', NULL),
(21, 'Bolzano', 'BZ', NULL),
(22, 'Trento', 'TN', NULL),
(23, 'Verona', 'VR', 1),
(24, 'Vicenza', 'VI', NULL),
(25, 'Belluno', 'BL', NULL),
(26, 'Treviso', 'TV', NULL),
(27, 'Venezia', 'VE', NULL),
(28, 'Padova', 'PD', NULL),
(29, 'Rovigo', 'RO', NULL),
(30, 'Udine', 'UD', NULL),
(31, 'Gorizia', 'GO', NULL),
(32, 'Trieste', 'TS', NULL),
(93, 'Pordenone', 'PN', NULL),
(8, 'Imperia', 'IM', NULL),
(9, 'Savona', 'SV', NULL),
(10, 'Genova', 'GE', NULL),
(11, 'La Spezia', 'SP', NULL),
(33, 'Piacenza', 'PC', NULL),
(34, 'Parma', 'PR', NULL),
(35, 'Reggio nell\'Emilia', 'RE', NULL),
(36, 'Modena', 'MO', NULL),
(37, 'Bologna', 'BO', NULL),
(38, 'Ferrara', 'FE', NULL),
(39, 'Ravenna', 'RA', NULL),
(40, 'Forlì-Cesena', 'FC', 0),
(99, 'Rimini', 'RN', NULL),
(45, 'Massa-Carrara', 'MS', NULL),
(46, 'Lucca', 'LU', NULL),
(47, 'Pistoia', 'PT', NULL),
(48, 'Firenze', 'FI', NULL),
(49, 'Livorno', 'LI', NULL),
(50, 'Pisa', 'PI', NULL),
(51, 'Arezzo', 'AR', NULL),
(52, 'Siena', 'SI', NULL),
(53, 'Grosseto', 'GR', NULL),
(100, 'Prato', 'PO', NULL),
(54, 'Perugia', 'PG', NULL),
(55, 'Terni', 'TR', NULL),
(41, 'Pesaro Urbino', 'PU', NULL),
(42, 'Ancona', 'AN', NULL),
(43, 'Macerata', 'MC', NULL),
(44, 'Ascoli Piceno', 'AP', NULL),
(56, 'Viterbo', 'VT', NULL),
(57, 'Rieti', 'RI', NULL),
(58, 'Roma', 'ROMA', NULL),
(59, 'Latina', 'LT', NULL),
(60, 'Frosinone', 'FR', NULL),
(66, 'L\'Aquila', 'AQ', NULL),
(67, 'Teramo', 'TE', NULL),
(68, 'Pescara', 'PE', NULL),
(69, 'Chieti', 'CH', NULL),
(70, 'Campobasso', 'CB', NULL),
(94, 'Isernia', 'IS', NULL),
(61, 'Caserta', 'CE', NULL),
(62, 'Benevento', 'BN', 1),
(63, 'Napoli', 'NA', NULL),
(64, 'Avellino', 'AV', 1),
(65, 'Salerno', 'SA', NULL),
(71, 'Foggia', 'FG', NULL),
(72, 'Bari', 'BA', NULL),
(73, 'Taranto', 'TA', NULL),
(74, 'Brindisi', 'BR', NULL),
(75, 'Lecce', 'LE', NULL),
(76, 'Potenza', 'PZ', NULL),
(77, 'Matera', 'MT', NULL),
(78, 'Cosenza', 'CS', NULL),
(79, 'Catanzaro', 'CZ', NULL),
(80, 'Reggio di Calabria', 'RC', NULL),
(101, 'Crotone', 'KR', NULL),
(102, 'Vibo Valentia', 'VV', NULL),
(81, 'Trapani', 'TP', NULL),
(82, 'Palermo', 'PA', NULL),
(83, 'Messina', 'ME', NULL),
(84, 'Agrigento', 'AG', NULL),
(85, 'Caltanissetta', 'CL', NULL),
(86, 'Enna', 'EN', NULL),
(87, 'Catania', 'CT', NULL),
(88, 'Ragusa', 'RG', NULL),
(89, 'Siracusa', 'SR', NULL),
(90, 'Sassari', 'SS', NULL),
(91, 'Nuoro', 'NU', NULL),
(92, 'Cagliari', 'CA', NULL),
(95, 'Oristano', 'OR', NULL),
(104, 'Olbia-Tempio', 'OT', NULL),
(105, 'Ogliastra', 'OG', NULL),
(106, 'Medio Campidano', 'VS', NULL),
(107, 'Carbonia-Iglesias', 'CI', NULL),
(109, 'Cesena', 'CS', NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `app130_location_province`
--
ALTER TABLE `app130_location_province`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `app130_location_province`
--
ALTER TABLE `app130_location_province`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
