CREATE DATABASE ums;
USE ums;
CREATE TABLE Customer (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerType ENUM('Household','Business','Government') NOT NULL,
    FullName VARCHAR(100) NOT NULL,
    Address VARCHAR(255) NOT NULL,
    Email VARCHAR(100) UNIQUE,
    Phone VARCHAR(20) NOT NULL,
    RegistrationDate DATE NOT NULL
);
CREATE TABLE UtilityType (
    UtilityTypeID INT AUTO_INCREMENT PRIMARY KEY,
    UtilityName ENUM('Electricity','Water','Gas') NOT NULL,
    Unit VARCHAR(20) NOT NULL
);
CREATE TABLE TariffPlan (
    TariffID INT AUTO_INCREMENT PRIMARY KEY,
    UtilityTypeID INT NOT NULL,
    SlabStart DECIMAL(10,2) NOT NULL,
    SlabEnd DECIMAL(10,2) NOT NULL,
    RatePerUnit DECIMAL(10,2) NOT NULL,
    EffectiveDate DATE NOT NULL,
    FOREIGN KEY (UtilityTypeID) REFERENCES UtilityType(UtilityTypeID)
);
CREATE TABLE Meter (
    MeterID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT NOT NULL,
    UtilityTypeID INT NOT NULL,
    MeterSerial VARCHAR(50) UNIQUE NOT NULL,
    InstallationDate DATE NOT NULL,
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID),
    FOREIGN KEY (UtilityTypeID) REFERENCES UtilityType(UtilityTypeID)
);
CREATE TABLE MeterReading (
    ReadingID INT AUTO_INCREMENT PRIMARY KEY,
    MeterID INT NOT NULL,
    ReadingDate DATE NOT NULL,
    CurrentReading DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (MeterID) REFERENCES Meter(MeterID)
);
CREATE TABLE Bill (
    BillID INT AUTO_INCREMENT PRIMARY KEY,
    MeterID INT NOT NULL,
    BillingMonth VARCHAR(20) NOT NULL,
    Consumption DECIMAL(10,2) NOT NULL,
    TotalAmount DECIMAL(10,2) NOT NULL,
    BillDate DATE NOT NULL,
    Status ENUM('Paid','Unpaid') DEFAULT 'Unpaid',
    FOREIGN KEY (MeterID) REFERENCES Meter(MeterID)
);
CREATE TABLE Payment (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    BillID INT NOT NULL,
    PaymentDate DATE NOT NULL,
    AmountPaid DECIMAL(10 , 2 ) NOT NULL,
    Method ENUM('Cash', 'Card', 'Online') NOT NULL,
    FOREIGN KEY (BillID)
        REFERENCES Bill (BillID)
);

INSERT INTO Customer (CustomerType, FullName, Address, Email, Phone, RegistrationDate) VALUES
('Household','John Silva','No.12 Main St','john@example.com','0711234567','2023-01-10'),
('Business','Royal Bakery','23 Market Road','rbakery@example.com','0772345678','2023-02-11'),
('Household','Nimal Perera','45 Green Lane','nimal@example.com','0753456789','2023-03-15'),
('Government','City Council','1 Govt Ave','council@example.com','0115678901','2023-04-01'),
('Household','Amy Fernando','88 Lake Rd','amy@example.com','0719876543','2023-05-20'),
('Business','TechNova Pvt Ltd','IT Park 5','info@technova.com','0721112223','2023-06-18'),
('Household','Kamal Jayasuriya','19 Temple Rd','kamal@example.com','0711114444','2023-07-12'),
('Government','Health Dept','2 Public Rd','health@example.com','0113344556','2023-08-05'),
('Business','FreshMart','78 Supermarket St','freshmart@example.com','0775556666','2023-09-15'),
('Household','Sajith Wijesinghe','120 Flower St','sajith@example.com','0769876543','2023-10-20');
INSERT INTO UtilityType (UtilityName, Unit) VALUES
('Electricity','kWh'),
('Water','m3'),
('Gas','m3');
INSERT INTO TariffPlan (UtilityTypeID, SlabStart, SlabEnd, RatePerUnit, EffectiveDate) VALUES
(1,0,60,7.50,'2023-01-01'),
(1,61,120,10.00,'2023-01-01'),
(1,121,99999,20.00,'2023-01-01'),
(2,0,30,5.00,'2023-01-01'),
(2,31,99999,15.00,'2023-01-01'),
(3,0,50,8.00,'2023-01-01'),
(3,51,99999,12.00,'2023-01-01');
INSERT INTO Meter (CustomerID, UtilityTypeID, MeterSerial, InstallationDate) VALUES
(1,1,'ELEC-A001','2023-01-15'),
(1,2,'WAT-A001','2023-01-20'),
(2,1,'ELEC-B001','2023-02-15'),
(3,1,'ELEC-C001','2023-03-20'),
(4,2,'WAT-D001','2023-04-10'),
(5,3,'GAS-E001','2023-05-25'),
(6,1,'ELEC-F001','2023-06-30'),
(7,2,'WAT-G001','2023-07-12'),
(8,3,'GAS-H001','2023-08-18'),
(9,1,'ELEC-I001','2023-09-22');
INSERT INTO MeterReading (MeterID, ReadingDate, CurrentReading) VALUES
(1,'2023-02-01',120),
(1,'2023-03-01',240),
(2,'2023-02-01',20),
(3,'2023-03-01',300),
(4,'2023-04-01',150),
(5,'2023-06-01',40),
(6,'2023-07-01',180),
(7,'2023-08-01',30),
(8,'2023-09-01',70),
(9,'2023-10-01',500);
INSERT INTO Bill (MeterID, BillingMonth, Consumption, TotalAmount, BillDate, Status) VALUES
(1,'2023-03',120,900,'2023-03-05','Paid'),
(2,'2023-03',20,100,'2023-03-05','Unpaid'),
(3,'2023-04',80,600,'2023-04-05','Paid'),
(4,'2023-04',50,375,'2023-04-06','Unpaid'),
(5,'2023-07',40,320,'2023-07-05','Paid'),
(6,'2023-08',90,1350,'2023-08-04','Unpaid');
INSERT INTO Payment (BillID, PaymentDate, AmountPaid, Method) VALUES
(1,'2023-03-10',900,'Cash'),
(3,'2023-04-10',600,'Online'),
(5,'2023-07-10',320,'Card');
