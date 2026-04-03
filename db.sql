drop database pucaronas;
CREATE DATABASE pucaronas;
USE pucaronas;
CREATE TABLE usuario(
id INT PRIMARY KEY AUTO_INCREMENT,
nome VARCHAR(100),
email VARCHAR(100) UNIQUE,
senha_hash VARCHAR(128),
data_nasc DATE,
status VARCHAR(20),
cargo ENUM('passageiro','motorista','admin'),
doc VARCHAR(255)
);

CREATE TABLE veiculo(
id INT PRIMARY KEY AUTO_INCREMENT,
id_motorista INT,
modelo VARCHAR(128),
placa VARCHAR(7),
n_assentos INT,
FOREIGN KEY (id_motorista) REFERENCES usuario(id)
);
 
CREATE TABLE carona(
id INT PRIMARY KEY AUTO_INCREMENT,
id_motorista INT,
id_veiculo INT, 
titulo VARCHAR(50),
descricao VARCHAR(200),
mensagem VARCHAR(50),
vagas INT, 
status VARCHAR(50), 
origem VARCHAR(50),
destino VARCHAR(50),
FOREIGN KEY (id_motorista) REFERENCES usuario(id),
FOREIGN KEY (id_veiculo) REFERENCES veiculo(id)
);

CREATE TABLE aplicacao (
id INT AUTO_INCREMENT PRIMARY KEY,
id_passageiro INT,
id_carona INT,
status VARCHAR(10), 
data_aplicacao TIMESTAMP, 
data_revisao TIMESTAMP,
mensagem VARCHAR(100),
FOREIGN KEY (id_passageiro) REFERENCES usuario(id),
FOREIGN KEY (id_carona) REFERENCES carona(id)
);

