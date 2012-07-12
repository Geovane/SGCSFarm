-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: 12/07/2012 às 16h24min
-- Versão do Servidor: 5.5.16
-- Versão do PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Banco de Dados: `2faseteste`
--

-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `acesso_funcionarios`
--
CREATE TABLE IF NOT EXISTS `acesso_funcionarios` (
`loginSys` varchar(45)
,`senhaSys` varchar(45)
,`usuarioBugzilla` varchar(45)
,`senhaBugzilla` varchar(45)
,`usuarioGit` varchar(45)
,`senhaGit` varchar(45)
,`nomeFunc` varchar(45)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `colaboradores`
--

CREATE TABLE IF NOT EXISTS `colaboradores` (
  `projeto_idprojeto` int(11) NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  `idcolaboradores` int(11) NOT NULL AUTO_INCREMENT,
  `dedicacaoMes` int(11) DEFAULT '0',
  `funcaoProjeto_idfuncaoProjeto` int(11) NOT NULL,
  PRIMARY KEY (`idcolaboradores`),
  UNIQUE KEY `idcolaboradores_UNIQUE` (`idcolaboradores`),
  KEY `fk_colaboradores_funcaoProjeto1` (`funcaoProjeto_idfuncaoProjeto`),
  KEY `fk_colaboradores_funcionario1` (`funcionario_idfuncionario`),
  KEY `fk_colaboradores_projeto1` (`projeto_idprojeto`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='IdColaboradoes é uma chave indexada, da tabela colaboradores, isso foi nescessario pois funcionarios não podem se repetir em projetos, logo idprojeto e idfuncionario são chaves.\nObs: quem identifica a tabela colaborars nas relaçoes externas é idColaboradores' AUTO_INCREMENT=36 ;

--
-- Extraindo dados da tabela `colaboradores`
--

INSERT INTO `colaboradores` (`projeto_idprojeto`, `funcionario_idfuncionario`, `idcolaboradores`, `dedicacaoMes`, `funcaoProjeto_idfuncaoProjeto`) VALUES
(1, 1, 10, 30, 40),
(1, 2, 11, 25, 10),
(1, 3, 12, 22, 20),
(2, 7, 16, 25, 10),
(2, 8, 17, 20, 20),
(2, 9, 18, 15, 30),
(3, 10, 20, 12, 10),
(3, 2, 21, 20, 10),
(3, 19, 23, 12, 10),
(1, 19, 24, 40, 10),
(1, 9, 30, 56, 10),
(1, 10, 35, 34, 30);

-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `colaboradores_projetos`
--
CREATE TABLE IF NOT EXISTS `colaboradores_projetos` (
`estado_idestado` int(11)
,`idGerente` int(11)
,`dataFim` date
,`dataInc` date
,`idprojeto` int(11)
,`nomeProj` varchar(45)
,`descricaoProj` text
,`nomeFuncinario` varchar(45)
,`emailFuncinario` varchar(45)
,`idfuncionario` int(11)
,`dedicacaoMes` int(11)
,`funcao` varchar(45)
);
-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `dados_funcionario_filial`
--
CREATE TABLE IF NOT EXISTS `dados_funcionario_filial` (
`idfuncionario` int(11)
,`nome` varchar(45)
,`foto` varchar(100)
,`nomeEmpresa` varchar(30)
,`idempresaFilial` int(11)
,`empresa_idempresa` int(11)
);
-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `dados_git_projeto`
--
CREATE TABLE IF NOT EXISTS `dados_git_projeto` (
`repositorioGit` varchar(100)
,`chave` text
,`nomeProj` varchar(45)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `empresa`
--

CREATE TABLE IF NOT EXISTS `empresa` (
  `idempresa` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `tel` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `site` varchar(45) DEFAULT NULL,
  `cep` varchar(45) DEFAULT NULL,
  `endereco` varchar(45) NOT NULL,
  `rezaoSocial` varchar(45) DEFAULT NULL,
  `responsavelGeral` int(11) NOT NULL,
  PRIMARY KEY (`idempresa`),
  UNIQUE KEY `nome_UNIQUE` (`nome`),
  KEY `fk_empresa_funcionario1` (`responsavelGeral`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Extraindo dados da tabela `empresa`
--

INSERT INTO `empresa` (`idempresa`, `nome`, `tel`, `email`, `site`, `cep`, `endereco`, `rezaoSocial`, `responsavelGeral`) VALUES
(1, 'SoftFarm', '73 3634 1874', 'softfarm@gmail.com', 'www.softfarm.com', '45 650 000', 'Rua Conselheiro Dantas 23 Centro Ilheus BA', '03454876000109', 19);

-- --------------------------------------------------------

--
-- Estrutura da tabela `empresafilial`
--

CREATE TABLE IF NOT EXISTS `empresafilial` (
  `idempresaFilial` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) NOT NULL,
  `tel` varchar(15) NOT NULL,
  `endereco` varchar(45) NOT NULL,
  `responsavel` int(11) NOT NULL,
  `empresa_idempresa` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  `cep` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idempresaFilial`),
  KEY `fk_empresaFilial_empresa1` (`empresa_idempresa`),
  KEY `fk_empresaFilial_funcionario1` (`responsavel`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `empresafilial`
--

INSERT INTO `empresafilial` (`idempresaFilial`, `nome`, `tel`, `endereco`, `responsavel`, `empresa_idempresa`, `email`, `cep`) VALUES
(1, 'SoftFarm - EUA', '56 8945 9891', 'FDGDFSGFDSGS', 24, 1, 'ghd@hud.com', '34 567 678'),
(2, 'SoftFarm - BR', '73 3639 5149', 'Rua da Linha 1029 Barra Ilheus BA ', 19, 1, 'softfarmbr@gmail.com', '45 650 000');

-- --------------------------------------------------------

--
-- Estrutura da tabela `estado`
--

CREATE TABLE IF NOT EXISTS `estado` (
  `idestado` int(11) NOT NULL AUTO_INCREMENT,
  `tipoDeEstado` varchar(45) NOT NULL,
  PRIMARY KEY (`idestado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Cadastrados pelo desenvolvedores' AUTO_INCREMENT=8 ;

--
-- Extraindo dados da tabela `estado`
--

INSERT INTO `estado` (`idestado`, `tipoDeEstado`) VALUES
(1, 'Fase de planejamento'),
(2, 'Fase inicial'),
(3, 'Em andamento'),
(4, 'Fase de teste'),
(5, 'Fase de revisao'),
(6, 'Fase de conclusao'),
(7, 'Concluido');

-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `estado_projeto`
--
CREATE TABLE IF NOT EXISTS `estado_projeto` (
`tipoDeEstado` varchar(45)
,`nomeProj` varchar(45)
,`idprojeto` int(11)
);
-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `filias_das_empresas`
--
CREATE TABLE IF NOT EXISTS `filias_das_empresas` (
`nomeEmp` varchar(45)
,`nomeFilial` varchar(30)
,`telFilial` varchar(15)
,`enderecoFilial` varchar(45)
,`responsavelFilial` int(11)
,`telEmp` varchar(45)
,`emailEmp` varchar(45)
,`site` varchar(45)
,`cepEmp` varchar(45)
,`endereco` varchar(45)
,`rezaoSocialEmp` varchar(45)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `funcaoprojeto`
--

CREATE TABLE IF NOT EXISTS `funcaoprojeto` (
  `idfuncaoProjeto` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idfuncaoProjeto`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

--
-- Extraindo dados da tabela `funcaoprojeto`
--

INSERT INTO `funcaoprojeto` (`idfuncaoProjeto`, `descricao`) VALUES
(10, 'DBA'),
(20, 'Programador'),
(30, 'Analista'),
(40, 'Gerente');

-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `funcao_colaboradores_projeto`
--
CREATE TABLE IF NOT EXISTS `funcao_colaboradores_projeto` (
`descricao` varchar(45)
,`nomeProj` varchar(45)
,`nomeFunc` varchar(45)
,`dedicacaoMes` int(11)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionario`
--

CREATE TABLE IF NOT EXISTS `funcionario` (
  `idfuncionario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `documentoIdentificacao` varchar(45) NOT NULL,
  `login` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `empresaFilial_idempresaFilial` int(11) DEFAULT '0',
  `primeiroAcesso` tinyint(1) DEFAULT '0',
  `foto` varchar(100) NOT NULL,
  PRIMARY KEY (`idfuncionario`),
  UNIQUE KEY `login_UNIQUE` (`login`),
  UNIQUE KEY `cpf_UNIQUE` (`documentoIdentificacao`),
  KEY `fk_funcionario_empresaFilial1` (`empresaFilial_idempresaFilial`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `funcionario`
--

INSERT INTO `funcionario` (`idfuncionario`, `nome`, `documentoIdentificacao`, `login`, `senha`, `email`, `empresaFilial_idempresaFilial`, `primeiroAcesso`, `foto`) VALUES
(1, 'Mateus Passos', '123456', 'MPassos', '123', 'tetus4@hotmail.com', 2, 0, '/images/fotosFunc/usuarioPadrao.jpg'),
(2, 'Luiz Daud', '1233', 'LDaud', '123', 'luizdaud@hotmail.com', 2, 0, '/images/fotosFunc/usuarioPadrao.jpg'),
(3, 'Bruno Pereira', '138217840', 'BPereira', '123', 'brunopereira@hotmail.com', 2, 0, '/images/fotosFunc/usuarioPadrao.jpg'),
(4, 'Carlos Henrique', '12345678', 'CHenrique', '123', 'carloshenrique@hotmail.com', 2, 0, '/images/fotosFunc/usuarioPadrao.jpg'),
(5, 'Sabina Moreno', '134565432', 'SMoreno', '123', 'sabinamoreno@hotmail.com', 2, 0, '/images/fotosFunc/usuarioPadrao.jpg');

-- --------------------------------------------------------

--
-- Estrutura da tabela `funfaztarefa`
--

CREATE TABLE IF NOT EXISTS `funfaztarefa` (
  `tarefa_idtarefa` int(11) NOT NULL,
  `colaboradores_idcolaboradores` int(11) NOT NULL,
  PRIMARY KEY (`tarefa_idtarefa`,`colaboradores_idcolaboradores`),
  KEY `fk_funFazTarefa_colaboradores1` (`colaboradores_idcolaboradores`),
  KEY `fk_funFazTarefa_tarefa1` (`tarefa_idtarefa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `funfaztarefa`
--

INSERT INTO `funfaztarefa` (`tarefa_idtarefa`, `colaboradores_idcolaboradores`) VALUES
(33, 10),
(37, 10),
(41, 17),
(42, 17),
(44, 20),
(46, 20),
(45, 24);

-- --------------------------------------------------------

--
-- Estrutura da tabela `projeto`
--

CREATE TABLE IF NOT EXISTS `projeto` (
  `idprojeto` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `descricao` text,
  `dataInc` date NOT NULL,
  `dataFim` date NOT NULL,
  `idGerente` int(11) NOT NULL,
  `estado_idestado` int(11) NOT NULL,
  PRIMARY KEY (`idprojeto`),
  UNIQUE KEY `nome_UNIQUE` (`nome`),
  KEY `fk_projeto_estado1` (`estado_idestado`),
  KEY `fk_projeto_funcionario1` (`idGerente`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Extraindo dados da tabela `projeto`
--

INSERT INTO `projeto` (`idprojeto`, `nome`, `descricao`, `dataInc`, `dataFim`, `idGerente`, `estado_idestado`) VALUES
(1, 'Proj A', 'Desenvolvimento de um sistema para gerenciamento de configuração de software.', '2012-04-01', '2012-07-30', 1, 2),
(2, 'Proj B', 'Sistema completo de automação comercial.', '2012-04-01', '2012-07-30', 7, 5),
(3, 'Proj C', 'teste', '2012-07-03', '1989-09-09', 24, 2),
(4, 'Proj D', 'Lorem Ipsum é simplesment', '2012-07-03', '1289-09-09', 19, 2),
(5, 'Proj E', 'testesteatestesteatestest', '1989-09-09', '1890-09-09', 19, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `projetobugzilla`
--

CREATE TABLE IF NOT EXISTS `projetobugzilla` (
  `projeto_idprojeto` int(11) NOT NULL,
  `nomeProjeto` varchar(45) NOT NULL,
  PRIMARY KEY (`projeto_idprojeto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `projetobugzilla`
--

INSERT INTO `projetobugzilla` (`projeto_idprojeto`, `nomeProjeto`) VALUES
(1, 'Proj A'),
(3, 'Proj C'),
(4, 'Proj D'),
(5, 'Proj E');

-- --------------------------------------------------------

--
-- Estrutura da tabela `projetogit`
--

CREATE TABLE IF NOT EXISTS `projetogit` (
  `projeto_idprojeto` int(11) NOT NULL,
  `repositorio` varchar(100) NOT NULL,
  `chave` text NOT NULL,
  PRIMARY KEY (`projeto_idprojeto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `projetogit`
--

INSERT INTO `projetogit` (`projeto_idprojeto`, `repositorio`, `chave`) VALUES
(2, 'Proj B', 'ydhbcgfhkchn'),
(3, 'Proj C', 'asdasdsda'),
(4, 'Proj D', 'asdasdsda'),
(5, 'Proj E', 'asdasdsda');

-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `projetos_filiais`
--
CREATE TABLE IF NOT EXISTS `projetos_filiais` (
`nomeProj` varchar(45)
,`idprojeto` int(11)
,`descricao` text
,`dataInc` date
,`dataFim` date
,`idGerente` int(11)
,`estado_idestado` int(11)
,`nome` varchar(45)
,`email` varchar(45)
,`idempresaFilial` int(11)
,`nomeFilial` varchar(30)
);
-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `projetos_gerente_filial_colaboradores`
--
CREATE TABLE IF NOT EXISTS `projetos_gerente_filial_colaboradores` (
`idprojeto` int(11)
,`nomeProj` varchar(45)
,`dataFim` date
,`dataInc` date
,`estadoProj` varchar(45)
,`idGerente` int(11)
,`nomeGerente` varchar(45)
,`descricaoProj` text
,`idFilialProj` int(11)
,`nomeFilialProj` varchar(30)
,`idFuncionarioColaborador` int(11)
,`nomeColaborador` varchar(45)
,`dedicacaoMesColaborador` int(11)
,`idColaborador` int(11)
,`funcaoColaborador` varchar(45)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `redefinirsenha`
--

CREATE TABLE IF NOT EXISTS `redefinirsenha` (
  `hash` varchar(40) NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  PRIMARY KEY (`hash`),
  KEY `fk_redefinirSenha_funcionario1` (`funcionario_idfuncionario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `redefinirsenha`
--

INSERT INTO `redefinirsenha` (`hash`, `funcionario_idfuncionario`) VALUES
('095hjvbgt56', 1),
('b236hmz34o9', 2),
('ie64jd67490', 3),
('8rhdkio278d', 4),
('78dgh389oa2', 5),
('io89bn45mj1', 7),
('o9lpmszv23n5', 8),
('76ghbvzx34g', 9),
('10plmzxc67h', 10);

-- --------------------------------------------------------

--
-- Estrutura da tabela `sysconfig`
--

CREATE TABLE IF NOT EXISTS `sysconfig` (
  `idsysConfig` int(11) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `valor` varchar(45) NOT NULL,
  `empresa_idempresa` int(11) NOT NULL,
  PRIMARY KEY (`idsysConfig`),
  UNIQUE KEY `nome_UNIQUE` (`nome`),
  KEY `fk_sysConfig_empresa1` (`empresa_idempresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='configurações gerais do sistema\n\nPrimeira: Usuario git da empresa';

--
-- Extraindo dados da tabela `sysconfig`
--

INSERT INTO `sysconfig` (`idsysConfig`, `nome`, `valor`, `empresa_idempresa`) VALUES
(1, 'Usuario Git Master', 'SoftFarm', 1),
(2, 'Usuario Bugzila Master', 'SoftFarm', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tarefa`
--

CREATE TABLE IF NOT EXISTS `tarefa` (
  `idtarefa` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` text NOT NULL,
  `dataInc` datetime NOT NULL,
  `dataFim` datetime DEFAULT NULL,
  `estado_idestado` int(11) NOT NULL,
  `dataEntrega` datetime NOT NULL,
  PRIMARY KEY (`idtarefa`),
  KEY `fk_tarefa_estado1` (`estado_idestado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=47 ;

--
-- Extraindo dados da tabela `tarefa`
--

INSERT INTO `tarefa` (`idtarefa`, `descricao`, `dataInc`, `dataFim`, `estado_idestado`, `dataEntrega`) VALUES
(32, 'analisar as tarefas concluidas', '2012-06-08 00:00:00', '2012-07-10 00:00:00', 1, '2012-06-28 00:00:00'),
(33, 'gerenciar as tarefas', '2012-04-01 00:00:00', '2012-07-30 00:00:00', 3, '2012-08-10 00:00:00'),
(34, 'xafsadfsdfs', '2012-06-05 07:10:05', '2012-06-30 07:10:05', 3, '2012-07-20 07:10:05'),
(35, 'sdadasd', '2012-06-01 19:39:50', '2012-07-04 19:39:50', 2, '2012-08-04 19:39:50'),
(36, 'dscxcz\\', '2012-06-03 19:50:21', '2012-06-16 19:50:21', 1, '2012-07-14 19:51:55'),
(37, 'hgjjhgjgh', '2012-06-01 07:08:09', '2012-06-30 07:08:09', 2, '2012-06-30 07:09:03'),
(41, 'fsdfsdf', '2012-06-01 19:42:13', '2012-06-30 19:42:13', 2, '0000-00-00 00:00:00'),
(42, 'dasda', '2012-06-03 05:28:45', '2012-06-30 05:28:45', 2, '0000-00-00 00:00:00'),
(44, 'teste', '2012-07-03 20:47:25', '2012-07-03 20:47:25', 7, '0000-00-00 00:00:00'),
(45, 'testes', '2012-07-03 03:09:05', '2012-07-31 03:09:05', 2, '0000-00-00 00:00:00'),
(46, ',n,mn,m', '2012-07-17 04:05:19', '2012-07-27 04:05:19', 2, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `tarefa_colaborador_projetos`
--
CREATE TABLE IF NOT EXISTS `tarefa_colaborador_projetos` (
`nomeProj` varchar(45)
,`idColab` int(11)
,`nomeFunc` varchar(45)
,`idtarefa` int(11)
,`descricaoTarefa` text
,`dataInc` datetime
,`estado_idestado` int(11)
,`dataFim` datetime
,`dataEntrega` datetime
);
-- --------------------------------------------------------

--
-- Estrutura stand-in para visualizar `tipo_estado_tarefa_colaborador_projetos`
--
CREATE TABLE IF NOT EXISTS `tipo_estado_tarefa_colaborador_projetos` (
`nomeProj` varchar(45)
,`idfuncionario` int(11)
,`nomeFunc` varchar(45)
,`descricaoTarefa` text
,`dataInc` datetime
,`dataFim` datetime
,`tipoDeEstado` varchar(45)
);
-- --------------------------------------------------------

--
-- Estrutura da tabela `usuariobugzilla`
--

CREATE TABLE IF NOT EXISTS `usuariobugzilla` (
  `funcionario_idfuncionario` int(11) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  PRIMARY KEY (`funcionario_idfuncionario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuariobugzilla`
--

INSERT INTO `usuariobugzilla` (`funcionario_idfuncionario`, `usuario`, `senha`) VALUES
(1, 'm56h', 'f45hj667'),
(2, 'm78s', 'mw873542'),
(3, 'p45r', 'pg783545'),
(4, 'c23e', 'ec348912'),
(5, 'j71l', 'js672234'),
(7, 'm34c', 'ma763904'),
(8, 'm90m', 'ms543444'),
(9, 'p63d', 'pb897821'),
(10, 'd66e', 'dd104487'),
(19, 'x9', '99a2dd0d8fa45d2ebe6e48c8c024ededa3287688'),
(20, 'geovane', '3827657f02349634eb0ffbe5eb4bd42c620b4870'),
(23, 'teste', '7c4a8d09ca3762af61e59520943dc26494f8941b'),
(24, 'bruno', '40bd001563085fc35165329ea1ff5c5ecbdbbeef'),
(26, 'sadasd', '8cb2237d0679ca88db6464eac60da96345513964'),
(27, 'zezinho', '250454f6b8bbe438b3679cd8e510c583b7dfd4d6');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuariogit`
--

CREATE TABLE IF NOT EXISTS `usuariogit` (
  `funcionario_idfuncionario` int(11) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  PRIMARY KEY (`funcionario_idfuncionario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuariogit`
--

INSERT INTO `usuariogit` (`funcionario_idfuncionario`, `usuario`, `senha`) VALUES
(1, 'm56h', 'f45hj667'),
(2, 'm78s', 'mw873542'),
(3, 'p45r', 'pg783545'),
(4, 'c23e', 'ec348912'),
(5, 'j71l', 'js672234'),
(7, 'm34c', 'ma763904'),
(8, 'm90m', 'ms543444'),
(9, 'p63d', 'pb897821'),
(10, 'd66e', 'dd104487'),
(19, 'x9', '99a2dd0d8fa45d2ebe6e48c8c024ededa3287688'),
(20, 'geovane', '3827657f02349634eb0ffbe5eb4bd42c620b4870'),
(23, 'teste', '7c4a8d09ca3762af61e59520943dc26494f8941b'),
(24, 'bruno', '40bd001563085fc35165329ea1ff5c5ecbdbbeef'),
(26, 'sadasd', '8cb2237d0679ca88db6464eac60da96345513964'),
(27, 'zezinho', '250454f6b8bbe438b3679cd8e510c583b7dfd4d6');

-- --------------------------------------------------------

--
-- Estrutura para visualizar `acesso_funcionarios`
--
DROP TABLE IF EXISTS `acesso_funcionarios`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `acesso_funcionarios` AS select `funcionario`.`login` AS `loginSys`,`funcionario`.`senha` AS `senhaSys`,`usuariobugzilla`.`usuario` AS `usuarioBugzilla`,`usuariobugzilla`.`senha` AS `senhaBugzilla`,`usuariogit`.`usuario` AS `usuarioGit`,`usuariogit`.`senha` AS `senhaGit`,`funcionario`.`nome` AS `nomeFunc` from ((`funcionario` join `usuariogit` on((`funcionario`.`idfuncionario` = `usuariogit`.`funcionario_idfuncionario`))) join `usuariobugzilla` on((`funcionario`.`idfuncionario` = `usuariobugzilla`.`funcionario_idfuncionario`))) order by `funcionario`.`login`;

-- --------------------------------------------------------

--
-- Estrutura para visualizar `colaboradores_projetos`
--
DROP TABLE IF EXISTS `colaboradores_projetos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `colaboradores_projetos` AS select `projeto`.`estado_idestado` AS `estado_idestado`,`projeto`.`idGerente` AS `idGerente`,`projeto`.`dataFim` AS `dataFim`,`projeto`.`dataInc` AS `dataInc`,`projeto`.`idprojeto` AS `idprojeto`,`projeto`.`nome` AS `nomeProj`,`projeto`.`descricao` AS `descricaoProj`,`funcionario`.`nome` AS `nomeFuncinario`,`funcionario`.`email` AS `emailFuncinario`,`funcionario`.`idfuncionario` AS `idfuncionario`,`colaboradores`.`dedicacaoMes` AS `dedicacaoMes`,`funcaoprojeto`.`descricao` AS `funcao` from (((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) join `funcaoprojeto` on((`colaboradores`.`funcaoProjeto_idfuncaoProjeto` = `funcaoprojeto`.`idfuncaoProjeto`))) order by `projeto`.`nome` desc;

-- --------------------------------------------------------

--
-- Estrutura para visualizar `dados_funcionario_filial`
--
DROP TABLE IF EXISTS `dados_funcionario_filial`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `dados_funcionario_filial` AS select `funcionario`.`idfuncionario` AS `idfuncionario`,`funcionario`.`nome` AS `nome`,`funcionario`.`foto` AS `foto`,`empresafilial`.`nome` AS `nomeEmpresa`,`empresafilial`.`idempresaFilial` AS `idempresaFilial`,`empresafilial`.`empresa_idempresa` AS `empresa_idempresa` from (`funcionario` join `empresafilial` on((`funcionario`.`empresaFilial_idempresaFilial` = `empresafilial`.`idempresaFilial`)));

-- --------------------------------------------------------

--
-- Estrutura para visualizar `dados_git_projeto`
--
DROP TABLE IF EXISTS `dados_git_projeto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `dados_git_projeto` AS select `projetogit`.`repositorio` AS `repositorioGit`,`projetogit`.`chave` AS `chave`,`projeto`.`nome` AS `nomeProj` from (`projetogit` join `projeto` on((`projetogit`.`projeto_idprojeto` = `projeto`.`idprojeto`))) order by `projeto`.`nome`;

-- --------------------------------------------------------

--
-- Estrutura para visualizar `estado_projeto`
--
DROP TABLE IF EXISTS `estado_projeto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `estado_projeto` AS select `estado`.`tipoDeEstado` AS `tipoDeEstado`,`projeto`.`nome` AS `nomeProj`,`projeto`.`idprojeto` AS `idprojeto` from (`projeto` join `estado` on((`projeto`.`estado_idestado` = `estado`.`idestado`))) order by `projeto`.`nome`;

-- --------------------------------------------------------

--
-- Estrutura para visualizar `filias_das_empresas`
--
DROP TABLE IF EXISTS `filias_das_empresas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `filias_das_empresas` AS select `empresa`.`nome` AS `nomeEmp`,`empresafilial`.`nome` AS `nomeFilial`,`empresafilial`.`tel` AS `telFilial`,`empresafilial`.`endereco` AS `enderecoFilial`,`empresafilial`.`responsavel` AS `responsavelFilial`,`empresa`.`tel` AS `telEmp`,`empresa`.`email` AS `emailEmp`,`empresa`.`site` AS `site`,`empresa`.`cep` AS `cepEmp`,`empresa`.`endereco` AS `endereco`,`empresa`.`rezaoSocial` AS `rezaoSocialEmp` from (`empresa` join `empresafilial` on((`empresa`.`idempresa` = `empresafilial`.`empresa_idempresa`)));

-- --------------------------------------------------------

--
-- Estrutura para visualizar `funcao_colaboradores_projeto`
--
DROP TABLE IF EXISTS `funcao_colaboradores_projeto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `funcao_colaboradores_projeto` AS select `funcaoprojeto`.`descricao` AS `descricao`,`projeto`.`nome` AS `nomeProj`,`funcionario`.`nome` AS `nomeFunc`,`colaboradores`.`dedicacaoMes` AS `dedicacaoMes` from (((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcaoprojeto` on((`colaboradores`.`funcaoProjeto_idfuncaoProjeto` = `funcaoprojeto`.`idfuncaoProjeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) order by `projeto`.`nome`,`funcionario`.`nome`;

-- --------------------------------------------------------

--
-- Estrutura para visualizar `projetos_filiais`
--
DROP TABLE IF EXISTS `projetos_filiais`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `projetos_filiais` AS select `projeto`.`nome` AS `nomeProj`,`projeto`.`idprojeto` AS `idprojeto`,`projeto`.`descricao` AS `descricao`,`projeto`.`dataInc` AS `dataInc`,`projeto`.`dataFim` AS `dataFim`,`projeto`.`idGerente` AS `idGerente`,`projeto`.`estado_idestado` AS `estado_idestado`,`funcionario`.`nome` AS `nome`,`funcionario`.`email` AS `email`,`empresafilial`.`idempresaFilial` AS `idempresaFilial`,`empresafilial`.`nome` AS `nomeFilial` from ((`projeto` join `funcionario` on((`projeto`.`idGerente` = `funcionario`.`idfuncionario`))) join `empresafilial` on((`funcionario`.`empresaFilial_idempresaFilial` = `empresafilial`.`idempresaFilial`)));

-- --------------------------------------------------------

--
-- Estrutura para visualizar `projetos_gerente_filial_colaboradores`
--
DROP TABLE IF EXISTS `projetos_gerente_filial_colaboradores`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `projetos_gerente_filial_colaboradores` AS select `projeto`.`idprojeto` AS `idprojeto`,`projeto`.`nome` AS `nomeProj`,`projeto`.`dataFim` AS `dataFim`,`projeto`.`dataInc` AS `dataInc`,`estado`.`tipoDeEstado` AS `estadoProj`,`projeto`.`idGerente` AS `idGerente`,`funcionario1`.`nome` AS `nomeGerente`,`projeto`.`descricao` AS `descricaoProj`,`empresafilial`.`idempresaFilial` AS `idFilialProj`,`empresafilial`.`nome` AS `nomeFilialProj`,`funcionario`.`idfuncionario` AS `idFuncionarioColaborador`,`funcionario`.`nome` AS `nomeColaborador`,`colaboradores`.`dedicacaoMes` AS `dedicacaoMesColaborador`,`colaboradores`.`idcolaboradores` AS `idColaborador`,`funcaoprojeto`.`descricao` AS `funcaoColaborador` from ((((((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) join `funcaoprojeto` on((`colaboradores`.`funcaoProjeto_idfuncaoProjeto` = `funcaoprojeto`.`idfuncaoProjeto`))) join `funcionario` `funcionario1` on((`projeto`.`idGerente` = `funcionario1`.`idfuncionario`))) join `estado` on((`projeto`.`estado_idestado` = `estado`.`idestado`))) join `empresafilial` on((`funcionario1`.`empresaFilial_idempresaFilial` = `empresafilial`.`idempresaFilial`))) order by `projeto`.`nome` desc;

-- --------------------------------------------------------

--
-- Estrutura para visualizar `tarefa_colaborador_projetos`
--
DROP TABLE IF EXISTS `tarefa_colaborador_projetos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tarefa_colaborador_projetos` AS select `projeto`.`nome` AS `nomeProj`,`colaboradores`.`idcolaboradores` AS `idColab`,`funcionario`.`nome` AS `nomeFunc`,`tarefa`.`idtarefa` AS `idtarefa`,`tarefa`.`descricao` AS `descricaoTarefa`,`tarefa`.`dataInc` AS `dataInc`,`tarefa`.`estado_idestado` AS `estado_idestado`,`tarefa`.`dataFim` AS `dataFim`,`tarefa`.`dataEntrega` AS `dataEntrega` from ((((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) join `funfaztarefa` on((`colaboradores`.`idcolaboradores` = `funfaztarefa`.`colaboradores_idcolaboradores`))) join `tarefa` on((`funfaztarefa`.`tarefa_idtarefa` = `tarefa`.`idtarefa`))) order by `projeto`.`nome`;

-- --------------------------------------------------------

--
-- Estrutura para visualizar `tipo_estado_tarefa_colaborador_projetos`
--
DROP TABLE IF EXISTS `tipo_estado_tarefa_colaborador_projetos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tipo_estado_tarefa_colaborador_projetos` AS select `projeto`.`nome` AS `nomeProj`,`funcionario`.`idfuncionario` AS `idfuncionario`,`funcionario`.`nome` AS `nomeFunc`,`tarefa`.`descricao` AS `descricaoTarefa`,`tarefa`.`dataInc` AS `dataInc`,`tarefa`.`dataFim` AS `dataFim`,`estado`.`tipoDeEstado` AS `tipoDeEstado` from (((((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) join `funfaztarefa` on((`colaboradores`.`idcolaboradores` = `funfaztarefa`.`colaboradores_idcolaboradores`))) join `tarefa` on((`funfaztarefa`.`tarefa_idtarefa` = `tarefa`.`idtarefa`))) join `estado` on((`tarefa`.`estado_idestado` = `estado`.`idestado`))) order by `projeto`.`nome`;

--
-- Restrições para as tabelas dumpadas
--

--
-- Restrições para a tabela `colaboradores`
--
ALTER TABLE `colaboradores`
  ADD CONSTRAINT `fk_colaboradores_funcaoProjeto1` FOREIGN KEY (`funcaoProjeto_idfuncaoProjeto`) REFERENCES `funcaoprojeto` (`idfuncaoProjeto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_colaboradores_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_colaboradores_projeto1` FOREIGN KEY (`projeto_idprojeto`) REFERENCES `projeto` (`idprojeto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `empresa`
--
ALTER TABLE `empresa`
  ADD CONSTRAINT `fk_empresa_funcionario1` FOREIGN KEY (`responsavelGeral`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `empresafilial`
--
ALTER TABLE `empresafilial`
  ADD CONSTRAINT `fk_empresaFilial_empresa1` FOREIGN KEY (`empresa_idempresa`) REFERENCES `empresa` (`idempresa`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_empresaFilial_funcionario1` FOREIGN KEY (`responsavel`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `funfaztarefa`
--
ALTER TABLE `funfaztarefa`
  ADD CONSTRAINT `fk_funFazTarefa_colaboradores1` FOREIGN KEY (`colaboradores_idcolaboradores`) REFERENCES `colaboradores` (`idcolaboradores`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_funFazTarefa_tarefa1` FOREIGN KEY (`tarefa_idtarefa`) REFERENCES `tarefa` (`idtarefa`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `projeto`
--
ALTER TABLE `projeto`
  ADD CONSTRAINT `fk_projeto_estado1` FOREIGN KEY (`estado_idestado`) REFERENCES `estado` (`idestado`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_projeto_funcionario1` FOREIGN KEY (`idGerente`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `projetobugzilla`
--
ALTER TABLE `projetobugzilla`
  ADD CONSTRAINT `fk_projetoBugzilla_projeto1` FOREIGN KEY (`projeto_idprojeto`) REFERENCES `projeto` (`idprojeto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `projetogit`
--
ALTER TABLE `projetogit`
  ADD CONSTRAINT `fk_projetoGit_projeto1` FOREIGN KEY (`projeto_idprojeto`) REFERENCES `projeto` (`idprojeto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para a tabela `redefinirsenha`
--
ALTER TABLE `redefinirsenha`
  ADD CONSTRAINT `fk_redefinirSenha_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
