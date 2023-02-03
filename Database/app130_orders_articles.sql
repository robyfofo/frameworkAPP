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
-- Struttura della tabella `app130_orders_articles`
--

CREATE TABLE `app130_orders_articles` (
  `id` int(8) NOT NULL,
  `users_id` int(8) NOT NULL DEFAULT '0',
  `orders_id` int(8) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `price_unity` varchar(10) NOT NULL,
  `tax` varchar(5) NOT NULL,
  `price_tax` float(10,2) NOT NULL,
  `price_total` float(10,2) NOT NULL,
  `quantity` float(4,1) NOT NULL,
  `created` datetime NOT NULL,
  `active` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `app130_orders_articles`
--
ALTER TABLE `app130_orders_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `orders_id` (`orders_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `app130_orders_articles`
--
ALTER TABLE `app130_orders_articles`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
