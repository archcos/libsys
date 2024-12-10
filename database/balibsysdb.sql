-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 11:19 AM
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
(4, 'Arjay', 'Charcos'),
(5, 'Jay', 'Z');

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
(39, 'The Book', '2024-12-05 10:07:00', 1, 4, 3, '123', '123', '123', 'Ako', '2024-12-10'),
(41, 'The Nesst', '2024-12-06 00:30:10', 10, 5, 4, '1234', '1242', '2143', 'What', '2024-12-10');

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
  `remarks` enum('1','0','','') NOT NULL,
  `receipt` enum('No','Yes','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblborrowers`
--

INSERT INTO `tblborrowers` (`idNumber`, `borrowerType`, `dateRegistered`, `libraryId`, `surName`, `firstName`, `middleName`, `emailAddress`, `course`, `year`, `position`, `gender`, `birthDate`, `homeAddress`, `remarks`, `receipt`) VALUES
(1, 'Student', '2024-12-06 05:52:24', 1, 'Uzumaki', 'Naruto', 'Secret', 'naruto@gmail.com', 'BS Information Techn', 4, '', 'Male', '2002-02-11', 'Konohas', '1', 'Yes');

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
(3, 'Science'),
(4, 'Mathematics'),
(6, 'JJ');

-- --------------------------------------------------------

--
-- Table structure for table `tblnotifications`
--

CREATE TABLE `tblnotifications` (
  `notificationId` int(11) NOT NULL,
  `borrowerId` int(11) DEFAULT NULL,
  `bookId` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `type` enum('borrow','return','','') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblnotifications`
--

INSERT INTO `tblnotifications` (`notificationId`, `borrowerId`, `bookId`, `message`, `status`, `type`, `timestamp`) VALUES
(67, 1, 39, 'Naruto Uzumaki has returned the book: The Book', 'read', 'return', '2024-12-06 00:07:52'),
(68, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-06 00:09:22'),
(69, 1, 41, 'Naruto Uzumaki has requested to borrow the book: The Nesst', 'read', 'borrow', '2024-12-06 00:30:20'),
(70, 1, 41, 'Naruto Uzumaki has returned the book: The Nesst', 'read', 'return', '2024-12-06 01:11:26'),
(71, 1, 39, ' has returned the book: The Book', 'read', 'return', '2024-12-06 03:13:39'),
(72, 1, 39, ' has returned the book: The Book', 'read', 'return', '2024-12-06 03:15:22'),
(73, 1, 39, ' has returned the book: The Book', 'read', 'return', '2024-12-06 03:15:31'),
(74, 1, 39, ' has returned the book: The Book', 'read', 'return', '2024-12-06 03:15:34'),
(75, 1, 41, 'Naruto Uzumaki has requested to borrow the book: The Nesst', 'read', 'borrow', '2024-12-06 03:23:39'),
(76, 1, 39, 'Naruto Uzumaki has returned the book: The Book', 'read', 'return', '2024-12-06 03:24:54'),
(77, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-06 03:25:34'),
(78, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-06 03:25:57'),
(79, 1, 39, 'Naruto Uzumaki has returned the book: The Book', 'read', 'return', '2024-12-06 05:17:21'),
(80, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-06 05:55:53'),
(81, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-06 06:16:34'),
(82, 1, 41, 'Naruto Uzumaki has requested to borrow the book: The Nesst', 'read', 'borrow', '2024-12-10 06:55:12'),
(83, 1, 41, 'Naruto Uzumaki has returned the book: The Nesst', 'read', 'return', '2024-12-10 06:55:51'),
(84, 1, 39, 'Naruto Uzumaki has returned the book: The Book', 'read', 'return', '2024-12-10 07:01:52'),
(85, 1, 41, 'Naruto Uzumaki has requested to borrow the book: The Nesst', 'read', 'borrow', '2024-12-10 07:03:29'),
(87, 1, 41, 'Naruto Uzumaki has returned the book: The Nesst', 'read', 'return', '2024-12-10 07:04:30'),
(91, 1, 41, 'Naruto Uzumaki has requested to borrow the book: The Nesst', 'read', 'borrow', '2024-12-10 07:28:37'),
(92, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-10 09:34:22'),
(93, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-10 09:34:42'),
(94, 1, 41, 'Naruto Uzumaki has requested to borrow the book: The Nesst', 'read', 'borrow', '2024-12-10 09:34:53'),
(95, 1, 41, 'Naruto Uzumaki has returned the book: The Nesst', 'read', 'return', '2024-12-10 09:36:29'),
(96, 1, 41, 'Naruto Uzumaki has returned the book: The Nesst', 'read', 'return', '2024-12-10 09:36:38'),
(97, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-10 09:39:39'),
(98, 1, 39, 'Naruto Uzumaki has returned the book: The Book', 'read', 'return', '2024-12-10 09:39:58'),
(99, 1, 41, 'Naruto Uzumaki has requested to borrow the book: The Nesst', 'read', 'borrow', '2024-12-10 09:45:06'),
(100, 1, 41, 'Naruto Uzumaki has returned the book: The Nesst', 'read', 'return', '2024-12-10 09:45:21'),
(101, 1, 41, 'Naruto Uzumaki has requested to borrow the book: The Nesst', 'read', 'borrow', '2024-12-10 09:47:27'),
(102, 1, 41, 'Naruto Uzumaki has returned the book: The Nesst', 'read', 'return', '2024-12-10 09:47:41'),
(103, 1, 39, 'Naruto Uzumaki has requested to borrow the book: The Book', 'read', 'borrow', '2024-12-10 10:17:46'),
(104, 1, 39, 'Naruto Uzumaki has returned the book: The Book', 'read', 'return', '2024-12-10 10:18:01');

-- --------------------------------------------------------

--
-- Table structure for table `tblpenalties`
--

CREATE TABLE `tblpenalties` (
  `penaltyId` int(11) NOT NULL,
  `borrowerId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `penalty` enum('Medium','Severe','Normal') NOT NULL,
  `cost` int(11) NOT NULL,
  `paid` enum('No','Yes','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Dumping data for table `tblreference`
--

INSERT INTO `tblreference` (`referenceId`, `borrowerId`, `author`, `title`, `category`, `date`) VALUES
(7, 1, 'The |Guko', 'What', 'Science', '2024-12-31');

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
(39, '2024-12-06 00:09:36', '2024-12-10', 1, 39, 'Me', 'Yes'),
(40, '2024-12-06 00:30:47', '2024-12-05', 1, 41, 'Jay z', 'Yes'),
(41, '2024-12-06 03:26:14', '2024-12-10', 1, 39, 'Arjay', 'Yes'),
(42, '2024-12-06 05:56:10', '2024-12-10', 1, 39, '', 'Yes'),
(43, '2024-12-06 06:16:48', '2024-12-10', 1, 39, 'Me', 'Yes'),
(44, '2024-12-06 07:55:55', '2024-12-10', 1, 39, 'Test', 'Yes'),
(45, '2024-12-10 06:55:38', '2024-12-25', 1, 41, 'Me', 'Yes'),
(46, '2024-12-10 07:04:08', '2024-12-27', 1, 41, 'daw', 'Yes'),
(47, '2024-12-10 07:28:49', '0000-00-00', 1, 41, '', 'Yes'),
(48, '2024-12-10 09:36:21', '2024-12-22', 1, 41, 'dawda', 'Yes'),
(49, '2024-12-10 09:39:50', '2024-12-11', 1, 39, 'whar', 'Yes'),
(50, '2024-12-10 09:40:35', '2024-12-26', 1, 39, 'dawda', 'Yes'),
(51, '2024-12-10 09:45:15', '2024-12-17', 1, 41, 'dawda', 'Yes'),
(52, '2024-12-10 09:47:36', '2024-12-26', 1, 41, 'dwada', 'Yes'),
(53, '2024-12-10 10:17:55', '2024-12-16', 1, 39, 'dawda', 'Yes');

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
(1, 'Admin', 'rain', 'fbec17cb2fcbbd1c659b252230b48826fc563788', 'Hottest', 'Person', '2024-12-10 06:55:24', '2024-11-25 02:40:02'),
(2, 'Librarian', 'test', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4', 'Testing', 'Ko', '2024-12-03 09:01:14', '2024-12-03 09:01:14'),
(4, 'Librarian', 'rain214', '8cb2237d0679ca88db6464eac60da96345513964', 'password', 'password', '2024-12-09 07:13:28', '2024-12-03 09:24:16'),
(5, 'Admin', 'dwa', 'f98421770a791fdf0338f87df795cd758ad5d87b', 'dwad', 'dwad', '2024-12-09 07:15:07', '2024-12-09 07:15:07');

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
-- Indexes for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  ADD PRIMARY KEY (`notificationId`),
  ADD KEY `borrowerId` (`borrowerId`),
  ADD KEY `fk_bookId` (`bookId`);

--
-- Indexes for table `tblpenalties`
--
ALTER TABLE `tblpenalties`
  ADD PRIMARY KEY (`penaltyId`);

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
  MODIFY `authorId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblbooks`
--
ALTER TABLE `tblbooks`
  MODIFY `bookId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  MODIFY `notificationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `tblpenalties`
--
ALTER TABLE `tblpenalties`
  MODIFY `penaltyId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblreference`
--
ALTER TABLE `tblreference`
  MODIFY `referenceId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblreturnborrow`
--
ALTER TABLE `tblreturnborrow`
  MODIFY `borrowId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  ADD CONSTRAINT `fk_bookId` FOREIGN KEY (`bookId`) REFERENCES `tblbooks` (`bookId`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `tblnotifications_ibfk_1` FOREIGN KEY (`borrowerId`) REFERENCES `tblborrowers` (`idNumber`) ON DELETE CASCADE;

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
