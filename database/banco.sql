drop database gerenciador_financeiro;

create database gerenciador_financeiro;

use gerenciador_financeiro;

CREATE TABLE Usuarios ( 
    id serial NOT NULL UNIQUE, 
    name varchar(255) NOT NULL UNIQUE, -- mudar pra "email"
    password varchar(255), -- mudar pra "senha"
    CONSTRAINT PK_Usuarios PRIMARY KEY (ID)
);

CREATE TABLE Contas ( 
    id serial NOT NULL UNIQUE,
    nome varchar(255) NOT NULL,
    saldo NUMERIC(24,2) not null,
    usuario_id bigint not null,
    CONSTRAINT PK_Contas PRIMARY KEY (id),
    CONSTRAINT FK_ContasUsuarios FOREIGN KEY (usuario_id) references Usuarios(id)
);

select * from Usuarios;
insert into Contas (nome,saldo,usuario_id) values ('conta 1',100.00, 12);
insert into Contas (nome,saldo,usuario_id) values ('conta 2',2100.00, 12);

select * from Contas;


CREATE TABLE Movimentos (
    id serial NOT NULL UNIQUE,
    descricao varchar(255) NOT NULL,
    valor NUMERIC(24,2) NOT NULL,
    tipo  smallint NOT NULL,
    usuario_id bigint not null,
    conta_id bigint not null,
    CONSTRAINT PK_Movimentos PRIMARY KEY (id),
    CONSTRAINT FK_MovimentosContas  FOREIGN KEY (conta_id) references Contas(id),
    CONSTRAINT FK_MovimentosUsuarios  FOREIGN KEY (usuario_id) references Usuarios(id)
);


-- DAR UM INSERT NUM MOVIMENTO DIRETO PELO BANCO (PARA TER MOVIMENTOS PARA CONSULTAR NO ITEM ABAIXO)

INSERT INTO Movimentos (descricao, valor, tipo, usuario_id, conta_id)
VALUES('primeiro movimento', 500, 1, 12, 4);

/**

PROXIMO PASSO

CRIAR UMA ROTA PARA LISTAR TODOS OS MOVIMENTOS DE UMA CONTA

COMO VC VAI TESTAR ESSE AQUI EM CIMA?




CRIAR UMA ROTA PARA CRIAR UM MOVIMENTO EM UMA CONTA

CRIAR A TELA PARA EXIBIR OS MOVIMENTOS DE UMA CONTA

CRIAR A TELA PARA CADASTRAR OS MOVIMENTOS DE UMA CONTA

*/



-- https://www.postgresql.org/docs/9.5/datatype-numeric.html#DATATYPE-FLOAT