-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 06/02/2026 às 18:13
-- Versão do servidor: 11.8.3-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u566100020_marcio`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `empresa_cnpj` varchar(20) NOT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `ie_rg` varchar(50) DEFAULT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL,
  `contribuinte` tinyint(1) DEFAULT 0,
  `cidade_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas`
--

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL,
  `razao_social` varchar(255) NOT NULL,
  `nome_fantasia` varchar(255) DEFAULT NULL,
  `rua` varchar(255) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `ie_rg` varchar(50) DEFAULT NULL,
  `cpf_cnpj` varchar(20) DEFAULT NULL,
  `numero_serie_nfe` varchar(50) DEFAULT NULL,
  `ultimo_numero_nfe` int(11) DEFAULT 0,
  `certificado` text DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `ambiente` enum('homologacao','producao') DEFAULT 'homologacao',
  `cidade_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notas_fiscais`
--

CREATE TABLE `notas_fiscais` (
  `id` int(11) NOT NULL,
  `orcamento_id` int(11) NOT NULL,
  `chave_acesso` varchar(44) NOT NULL,
  `nNF` int(11) NOT NULL,
  `nRec` varchar(20) DEFAULT NULL,
  `protocolo` varchar(20) DEFAULT NULL,
  `xml` text NOT NULL,
  `s_xml` text DEFAULT NULL,
  `status` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos`
--

CREATE TABLE `orcamentos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `data_emissao` date NOT NULL,
  `total_produtos` decimal(10,2) DEFAULT 0.00,
  `total_servicos` decimal(10,2) DEFAULT 0.00,
  `total_geral` decimal(10,2) GENERATED ALWAYS AS (`total_produtos` + `total_servicos`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamento_itens`
--

CREATE TABLE `orcamento_itens` (
  `id` int(11) NOT NULL,
  `orcamento_id` int(11) NOT NULL,
  `tipo_item` enum('produto','servico') NOT NULL,
  `item_id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `quantidade` int(11) DEFAULT 1,
  `valor_unit` decimal(10,2) NOT NULL,
  `valor_total` decimal(10,2) GENERATED ALWAYS AS (`quantidade` * `valor_unit`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `cfop_int` varchar(10) DEFAULT NULL,
  `cfop_ext` varchar(10) DEFAULT NULL,
  `ncm` varchar(20) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `unidade_venda` varchar(10) DEFAULT NULL,
  `perc_icms` decimal(5,2) DEFAULT NULL,
  `perc_pis` decimal(5,2) DEFAULT NULL,
  `perc_cofins` decimal(5,2) DEFAULT NULL,
  `perc_ipi` decimal(5,2) DEFAULT NULL,
  `cst_csosn` varchar(10) DEFAULT NULL,
  `cst_pis` varchar(10) DEFAULT NULL,
  `cst_cofins` varchar(10) DEFAULT NULL,
  `cst_ipi` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `sat_cupons`
--

CREATE TABLE `sat_cupons` (
  `id` int(11) NOT NULL,
  `orcamento_id` int(11) NOT NULL,
  `numero_cupom` int(11) NOT NULL,
  `resposta_json` text NOT NULL,
  `qrcode` text NOT NULL,
  `status` smallint(6) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `empresa_cnpj` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `notas_fiscais`
--
ALTER TABLE `notas_fiscais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orcamento_id` (`orcamento_id`);

--
-- Índices de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `empresa_id` (`empresa_id`);

--
-- Índices de tabela `orcamento_itens`
--
ALTER TABLE `orcamento_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orcamento_id` (`orcamento_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sat_cupons`
--
ALTER TABLE `sat_cupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orcamento_id` (`orcamento_id`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notas_fiscais`
--
ALTER TABLE `notas_fiscais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orcamento_itens`
--
ALTER TABLE `orcamento_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `sat_cupons`
--
ALTER TABLE `sat_cupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `notas_fiscais`
--
ALTER TABLE `notas_fiscais`
  ADD CONSTRAINT `fk_notas_fiscais_orcamentos` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD CONSTRAINT `fk_orcamentos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orcamentos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orcamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `orcamentos_ibfk_2` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`);

--
-- Restrições para tabelas `orcamento_itens`
--
ALTER TABLE `orcamento_itens`
  ADD CONSTRAINT `fk_orc_itens_orcamentos` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orcamento_itens_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`);

--
-- Restrições para tabelas `sat_cupons`
--
ALTER TABLE `sat_cupons`
  ADD CONSTRAINT `sat_cupons_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
