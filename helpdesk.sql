/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para db_helpdesk_rocio
CREATE DATABASE IF NOT EXISTS `db_helpdesk` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_helpdesk`;

-- Copiando estrutura para tabela db_helpdesk_rocio.chamados
CREATE TABLE IF NOT EXISTS `chamados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `protocolo` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_solicitante` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `data_abertura` datetime DEFAULT NULL,
  `data_finalizado` datetime DEFAULT NULL,
  `id_equipamento` int NOT NULL,
  `id_set_solicitante` int NOT NULL,
  `id_set_destino` int NOT NULL,
  `id_estabelecimento` int NOT NULL,
  `ramal` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `computador` text COLLATE utf8mb4_unicode_ci,
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anexo` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `protocolo` (`protocolo`,`id`),
  KEY `set_solicitante_chamados` (`id_set_solicitante`),
  KEY `estabelecimentos_chamados` (`id_estabelecimento`),
  KEY `set_dest_chamados` (`id_set_destino`),
  KEY `id_equipamento` (`id_equipamento`),
  CONSTRAINT `estabelecimentos_chamados` FOREIGN KEY (`id_estabelecimento`) REFERENCES `estabelecimentos` (`id`),
  CONSTRAINT `id_eqp_chamados` FOREIGN KEY (`id_equipamento`) REFERENCES `equipamentos` (`id`),
  CONSTRAINT `set_dest_chamados` FOREIGN KEY (`id_set_destino`) REFERENCES `setores` (`id`),
  CONSTRAINT `set_solicitante_chamados` FOREIGN KEY (`id_set_solicitante`) REFERENCES `setores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela db_helpdesk_rocio.chamados_atendimento
CREATE TABLE IF NOT EXISTS `chamados_atendimento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_chamado` int NOT NULL,
  `id_usuario_atendimento` int DEFAULT NULL,
  `id_set_atual` int DEFAULT NULL,
  `id_set_transferencia` int DEFAULT NULL,
  `data` datetime DEFAULT NULL,
  `data_atendimento` datetime DEFAULT NULL,
  `data_transferencia` datetime DEFAULT NULL,
  `data_finalizado` datetime DEFAULT NULL,
  `obs_transf` text COLLATE utf8mb4_unicode_ci,
  `status` enum('Aguardando','Em atendimento','Finalizado') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `chamado_atend` (`id_chamado`),
  KEY `usuario_atend` (`id_usuario_atendimento`),
  KEY `setor_atual_atend` (`id_set_atual`),
  KEY `set_transf_atend` (`id_set_transferencia`),
  CONSTRAINT `chamado_atend` FOREIGN KEY (`id_chamado`) REFERENCES `chamados` (`id`),
  CONSTRAINT `set_transf_atend` FOREIGN KEY (`id_set_transferencia`) REFERENCES `setores` (`id`),
  CONSTRAINT `setor_atual_atend` FOREIGN KEY (`id_set_atual`) REFERENCES `setores` (`id`),
  CONSTRAINT `usuario_atend` FOREIGN KEY (`id_usuario_atendimento`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela db_helpdesk_rocio.chamados_historico
CREATE TABLE IF NOT EXISTS `chamados_historico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_chamado` int NOT NULL,
  `id_usuario` int DEFAULT NULL,
  `conteudo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_mensagem` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `chamado_historico` (`id_chamado`),
  KEY `usuario_historico` (`id_usuario`),
  CONSTRAINT `chamado_historico` FOREIGN KEY (`id_chamado`) REFERENCES `chamados` (`id`),
  CONSTRAINT `usuario_historico` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela db_helpdesk_rocio.chamados_substituicao
CREATE TABLE IF NOT EXISTS `chamados_substituicao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_chamado` int NOT NULL,
  `id_peca` int NOT NULL,
  `id_usuario` int NOT NULL,
  `quantidade` int NOT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `chamado_sub` (`id_chamado`),
  KEY `peca_sub` (`id_peca`),
  KEY `usuario_sub` (`id_usuario`),
  CONSTRAINT `chamado_sub` FOREIGN KEY (`id_chamado`) REFERENCES `chamados` (`id`),
  CONSTRAINT `peca_sub` FOREIGN KEY (`id_peca`) REFERENCES `pecas` (`id`),
  CONSTRAINT `usuario_sub` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela db_helpdesk_rocio.equipamentos
CREATE TABLE IF NOT EXISTS `equipamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_setor` int NOT NULL,
  `status` enum('A','I') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `setor_equipamentos` (`id_setor`),
  CONSTRAINT `setor_equipamentos` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela db_helpdesk_rocio.estabelecimentos
CREATE TABLE IF NOT EXISTS `estabelecimentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para procedure db_helpdesk_rocio.Limpeza
DELIMITER //
CREATE PROCEDURE `Limpeza`()
BEGIN
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE chamados;
TRUNCATE TABLE chamados_atendimento;
TRUNCATE TABLE chamados_historico;
TRUNCATE TABLE chamados_substituicao;
SET FOREIGN_KEY_CHECKS = 1;
END//
DELIMITER ;

