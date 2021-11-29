DROP DATABASE IF EXISTS `EMAVRA`;
CREATE DATABASE `EMVRA`;
USE `EMVRA`;



DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `Carnet` int NOT NULL,
  `IDempleado` int NOT NULL,
  `Apellido` varchar(30) DEFAULT NULL,
  `Nombre` varchar(30) DEFAULT NULL,
  `Puesto` varchar(30) DEFAULT NULL,
  `Sueldo` int NOT NULL,
  `Bono_de_venta` int NULL,
  `Fecha_nacimiento` varchar(50) DEFAULT NULL,
  `Lugar_nacimiendo` varchar(50) DEFAULT NULL,
  `Estado_civil` varchar(30) DEFAULT NULL,
  `Telefono` int DEFAULT NULL,
  PRIMARY KEY (`Carnet`, `IDempleado`)
 /* CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`IDpublicidad`) REFERENCES `publicidad` (`ID`),
  CONSTRAINT `ads_ibfk_2` FOREIGN KEY (`IDinter`) REFERENCES `interfaz` (`ID`)*/ 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `procesos`;
CREATE TABLE `procesos`(
`IDproceso` int NOT NULL,
`Fecha` varchar(30) DEFAULT NULL,
`Ciclo_productivo` int DEFAULT NULL,
`Division` varchar(30) DEFAULT NULL,
`IDprovedor` int NULL,
`Cultivo` varchar(50) DEFAULT NULL,
`Cantidad` int DEFAULT NULL,
`Tipo_de_proceso` varchar(50) DEFAULT NULL,
`Precio` int DEFAULT NULL,
`Abono` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes`(
  `Carnet` int NOT NULL,
  `Apellido` varchar(30) DEFAULT NULL,
  `Nombre` varchar(30) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Numero` varchar(15) DEFAULT NULL,
  `Nit` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`Carnet`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios`(
  `Carnet` int NOT NULL,
  `Tipo_de_usuario` varchar(30) DEFAULT NULL,
  `Apellido` varchar(30) DEFAULT NULL,
  `Nombre` varchar(30) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Contraseña` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`Carnet`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
  
  DROP TABLE IF EXISTS `provedores`;
  CREATE TABLE `provedores` (
  `IDprovedor` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Producto` varchar(50) NOT NULL,
    `Nit` varchar(30) DEFAULT NULL,
  `Numero` varchar(100) NOT NULL,
  `Correo` varchar(100) NOT NULL,

  `Empresa donde trabaja` varchar(100) NOT NULL, /*?????*/
  PRIMARY KEY (`IDprovedor`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

DROP TABLE IF EXISTS `registros_de_actividad`;
CREATE TABLE `registros_de_actividad`(
  `Carnet` int NOT NULL,
  `Fecha` varchar(30) DEFAULT NULL,
  `IDusuario` int DEFAULT NULL,
  `Contraseña` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`Carnet`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
  
  DROP TABLE IF EXISTS `reservas`;
  CREATE TABLE `reservas` (
  `IDreserva` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Folio` varchar(50) NOT NULL,
  `Fecha_reserva` varchar(50) NOT NULL,
  `IDCliente` varchar(50) NOT NULL,
  `Concepto` varchar(30) DEFAULT NULL,
  `Total` int(20) NOT NULL,
  `Saldo` int(100) NOT NULL,
  `Correo` varchar(100) NOT NULL,

  `Empresa donde trabaja` varchar(100) NOT NULL, /*?????*/
  PRIMARY KEY (`IDprovedor`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

  
  
