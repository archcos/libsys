-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2024 at 11:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `balibsysdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblauthor`
--

CREATE TABLE `tblauthor` (
  `authorId` int(11) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `lastName` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblauthor`
--

INSERT INTO `tblauthor` (`authorId`, `firstName`, `lastName`) VALUES
(1, 'Arjay', 'Charcos'),
(2, 'Goat', 'Toad'),
(3, 'Marlo', 'Jay');

-- --------------------------------------------------------

--
-- Table structure for table `tblbooks`
--

CREATE TABLE `tblbooks` (
  `bookId` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `dateAdded` timestamp NOT NULL DEFAULT current_timestamp(),
  `quantity` int(11) NOT NULL,
  `authorId` int(11) DEFAULT NULL,
  `categoryId` int(11) DEFAULT NULL,
  `callNum` varchar(11) NOT NULL,
  `accessionNum` varchar(5) NOT NULL,
  `barcodeNum` varchar(11) NOT NULL,
  `publisher` varchar(30) NOT NULL,
  `publishedDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblbooks`
--

INSERT INTO `tblbooks` (`bookId`, `title`, `dateAdded`, `quantity`, `authorId`, `categoryId`, `callNum`, `accessionNum`, `barcodeNum`, `publisher`, `publishedDate`) VALUES
(21, 'The DeathWonDo', '2024-12-02 03:17:42', 2, 1, 1, '3123', '31231', '31231', '', NULL),
(22, '1+1', '2024-12-02 06:49:55', 10, 2, 2, '42342', '64564', 'dadwa321', '', NULL),
(36, 'The Newest', '2024-12-02 07:54:55', 0, 1, 1, '3312', '534', '32131d', '', NULL),
(37, 'The Strongest Sorcerer', '2024-12-02 09:38:57', 11, 3, 2, '12432123', '32131', '213121', '', NULL),
(38, 'Strongest', '2024-12-03 09:44:40', 21, 1, 1, 'WADA12', 'DWA42', '2342DAWDAW', 'ARJAY PUB', '2024-11-25');

-- --------------------------------------------------------

--
-- Table structure for table `tblborrowers`
--

CREATE TABLE `tblborrowers` (
  `idNumber` int(15) NOT NULL,
  `borrowerType` varchar(10) NOT NULL,
  `dateRegistered` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `libraryId` int(11) NOT NULL,
  `surName` varchar(20) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `middleName` varchar(20) NOT NULL,
  `emailAddress` varchar(40) NOT NULL,
  `course` varchar(20) DEFAULT NULL,
  `year` int(1) DEFAULT NULL,
  `position` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','','') NOT NULL,
  `birthDate` varchar(15) NOT NULL,
  `homeAddress` varchar(100) NOT NULL,
  `remarks` varchar(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblborrowers`
--

INSERT INTO `tblborrowers` (`idNumber`, `borrowerType`, `dateRegistered`, `libraryId`, `surName`, `firstName`, `middleName`, `emailAddress`, `course`, `year`, `position`, `gender`, `birthDate`, `homeAddress`, `remarks`) VALUES
(44, 'Student', '2024-11-29 03:41:01', 1, 'Whaaat', 'Theee', 'Joj', 'jayarcharcos@gmail.com', 'IS', 2, '', 'Male', '2024-11-21', 'Secret', '0'),
(111, 'Staff', '2024-11-29 09:17:56', 1, 'Charcos', 'Arjay', 'Audiencia', 'joedavid1345@gmail.com', '', 0, 'IT', 'Male', '2024-11-14', 'daw', '1'),
(214, 'Student', '2024-12-03 07:19:36', 1, 'Charcos', 'Arjays', 'daw', 'yahoo@gmail.com', 'IT', 2, '', 'Male', '2024-11-14', 'Opol', '1'),
(231, 'Faculty', '2024-11-26 06:33:46', 1, 'dwada', 'dwada', 'dwada', '', '', 0, 'dwada', 'Male', '2024-11-14', 'dwad', '1'),
(322, 'Faculty', '2024-11-27 10:15:18', 1, 'Charcos', 'Arjay', 'Audiencia', '', '', 0, 'IT Supervisor', 'Male', '2024-10-29', 'Igpit, Opol, Mis. Or.', ''),
(1211, 'Faculty', '2024-11-27 09:19:40', 1, 'Charccc', 'What', 'Wa', '', '', 0, '|WADA', 'Male', '2024-11-11', 'dawd', '0'),
(1234, 'Student', '2024-11-29 02:39:49', 1, 'Charcos', 'Arjay', 'A', 'arjay.charcos25@gmail.com', 'BS IT', 2, '', 'Male', '2024-11-26', 'Igpit', ''),
(2124, 'Faculty', '2024-11-26 09:40:01', 1, 'Ror', 'Char', 'Aud', '', '', 0, 'Lol', 'Male', '2024-11-14', 'Lol', '1'),
(2141, 'Faculty', '2024-11-26 09:40:57', 1, 'Wd', 'WA', 'DWA', '', '', 0, 'dwada', 'Male', '2024-11-08', 'AWDA', '1'),
(2143, 'Staff', '2024-11-26 09:29:55', 1, 'WD', 'wa', 'ds', '', '', 0, 'dwa', 'Male', '2024-11-18', 'daw', '1'),
(21213, 'Student', '2024-11-29 03:31:09', 1, 'Millanar', 'Marlo Jay', 'Dunno', 'marlojaymillanar2002@gmail.com', 'BS Information Techn', 3, '', 'Male', '2024-11-18', 'Balubal USTP', ''),
(21435, 'Staff', '2024-11-29 03:09:49', 1, 'Baculio', 'April Maxine', 'Maurin', 'rain.shigatsu@gmail.com', '', 0, 'IT Supervisor', 'Male', '2024-11-05', 'Igpit, Opol, Misamis Oriental', '1'),
(21444, 'Faculty', '2024-11-26 08:59:49', 1, 'dwada', 'dwada', 'dawd', '', '', 0, 'dwada', 'Male', '2024-11-15', 'dawda', '1'),
(214356, 'Student', '2024-11-27 09:58:22', 1, 'Charcos', 'Arjay', 'Audiencia', '', 'BS Information Techn', 4, '', 'Male', '2024-11-07', 'Igpit, Opol, Misamis Oriental', ''),
(214443, 'Student', '2024-11-26 09:57:58', 1, 'Charcos', 'Arjay', 'Whatttt', '', 'ITTTTT', 1, '', 'Male', '2024-11-13', 'Igpit', '1'),
(341241, 'Faculty', '2024-11-26 09:45:15', 121, 'dawdada', 'dawda', 'dawdawd', '', '', 0, 'dawda', 'Male', '2024-11-04', 'dawda', '1'),
(213112312, 'Faculty', '2024-11-27 07:59:28', 1, 'Arjay', 'sho ', 'hot', '', '', 0, 'WDA', 'Male', '2024-11-07', 'dawda', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `categoryId` int(11) NOT NULL,
  `categoryName` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`categoryId`, `categoryName`) VALUES
(1, 'Science'),
(2, 'Mathematics');

-- --------------------------------------------------------

--
-- Table structure for table `tblreference`
--

CREATE TABLE `tblreference` (
  `referenceId` int(11) NOT NULL,
  `borrowerId` int(11) NOT NULL,
  `author` varchar(30) NOT NULL,
  `title` varchar(30) NOT NULL,
  `category` varchar(30) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblreturnborrow`
--

CREATE TABLE `tblreturnborrow` (
  `borrowId` int(11) NOT NULL,
  `borrowedDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `returnDate` date DEFAULT NULL,
  `borrowerId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `librarianName` varchar(50) NOT NULL,
  `returned` enum('No','Yes','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblreturnborrow`
--

INSERT INTO `tblreturnborrow` (`borrowId`, `borrowedDate`, `returnDate`, `borrowerId`, `bookId`, `librarianName`, `returned`) VALUES
(22, '2024-12-02 07:43:59', '2024-12-17', 111, 21, 'Arjay Charcos', 'Yes'),
(23, '2024-12-02 07:50:20', '2024-12-03', 111, 21, 'Arjay Charcos', 'Yes'),
(24, '2024-12-02 07:52:17', '2024-12-02', 111, 21, 'Arjay Charcos', 'Yes'),
(25, '2024-12-02 07:58:17', '2024-12-10', 111, 21, 'Arjay Charcos', 'No'),
(26, '2024-12-02 07:58:25', '2024-12-09', 111, 36, 'Arjay Charcos', 'No'),
(27, '2024-12-03 03:34:03', '2024-12-18', 2124, 21, 'The Godd', 'No'),
(28, '2024-12-03 03:34:04', '2024-12-18', 2124, 21, 'The Godd', 'No'),
(29, '2024-12-03 03:34:04', '2024-12-18', 2124, 21, 'The Godd', 'No'),
(30, '2024-12-03 03:34:05', '2024-12-18', 2124, 21, 'The Godd', 'No'),
(31, '2024-12-03 03:37:48', '2024-12-23', 2124, 21, 'The Godd', 'No'),
(32, '2024-12-03 03:41:36', '2024-12-23', 2124, 21, 'Gos Htot', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `userId` int(11) NOT NULL,
  `accountType` enum('Librarian','Admin','','') NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `firstName` varchar(20) NOT NULL,
  `lastName` varchar(20) NOT NULL,
  `lastLogin` timestamp NOT NULL DEFAULT current_timestamp(),
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`userId`, `accountType`, `username`, `password`, `firstName`, `lastName`, `lastLogin`, `dateCreated`) VALUES
(1, 'Admin', 'rain', 'fbec17cb2fcbbd1c659b252230b48826fc563788', 'Hottest', 'Person', '2024-12-03 09:49:15', '2024-11-25 02:40:02'),
(2, 'Librarian', 'test', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 'Testing', 'Ko', '2024-12-03 09:01:14', '2024-12-03 09:01:14'),
(3, 'Librarian', 'test', '8cb2237d0679ca88db6464eac60da96345513964', 'arjay', 'charcos', '2024-12-03 09:06:24', '2024-12-03 09:06:24'),
(4, 'Librarian', 'rain214', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'password', 'password', '2024-12-03 09:49:40', '2024-12-03 09:24:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblauthor`
--
ALTER TABLE `tblauthor`
  ADD PRIMARY KEY (`authorId`);

--
-- Indexes for table `tblbooks`
--
ALTER TABLE `tblbooks`
  ADD PRIMARY KEY (`bookId`),
  ADD KEY `fk_author` (`authorId`),
  ADD KEY `fk_category` (`categoryId`);

--
-- Indexes for table `tblborrowers`
--
ALTER TABLE `tblborrowers`
  ADD PRIMARY KEY (`idNumber`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`categoryId`);

--
-- Indexes for table `tblreference`
--
ALTER TABLE `tblreference`
  ADD PRIMARY KEY (`referenceId`),
  ADD KEY `fk_borrowerId` (`borrowerId`);

--
-- Indexes for table `tblreturnborrow`
--
ALTER TABLE `tblreturnborrow`
  ADD PRIMARY KEY (`borrowId`),
  ADD KEY `fk_borrower` (`borrowerId`),
  ADD KEY `fk_book` (`bookId`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblauthor`
--
ALTER TABLE `tblauthor`
  MODIFY `authorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblbooks`
--
ALTER TABLE `tblbooks`
  MODIFY `bookId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblreference`
--
ALTER TABLE `tblreference`
  MODIFY `referenceId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblreturnborrow`
--
ALTER TABLE `tblreturnborrow`
  MODIFY `borrowId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblbooks`
--
ALTER TABLE `tblbooks`
  ADD CONSTRAINT `fk_author` FOREIGN KEY (`authorId`) REFERENCES `tblauthor` (`authorId`),
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`categoryId`) REFERENCES `tblcategory` (`categoryId`);

--
-- Constraints for table `tblreference`
--
ALTER TABLE `tblreference`
  ADD CONSTRAINT `fk_borrowerId` FOREIGN KEY (`borrowerId`) REFERENCES `tblborrowers` (`idNumber`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `tblreturnborrow`
--
ALTER TABLE `tblreturnborrow`
  ADD CONSTRAINT `fk_book` FOREIGN KEY (`bookId`) REFERENCES `tblbooks` (`bookId`),
  ADD CONSTRAINT `fk_borrower` FOREIGN KEY (`borrowerId`) REFERENCES `tblborrowers` (`idNumber`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
