-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2026 at 06:10 AM
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
-- Database: `ums`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GenerateBillForMeter` (IN `p_meter_id` INT, IN `p_billing_month` VARCHAR(20), IN `p_start_date` DATE, IN `p_end_date` DATE, IN `p_bill_date` DATE)   BEGIN
    DECLARE util_type_id INT;
    DECLARE cons DECIMAL(10,2);
    DECLARE total_amt DECIMAL(10,2);

    SELECT UtilityTypeID INTO util_type_id
    FROM Meter
    WHERE MeterID = p_meter_id;

    SET cons = CalculateConsumption(p_meter_id, p_start_date, p_end_date);

    SET total_amt = CalculateBillAmount(util_type_id, cons);

    IF NOT EXISTS (SELECT 1 FROM Bill WHERE MeterID = p_meter_id AND BillingMonth = p_billing_month) THEN
        INSERT INTO Bill (MeterID, BillingMonth, Consumption, TotalAmount, BillDate, Status)
        VALUES (p_meter_id, p_billing_month, cons, total_amt, p_bill_date, 'Unpaid');
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Bill for this meter and month already exists.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ListDefaulters` (IN `min_outstanding_amount` DECIMAL(10,2))   BEGIN
    SELECT
        C.CustomerID,
        C.FullName,
        C.Address,
        C.Phone,
        SUM(B.TotalAmount) AS OutstandingBalance
    FROM
        Customer C
    JOIN
        Meter M ON C.CustomerID = M.CustomerID
    JOIN
        Bill B ON M.MeterID = B.MeterID
    WHERE
        B.Status = 'Unpaid'
    GROUP BY
        C.CustomerID, C.FullName, C.Address, C.Phone
    HAVING
        OutstandingBalance >= min_outstanding_amount
    ORDER BY
        OutstandingBalance DESC;
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `CalculateBillAmount` (`utility_type_id` INT, `consumption` DECIMAL(10,2)) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE remaining_consumption DECIMAL(10,2);
    DECLARE total_bill DECIMAL(10,2) DEFAULT 0.00;
    DECLARE slab_start DECIMAL(10,2);
    DECLARE slab_end DECIMAL(10,2);
    DECLARE rate_per_unit DECIMAL(10,2);
    DECLARE consumed_in_slab DECIMAL(10,2);

    DECLARE tariff_cursor CURSOR FOR
        SELECT SlabStart, SlabEnd, RatePerUnit
        FROM TariffPlan
        WHERE UtilityTypeID = utility_type_id
        ORDER BY SlabStart ASC;

    -- DECLARE HANDLER
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET remaining_consumption = -1;

    SET remaining_consumption = consumption;

    OPEN tariff_cursor;

    slab_loop: LOOP
        FETCH tariff_cursor INTO slab_start, slab_end, rate_per_unit;

        IF remaining_consumption <= 0 OR remaining_consumption = -1 THEN
            LEAVE slab_loop;
        END IF;

        SET consumed_in_slab = LEAST(remaining_consumption, slab_end - slab_start + 1);

        SET total_bill = total_bill + (consumed_in_slab * rate_per_unit);

        SET remaining_consumption = remaining_consumption - consumed_in_slab;
    END LOOP slab_loop;

    CLOSE tariff_cursor;

    RETURN total_bill;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `CalculateConsumption` (`meter_id` INT, `start_date` DATE, `end_date` DATE) RETURNS DECIMAL(10,2) DETERMINISTIC BEGIN
    DECLARE final_reading DECIMAL(10,2);
    DECLARE initial_reading DECIMAL(10,2);

    SELECT CurrentReading INTO final_reading
    FROM MeterReading
    WHERE MeterID = meter_id AND ReadingDate <= end_date
    ORDER BY ReadingDate DESC, ReadingID DESC
    LIMIT 1;

    SELECT CurrentReading INTO initial_reading
    FROM MeterReading
    WHERE MeterID = meter_id AND ReadingDate < start_date
    ORDER BY ReadingDate DESC, ReadingID DESC
    LIMIT 1;

    IF initial_reading IS NULL THEN
        SET initial_reading = 0;
    END IF;

    IF final_reading IS NOT NULL THEN
        RETURN final_reading - initial_reading;
    ELSE
        RETURN 0;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`) VALUES
(1, 'admin@utilitypro.com', 'password123', 'System Admin');

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

CREATE TABLE `bill` (
  `BillID` int(11) NOT NULL,
  `MeterID` int(11) NOT NULL,
  `BillingMonth` varchar(20) NOT NULL,
  `Consumption` decimal(10,2) NOT NULL,
  `TotalAmount` decimal(10,2) NOT NULL,
  `BillDate` date NOT NULL,
  `Status` enum('Paid','Unpaid') DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bill`
