drop database gerenciador_financeiro;

create database gerenciador_financeiro;

use gerenciador_financeiro;

CREATE TABLE Usuarios ( 
    ID int NOT NULL UNIQUE AUTO_INCREMENT, 
    name varchar(255) NOT NULL UNIQUE, 
    password varchar(255),
    CONSTRAINT PK_Usuarios PRIMARY KEY (ID)
);