-- SCRIPT DE DDL - DATA DEFINITION LANGUAGE
-- apaga o banco de dados escolabd, se ele existir.
DROP SCHEMA IF EXISTS pccsampledb;

-- cria um banco de dados chamado escolabd.
CREATE SCHEMA pccsampledb;

-- cria uma tabela chamada usuarios com os campos: 
-- id, email, password, nome, status, perfil e data_cadastro.
CREATE TABLE pccsampledb.usuarios (
    id              INTEGER PRIMARY KEY AUTO_INCREMENT,
    email           VARCHAR(255) NOT NULL UNIQUE,
    password        VARCHAR(60) NOT NULL,
    nome            VARCHAR(40) NOT NULL,
    tel             VARCHAR(14) NOT NULL,
    msg             VARCHAR(150) NOT NULL,
    perfil          CHAR(3) NOT NULL DEFAULT 'USU' COMMENT 'ADM=Administrador\nGER=Gerente\nEDI=Editor\nUSU=Usuario', 
    status          BOOLEAN NOT NULL DEFAULT TRUE,  
    data_cadastro   DATETIME NOT NULL DEFAULT NOW()
);

-- cria uma tabela chamada categorias com os campos: 
-- id, nome e status.
CREATE TABLE pccsampledb.categorias (
    id              INTEGER PRIMARY KEY AUTO_INCREMENT,
    nome            VARCHAR(40) NOT NULL,
    status          BOOLEAN NOT NULL DEFAULT TRUE,
    tipo            CHAR(3) NOT NULL DEFAULT 'ART' COMMENT 'ART=Artigo\nCUR=Curso' 
);

-- cria uma tabela chamada artigos com os campos: 
-- id, titulo, texto, status, data_publicacao, categoria e usuario.
CREATE TABLE pccsampledb.artigos (
    id                  INTEGER PRIMARY KEY AUTO_INCREMENT,
    titulo              VARCHAR(100) NOT NULL,
    texto               TEXT,
    status              BOOLEAN NOT NULL DEFAULT FALSE,
    data_publicacao     DATETIME DEFAULT NOW(),
    imagem              VARCHAR(255) NULL,
    imagem_externa      BOOLEAN NOT NULL DEFAULT TRUE,
    categoria_id        INTEGER NOT NULL,
    usuario_id          INTEGER NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES pccsampledb.categorias(id),
    FOREIGN KEY (usuario_id) REFERENCES pccsampledb.usuarios(id)
);

-- SCRIPT DE DML - DATA MANIPULATION LANGUAGE
-- inserir dados na tabela de usuario. 
INSERT INTO pccsampledb.usuarios (id, email, password, nome, perfil, tel, msg, status) 
    VALUES (1, 'admin@email.com', md5('123'), 'Admin', 'ADM','61 999999999','QNP13', 1), 
    (2, 'gerente@email.com', md5('1234'), 'Gerente', 'GER', '61 999999999', 'QNP13', 0), 
    (3, 'editor@email.com', md5('12345'), 'Editor', 'EDI', '61 999999999', 'QNP13', 0), 
    (4, 'usuario@email.com', md5('123456'), 'Usuário', 'USU', '61 999999999', 'QNP13', 1); 


-- inserir dados na tabela de usuario. 
INSERT INTO pccsampledb.categorias (id, nome, status, tipo)
    VALUES (),
            (),
            (),
            (),
            ();

-- inserir dados na tabela de artigos.
INSERT INTO pccsampledb.artigos (titulo, status, data_publicacao, categoria_id, usuario_id, texto, imagem, imagem_externa) 
    VALUES(),
    (),
    (),
    (),
    (),
    (),
    (),
    (),
    ();