--

INSERT INTO `bill` (`BillID`, `MeterID`, `BillingMonth`, `Consumption`, `TotalAmount`, `BillDate`, `Status`) VALUES
(1, 1, '2025-03', 120.00, 900.00, '2025-03-05', 'Paid'),
(2, 2, '2025-03', 20.00, 100.00, '2025-03-05', 'Unpaid'),
(3, 3, '2025-04', 80.00, 600.00, '2025-04-05', 'Paid'),
(4, 4, '2025-04', 50.00, 375.00, '2025-04-06', 'Unpaid'),
(5, 5, '2025-07', 40.00, 320.00, '2025-07-05', 'Paid'),
(6, 6, '2025-08', 90.00, 1350.00, '2025-08-04', 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `CustomerID` int(11) NOT NULL,
  `CustomerType` enum('Household','Business','Government') NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone` varchar(20) NOT NULL,
  `RegistrationDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CustomerID`, `CustomerType`, `FullName`, `Address`, `Email`, `Phone`, `RegistrationDate`) VALUES
(1, 'Household', 'John Silva', 'No.12 Main St', 'john@example.com', '0711234567', '2025-01-10'),
(2, 'Business', 'Royal Bakery', '23 Market Road', 'rbakery@example.com', '0772345678', '2025-02-11'),
(3, 'Household', 'Nimal Perera', '45 Green Lane', 'nimal@example.com', '0753456789', '2025-03-15'),
(4, 'Government', 'City Council', '1 Govt Ave', 'council@example.com', '0115678901', '2025-04-01'),
(5, 'Household', 'Amy Fernando', '88 Lake Rd', 'amy@example.com', '0719876543', '2025-05-20'),
(6, 'Business', 'TechNova Pvt Ltd', 'IT Park 5', 'info@technova.com', '0721112223', '2025-06-18'),
(7, 'Household', 'Kamal Jayasuriya', '19 Temple Rd', 'kamal@example.com', '0711114444', '2025-07-12'),
(8, 'Government', 'Health Dept', '2 Public Rd', 'health@example.com', '0113344556', '2025-08-05'),
(9, 'Business', 'FreshMart', '78 Supermarket St', 'freshmart@example.com', '0775556666', '2025-09-15'),
(10, 'Household', 'Sajith Wijesinghe', '120 Flower St', 'sajith@example.com', '0769876543', '2025-10-20');

-- --------------------------------------------------------

--
-- Table structure for table `meter`
--

CREATE TABLE `meter` (
  `MeterID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `UtilityTypeID` int(11) NOT NULL,
  `MeterSerial` varchar(50) NOT NULL,
  `InstallationDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meter`
--

INSERT INTO `meter` (`MeterID`, `CustomerID`, `UtilityTypeID`, `MeterSerial`, `InstallationDate`) VALUES
(1, 1, 1, 'ELEC-A001', '2025-01-15'),
(2, 1, 2, 'WAT-A001', '2025-01-20'),
(3, 2, 1, 'ELEC-B001', '2025-02-15'),
(4, 3, 1, 'ELEC-C001', '2025-03-20'),
(5, 4, 2, 'WAT-D001', '2025-04-10'),
(6, 5, 3, 'GAS-E001', '2025-05-25'),
(7, 6, 1, 'ELEC-F001', '2025-06-30'),
(8, 7, 2, 'WAT-G001', '2025-07-12'),
(9, 8, 3, 'GAS-H001', '2025-08-18'),
(10, 9, 1, 'ELEC-I001', '2025-09-22');

-- --------------------------------------------------------

--
-- Table structure for table `meterreading`
--

CREATE TABLE `meterreading` (
  `ReadingID` int(11) NOT NULL,
  `MeterID` int(11) NOT NULL,
  `ReadingDate` date NOT NULL,
  `CurrentReading` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meterreading`
--

INSERT INTO `meterreading` (`ReadingID`, `MeterID`, `ReadingDate`, `CurrentReading`) VALUES
(1, 1, '2025-02-01', 120.00),
(2, 1, '2025-03-01', 240.00),
(3, 2, '2025-02-01', 20.00),
(4, 3, '2025-03-01', 300.00),
(5, 4, '2025-04-01', 150.00),
(6, 5, '2025-06-01', 40.00),
(7, 6, '2025-07-01', 180.00),
(8, 7, '2025-08-01', 30.00),
(9, 8, '2025-09-01', 70.00),
(10, 9, '2025-10-01', 500.00);

--
-- Triggers `meterreading`
--
DELIMITER $$
CREATE TRIGGER `before_meterreading_insert` BEFORE INSERT ON `meterreading` FOR EACH ROW BEGIN
    DECLARE last_reading DECIMAL(10,2);

    SELECT MAX(CurrentReading) INTO last_reading
    FROM MeterReading
    WHERE MeterID = NEW.MeterID;

    IF last_reading IS NOT NULL AND NEW.CurrentReading <= last_reading THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'New meter reading must be greater than the previous reading.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `monthlyrevenuereport`
-- (See below for the actual view)
--
CREATE TABLE `monthlyrevenuereport` (
`PaymentMonth` varchar(7)
,`UtilityName` enum('Electricity','Water','Gas')
,`TotalPayments` bigint(21)
,`TotalRevenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PaymentID` int(11) NOT NULL,
  `BillID` int(11) NOT NULL,
  `PaymentDate` date NOT NULL,
  `AmountPaid` decimal(10,2) NOT NULL,
  `Method` enum('Cash','Card','Online') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`PaymentID`, `BillID`, `PaymentDate`, `AmountPaid`, `Method`) VALUES
(1, 1, '2025-03-10', 900.00, 'Cash'),
(2, 3, '2025-04-10', 600.00, 'Online'),
(3, 5, '2025-07-10', 320.00, 'Card');

--
-- Triggers `payment`
--
DELIMITER $$
CREATE TRIGGER `after_payment_insert` AFTER INSERT ON `payment` FOR EACH ROW BEGIN
    UPDATE Bill
    SET Status = 'Paid'
    WHERE BillID = NEW.BillID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tariffplan`
--

CREATE TABLE `tariffplan` (
  `TariffID` int(11) NOT NULL,
  `UtilityTypeID` int(11) NOT NULL,
  `SlabStart` decimal(10,2) NOT NULL,
  `SlabEnd` decimal(10,2) NOT NULL,
  `RatePerUnit` decimal(10,2) NOT NULL,
  `EffectiveDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tariffplan`
--

INSERT INTO `tariffplan` (`TariffID`, `UtilityTypeID`, `SlabStart`, `SlabEnd`, `RatePerUnit`, `EffectiveDate`) VALUES
(1, 1, 0.00, 60.00, 7.50, '2025-01-01'),
(2, 1, 61.00, 120.00, 10.00, '2025-01-01'),
(3, 1, 121.00, 99999.00, 20.00, '2025-01-01'),
(4, 2, 0.00, 30.00, 5.00, '2025-01-01'),
(5, 2, 31.00, 99999.00, 15.00, '2025-01-01'),
(6, 3, 0.00, 50.00, 8.00, '2025-01-01'),
(7, 3, 51.00, 99999.00, 12.00, '2025-01-01');

-- --------------------------------------------------------

--
-- Stand-in structure for view `unpaidbillssummary`
-- (See below for the actual view)
--
CREATE TABLE `unpaidbillssummary` (
`BillID` int(11)
,`CustomerName` varchar(100)
,`UtilityName` enum('Electricity','Water','Gas')
,`BillingMonth` varchar(20)
,`TotalAmount` decimal(10,2)
,`BillDate` date
);

-- --------------------------------------------------------

--
-- Table structure for table `utilitytype`
--

CREATE TABLE `utilitytype` (
  `UtilityTypeID` int(11) NOT NULL,
  `UtilityName` enum('Electricity','Water','Gas') NOT NULL,
  `Unit` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilitytype`
--

INSERT INTO `utilitytype` (`UtilityTypeID`, `UtilityName`, `Unit`) VALUES
(1, 'Electricity', 'kWh'),
(2, 'Water', 'm3'),
(3, 'Gas', 'm3');

-- --------------------------------------------------------

--
-- Structure for view `monthlyrevenuereport`
--
DROP TABLE IF EXISTS `monthlyrevenuereport`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `monthlyrevenuereport`  AS SELECT date_format(`p`.`PaymentDate`,'%Y-%m') AS `PaymentMonth`, `ut`.`UtilityName` AS `UtilityName`, count(`p`.`PaymentID`) AS `TotalPayments`, sum(`p`.`AmountPaid`) AS `TotalRevenue` FROM (((`payment` `p` join `bill` `b` on(`p`.`BillID` = `b`.`BillID`)) join `meter` `m` on(`b`.`MeterID` = `m`.`MeterID`)) join `utilitytype` `ut` on(`m`.`UtilityTypeID` = `ut`.`UtilityTypeID`)) GROUP BY date_format(`p`.`PaymentDate`,'%Y-%m'), `ut`.`UtilityName` ORDER BY date_format(`p`.`PaymentDate`,'%Y-%m') DESC, `ut`.`UtilityName` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `unpaidbillssummary`
--
DROP TABLE IF EXISTS `unpaidbillssummary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `unpaidbillssummary`  AS SELECT `b`.`BillID` AS `BillID`, `c`.`FullName` AS `CustomerName`, `ut`.`UtilityName` AS `UtilityName`, `b`.`BillingMonth` AS `BillingMonth`, `b`.`TotalAmount` AS `TotalAmount`, `b`.`BillDate` AS `BillDate` FROM (((`bill` `b` join `meter` `m` on(`b`.`MeterID` = `m`.`MeterID`)) join `customer` `c` on(`m`.`CustomerID` = `c`.`CustomerID`)) join `utilitytype` `ut` on(`m`.`UtilityTypeID` = `ut`.`UtilityTypeID`)) WHERE `b`.`Status` = 'Unpaid' ORDER BY `b`.`BillDate` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`BillID`),
  ADD KEY `MeterID` (`MeterID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `meter`
--
ALTER TABLE `meter`
  ADD PRIMARY KEY (`MeterID`),
  ADD UNIQUE KEY `MeterSerial` (`MeterSerial`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `UtilityTypeID` (`UtilityTypeID`);

--
-- Indexes for table `meterreading`
--
ALTER TABLE `meterreading`
  ADD PRIMARY KEY (`ReadingID`),
  ADD KEY `MeterID` (`MeterID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `BillID` (`BillID`);

--
-- Indexes for table `tariffplan`
--
ALTER TABLE `tariffplan`
  ADD PRIMARY KEY (`TariffID`),
  ADD KEY `UtilityTypeID` (`UtilityTypeID`);

--
-- Indexes for table `utilitytype`
--
ALTER TABLE `utilitytype`
  ADD PRIMARY KEY (`UtilityTypeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bill`
--
ALTER TABLE `bill`
  MODIFY `BillID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `meter`
--
ALTER TABLE `meter`
  MODIFY `MeterID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `meterreading`
--
ALTER TABLE `meterreading`
  MODIFY `ReadingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tariffplan`
--
ALTER TABLE `tariffplan`
  MODIFY `TariffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `utilitytype`
--
ALTER TABLE `utilitytype`
  MODIFY `UtilityTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`MeterID`) REFERENCES `meter` (`MeterID`);

--
-- Constraints for table `meter`
--
ALTER TABLE `meter`
  ADD CONSTRAINT `meter_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`),
  ADD CONSTRAINT `meter_ibfk_2` FOREIGN KEY (`UtilityTypeID`) REFERENCES `utilitytype` (`UtilityTypeID`);

--
-- Constraints for table `meterreading`
--
ALTER TABLE `meterreading`
  ADD CONSTRAINT `meterreading_ibfk_1` FOREIGN KEY (`MeterID`) REFERENCES `meter` (`MeterID`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`BillID`) REFERENCES `bill` (`BillID`);

--
-- Constraints for table `tariffplan`
--
ALTER TABLE `tariffplan`
  ADD CONSTRAINT `tariffplan_ibfk_1` FOREIGN KEY (`UtilityTypeID`) REFERENCES `utilitytype` (`UtilityTypeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
