CREATE DATABASE servicofacil CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE servicofacil;

CREATE TABLE usuarios (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          nome VARCHAR(100) NOT NULL,
                          email VARCHAR(150) NOT NULL UNIQUE,
                          senha VARCHAR(255) NOT NULL,
                          tipo ENUM('cliente','prestador') NOT NULL,
                          credito DECIMAL(10,2) NOT NULL DEFAULT 0
);

CREATE TABLE solicitacoes (
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              numero VARCHAR(20) NOT NULL UNIQUE,
                              cliente_id INT NOT NULL,
                              prestador_id INT NOT NULL,
                              tipo_servico VARCHAR(100) NOT NULL,
                              servico VARCHAR(150) NOT NULL,
                              descricao TEXT,
                              preco DECIMAL(10,2) NOT NULL,
                              valor_cobrado DECIMAL(10,2) NOT NULL,
                              status ENUM('novo','pendente','execucao','concluido','cancelado') NOT NULL,
                              data_criacao DATETIME NOT NULL,
                              data_conclusao DATETIME DEFAULT NULL,
                              avaliado TINYINT(1) NOT NULL DEFAULT 0,
                              nota_avaliacao INT DEFAULT NULL,
                              comentario_avaliacao TEXT,
                              FOREIGN KEY (cliente_id) REFERENCES usuarios(id),
                              FOREIGN KEY (prestador_id) REFERENCES usuarios(id)
);

INSERT INTO usuarios (nome,email,senha,tipo,credito) VALUES
                                                         ('Cliente 1','cliente1@teste.com.br','senha123','cliente',40.00),
                                                         ('Cliente 2','cliente2@teste.com.br','senha321','cliente',0.00),
                                                         ('Prestador 1','prestador1@teste.com.br','senha456','prestador',0.00),
                                                         ('Prestador 2','prestador2@teste.com.br','senha654','prestador',0.00);

INSERT INTO solicitacoes
(numero,cliente_id,prestador_id,tipo_servico,servico,descricao,preco,valor_cobrado,status,data_criacao)
VALUES
    ('700001',1,4,'Serviço Elétrico','Conserto de tomada','Tomada com problema na sala',200.00,160.00,'execucao','2017-02-01 10:00:00'),
    ('700002',2,4,'Serviço Elétrico','Troca de disjuntor','Disjuntor desarmando',150.00,150.00,'pendente','2017-02-02 10:00:00');
