DROP DATABASE IF EXISTS `EMAVRA`;
CREATE DATABASE `EMVRA`;
USE `EMVRA`;



DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `Carnet` int NOT NULL,
  `IDempleado` int NOT NULL,
  `Apellido` varchar(50) DEFAULT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Fecha_nacimiento` varchar(50) DEFAULT NULL,
  `Lugar_nacimiendo` varchar(50) DEFAULT NULL,
  `Estado_civil` varchar(50) DEFAULT NULL,
  `Telefono` int DEFAULT NULL,
  PRIMARY KEY (`Carnet`, `IDempleado`)
 /* CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`IDpublicidad`) REFERENCES `publicidad` (`ID`),
  CONSTRAINT `ads_ibfk_2` FOREIGN KEY (`IDinter`) REFERENCES `interfaz` (`ID`)*/ 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
  
  DROP TABLE IF EXISTS `provedores`;
  CREATE TABLE `provedores` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(50) NOT NULL,
  `Apellido` varchar(50) NOT NULL,
  `Producto` varchar(50) NOT NULL,
    `Nit` varchar(50) DEFAULT NULL,
  `Numero` varchar(50) NOT NULL,
  `Correo` varchar(50) NOT NULL,
  `Empresa donde trabaja` varchar(100) NOT NULL, /*?????*/
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

DROP TABLE IF EXISTS `procesos`;
CREATE TABLE `procesos`(
`ID` int NOT NULL,
`Fecha` varchar(50) DEFAULT NULL,
`Ciclo_productivo` int DEFAULT NULL,
`Division` varchar(50) DEFAULT NULL,
`IDprovedor` int NOT NULL,
`Cultivo` varchar(50) DEFAULT NULL,
`Cantidad` int DEFAULT NULL,
`Tipo_de_proceso` varchar(50) DEFAULT NULL,
`Precio` int DEFAULT NULL,
`Abono` varchar(50) DEFAULT NULL,
PRIMARY KEY (`ID`),
KEY (`IDprovedor`),
CONSTRAINT `reaccion_ibfk_2` FOREIGN KEY (`IDprovedor`) REFERENCES `provedores` (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes`(
  `Carnet` int NOT NULL,
  `Apellido` varchar(50) DEFAULT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Numero` varchar(50) DEFAULT NULL,
  `Nit` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Carnet`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios`(
  `Carnet` int NOT NULL,
  `Tipo_de_usuario` varchar(50) DEFAULT NULL,
  `Apellido` varchar(50) DEFAULT NULL,
  `Nombre` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Contraseña` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Carnet`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
  

DROP TABLE IF EXISTS `registros_de_actividad`;
CREATE TABLE `registros_de_actividad`(
  `Carnet` int NOT NULL,
  `Fecha` varchar(50) DEFAULT NULL,
  `Hora` varchar(50) DEFAULT NULL,
  `IDusuario` int NOT NULL,
  `Tipo_actividad` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Carnet`), 
    KEY `IDusuario` (`IDusuario`),
	CONSTRAINT `reaccion_ibfk_2` FOREIGN KEY (`IDusuario`) REFERENCES `usuarios` (`ID`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
  
  DROP TABLE IF EXISTS `reservas`;
  CREATE TABLE `reservas` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Folio` varchar(50) NOT NULL,
  `Fecha_reserva` varchar(50) NOT NULL,
  `IDCliente` varchar(50) NOT NULL,
  `Concepto` varchar(50) DEFAULT NULL,
  `Total` int(20) NOT NULL,
  `Saldo` int(100) NOT NULL,
  PRIMARY KEY (`ID`), 
    KEY `IDcliente` (`IDcliente`),
	CONSTRAINT `reaccion_ibfk_2` FOREIGN KEY (`IDcliente`) REFERENCES `clientes` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

  
  DROP TABLE IF EXISTS `ventas`;
  CREATE TABLE `ventas` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Tipo` varchar(50) DEFAULT NULL,
  `Producto` varchar(50) DEFAULT NULL,
  `Bono_venta` double NOT NULL,
  `Stock` int DEFAULT NULL,
  `Precio_unitario` double DEFAULT NULL,
  `Descuento` double DEFAULT NULL,
  `Impuesto` double DEFAULT NULL,
  `Unidad_medida` varchar(50) DEFAULT NULL,
  `IDcliente` int NOT NULL,
  PRIMARY KEY (`ID`), 
    KEY `IDcliente` (`IDcliente`),
	CONSTRAINT `reaccion_ibfk_2` FOREIGN KEY (`IDcliente`) REFERENCES `clientes` (`ID`)
  )ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;
  
  DROP TABLE IF EXISTS `facturacion`;
  CREATE TABLE `facturacion` (
	`Carnet` int NOT NULL,
    `folio` varchar(50) DEFAULT NULL,
    `titulo` varchar(50) DEFAULT NULL,
    `IDcliente` int NOT NULL,
    `Num_embarque` int DEFAULT NULL,
    `Fecha_embarque` varchar(50) NOT NULL,
    `Fecha_nacimiento` varchar(50) DEFAULT NULL,
    `Estado` varchar(50) DEFAULT NULL,
    `Moneda` varchar(50) DEFAULT NULL,
    `Uso_factura` varchar(50) DEFAULT NULL,
    `Metodo_pago` varchar(50) DEFAULT NULL,
    `Elaboro` varchar(50) DEFAULT NULL,
    `Comentarios` varchar(50) DEFAULT NULL,
    PRIMARY KEY (`Carnet`), 
    KEY `IDcliente` (`IDcliente`),
	CONSTRAINT `reaccion_ibfk_2` FOREIGN KEY (`IDcliente`) REFERENCES `clientes` (`ID`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;
/*CONSTRAINT `reaccion_ibfk_1` FOREIGN KEY (`IDPublicacion`) REFERENCES `publicacion` (`ID`),
  CONSTRAINT `reaccion_ibfk_2` FOREIGN KEY (`IDUsuario`) REFERENCES `usuario` (`ID`)*/