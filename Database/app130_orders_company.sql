-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Ott 01, 2020 alle 17:49
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
-- Struttura della tabella `app130_orders_company`
--

CREATE TABLE `app130_orders_company` (
  `id` int(8) NOT NULL,
  `orders_id` int(8) NOT NULL DEFAULT '0',
  `company_ragione_sociale` varchar(255) DEFAULT NULL,
  `company_name` varchar(50) DEFAULT NULL,
  `company_surname` varchar(50) DEFAULT NULL,
  `company_street` varchar(100) DEFAULT NULL,
  `company_comune` varchar(150) DEFAULT NULL,
  `company_zip_code` varchar(10) DEFAULT NULL,
  `company_provincia` varchar(150) DEFAULT NULL,
  `company_nation` varchar(150) DEFAULT NULL,
  `company_telephone` varchar(20) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `company_fax` varchar(20) DEFAULT NULL,
  `company_partita_iva` varchar(50) DEFAULT NULL,
  `company_codice_fiscale` varchar(50) DEFAULT NULL,
  `company_pec` varchar(255) DEFAULT NULL,
  `company_sid` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `app130_orders_company`
--
ALTER TABLE `app130_orders_company`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_customer` (`orders_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `app130_orders_company`
--
ALTER TABLE `app130_orders_company`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