-- Copiando estrutura para tabela db_helpdesk_rocio.pecas
CREATE TABLE IF NOT EXISTS `pecas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidade` enum('UNIDADE','METRO','CENTIMETRO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_setor` int NOT NULL,
  `status` enum('A','I') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `setor_pecas` (`id_setor`),
  CONSTRAINT `setor_pecas` FOREIGN KEY (`id_setor`) REFERENCES `setores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela db_helpdesk_rocio.setores
CREATE TABLE IF NOT EXISTS `setores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suporte` enum('S','N') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela db_helpdesk_rocio.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwd` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('SUPORTE','ADM','MAN','MOD') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_setor_suporte` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`login`,`email`),
  KEY `usuarios_setor_sup` (`id_setor_suporte`),
  CONSTRAINT `usuarios_setor_sup` FOREIGN KEY (`id_setor_suporte`) REFERENCES `setores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_7d
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_atend_7d` (
	`quantidade` BIGINT(19) NOT NULL,
	`id_set_atual` INT(10) NULL,
	`data` VARCHAR(5) NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_7m
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_atend_7m` (
	`quantidade` BIGINT(19) NOT NULL,
	`id_set_atual` INT(10) NULL,
	`data` VARCHAR(7) NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_aguard
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_atend_aguard` (
	`quantidade` BIGINT(19) NOT NULL,
	`id_set_atual` INT(10) NULL
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_atual
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_atend_atual` (
	`quantidade` BIGINT(19) NOT NULL,
	`id_set_atual` INT(10) NULL
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_criado
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_atend_criado` (
	`quantidade` BIGINT(19) NOT NULL,
	`id_set_atual` INT(10) NULL
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_fim
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_atend_fim` (
	`quantidade` BIGINT(19) NOT NULL,
	`id_set_atual` INT(10) NULL
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_setor
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_atend_setor` (
	`quantidade` BIGINT(19) NOT NULL,
	`id_set_solicitante` INT(10) NULL,
	`id_set_atual` INT(10) NULL,
	`nome` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_usuarios
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_atend_usuarios` (
	`quantidade` BIGINT(19) NOT NULL,
	`id_set_atual` INT(10) NULL,
	`nome` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_chamados
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_chamados` (
	`id_chamado` INT(10) NOT NULL,
	`id_atendimento` INT(10) NULL,
	`protocolo` VARCHAR(16) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`nome_solicitante` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`descricao` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`ramal` VARCHAR(6) NULL COLLATE 'utf8mb4_unicode_ci',
	`computador` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`ip` VARCHAR(15) NULL COLLATE 'utf8mb4_unicode_ci',
	`anexo` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`equipamento` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`setor_solicitante` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`setor_destino` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`setor_transferencia` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`estabelecimento` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`status` ENUM('Aguardando','Em atendimento','Finalizado') NULL COLLATE 'utf8mb4_unicode_ci',
	`usuario_atendimento` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`data_abertura` DATETIME NULL,
	`data_atendimento` DATETIME NULL,
	`data_transferencia` DATETIME NULL,
	`data_finalizado` DATETIME NULL
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_historico
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_historico` (
	`id_chamado` INT(10) NOT NULL,
	`usuario_atendimento` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`peca` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`setor_destino` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`setor_transferencia` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci',
	`data` DATETIME NULL,
	`conteudo` MEDIUMTEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`quantidade` INT(10) NULL,
	`unidade` ENUM('UNIDADE','METRO','CENTIMETRO') NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_usuarios
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `vw_usuarios` (
	`id` INT(10) NOT NULL,
	`nome` VARCHAR(60) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`login` VARCHAR(60) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`email` VARCHAR(120) NULL COLLATE 'utf8mb4_unicode_ci',
	`status` ENUM('A','I') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`tipo` ENUM('SUPORTE','ADM','MAN','MOD') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`setor_id` INT(10) NOT NULL,
	`setor` VARCHAR(60) NULL COLLATE 'utf8mb4_unicode_ci'
) ENGINE=MyISAM;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_7d
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_atend_7d`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_atend_7d` AS select count(`chamados_atendimento`.`id`) AS `quantidade`,`chamados_atendimento`.`id_set_atual` AS `id_set_atual`,date_format(`chamados_atendimento`.`data`,'%d-%m') AS `data` from `chamados_atendimento` where (cast(`chamados_atendimento`.`data` as date) between (curdate() + interval -(7) day) and curdate()) group by cast(`chamados_atendimento`.`data` as date),`chamados_atendimento`.`id_set_atual` having (count(`chamados_atendimento`.`id`) > 0) order by `chamados_atendimento`.`data` desc

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_7m
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_atend_7m`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_atend_7m` AS select count(`chamados_atendimento`.`id`) AS `quantidade`,`chamados_atendimento`.`id_set_atual` AS `id_set_atual`,date_format(`chamados_atendimento`.`data`,'%m-%Y') AS `data` from `chamados_atendimento` where (cast(`chamados_atendimento`.`data` as date) between (curdate() + interval -(7) month) and curdate()) group by month(`chamados_atendimento`.`data`),year(`chamados_atendimento`.`data`),`chamados_atendimento`.`id_set_atual` having (count(`chamados_atendimento`.`id`) > 0) order by `chamados_atendimento`.`data` desc

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_aguard
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_atend_aguard`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_atend_aguard` AS select count(`chamados_atendimento`.`id_chamado`) AS `quantidade`,`chamados_atendimento`.`id_set_atual` AS `id_set_atual` from `chamados_atendimento` where (`chamados_atendimento`.`status` = 'Aguardando') group by `chamados_atendimento`.`id_set_atual` having (count(`chamados_atendimento`.`id_chamado`) > 0);

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_atual
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_atend_atual`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_atend_atual` AS select count(`chamados_atendimento`.`id_chamado`) AS `quantidade`,`chamados_atendimento`.`id_set_atual` AS `id_set_atual` from `chamados_atendimento` where (`chamados_atendimento`.`status` = 'Em atendimento') group by `chamados_atendimento`.`id_set_atual` having (count(`chamados_atendimento`.`id_chamado`) > 0);

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_criado
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_atend_criado`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_atend_criado` AS select count(`chamados_atendimento`.`id_chamado`) AS `quantidade`,`chamados_atendimento`.`id_set_atual` AS `id_set_atual` from `chamados_atendimento` group by `chamados_atendimento`.`id_set_atual` having (count(`chamados_atendimento`.`id_chamado`) > 0);

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_fim
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_atend_fim`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_atend_fim` AS select count(`chamados_atendimento`.`id_chamado`) AS `quantidade`,`chamados_atendimento`.`id_set_atual` AS `id_set_atual` from `chamados_atendimento` where (`chamados_atendimento`.`status` = 'Finalizado') group by `chamados_atendimento`.`id_set_atual` having (count(`chamados_atendimento`.`id_chamado`) > 0);

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_setor
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_atend_setor`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_atend_setor` AS select count(`a`.`id`) AS `quantidade`,`c`.`id_set_solicitante` AS `id_set_solicitante`,`a`.`id_set_atual` AS `id_set_atual`,`s`.`nome` AS `nome` from ((`chamados_atendimento` `a` left join `chamados` `c` on((`c`.`id` = `a`.`id_chamado`))) left join `setores` `s` on((`c`.`id_set_solicitante` = `s`.`id`))) where (`s`.`status` = 'A') group by `c`.`id_set_solicitante`,`a`.`id_set_atual` having (count(`a`.`id`) > 0) order by `quantidade` desc;

-- Copiando estrutura para view db_helpdesk_rocio.vw_atend_usuarios
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_atend_usuarios`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_atend_usuarios` AS select count(`a`.`id`) AS `quantidade`,`a`.`id_set_atual` AS `id_set_atual`,`u`.`nome` AS `nome` from (`chamados_atendimento` `a` left join `usuarios` `u` on((`a`.`id_usuario_atendimento` = `u`.`id`))) where ((`a`.`status` = 'Finalizado') and (`u`.`status` = 'A')) group by `u`.`nome` having (count(`a`.`id`) > 0) order by `quantidade` desc;

-- Copiando estrutura para view db_helpdesk_rocio.vw_chamados
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_chamados`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_chamados` AS select `c`.`id` AS `id_chamado`,`a`.`id` AS `id_atendimento`,`c`.`protocolo` AS `protocolo`,`c`.`nome_solicitante` AS `nome_solicitante`,`c`.`descricao` AS `descricao`,`c`.`ramal` AS `ramal`,`c`.`computador` AS `computador`,`c`.`ip` AS `ip`,`c`.`anexo` AS `anexo`,`e`.`nome` AS `equipamento`,`s`.`nome` AS `setor_solicitante`,`se`.`nome` AS `setor_destino`,`see`.`nome` AS `setor_transferencia`,`l`.`nome` AS `estabelecimento`,`a`.`status` AS `status`,`u`.`nome` AS `usuario_atendimento`,`a`.`data` AS `data_abertura`,`a`.`data_atendimento` AS `data_atendimento`,`a`.`data_transferencia` AS `data_transferencia`,`a`.`data_finalizado` AS `data_finalizado` from (((((((`chamados` `c` left join `chamados_atendimento` `a` on((`c`.`id` = `a`.`id_chamado`))) left join `equipamentos` `e` on((`c`.`id_equipamento` = `e`.`id`))) left join `setores` `s` on((`c`.`id_set_solicitante` = `s`.`id`))) left join `setores` `se` on((`a`.`id_set_atual` = `se`.`id`))) left join `setores` `see` on((`a`.`id_set_transferencia` = `see`.`id`))) left join `estabelecimentos` `l` on((`c`.`id_estabelecimento` = `l`.`id`))) left join `usuarios` `u` on((`a`.`id_usuario_atendimento` = `u`.`id`)));

-- Copiando estrutura para view db_helpdesk_rocio.vw_historico
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_historico`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_historico` AS select distinct `t`.`id_chamado` AS `id_chamado`,`t`.`usuario_atendimento` AS `usuario_atendimento`,`t`.`peca` AS `peca`,`t`.`setor_destino` AS `setor_destino`,`t`.`setor_transferencia` AS `setor_transferencia`,`t`.`data` AS `data`,`t`.`conteudo` AS `conteudo`,`t`.`quantidade` AS `quantidade`,`t`.`unidade` AS `unidade` from (select `v`.`id_chamado` AS `id_chamado`,`v`.`usuario_atendimento` AS `usuario_atendimento`,NULL AS `peca`,`v`.`setor_destino` AS `setor_destino`,`v`.`setor_transferencia` AS `setor_transferencia`,`v`.`data_transferencia` AS `data`,`a`.`obs_transf` AS `conteudo`,NULL AS `quantidade`,NULL AS `unidade` from (`vw_chamados` `v` left join `chamados_atendimento` `a` on((`a`.`id` = `v`.`id_atendimento`))) union all select `m`.`id_chamado` AS `id_chamado`,`u`.`nome` AS `usuario_atendimento`,NULL AS `peca`,NULL AS `setor_destino`,NULL AS `setor_transferencia`,`m`.`data_mensagem` AS `data`,`m`.`conteudo` AS `conteudo`,NULL AS `quantidade`,NULL AS `unidade` from (`chamados_historico` `m` left join `usuarios` `u` on((`m`.`id_usuario` = `u`.`id`))) union all select `s`.`id_chamado` AS `id_chamado`,`u`.`nome` AS `usuario_atendimento`,`p`.`nome` AS `peca`,NULL AS `setor_destino`,NULL AS `setor_transferencia`,`s`.`data` AS `data`,NULL AS `conteudo`,`s`.`quantidade` AS `quantidade`,`p`.`unidade` AS `unidade` from ((`chamados_substituicao` `s` left join `pecas` `p` on((`p`.`id` = `s`.`id_peca`))) left join `usuarios` `u` on((`u`.`id` = `s`.`id_usuario`)))) `t` where (`t`.`data` is not null) order by `t`.`data`;

-- Copiando estrutura para view db_helpdesk_rocio.vw_usuarios
-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `vw_usuarios`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_usuarios` AS select `u`.`id` AS `id`,`u`.`nome` AS `nome`,`u`.`login` AS `login`,`u`.`email` AS `email`,`u`.`status` AS `status`,`u`.`tipo` AS `tipo`,`s`.`id` AS `setor_id`,`s`.`nome` AS `setor` from (`usuarios` `u` join `setores` `s`) where (`u`.`id_setor_suporte` = `s`.`id`);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
