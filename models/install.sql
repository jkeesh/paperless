-- phpMyAdmin SQL Dump
-- version 2.11.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 18, 2010 at 10:21 AM
-- Server version: 5.0.41
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `paperless`
--

-- --------------------------------------------------------

--
-- Table structure for table `AssignmentComments`
--

CREATE TABLE IF NOT EXISTS `AssignmentComments` (
  `ID` int(11) NOT NULL auto_increment,
  `AssignmentFile` int(11) NOT NULL,
  `LineNumber` int(11) NOT NULL,
  `CommentLength` int(11) NOT NULL,
  `CommentText` text,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `AssignmentComments`
--


-- --------------------------------------------------------

--
-- Table structure for table `AssignmentFiles`
--

CREATE TABLE IF NOT EXISTS `AssignmentFiles` (
  `ID` int(11) NOT NULL auto_increment,
  `GradedAssignment` varchar(128) default NULL,
  `FilePath` varchar(256) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `AssignmentFiles`
--


-- --------------------------------------------------------

--
-- Table structure for table `CourseRelations`
--

CREATE TABLE IF NOT EXISTS `CourseRelations` (
  `ID` int(11) NOT NULL auto_increment,
  `Person` int(11) NOT NULL,
  `Quarter` int(11) NOT NULL,
  `Class` int(11) NOT NULL,
  `Position` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `CourseRelations`
--


-- --------------------------------------------------------

--
-- Table structure for table `Courses`
--

CREATE TABLE IF NOT EXISTS `Courses` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(32) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `Courses`
--

INSERT INTO `Courses` (`ID`, `Name`) VALUES(1, 'CS106A');
INSERT INTO `Courses` (`ID`, `Name`) VALUES(2, 'CS106B');
INSERT INTO `Courses` (`ID`, `Name`) VALUES(3, 'CS106X');

-- --------------------------------------------------------

--
-- Table structure for table `People`
--

CREATE TABLE IF NOT EXISTS `People` (
  `ID` int(11) NOT NULL auto_increment,
  `SUNetID` int(11) NOT NULL,
  `FirstName` varchar(32) default NULL,
  `LastName` varchar(32) default NULL,
  `Email` varchar(64) default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `SUNetID` (`SUNetID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `People`
--


-- --------------------------------------------------------

--
-- Table structure for table `Quarters`
--

CREATE TABLE IF NOT EXISTS `Quarters` (
  `ID` int(11) NOT NULL auto_increment,
  `Quarter` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=88 ;

--
-- Dumping data for table `Quarters`
--

INSERT INTO `Quarters` (`ID`, `Quarter`, `Year`) VALUES(87, 4, 2010);

-- --------------------------------------------------------

--
-- Table structure for table `State`
--

CREATE TABLE IF NOT EXISTS `State` (
  `CurrentQuarter` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `State`
--

INSERT INTO `State` (`CurrentQuarter`) VALUES(87);
