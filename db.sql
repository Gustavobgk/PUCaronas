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
INSERT INTO usuario (nome, email, senha_hash, data_nasc, status, cargo, doc) VALUES
('ADM', 'ADM@pucpr.edu.br', 'ADM12345', '1986-04-26', 'ativo', 'admin', 'https://www.youtube.com/watch?v=qbWlwL9CygM');

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


DELIMITER $$
CREATE PROCEDURE deletar_carona(IN p_id_carona INT)
BEGIN
    DECLARE v_status_carona VARCHAR(50);
    DECLARE v_vagas_original INT;

    SELECT status, vagas INTO v_status_carona, v_vagas_original
    FROM carona
    WHERE id = p_id_carona;

    IF v_status_carona IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Erro: Carona não encontrada.';

    ELSEIF v_status_carona = 'em_andamento' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Erro: Não é possível deletar uma carona em andamento.';

    ELSE
        UPDATE corrida
        SET status = 'cancelada'
        WHERE id_carona = p_id_carona
          AND status IN ('pendente', 'em_andamento');

        DELETE FROM aplicacao
        WHERE id_carona = p_id_carona;

        DELETE FROM carona
        WHERE id = p_id_carona;

    END IF;
END $$
DELIMITER ;


DELIMITER $$
CREATE PROCEDURE deletar_usuario(IN p_id_usuario INT)
BEGIN
    DECLARE v_cargo VARCHAR(20);
    DECLARE v_status VARCHAR(20);
    DECLARE v_id_carona INT;

    SELECT cargo, status INTO v_cargo, v_status
    FROM usuario
    WHERE id = p_id_usuario;

    IF v_cargo IS NULL THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Erro: Usuário não encontrado.';

    ELSEIF v_cargo = 'admin' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Erro: Não é possível deletar um administrador.';

    ELSE
        IF v_cargo = 'motorista' THEN

            UPDATE corrida
            SET status = 'cancelada'
            WHERE id_motorista = p_id_usuario
              AND status IN ('pendente', 'em_andamento');

            DELETE FROM aplicacao
            WHERE id_carona IN (
                SELECT id FROM carona WHERE id_motorista = p_id_usuario
            );

            UPDATE corrida
            SET status = 'cancelada'
            WHERE id_carona IN (
                SELECT id FROM carona WHERE id_motorista = p_id_usuario
            ) AND status IN ('pendente', 'em_andamento');

            DELETE FROM carona
            WHERE id_motorista = p_id_usuario;

            DELETE FROM veiculo
            WHERE id_motorista = p_id_usuario;

        END IF;

        IF v_cargo = 'passageiro' THEN

            UPDATE corrida
            SET status = 'cancelada'
            WHERE id_passageiro = p_id_usuario
              AND status IN ('pendente', 'em_andamento');

            DELETE FROM aplicacao
            WHERE id_passageiro = p_id_usuario;

        END IF;

        DELETE FROM log_usuario_status
        WHERE id_usuario = p_id_usuario;

        DELETE FROM usuario
        WHERE id = p_id_usuario;

    END IF;
END $$
DELIMITER ;



