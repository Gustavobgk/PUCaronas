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


CREATE TABLE corrida (
id INT PRIMARY KEY AUTO_INCREMENT,
id_motorista INT,
id_passageiro INT,
id_carona INT,

data_inicio DATETIME,
data_fim DATETIME,

origem VARCHAR(100),
destino VARCHAR(100),

valor DECIMAL(10,2),

status ENUM('pendente','em_andamento','finalizada','cancelada'),

FOREIGN KEY (id_motorista) REFERENCES usuario(id),
FOREIGN KEY (id_passageiro) REFERENCES usuario(id),
FOREIGN KEY (id_carona) REFERENCES carona(id)
);


DELIMITER $$

CREATE TRIGGER aplicacao_corrida 
AFTER UPDATE ON aplicacao
FOR EACH ROW 
BEGIN
    DECLARE v_id_motorista INT;
    DECLARE v_origem VARCHAR(50);
    DECLARE v_destino VARCHAR(50);
    IF NEW.status = 'aprovado' AND OLD.status <> 'aprovado' THEN
 
        SELECT id_motorista, origem, destino 
        INTO v_id_motorista, v_origem, v_destino
        FROM carona 
        WHERE id = NEW.id_carona;

        INSERT INTO corrida (
            id_motorista, 
            id_passageiro, 
            id_carona, 
            origem, 
            destino, 
            status
        ) VALUES (
            v_id_motorista,
            NEW.id_passageiro,
            NEW.id_carona,
            v_origem,
            v_destino,
            'pendente'
        );
        
        UPDATE carona 
        SET vagas = vagas - 1 
        WHERE id = NEW.id_carona AND vagas > 0;
        
    END IF;
END$$

DELIMITER ;

CREATE TABLE log_usuario_status(
	id_usuario INT,
	status_anterior VARCHAR(10),
	status_novo VARCHAR(10),
	data_hora TIMESTAMP
);

DELIMITER $$

CREATE TRIGGER usuario_status 
AFTER UPDATE ON usuario
FOR EACH ROW 
BEGIN
 IF NEW.status = 'aprovado' AND OLD.status <> 'espera' THEN
INSERT INTO log_usuario_status (id_usuario,status_anterior,status_novo,data_hora) VALUES (OLD.id, OLD.status,NEW.status,NOW() );
 END IF;
END $$

DELIMITER ;

DELIMITER $$ 

CREATE PROCEDURE aprovar_usuario(IN p_id_usuario INT)
BEGIN
    UPDATE usuario  
    SET status = 'aprovado'
    WHERE id = p_id_usuario;
END $$






