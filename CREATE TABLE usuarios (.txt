CREATE DATABASE  IF NOT EXISTS `jordan` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `jordan`;
-- MySQL dump 10.13  Distrib 5.6.23, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: jordan
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS usuarios;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE usuarios (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  nome varchar(255) NOT NULL,
  cpf varchar(15) NOT NULL,
  data_nac date NOT NULL,
  telefone varchar(20) DEFAULT NULL,
  email varchar(255) NOT NULL,
  senha varchar(255) NOT NULL,
  tipo enum('cliente','estoquista','admin') NOT NULL DEFAULT 'cliente',
  ativo bit(1) NOT NULL DEFAULT b'1',
  criado_em datetime DEFAULT current_timestamp(),
  modificado_em datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  genero enum('Homem','Mulher','Outro') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY cpf (cpf),
  UNIQUE KEY email (email)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES usuarios WRITE;
/*!40000 ALTER TABLE usuarios DISABLE KEYS */;
INSERT INTO usuarios VALUES (1,'','','0000-00-00',NULL,'l@l.com','a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3','cliente','','2025-02-24 16:40:29','2025-02-24 16:58:49',NULL),(3,'Eduardo','47638845811','0000-00-00',NULL,'teste','$2y$10$at3D9.v7mBX9lMYtjRjzYuC8hEJAsvci353hTq08/l9eWi8um13uO','estoquista','','2025-02-24 16:45:41','2025-02-24 15:04:48',NULL),(4,'Eduardo','434.488.438-81','2025-01-29','(11) 99592-0759','cheaterlife333@gmail.com','$2y$10$9oQZo9xrKynGi9mFh.fUfuJdxsoaOoW6VnyM.kXrpW4WQMVDGVuja','admin','\0','2025-02-24 14:42:35','2025-02-24 14:57:23','Homem');
/*!40000 ALTER TABLE usuarios ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-24 15:19:04
