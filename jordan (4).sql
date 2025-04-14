-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 14/04/2025 às 05:54
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `jordan`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `imagens_produto`
--

CREATE TABLE `imagens_produto` (
  `id` bigint(20) NOT NULL,
  `produto_id` bigint(20) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `imagens_produto`
--

INSERT INTO `imagens_produto` (`id`, `produto_id`, `caminho`, `principal`) VALUES
(1, 1, 'uploads/img_67cbd87b987d5.png', 1),
(2, 2, 'uploads/img_67cbd89c34cf3.webp', 1),
(3, 2, 'uploads/img_67cbd89c360b6.jpg', 0),
(4, 3, 'uploads/img_67cbdeb9d490c.jpg', 1),
(5, 3, 'uploads/img_67cbdeb9d5a1d.jpg', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` bigint(20) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `avaliacao` decimal(2,1) DEFAULT NULL CHECK (`avaliacao` between 1.0 and 5.0 and `avaliacao` MOD 0.5 = 0),
  `descricao` varchar(2000) DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL CHECK (`preco` >= 0),
  `quantidade_estoque` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `avaliacao`, `descricao`, `preco`, `quantidade_estoque`, `status`) VALUES
(1, 'caneca', 1.0, 'caneca', 5500.00, 1, 1),
(2, 'oncinha', 5.0, 'teste', 10.00, 2, 1),
(3, 'pizza', 1.0, 'Pizza', 77.00, 4, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `grupo` enum('ADM','EST','CLI') NOT NULL,
  `ativo` tinyint(4) NOT NULL DEFAULT 1,
  `nome` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `genero` enum('masculino','feminino','outro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `email`, `senha`, `grupo`, `ativo`, `nome`, `cpf`, `data_nascimento`, `genero`) VALUES
(1, 'cheaterlife333@gmail.com', '$2y$10$jIcy7DeGWo4Pd0ei9vS0i.CvQ1mhWrtgMoTwvFV5Lc1FcJku5p/H.', '', 1, 'Eduardo', '476.388.458-11', NULL, 'masculino'),
(2, 'e@gmail.com', '$2y$10$/DaC6.YKl8/Lb1JQp.NkQ.hJbSlAVCtfy2fjZH/HKEh6//n1ZnxEy', 'CLI', 1, 'Eduardo edu', '02141857295', NULL, 'masculino'),
(3, 'a@g.com', '$2y$10$WVFYGywt/NqW1B2sqfLTbO.SL1IiAeQVH48lX7iSfbXYvr48BotsS', 'ADM', 1, 'Eduardo edus', '68514892010', NULL, 'masculino'),
(4, '123456@gmail.com', '$2y$10$wQJqV9rYcZPgMxrAie61wONrbW9zlmTE6I4RnBbrIfGK2kx13Rr5u', 'EST', 1, 'Eduardo eduss', '02013144601', NULL, 'masculino'),
(5, 'a@a.com', '123', 'ADM', 1, 'a', '123', '2016-04-07', 'masculino');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `imagens_produto`
--
ALTER TABLE `imagens_produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `imagens_produto`
--
ALTER TABLE `imagens_produto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `imagens_produto`
--
ALTER TABLE `imagens_produto`
  ADD CONSTRAINT `imagens_produto_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
