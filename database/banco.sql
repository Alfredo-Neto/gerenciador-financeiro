drop database gerenciador_financeiro;

create database gerenciador_financeiro;

use gerenciador_financeiro;

CREATE TABLE Usuarios ( 
    ID serial NOT NULL UNIQUE, 
    name varchar(255) NOT NULL UNIQUE,
    password varchar(255),
    CONSTRAINT PK_Usuarios PRIMARY KEY (ID)
);

CREATE TABLE Usuarios ( 
    ID int NOT NULL UNIQUE, 
    name text
);


46259
46260