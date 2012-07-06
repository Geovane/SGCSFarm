# MySQL-Front 5.1  (Build 4.13)

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;


# Host: localhost    Database: 2fase
# ------------------------------------------------------
# Server version 5.5.8

DROP DATABASE IF EXISTS `2fase`;
CREATE DATABASE `2fase` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `2fase`;

#
# Source for table colaboradores
#

DROP TABLE IF EXISTS `colaboradores`;
CREATE TABLE `colaboradores` (
  `projeto_idprojeto` int(11) NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  `idcolaboradores` int(11) NOT NULL,
  `dedicacaoMes` int(11) DEFAULT '0',
  `funcaoProjeto_idfuncaoProjeto` int(11) NOT NULL,
  PRIMARY KEY (`projeto_idprojeto`,`funcionario_idfuncionario`),
  UNIQUE KEY `idcolaboradores_UNIQUE` (`idcolaboradores`),
  KEY `fk_colaboradores_funcaoProjeto1` (`funcaoProjeto_idfuncaoProjeto`),
  KEY `fk_colaboradores_funcionario1` (`funcionario_idfuncionario`),
  KEY `fk_colaboradores_projeto1` (`projeto_idprojeto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='IdColaboradoes é uma chave indexada, da tabela colaboradores, isso foi nescessario pois funcionarios não podem se repetir em projetos, logo idprojeto e idfuncionario são chaves.\nObs: quem identifica a tabela colaborars nas relaçoes externas é idColaboradores';

#
# Dumping data for table colaboradores
#

LOCK TABLES `colaboradores` WRITE;
/*!40000 ALTER TABLE `colaboradores` DISABLE KEYS */;
INSERT INTO `colaboradores` VALUES (1,1,10,30,40);
INSERT INTO `colaboradores` VALUES (1,2,11,25,10);
INSERT INTO `colaboradores` VALUES (1,3,12,20,20);
INSERT INTO `colaboradores` VALUES (1,4,13,15,30);
INSERT INTO `colaboradores` VALUES (1,5,14,10,20);
INSERT INTO `colaboradores` VALUES (1,19,24,10,10);
INSERT INTO `colaboradores` VALUES (1,20,22,10,30);
INSERT INTO `colaboradores` VALUES (2,7,16,25,10);
INSERT INTO `colaboradores` VALUES (2,8,17,20,20);
INSERT INTO `colaboradores` VALUES (2,9,18,15,30);
INSERT INTO `colaboradores` VALUES (2,10,19,10,20);
INSERT INTO `colaboradores` VALUES (3,2,21,10,10);
INSERT INTO `colaboradores` VALUES (3,19,23,10,10);
INSERT INTO `colaboradores` VALUES (3,20,20,10,10);
/*!40000 ALTER TABLE `colaboradores` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table empresa
#

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE `empresa` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Dumping data for table empresa
#

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
INSERT INTO `empresa` VALUES (1,'SoftFarm','73 3634 1874','softfarm@gmail.com','www.softfarm.com','45 650 000','Rua Conselheiro Dantas 23 Centro Ilheus BA','03454876000109',19);
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table empresafilial
#

DROP TABLE IF EXISTS `empresafilial`;
CREATE TABLE `empresafilial` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Dumping data for table empresafilial
#

LOCK TABLES `empresafilial` WRITE;
/*!40000 ALTER TABLE `empresafilial` DISABLE KEYS */;
INSERT INTO `empresafilial` VALUES (1,'SoftFarm - EUA','56 8945 9891','FDGDFSGFDSGS',24,1,'ghd@hud.com','34 567 678');
INSERT INTO `empresafilial` VALUES (2,'SoftFarm - BR','73 3639 5149','Rua da Linha 1029 Barra Ilheus BA ',19,1,'softfarmbr@gmail.com','45 650 000');
/*!40000 ALTER TABLE `empresafilial` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table estado
#

DROP TABLE IF EXISTS `estado`;
CREATE TABLE `estado` (
  `idestado` int(11) NOT NULL AUTO_INCREMENT,
  `tipoDeEstado` varchar(45) NOT NULL,
  PRIMARY KEY (`idestado`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Cadastrados pelo desenvolvedores';

#
# Dumping data for table estado
#

LOCK TABLES `estado` WRITE;
/*!40000 ALTER TABLE `estado` DISABLE KEYS */;
INSERT INTO `estado` VALUES (1,'Fase de planejamento');
INSERT INTO `estado` VALUES (2,'Fase inicial');
INSERT INTO `estado` VALUES (3,'Em andamento');
INSERT INTO `estado` VALUES (4,'Fase de teste');
INSERT INTO `estado` VALUES (5,'Fase de revisao');
INSERT INTO `estado` VALUES (6,'Fase de conclusao');
INSERT INTO `estado` VALUES (7,'Concluido');
/*!40000 ALTER TABLE `estado` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table funcaoprojeto
#

DROP TABLE IF EXISTS `funcaoprojeto`;
CREATE TABLE `funcaoprojeto` (
  `idfuncaoProjeto` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idfuncaoProjeto`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

#
# Dumping data for table funcaoprojeto
#

LOCK TABLES `funcaoprojeto` WRITE;
/*!40000 ALTER TABLE `funcaoprojeto` DISABLE KEYS */;
INSERT INTO `funcaoprojeto` VALUES (10,'DBA');
INSERT INTO `funcaoprojeto` VALUES (20,'Programador');
INSERT INTO `funcaoprojeto` VALUES (30,'Analista');
INSERT INTO `funcaoprojeto` VALUES (40,'Gerente');
/*!40000 ALTER TABLE `funcaoprojeto` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table funcionario
#

DROP TABLE IF EXISTS `funcionario`;
CREATE TABLE `funcionario` (
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

#
# Dumping data for table funcionario
#

LOCK TABLES `funcionario` WRITE;
/*!40000 ALTER TABLE `funcionario` DISABLE KEYS */;
INSERT INTO `funcionario` VALUES (1,'FABI MATOS LIMA','87345099218','m56h','40bd001563085fc35165329ea1ff5c5ecbdbbeef','f_lima@gmail.com',2,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (2,'CARLOS EDUARDO','11234566','carlo','40bd001563085fc35165329ea1ff5c5ecbdbbeef','carlosfonsa@yahoo.com',1,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (3,'MARIA CLARA DE JESUS AMARAL','12432654489','m34c','40bd001563085fc35165329ea1ff5c5ecbdbbeef','ma_clarajesus@gmail.com',2,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (4,'JOSE VINICIUS SANTOS PUENTES','78034236749','j76v','40bd001563085fc35165329ea1ff5c5ecbdbbeef','jose.puentes@hotmail.com',2,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (5,'MAX WILLIAMS SANCHEZ ','H3478H09','m78s','40bd001563085fc35165329ea1ff5c5ecbdbbeef','maxws@gmail.com',2,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (7,'MILLENA MOURA SABOIA','56128965667','m90m','40bd001563085fc35165329ea1ff5c5ecbdbbeef','millena.saboia@gmail.com',2,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (8,'POUL DENNY BUNTON','J678D835','p63d','40bd001563085fc35165329ea1ff5c5ecbdbbeef','pdbunton@yahoo.com',2,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (9,'JOANA LIMA DA GAMA SETUBAL','78234456721','j71l','40bd001563085fc35165329ea1ff5c5ecbdbbeef','joanasetubal@gmail.com',2,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (10,'DANNILLY EVANS DYER','M78B0956','d66e','40bd001563085fc35165329ea1ff5c5ecbdbbeef','dannillyedyer@gmail.com',2,0,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (19,'Geovane M. souza','12324','mimoso','40bd001563085fc35165329ea1ff5c5ecbdbbeef','ge@g1.com',2,1,'/images/fotosFunc/19_1341506295.jpg');
INSERT INTO `funcionario` VALUES (20,'Geovane Mimoso','1170192599','geovane','40bd001563085fc35165329ea1ff5c5ecbdbbeef','geovanemimoso@gmail.com',1,1,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (23,'teste','123456','teste','40bd001563085fc35165329ea1ff5c5ecbdbbeef','teste@teste',1,1,'/images/fotosFunc/usuarioPadrao.jpg');
INSERT INTO `funcionario` VALUES (24,'Bruno Pereira dos Santos','123','bruno','40bd001563085fc35165329ea1ff5c5ecbdbbeef','bruno.ps@live.com',1,1,'/images/fotosFunc/24_1341473472.jpg');
INSERT INTO `funcionario` VALUES (26,'Dhielber','12345','teste2','8cb2237d0679ca88db6464eac60da96345513964','dadasda',1,1,'/images/fotosFunc/usuarioPadrao.jpg');
/*!40000 ALTER TABLE `funcionario` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table funfaztarefa
#

DROP TABLE IF EXISTS `funfaztarefa`;
CREATE TABLE `funfaztarefa` (
  `tarefa_idtarefa` int(11) NOT NULL,
  `colaboradores_idcolaboradores` int(11) NOT NULL,
  PRIMARY KEY (`tarefa_idtarefa`,`colaboradores_idcolaboradores`),
  KEY `fk_funFazTarefa_colaboradores1` (`colaboradores_idcolaboradores`),
  KEY `fk_funFazTarefa_tarefa1` (`tarefa_idtarefa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table funfaztarefa
#

LOCK TABLES `funfaztarefa` WRITE;
/*!40000 ALTER TABLE `funfaztarefa` DISABLE KEYS */;
INSERT INTO `funfaztarefa` VALUES (33,10);
INSERT INTO `funfaztarefa` VALUES (37,10);
INSERT INTO `funfaztarefa` VALUES (41,17);
INSERT INTO `funfaztarefa` VALUES (42,17);
INSERT INTO `funfaztarefa` VALUES (44,20);
INSERT INTO `funfaztarefa` VALUES (45,24);
INSERT INTO `funfaztarefa` VALUES (46,20);
/*!40000 ALTER TABLE `funfaztarefa` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table projeto
#

DROP TABLE IF EXISTS `projeto`;
CREATE TABLE `projeto` (
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

#
# Dumping data for table projeto
#

LOCK TABLES `projeto` WRITE;
/*!40000 ALTER TABLE `projeto` DISABLE KEYS */;
INSERT INTO `projeto` VALUES (1,'Proj A','Desenvolvimento de um sistema para gerenciamento de configuração de software.','2012-04-01','2012-07-30',1,2);
INSERT INTO `projeto` VALUES (2,'Proj B','Sistema completo de automação comercial.','2012-04-01','2012-07-30',7,5);
INSERT INTO `projeto` VALUES (3,'Proj C','teste','2012-07-03','1989-09-09',24,2);
INSERT INTO `projeto` VALUES (4,'Proj D','Lorem Ipsum é simplesment','2012-07-03','1289-09-09',19,2);
INSERT INTO `projeto` VALUES (5,'Proj E','testesteatestesteatestest','1989-09-09','1890-09-09',19,2);
/*!40000 ALTER TABLE `projeto` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table projetobugzilla
#

DROP TABLE IF EXISTS `projetobugzilla`;
CREATE TABLE `projetobugzilla` (
  `projeto_idprojeto` int(11) NOT NULL,
  `nomeProjeto` varchar(45) NOT NULL,
  PRIMARY KEY (`projeto_idprojeto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table projetobugzilla
#

LOCK TABLES `projetobugzilla` WRITE;
/*!40000 ALTER TABLE `projetobugzilla` DISABLE KEYS */;
INSERT INTO `projetobugzilla` VALUES (1,'Proj A');
INSERT INTO `projetobugzilla` VALUES (3,'Proj C');
INSERT INTO `projetobugzilla` VALUES (4,'Proj D');
INSERT INTO `projetobugzilla` VALUES (5,'Proj E');
/*!40000 ALTER TABLE `projetobugzilla` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table projetogit
#

DROP TABLE IF EXISTS `projetogit`;
CREATE TABLE `projetogit` (
  `projeto_idprojeto` int(11) NOT NULL,
  `repositorio` varchar(100) NOT NULL,
  `chave` text NOT NULL,
  PRIMARY KEY (`projeto_idprojeto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table projetogit
#

LOCK TABLES `projetogit` WRITE;
/*!40000 ALTER TABLE `projetogit` DISABLE KEYS */;
INSERT INTO `projetogit` VALUES (2,'Proj B','ydhbcgfhkchn');
INSERT INTO `projetogit` VALUES (3,'Proj C','asdasdsda');
INSERT INTO `projetogit` VALUES (4,'Proj D','asdasdsda');
INSERT INTO `projetogit` VALUES (5,'Proj E','asdasdsda');
/*!40000 ALTER TABLE `projetogit` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table redefinirsenha
#

DROP TABLE IF EXISTS `redefinirsenha`;
CREATE TABLE `redefinirsenha` (
  `hash` varchar(40) NOT NULL,
  `funcionario_idfuncionario` int(11) NOT NULL,
  PRIMARY KEY (`hash`),
  KEY `fk_redefinirSenha_funcionario1` (`funcionario_idfuncionario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table redefinirsenha
#

LOCK TABLES `redefinirsenha` WRITE;
/*!40000 ALTER TABLE `redefinirsenha` DISABLE KEYS */;
INSERT INTO `redefinirsenha` VALUES ('095hjvbgt56',1);
INSERT INTO `redefinirsenha` VALUES ('10plmzxc67h',10);
INSERT INTO `redefinirsenha` VALUES ('76ghbvzx34g',9);
INSERT INTO `redefinirsenha` VALUES ('78dgh389oa2',5);
INSERT INTO `redefinirsenha` VALUES ('8rhdkio278d',4);
INSERT INTO `redefinirsenha` VALUES ('b236hmz34o9',2);
INSERT INTO `redefinirsenha` VALUES ('ie64jd67490',3);
INSERT INTO `redefinirsenha` VALUES ('io89bn45mj1',7);
INSERT INTO `redefinirsenha` VALUES ('o9lpmszv23n5',8);
/*!40000 ALTER TABLE `redefinirsenha` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table sysconfig
#

DROP TABLE IF EXISTS `sysconfig`;
CREATE TABLE `sysconfig` (
  `idsysConfig` int(11) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `valor` varchar(45) NOT NULL,
  `empresa_idempresa` int(11) NOT NULL,
  PRIMARY KEY (`idsysConfig`),
  UNIQUE KEY `nome_UNIQUE` (`nome`),
  KEY `fk_sysConfig_empresa1` (`empresa_idempresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='configurações gerais do sistema\n\nPrimeira: Usuario git da empresa';

#
# Dumping data for table sysconfig
#

LOCK TABLES `sysconfig` WRITE;
/*!40000 ALTER TABLE `sysconfig` DISABLE KEYS */;
INSERT INTO `sysconfig` VALUES (1,'Usuario Git Master','SoftFarm',1);
INSERT INTO `sysconfig` VALUES (2,'Usuario Bugzila Master','SoftFarm',1);
/*!40000 ALTER TABLE `sysconfig` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table tarefa
#

DROP TABLE IF EXISTS `tarefa`;
CREATE TABLE `tarefa` (
  `idtarefa` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` text NOT NULL,
  `dataInc` datetime NOT NULL,
  `dataFim` datetime DEFAULT NULL,
  `estado_idestado` int(11) NOT NULL,
  `dataEntrega` datetime NOT NULL,
  PRIMARY KEY (`idtarefa`),
  KEY `fk_tarefa_estado1` (`estado_idestado`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

#
# Dumping data for table tarefa
#

LOCK TABLES `tarefa` WRITE;
/*!40000 ALTER TABLE `tarefa` DISABLE KEYS */;
INSERT INTO `tarefa` VALUES (32,'analisar as tarefas concluidas','2012-06-08','2012-07-10',1,'2012-06-28');
INSERT INTO `tarefa` VALUES (33,'gerenciar as tarefas','2012-04-01','2012-07-30',3,'2012-08-10');
INSERT INTO `tarefa` VALUES (34,'xafsadfsdfs','2012-06-05 07:10:05','2012-06-30 07:10:05',3,'2012-07-20 07:10:05');
INSERT INTO `tarefa` VALUES (35,'sdadasd','2012-06-01 19:39:50','2012-07-04 19:39:50',2,'2012-08-04 19:39:50');
INSERT INTO `tarefa` VALUES (36,'dscxcz\\','2012-06-03 19:50:21','2012-06-16 19:50:21',1,'2012-07-14 19:51:55');
INSERT INTO `tarefa` VALUES (37,'hgjjhgjgh','2012-06-01 07:08:09','2012-06-30 07:08:09',2,'2012-06-30 07:09:03');
INSERT INTO `tarefa` VALUES (41,'fsdfsdf','2012-06-01 19:42:13','2012-06-30 19:42:13',2,'0000-00-00 00:00:00');
INSERT INTO `tarefa` VALUES (42,'dasda','2012-06-03 05:28:45','2012-06-30 05:28:45',2,'0000-00-00 00:00:00');
INSERT INTO `tarefa` VALUES (44,'teste','2012-07-03 20:47:25','2012-07-03 20:47:25',7,'0000-00-00 00:00:00');
INSERT INTO `tarefa` VALUES (45,'testes','2012-07-03 03:09:05','2012-07-31 03:09:05',2,'0000-00-00 00:00:00');
INSERT INTO `tarefa` VALUES (46,',n,mn,m','2012-07-17 04:05:19','2012-07-27 04:05:19',2,'0000-00-00 00:00:00');
/*!40000 ALTER TABLE `tarefa` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table usuariobugzilla
#

DROP TABLE IF EXISTS `usuariobugzilla`;
CREATE TABLE `usuariobugzilla` (
  `funcionario_idfuncionario` int(11) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  PRIMARY KEY (`funcionario_idfuncionario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table usuariobugzilla
#

LOCK TABLES `usuariobugzilla` WRITE;
/*!40000 ALTER TABLE `usuariobugzilla` DISABLE KEYS */;
INSERT INTO `usuariobugzilla` VALUES (1,'m56h','f45hj667');
INSERT INTO `usuariobugzilla` VALUES (2,'m78s','mw873542');
INSERT INTO `usuariobugzilla` VALUES (3,'p45r','pg783545');
INSERT INTO `usuariobugzilla` VALUES (4,'c23e','ec348912');
INSERT INTO `usuariobugzilla` VALUES (5,'j71l','js672234');
INSERT INTO `usuariobugzilla` VALUES (7,'m34c','ma763904');
INSERT INTO `usuariobugzilla` VALUES (8,'m90m','ms543444');
INSERT INTO `usuariobugzilla` VALUES (9,'p63d','pb897821');
INSERT INTO `usuariobugzilla` VALUES (10,'d66e','dd104487');
INSERT INTO `usuariobugzilla` VALUES (19,'x9','99a2dd0d8fa45d2ebe6e48c8c024ededa3287688');
INSERT INTO `usuariobugzilla` VALUES (20,'geovane','3827657f02349634eb0ffbe5eb4bd42c620b4870');
INSERT INTO `usuariobugzilla` VALUES (23,'teste','7c4a8d09ca3762af61e59520943dc26494f8941b');
INSERT INTO `usuariobugzilla` VALUES (24,'bruno','40bd001563085fc35165329ea1ff5c5ecbdbbeef');
INSERT INTO `usuariobugzilla` VALUES (26,'sadasd','8cb2237d0679ca88db6464eac60da96345513964');
/*!40000 ALTER TABLE `usuariobugzilla` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table usuariogit
#

DROP TABLE IF EXISTS `usuariogit`;
CREATE TABLE `usuariogit` (
  `funcionario_idfuncionario` int(11) NOT NULL,
  `usuario` varchar(45) NOT NULL,
  `senha` varchar(45) NOT NULL,
  PRIMARY KEY (`funcionario_idfuncionario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table usuariogit
#

LOCK TABLES `usuariogit` WRITE;
/*!40000 ALTER TABLE `usuariogit` DISABLE KEYS */;
INSERT INTO `usuariogit` VALUES (1,'m56h','f45hj667');
INSERT INTO `usuariogit` VALUES (2,'m78s','mw873542');
INSERT INTO `usuariogit` VALUES (3,'p45r','pg783545');
INSERT INTO `usuariogit` VALUES (4,'c23e','ec348912');
INSERT INTO `usuariogit` VALUES (5,'j71l','js672234');
INSERT INTO `usuariogit` VALUES (7,'m34c','ma763904');
INSERT INTO `usuariogit` VALUES (8,'m90m','ms543444');
INSERT INTO `usuariogit` VALUES (9,'p63d','pb897821');
INSERT INTO `usuariogit` VALUES (10,'d66e','dd104487');
INSERT INTO `usuariogit` VALUES (19,'x9','99a2dd0d8fa45d2ebe6e48c8c024ededa3287688');
INSERT INTO `usuariogit` VALUES (20,'geovane','3827657f02349634eb0ffbe5eb4bd42c620b4870');
INSERT INTO `usuariogit` VALUES (23,'teste','7c4a8d09ca3762af61e59520943dc26494f8941b');
INSERT INTO `usuariogit` VALUES (24,'bruno','40bd001563085fc35165329ea1ff5c5ecbdbbeef');
INSERT INTO `usuariogit` VALUES (26,'sadasd','8cb2237d0679ca88db6464eac60da96345513964');
/*!40000 ALTER TABLE `usuariogit` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for view acesso_funcionarios
#

DROP VIEW IF EXISTS `acesso_funcionarios`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `acesso_funcionarios` AS select `funcionario`.`login` AS `loginSys`,`funcionario`.`senha` AS `senhaSys`,`usuariobugzilla`.`usuario` AS `usuarioBugzilla`,`usuariobugzilla`.`senha` AS `senhaBugzilla`,`usuariogit`.`usuario` AS `usuarioGit`,`usuariogit`.`senha` AS `senhaGit`,`funcionario`.`nome` AS `nomeFunc` from ((`funcionario` join `usuariogit` on((`funcionario`.`idfuncionario` = `usuariogit`.`funcionario_idfuncionario`))) join `usuariobugzilla` on((`funcionario`.`idfuncionario` = `usuariobugzilla`.`funcionario_idfuncionario`))) order by `funcionario`.`login`;

#
# Source for view colaboradores_projetos
#

DROP VIEW IF EXISTS `colaboradores_projetos`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `colaboradores_projetos` AS select `projeto`.`estado_idestado` AS `estado_idestado`,`projeto`.`idGerente` AS `idGerente`,`projeto`.`dataFim` AS `dataFim`,`projeto`.`dataInc` AS `dataInc`,`projeto`.`idprojeto` AS `idprojeto`,`projeto`.`nome` AS `nomeProj`,`projeto`.`descricao` AS `descricaoProj`,`funcionario`.`nome` AS `nomeFuncinario`,`funcionario`.`idfuncionario` AS `idfuncionario`,`colaboradores`.`dedicacaoMes` AS `dedicacaoMes`,`funcaoprojeto`.`descricao` AS `funcao` from (((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) join `funcaoprojeto` on((`colaboradores`.`funcaoProjeto_idfuncaoProjeto` = `funcaoprojeto`.`idfuncaoProjeto`))) order by `projeto`.`nome` desc;

#
# Source for view dados_funcionario_filial
#

DROP VIEW IF EXISTS `dados_funcionario_filial`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `dados_funcionario_filial` AS select `funcionario`.`idfuncionario` AS `idfuncionario`,`funcionario`.`nome` AS `nome`,`funcionario`.`foto` AS `foto`,`empresafilial`.`nome` AS `nomeEmpresa`,`empresafilial`.`idempresaFilial` AS `idempresaFilial`,`empresafilial`.`empresa_idempresa` AS `empresa_idempresa` from (`funcionario` join `empresafilial` on((`funcionario`.`empresaFilial_idempresaFilial` = `empresafilial`.`idempresaFilial`)));

#
# Source for view dados_git_projeto
#

DROP VIEW IF EXISTS `dados_git_projeto`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `dados_git_projeto` AS select `projetogit`.`repositorio` AS `repositorioGit`,`projetogit`.`chave` AS `chave`,`projeto`.`nome` AS `nomeProj` from (`projetogit` join `projeto` on((`projetogit`.`projeto_idprojeto` = `projeto`.`idprojeto`))) order by `projeto`.`nome`;

#
# Source for view estado_projeto
#

DROP VIEW IF EXISTS `estado_projeto`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `estado_projeto` AS select `estado`.`tipoDeEstado` AS `tipoDeEstado`,`projeto`.`nome` AS `nomeProj` from (`projeto` join `estado` on((`projeto`.`estado_idestado` = `estado`.`idestado`))) order by `projeto`.`nome`;

#
# Source for view filias_das_empresas
#

DROP VIEW IF EXISTS `filias_das_empresas`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `filias_das_empresas` AS select `empresa`.`nome` AS `nomeEmp`,`empresafilial`.`nome` AS `nomeFilial`,`empresafilial`.`tel` AS `telFilial`,`empresafilial`.`endereco` AS `enderecoFilial`,`empresafilial`.`responsavel` AS `responsavelFilial`,`empresa`.`tel` AS `telEmp`,`empresa`.`email` AS `emailEmp`,`empresa`.`site` AS `site`,`empresa`.`cep` AS `cepEmp`,`empresa`.`endereco` AS `endereco`,`empresa`.`rezaoSocial` AS `rezaoSocialEmp` from (`empresa` join `empresafilial` on((`empresa`.`idempresa` = `empresafilial`.`empresa_idempresa`)));

#
# Source for view funcao_colaboradores_projeto
#

DROP VIEW IF EXISTS `funcao_colaboradores_projeto`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `funcao_colaboradores_projeto` AS select `funcaoprojeto`.`descricao` AS `descricao`,`projeto`.`nome` AS `nomeProj`,`funcionario`.`nome` AS `nomeFunc`,`colaboradores`.`dedicacaoMes` AS `dedicacaoMes` from (((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcaoprojeto` on((`colaboradores`.`funcaoProjeto_idfuncaoProjeto` = `funcaoprojeto`.`idfuncaoProjeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) order by `projeto`.`nome`,`funcionario`.`nome`;

#
# Source for view projetos_gerente_filial_colaboradores
#

DROP VIEW IF EXISTS `projetos_gerente_filial_colaboradores`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `projetos_gerente_filial_colaboradores` AS select `projeto`.`idprojeto` AS `idprojeto`,`projeto`.`nome` AS `nomeProj`,`projeto`.`dataFim` AS `dataFim`,`projeto`.`dataInc` AS `dataInc`,`estado`.`tipoDeEstado` AS `estadoProj`,`projeto`.`idGerente` AS `idGerente`,`funcionario1`.`nome` AS `nomeGerente`,`projeto`.`descricao` AS `descricaoProj`,`empresafilial`.`idempresaFilial` AS `idFilialProj`,`empresafilial`.`nome` AS `nomeFilialProj`,`funcionario`.`idfuncionario` AS `idFuncionarioColaborador`,`funcionario`.`nome` AS `nomeColaborador`,`colaboradores`.`dedicacaoMes` AS `dedicacaoMesColaborador`,`funcaoprojeto`.`descricao` AS `funcaoColaborador` from ((((((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) join `funcaoprojeto` on((`colaboradores`.`funcaoProjeto_idfuncaoProjeto` = `funcaoprojeto`.`idfuncaoProjeto`))) join `funcionario` `funcionario1` on((`projeto`.`idGerente` = `funcionario1`.`idfuncionario`))) join `estado` on((`projeto`.`estado_idestado` = `estado`.`idestado`))) join `empresafilial` on((`funcionario1`.`empresaFilial_idempresaFilial` = `empresafilial`.`idempresaFilial`))) order by `projeto`.`nome` desc;

#
# Source for view tarefa_colaborador_projetos
#

DROP VIEW IF EXISTS `tarefa_colaborador_projetos`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `tarefa_colaborador_projetos` AS select `projeto`.`nome` AS `nomeProj`,`colaboradores`.`idcolaboradores` AS `idColab`,`funcionario`.`nome` AS `nomeFunc`,`tarefa`.`idtarefa` AS `idtarefa`,`tarefa`.`descricao` AS `descricaoTarefa`,`tarefa`.`dataInc` AS `dataInc`,`tarefa`.`estado_idestado` AS `estado_idestado`,`tarefa`.`dataFim` AS `dataFim`,`tarefa`.`dataEntrega` AS `dataEntrega` from ((((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) join `funfaztarefa` on((`colaboradores`.`idcolaboradores` = `funfaztarefa`.`colaboradores_idcolaboradores`))) join `tarefa` on((`funfaztarefa`.`tarefa_idtarefa` = `tarefa`.`idtarefa`))) order by `projeto`.`nome`;

#
# Source for view tipo_estado_tarefa_colaborador_projetos
#

DROP VIEW IF EXISTS `tipo_estado_tarefa_colaborador_projetos`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `tipo_estado_tarefa_colaborador_projetos` AS select `projeto`.`nome` AS `nomeProj`,`funcionario`.`nome` AS `nomeFunc`,`tarefa`.`descricao` AS `descricaoTarefa`,`tarefa`.`dataInc` AS `dataInc`,`tarefa`.`dataFim` AS `dataFim`,`estado`.`tipoDeEstado` AS `tipoDeEstado` from (((((`projeto` join `colaboradores` on((`projeto`.`idprojeto` = `colaboradores`.`projeto_idprojeto`))) join `funcionario` on((`colaboradores`.`funcionario_idfuncionario` = `funcionario`.`idfuncionario`))) join `funfaztarefa` on((`colaboradores`.`idcolaboradores` = `funfaztarefa`.`colaboradores_idcolaboradores`))) join `tarefa` on((`funfaztarefa`.`tarefa_idtarefa` = `tarefa`.`idtarefa`))) join `estado` on((`tarefa`.`estado_idestado` = `estado`.`idestado`))) order by `projeto`.`nome`;

#
#  Foreign keys for table colaboradores
#

ALTER TABLE `colaboradores`
ADD CONSTRAINT `fk_colaboradores_funcaoProjeto1` FOREIGN KEY (`funcaoProjeto_idfuncaoProjeto`) REFERENCES `funcaoprojeto` (`idfuncaoProjeto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_colaboradores_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_colaboradores_projeto1` FOREIGN KEY (`projeto_idprojeto`) REFERENCES `projeto` (`idprojeto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table empresa
#

ALTER TABLE `empresa`
ADD CONSTRAINT `fk_empresa_funcionario1` FOREIGN KEY (`responsavelGeral`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table empresafilial
#

ALTER TABLE `empresafilial`
ADD CONSTRAINT `fk_empresaFilial_empresa1` FOREIGN KEY (`empresa_idempresa`) REFERENCES `empresa` (`idempresa`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_empresaFilial_funcionario1` FOREIGN KEY (`responsavel`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table funfaztarefa
#

ALTER TABLE `funfaztarefa`
ADD CONSTRAINT `fk_funFazTarefa_colaboradores1` FOREIGN KEY (`colaboradores_idcolaboradores`) REFERENCES `colaboradores` (`idcolaboradores`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_funFazTarefa_tarefa1` FOREIGN KEY (`tarefa_idtarefa`) REFERENCES `tarefa` (`idtarefa`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table projeto
#

ALTER TABLE `projeto`
ADD CONSTRAINT `fk_projeto_estado1` FOREIGN KEY (`estado_idestado`) REFERENCES `estado` (`idestado`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_projeto_funcionario1` FOREIGN KEY (`idGerente`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table projetobugzilla
#

ALTER TABLE `projetobugzilla`
ADD CONSTRAINT `fk_projetoBugzilla_projeto1` FOREIGN KEY (`projeto_idprojeto`) REFERENCES `projeto` (`idprojeto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table projetogit
#

ALTER TABLE `projetogit`
ADD CONSTRAINT `fk_projetoGit_projeto1` FOREIGN KEY (`projeto_idprojeto`) REFERENCES `projeto` (`idprojeto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table redefinirsenha
#

ALTER TABLE `redefinirsenha`
ADD CONSTRAINT `fk_redefinirSenha_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table sysconfig
#

ALTER TABLE `sysconfig`
ADD CONSTRAINT `fk_sysConfig_empresa1` FOREIGN KEY (`empresa_idempresa`) REFERENCES `empresa` (`idempresa`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table tarefa
#

ALTER TABLE `tarefa`
ADD CONSTRAINT `fk_tarefa_estado1` FOREIGN KEY (`estado_idestado`) REFERENCES `estado` (`idestado`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table usuariobugzilla
#

ALTER TABLE `usuariobugzilla`
ADD CONSTRAINT `fk_usuarioBugzilla_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

#
#  Foreign keys for table usuariogit
#

ALTER TABLE `usuariogit`
ADD CONSTRAINT `fk_usuarioGit_funcionario1` FOREIGN KEY (`funcionario_idfuncionario`) REFERENCES `funcionario` (`idfuncionario`) ON DELETE NO ACTION ON UPDATE NO ACTION;


/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
