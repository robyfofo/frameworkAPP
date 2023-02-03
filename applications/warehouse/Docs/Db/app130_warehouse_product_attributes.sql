-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Set 12, 2020 alle 15:30
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
-- Struttura della tabella `app130_warehouse_product_attributes`
--

CREATE TABLE `app130_warehouse_product_attributes` (
  `id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `products_attribute_types_id` int(1) NOT NULL DEFAULT '0',
  `code` varchar(100) DEFAULT NULL,
  `value_string` varchar(100) DEFAULT NULL,
  `value_int` int(8) DEFAULT '0',
  `value_float` float(10,2) DEFAULT NULL,
  `value_type` varchar(10) DEFAULT NULL,
  `quantity` int(8) DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `app130_warehouse_product_attributes`
--
ALTER TABLE `app130_warehouse_product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tbl451_ec_product_attributes_products_id_IDX` (`products_id`) USING BTREE,
  ADD KEY `tbl451_ec_product_attributes_products_attribute_type_IDX` (`products_attribute_types_id`) USING BTREE,
  ADD KEY `active` (`active`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `app130_warehouse_product_attributes`
--
ALTER TABLE `app130_warehouse_product_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
