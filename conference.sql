-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 21, 2025 at 07:15 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `conference`
--

-- --------------------------------------------------------

--
-- Table structure for table `Hotel`
--

CREATE TABLE `Hotel` (
  `roomNum` int(11) NOT NULL,
  `bedNum` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Jobs`
--

CREATE TABLE `Jobs` (
  `jobTitle` varchar(60) NOT NULL,
  `location` varchar(60) NOT NULL,
  `companyName` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Members`
--

CREATE TABLE `Members` (
  `fname` varchar(60) NOT NULL,
  `lname` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Professional`
--

CREATE TABLE `Professional` (
  `fname` varchar(60) NOT NULL,
  `lname` varchar(60) NOT NULL,
  `phoneNum` int(11) NOT NULL,
  `AMOUNT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Sessions`
--

CREATE TABLE `Sessions` (
  `fname` varchar(60) NOT NULL,
  `lname` varchar(60) NOT NULL,
  `dayNum` date NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `room` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Sponsor`
--

CREATE TABLE `Sponsor` (
  `fname` varchar(60) NOT NULL,
  `lname` varchar(60) NOT NULL,
  `phoneNum` int(11) NOT NULL,
  `AMOUNT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Sponsors`
--

CREATE TABLE `Sponsors` (
  `companyName` varchar(60) NOT NULL,
  `sponsorLvl` int(11) NOT NULL,
  `emailsSent` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Student`
--

CREATE TABLE `Student` (
  `fname` varchar(60) NOT NULL,
  `lname` varchar(60) NOT NULL,
  `phoneNum` int(11) NOT NULL,
  `AMOUNT` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SubCommittee`
--

CREATE TABLE `SubCommittee` (
  `clubName` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Hotel`
--
ALTER TABLE `Hotel`
  ADD PRIMARY KEY (`roomNum`);

--
-- Indexes for table `Jobs`
--
ALTER TABLE `Jobs`
  ADD PRIMARY KEY (`jobTitle`,`location`),
  ADD KEY `companyName` (`companyName`);

--
-- Indexes for table `Members`
--
ALTER TABLE `Members`
  ADD PRIMARY KEY (`fname`,`lname`);

--
-- Indexes for table `Professional`
--
ALTER TABLE `Professional`
  ADD PRIMARY KEY (`fname`,`lname`);

--
-- Indexes for table `Sessions`
--
ALTER TABLE `Sessions`
  ADD PRIMARY KEY (`fname`,`lname`);

--
-- Indexes for table `Sponsor`
--
ALTER TABLE `Sponsor`
  ADD PRIMARY KEY (`fname`,`lname`);

--
-- Indexes for table `Sponsors`
--
ALTER TABLE `Sponsors`
  ADD PRIMARY KEY (`companyName`);

--
-- Indexes for table `Student`
--
ALTER TABLE `Student`
  ADD PRIMARY KEY (`fname`,`lname`);

--
-- Indexes for table `SubCommittee`
--
ALTER TABLE `SubCommittee`
  ADD PRIMARY KEY (`clubName`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Jobs`
--
ALTER TABLE `Jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`companyName`) REFERENCES `Sponsors` (`companyName`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
