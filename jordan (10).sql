-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 05/05/2025 às 08:07
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
-- Estrutura para tabela `endereco`
--

CREATE TABLE `endereco` (
  `id` int(11) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `cep` varchar(8) NOT NULL,
  `logradouro` varchar(255) NOT NULL,
  `numero` varchar(10) NOT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) NOT NULL,
  `cidade` varchar(255) NOT NULL,
  `uf` varchar(2) NOT NULL,
  `tipo` enum('faturamento','entrega') NOT NULL,
  `padrao` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `endereco`
--

INSERT INTO `endereco` (`id`, `usuario_id`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `tipo`, `padrao`) VALUES
(1, 2, '04458090', 'Rua Francisco Ianni', '1', 'AA', 'Jardim Ubirajara (Zona Sul)', 'São Paulo', 'SP', 'faturamento', 0),
(2, 3, '04458090', 'Rua Francisco Ianni', '1', 'a', 'Jardim Ubirajara (Zona Sul)', 'São Paulo', 'SP', 'faturamento', 0),
(3, 4, '04458090', 'Rua Francisco Ianni', '2', 'a', 'Jardim Ubirajara (Zona Sul)', 'São Paulo', 'SP', 'faturamento', 0),
(4, 2, '04458-09', 'Rua Francisco Ianni', '03', '', 'Jardim Ubirajara (Zona Sul)', 'São Paulo', 'SP', 'entrega', 0),
(5, 2, '04458-09', 'Rua Francisco Ianni', '04', '', 'Jardim Ubirajara (Zona Sul)', 'São Paulo', 'SP', 'entrega', 1),
(14, 2, '04458090', 'Rua Francisco Ianni', '1233', '', 'Jardim Ubirajara (Zona Sul)', 'São Paulo', 'SP', 'entrega', 0);

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
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `endereco_id` int(11) NOT NULL,
  `pagamento_tipo` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'aguardando pagamento',
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `endereco_id`, `pagamento_tipo`, `total`, `status`, `data_criacao`) VALUES
(1, 2, 5, 'boleto', 5515.00, 'aguardando pagamento', '2025-05-04 18:16:58'),
(2, 2, 5, 'boleto', 5515.00, 'aguardando pagamento', '2025-05-04 18:18:01'),
(3, 2, 5, 'boleto', 5515.00, 'aguardando pagamento', '2025-05-04 18:20:18'),
(4, 2, 4, 'boleto', 338.00, 'aguardando pagamento', '2025-05-04 18:46:55'),
(5, 2, 5, 'cartao', 127.00, 'aguardando pagamento', '2025-05-04 18:49:33'),
(6, 2, 5, 'boleto', 11169.00, 'aguardando pagamento', '2025-05-05 02:59:17'),
(7, 2, 5, 'boleto', 127.00, 'aguardando pagamento', '2025-05-05 03:01:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` bigint(20) NOT NULL,
  `pedido_id` bigint(20) NOT NULL,
  `produto_id` bigint(20) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`) VALUES
(1, 7, 3, 1, 77.00);

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
(3, 'pizza', 1.0, 'Pizza', 77.00, 4, 1);

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
-- Índices de tabela `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `imagens_produto`
--
ALTER TABLE `imagens_produto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `endereco_id` (`endereco_id`);

--
-- Índices de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
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
-- AUTO_INCREMENT de tabela `endereco`
--
ALTER TABLE `endereco`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `imagens_produto`
--
ALTER TABLE `imagens_produto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Restrições para tabelas `endereco`
--
ALTER TABLE `endereco`
  ADD CONSTRAINT `endereco_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `imagens_produto`
--
ALTER TABLE `imagens_produto`
  ADD CONSTRAINT `imagens_produto_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
