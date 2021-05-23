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

-- https://www.postgresql.org/docs/9.5/datatype-numeric.html#DATATYPE-FLOAT