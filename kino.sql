-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 10 Kwi 2017, 21:58
-- Wersja serwera: 10.1.21-MariaDB
-- Wersja PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `kino`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `filmy`
--

CREATE TABLE `filmy` (
  `id_film` int(11) NOT NULL,
  `tytul` text COLLATE utf8_polish_ci NOT NULL,
  `czas trwania` int(11) NOT NULL,
  `gatunek` text COLLATE utf8_polish_ci NOT NULL,
  `rezyser` text COLLATE utf8_polish_ci NOT NULL,
  `rodzaj` text COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `filmy`
--

INSERT INTO `filmy` (`id_film`, `tytul`, `czas trwania`, `gatunek`, `rezyser`, `rodzaj`) VALUES
(1, 'Logan: Wolverine', 137, 'Akcja', 'James Manglod', '2D'),
(2, 'Ex Machina', 108, 'Sci-Fi', 'Alex Garland', '2D'),
(3, 'Mroczny Rycerz powstaje', 165, 'Akcja', 'Christopher Nolan', '2D'),
(4, 'Batman - Początek', 140, 'Akcja', 'Christopher Nolan', '2D'),
(5, 'Mroczny Rycerz', 153, 'Akcja', 'Christopher Nolan', '2D');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `hasla`
--

CREATE TABLE `hasla` (
  `id_haslo` int(11) NOT NULL,
  `haslo` text COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `hasla`
--

INSERT INTO `hasla` (`id_haslo`, `haslo`) VALUES
(1, 'admin1');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rezerwacje`
--

CREATE TABLE `rezerwacje` (
  `id_rezerwacji` int(11) NOT NULL,
  `id_seans` int(11) NOT NULL,
  `miejsce` int(11) NOT NULL,
  `email` text COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sale`
--

CREATE TABLE `sale` (
  `id_sala` int(11) NOT NULL,
  `ilosc_miejsc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_polish_ci;

--
-- Zrzut danych tabeli `sale`
--

INSERT INTO `sale` (`id_sala`, `ilosc_miejsc`) VALUES
(1, 113),
(2, 311),
(3, 418),
(4, 115),
(5, 195),
(6, 311),
(7, 195),
(8, 115),
(9, 534),
(10, 229),
(11, 113);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `seanse`
--

CREATE TABLE `seanse` (
  `id_seans` int(11) NOT NULL,
  `id_film` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `dzien` date NOT NULL,
  `godzina` time NOT NULL,
  `wolne_miejsca` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `seanse`
--

INSERT INTO `seanse` (`id_seans`, `id_film`, `id_sala`, `dzien`, `godzina`, `wolne_miejsca`) VALUES
(1, 1, 2, '2017-06-18', '12:00:00', 311),
(2, 4, 1, '2017-06-18', '12:00:00', 113),
(3, 5, 1, '2017-06-18', '14:50:00', 113),
(4, 3, 1, '2017-06-18', '18:50:00', 113),
(5, 3, 2, '2017-06-18', '14:45:00', 311),
(29, 3, 4, '2017-04-09', '10:03:00', 115),
(30, 4, 1, '2017-04-09', '10:00:00', 113);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `filmy`
--
ALTER TABLE `filmy`
  ADD PRIMARY KEY (`id_film`);

--
-- Indexes for table `hasla`
--
ALTER TABLE `hasla`
  ADD PRIMARY KEY (`id_haslo`);

--
-- Indexes for table `rezerwacje`
--
ALTER TABLE `rezerwacje`
  ADD PRIMARY KEY (`id_rezerwacji`);

--
-- Indexes for table `sale`
--
ALTER TABLE `sale`
  ADD PRIMARY KEY (`id_sala`);

--
-- Indexes for table `seanse`
--
ALTER TABLE `seanse`
  ADD PRIMARY KEY (`id_seans`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `filmy`
--
ALTER TABLE `filmy`
  MODIFY `id_film` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT dla tabeli `hasla`
--
ALTER TABLE `hasla`
  MODIFY `id_haslo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `rezerwacje`
--
ALTER TABLE `rezerwacje`
  MODIFY `id_rezerwacji` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT dla tabeli `sale`
--
ALTER TABLE `sale`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT dla tabeli `seanse`
--
ALTER TABLE `seanse`
  MODIFY `id_seans` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
