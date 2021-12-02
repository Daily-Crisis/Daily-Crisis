DROP DATABASE IF EXISTS `EMAVRA`;
CREATE DATABASE `EMVRA`;
USE `EMVRA`;

DROP TABLE IF EXISTS `persona`;
CREATE TABLE `persona` (
`Carnet` int NOT NULL,
`Apellido` varchar(70) DEFAULT NULL,
`Nombre` varchar(70) DEFAULT NULL,
`Correo` varchar(70) DEFAULT NULL,
`Telefono` varchar(70) DEFAULT NULL,
`Estado_civil` varchar(70) DEFAULT NULL,
`Fecha_nacimiento` date DEFAULT NULL,
`Lugar_nacimiento` varchar(70) DEFAULT NULL,
PRIMARY KEY(`Carnet`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `IDpersona` int NOT NULL,
  `IDempleado` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`IDpersona`, `IDempleado`),
  KEY (`IDpersona`),
  CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`IDpersona`) REFERENCES `persona` (`Carnet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
  
  DROP TABLE IF EXISTS `provedores`;
  CREATE TABLE `provedores` (
  `IDpersona` int NOT NULL,
  `Empresa` varchar(100) NOT NULL, 
  `Producto` varchar(70) NOT NULL,
  `Nit` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IDpersona`, `Empresa`),
  KEY(`IDpersona`),
  CONSTRAINT `provedores_ibfk_2` FOREIGN KEY (`IDpersona`) REFERENCES `persona` (`Carnet`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

DROP TABLE IF EXISTS `procesos`;
CREATE TABLE `procesos`(
`ID` int NOT NULL,
`Fecha` varchar(50) DEFAULT NULL,
`Ciclo_productivo` int DEFAULT NULL,
`Division` varchar(50) DEFAULT NULL,
`IDprovedor` int NOT NULL,
`Empresa_provedor` varchar(100) NOT NULL, 
`Cultivo` varchar(50) DEFAULT NULL,
`Cantidad` int DEFAULT NULL,
`Tipo_de_proceso` varchar(50) DEFAULT NULL,
`Precio` int DEFAULT NULL,
`Abono` varchar(50) DEFAULT NULL,
PRIMARY KEY (`ID`),
KEY (`IDprovedor`,`Empresa_provedor`),
CONSTRAINT `procesos_ibfk_2` FOREIGN KEY (`IDprovedor`,`Empresa_provedor`) REFERENCES `provedores` (`IDpersona`,`Empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes`(
  `Nit` varchar(50)  NOT NULL,
  `IDpersona` int NOT NULL,
  
  PRIMARY KEY (`Nit`),
  KEY (`IDcliente`),
  CONSTRAINT `clientes_ibfk_2` FOREIGN KEY (`IDpersona`) REFERENCES `persona` (`Carnet`)

  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios`(
  `IDusuario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `IDpersona` int NOT NULL,
  `Tipo_de_usuario` varchar(50) DEFAULT NULL,
  `Contraseña` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IDusuario`),
  KEY (`IDpersona`),
   CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`IDpersona`) REFERENCES `persona` (`Carnet`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
  
  

DROP TABLE IF EXISTS `registros_de_actividad`;
CREATE TABLE `registros_de_actividad`(
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Fecha` varchar(50) DEFAULT NULL,
  `Hora` varchar(50) DEFAULT NULL,
  `IDusuario` int NOT NULL,
  `Tipo_actividad` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`), 
    KEY `IDusuario` (`IDusuario`),
	CONSTRAINT `registros_de_actividad_ibfk_2` FOREIGN KEY (`IDusuario`) REFERENCES `usuarios` (`ID`)
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
	CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`IDcliente`) REFERENCES `clientes` (`Nit`)
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
	CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`IDcliente`) REFERENCES `clientes` (`Nit`)
  )ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;
  
  DROP TABLE IF EXISTS `facturacion`;
  CREATE TABLE `facturacion` (
	`IDfactura` int NOT NULL,
    `folio` varchar(50) DEFAULT NULL,
    `titulo` varchar(50) DEFAULT NULL,
    `IDcliente` int NOT NULL,
    `Num_embarque` int DEFAULT NULL,
    `Fecha_embarque` varchar(50) NOT NULL,
    `Estado` varchar(50) DEFAULT NULL,
    `Moneda` varchar(50) DEFAULT NULL,
    `Uso_factura` varchar(50) DEFAULT NULL,
    `Metodo_pago` varchar(50) DEFAULT NULL,
    `Elaboro` varchar(50) DEFAULT NULL,
    `Comentarios` varchar(200) DEFAULT NULL,
    PRIMARY KEY (`IDfactura`), 
    KEY `IDcliente` (`IDcliente`),
	CONSTRAINT `facturacion_ibfk_2` FOREIGN KEY (`IDcliente`) REFERENCES `clientes` (`Nit`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;
/*CONSTRAINT `reaccion_ibfk_1` FOREIGN KEY (`IDPublicacion`) REFERENCES `publicacion` (`ID`),
  CONSTRAINT `reaccion_ibfk_2` FOREIGN KEY (`IDUsuario`) REFERENCES `usuario` (`ID`)*/