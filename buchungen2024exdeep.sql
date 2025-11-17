-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 17. Nov 2025 um 04:33
-- Server-Version: 10.3.39-MariaDB-0+deb10u1
-- PHP-Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `bhokt25`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buchungen2024exdeep`
--
-- Erstellt am: 17. Nov 2025 um 03:30
-- Zuletzt aktualisiert: 17. Nov 2025 um 03:31
--

DROP TABLE IF EXISTS `buchungen2024exdeep`;
CREATE TABLE IF NOT EXISTS `buchungen2024exdeep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mandant_id` int(10) NOT NULL DEFAULT 3,
  `umsatz_id` varchar(50) NOT NULL,
  `datum` date NOT NULL,
  `quelle` varchar(100) NOT NULL,
  `buchungsart` enum('Einnahme','Ausgabe','Vorsteuer','Umsatzsteuer') NOT NULL,
  `belegnummer` varchar(100) DEFAULT NULL,
  `buchungstext` text NOT NULL,
  `betrag` decimal(10,2) NOT NULL,
  `betrag_neu` decimal(10,2) DEFAULT NULL,
  `netto_betrag` decimal(10,2) DEFAULT NULL,
  `mwst_satz` decimal(5,2) DEFAULT NULL,
  `mwst_betrag` decimal(15,2) DEFAULT NULL,
  `mwst_konto` varchar(20) DEFAULT NULL,
  `soll_konto_id` int(11) DEFAULT NULL,
  `soll_konto_nr` varchar(4) NOT NULL DEFAULT '1234',
  `haben_konto_id` varchar(11) DEFAULT NULL,
  `haben_konto_nr` varchar(4) NOT NULL DEFAULT '4561',
  `split_info` varchar(50) DEFAULT NULL,
  `ist_split_buchung` varchar(50) DEFAULT NULL,
  `hauptbuchung_id` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=546 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `buchungen2024exdeep`
--

INSERT INTO `buchungen2024exdeep` (`id`, `mandant_id`, `umsatz_id`, `datum`, `quelle`, `buchungsart`, `belegnummer`, `buchungstext`, `betrag`, `betrag_neu`, `netto_betrag`, `mwst_satz`, `mwst_betrag`, `mwst_konto`, `soll_konto_id`, `soll_konto_nr`, `haben_konto_id`, `haben_konto_nr`, `split_info`, `ist_split_buchung`, `hauptbuchung_id`) VALUES
(1, 3, '1', '2024-01-02', 'spk2024umsatz_plus', 'Ausgabe', '1', 'Vertrag AS-9836674342 Hausratversicherung Abteigasse 3, 91560 Heilsbronn 28.01.24 - 27.04.24', '40.11', NULL, '40.11', '0.00', '0.00', '', NULL, '1800', NULL, '1200', '', '0', NULL),
(2, 3, '2', '2024-01-02', 'spk2024umsatz_plus', 'Ausgabe', '2', 'Vertrag AS-9836735744 Privat-Haftpflichtversicherung 30.01.24 - 29.04.24', '21.78', NULL, '21.78', '0.00', '0.00', '', NULL, '1800', NULL, '1200', '', '0', NULL),
(12, 3, '12', '2024-01-09', 'spk2024umsatz_plus', 'Ausgabe', '12', '304-9482138-3139552 Amazon.de 4O2KWFXX6XRKTSMM (Netto 7%)', '13.08', NULL, '13.08', '7.00', '0.00', '1571', NULL, '3300', NULL, '1200', 'Hauptbuchung (Netto)', '0', NULL),
(13, 3, '12-MWST', '2024-01-09', 'spk2024umsatz_plus', 'Vorsteuer', '12-MWST', '304-9482138-3139552 Amazon.de 4O2KWFXX6XRKTSMM (Vorsteuer 7%)', '0.92', NULL, '0.92', '7.00', '0.92', '1571', NULL, '1571', NULL, '1200', 'MWST-Splitbuchung', '1', '12'),
(14, 3, '13', '2024-01-10', 'spk2024umsatz_plus', 'Einnahme', '13', 'KTO 71823054 Guthaben 30601835409 / 76,29- EUR faellig 05.01.24 Abteigasse 3 (Netto 7%)', '71.30', NULL, '71.30', '7.00', '0.00', '1771', NULL, '1200', NULL, '4245', 'Hauptbuchung (Netto)', '0', NULL),
(15, 3, '13-MWST', '2024-01-10', 'spk2024umsatz_plus', 'Umsatzsteuer', '13-MWST', 'KTO 71823054 Guthaben 30601835409 / 76,29- EUR faellig 05.01.24 Abteigasse 3 (Umsatzsteuer 7%)', '4.99', NULL, '4.99', '7.00', '4.99', '1771', NULL, '1200', NULL, '1771', 'MWST-Splitbuchung', '1', '13'),
(16, 3, '14', '2024-01-15', 'spk2024umsatz_plus', 'Ausgabe', '14', '/OBO/ DHL PAKET GMBH/DEBI 6180509718/ZBNR 2800182068RG 1124987721 VOM 10.01.2024/DHLPAKET/VFTN 0101BETRAG 64,49 EUR', '64.49', NULL, '64.49', '0.00', '0.00', '', NULL, '4910', NULL, '1200', '', '0', NULL),
(17, 3, '15', '2024-01-16', 'spk2024umsatz_plus', 'Ausgabe', '15', 'Rechnung SPARKASSE ANSBACH Entgelt SpkCard(Debitkarte)für 2024/KartNr.******4611 GISELA LOWIG 20240116-BY004-00019817456 ', '14.40', NULL, '14.40', '0.00', '0.00', '', NULL, '4970', NULL, '1200', '', '0', NULL),
(18, 3, '16', '2024-01-16', 'spk2024umsatz_plus', 'Ausgabe', '16', 'KD6180509718 2801107089 REF1533826644 RE1533826644 DAT10.01.2024 WARENPOST INTERNATIONAL (66 Warenpost International Pre', '10.57', NULL, '10.57', '0.00', '0.00', '', NULL, '4910', NULL, '1200', '', '0', NULL),
(19, 3, '17', '2024-01-17', 'spk2024umsatz_plus', 'Ausgabe', '17', 'Les amis d Objat // Beitrag 2024 // Naechster Einzug: 17.01.2025 // // www.lesamisdobjat.de // ', '15.00', NULL, '15.00', '0.00', '0.00', '', NULL, '1800', NULL, '1200', '', '0', NULL),
(20, 3, '18', '2024-01-19', 'spk2024umsatz_plus', 'Einnahme', '18', 'RUECKUEBERWEISUNG Kontonummer fehlerhaft (ungueltige IBAN)antiqu. Bücher4/2023', '496.12', NULL, '496.12', '0.00', '0.00', '', NULL, '1200', NULL, '3202', '', '0', NULL),
(21, 3, '19', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '19', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '96.19', NULL, '96.19', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(22, 3, '20', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '20', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '47.96', NULL, '47.96', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(23, 3, '21', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '21', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '12.70', NULL, '12.70', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(24, 3, '22', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '22', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '6.54', NULL, '6.54', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(25, 3, '23', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '23', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '6.04', NULL, '6.04', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(26, 3, '24', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '24', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '5.84', NULL, '5.84', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(27, 3, '25', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '25', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '118.74', NULL, '118.74', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(28, 3, '26', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '26', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '5.45', NULL, '5.45', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(29, 3, '27', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '27', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '496.12', NULL, '496.12', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(30, 3, '28', '2024-01-19', 'spk2024umsatz_plus', 'Ausgabe', '28', 'antiqu. Bücher4/2023 DATUM 19.01.2024, 05.54 UHR', '5.84', NULL, '5.84', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(31, 3, '29', '2024-01-22', 'spk2024umsatz_plus', 'Ausgabe', '29', 'Festnetz Vertragskonto 4781042110 RG 7520070881/09.01.2024 (Netto 19%)', '34.69', NULL, '34.69', '19.00', '0.00', '1576', NULL, '4920', NULL, '1200', 'Hauptbuchung (Netto)', '0', NULL),
(32, 3, '29-MWST', '2024-01-22', 'spk2024umsatz_plus', 'Vorsteuer', '29-MWST', 'Festnetz Vertragskonto 4781042110 RG 7520070881/09.01.2024 (Vorsteuer 19%)', '6.59', NULL, '6.59', '19.00', '6.59', '1576', NULL, '1576', NULL, '1200', 'MWST-Splitbuchung', '1', '29'),
(33, 3, '30', '2024-01-22', 'spk2024umsatz_plus', 'Ausgabe', '30', 'renr 2023/1600 plus1601 DATUM 20.01.2024, 10.20 UHR (Netto 19%)', '1097.77', NULL, '1097.77', '19.00', '0.00', '1576', NULL, '4955', NULL, '1200', 'Hauptbuchung (Netto)', '0', NULL),
(34, 3, '30-MWST', '2024-01-22', 'spk2024umsatz_plus', 'Vorsteuer', '30-MWST', 'renr 2023/1600 plus1601 DATUM 20.01.2024, 10.20 UHR (Vorsteuer 19%)', '208.58', NULL, '208.58', '19.00', '208.58', '1576', NULL, '1576', NULL, '1200', 'MWST-Splitbuchung', '1', '30'),
(35, 3, '31', '2024-01-22', 'spk2024umsatz_plus', 'Ausgabe', '31', 'antiqu. Bücher4/2023 DATUM 20.01.2024, 10.20 UHR', '496.12', NULL, '496.12', '0.00', '0.00', '', NULL, '3202', NULL, '1200', '', '0', NULL),
(36, 3, '32', '2024-01-22', 'spk2024umsatz_plus', 'Ausgabe', '32', 'Kd.Nr.3408227 Re.Nr. 2297392 DATUM 20.01.2024, 10.20 UHR (Netto 19%)', '16.20', NULL, '16.20', '19.00', '0.00', '1576', NULL, '4760', NULL, '1200', 'Hauptbuchung (Netto)', '0', NULL),
(37, 3, '32-MWST', '2024-01-22', 'spk2024umsatz_plus', 'Vorsteuer', '32-MWST', 'Kd.Nr.3408227 Re.Nr. 2297392 DATUM 20.01.2024, 10.20 UHR (Vorsteuer 19%)', '3.08', NULL, '3.08', '19.00', '3.08', '1576', NULL, '1576', NULL, '1200', 'MWST-Splitbuchung', '1', '32'),
(38, 3, '33', '2024-01-24', 'spk2024umsatz_plus', 'Ausgabe', '33', '1032064921335/PP.6604.PP/. bepro GmbH, Ihr Einkauf bei bepro GmbH', '75.26', NULL, '75.26', '0.00', '0.00', '', NULL, '1230', NULL, '1200', '', '0', NULL),
(39, 3, '34', '2024-01-24', 'spk2024umsatz_plus', 'Ausgabe', '34', '/OBO/ DHL PAKET GMBH/DEBI 6180509718/ZBNR 2800299400RG 1016802657 VOM 20.01.2024/DHLPAKET/VFTN 0101BETRAG 50,41 EUR', '50.41', NULL, '50.41', '0.00', '0.00', '', NULL, '4910', NULL, '1200', '', '0', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
