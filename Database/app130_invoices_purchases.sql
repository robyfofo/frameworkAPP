-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Set 29, 2020 alle 10:59
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
-- Struttura della tabella `app130_invoices_purchases`
--

CREATE TABLE `app130_invoices_purchases` (
  `id` int(8) NOT NULL,
  `users_id` int(8) NOT NULL DEFAULT '0',
  `id_customer` int(8) NOT NULL DEFAULT '0',
  `dateins` date NOT NULL,
  `datesca` date NOT NULL,
  `number` varchar(20) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `type` int(1) NOT NULL DEFAULT '0',
  `customer_ragione_sociale` varchar(255) DEFAULT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `customer_surname` varchar(50) DEFAULT NULL,
  `customer_street` varchar(100) DEFAULT NULL,
  `customer_city` varchar(100) DEFAULT NULL,
  `customer_zip_code` varchar(10) DEFAULT NULL,
  `customer_province` varchar(100) DEFAULT NULL,
  `customer_state` varchar(100) DEFAULT NULL,
  `customer_telephone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_fax` varchar(20) DEFAULT NULL,
  `customer_partita_iva` varchar(50) DEFAULT NULL,
  `customer_codice_fiscale` varchar(50) DEFAULT NULL,
  `customer_pec` varchar(255) DEFAULT NULL,
  `customer_sid` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `tax` int(2) NOT NULL DEFAULT '0',
  `rivalsa` int(2) NOT NULL DEFAULT '0',
  `pagata` int(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `app130_invoices_purchases`
--
ALTER TABLE `app130_invoices_purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_customer` (`id_customer`),
  ADD KEY `users_id` (`users_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `app130_invoices_purchases`
--
ALTER TABLE `app130_invoices_purchases`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
