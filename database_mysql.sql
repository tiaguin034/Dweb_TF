-- Script para MySQL (PlanetScale, Railway, 000webhost)
-- Execute este script no seu banco MySQL

-- Criar tabelas
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('tutor', 'org') NOT NULL,
    telefone VARCHAR(20),
    endereco TEXT,
    cidade VARCHAR(50),
    estado VARCHAR(2),
    cep VARCHAR(10),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS animais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    especie ENUM('Cachorro', 'Gato') NOT NULL,
    raca VARCHAR(50),
    idade INT,
    sexo ENUM('macho', 'femea') NOT NULL,
    tamanho ENUM('pequeno', 'medio', 'grande'),
    cor VARCHAR(30),
    descricao TEXT,
    foto_url VARCHAR(255),
    cidade VARCHAR(50) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    usuario_id INT NOT NULL,
    data_publicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    adotado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS solicitacoes_adocao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    solicitante_nome VARCHAR(100) NOT NULL,
    solicitante_email VARCHAR(100) NOT NULL,
    solicitante_telefone VARCHAR(20),
    mensagem TEXT,
    status ENUM('pendente', 'aprovada', 'rejeitada') DEFAULT 'pendente',
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animais(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS campanhas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    tipo ENUM('vacinacao', 'castracao', 'adocao', 'outros') NOT NULL,
    data_evento DATE NOT NULL,
    hora_inicio TIME,
    hora_fim TIME,
    cidade VARCHAR(50) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    endereco TEXT,
    usuario_id INT NOT NULL,
    vagas_disponiveis INT DEFAULT 0,
    vagas_preenchidas INT DEFAULT 0,
    ativa BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campanha_id INT,
    usuario_id INT NOT NULL,
    animal_nome VARCHAR(50),
    animal_especie ENUM('Cachorro', 'Gato'),
    data_agendamento DATE NOT NULL,
    hora_agendamento TIME,
    status ENUM('agendado', 'confirmado', 'realizado', 'cancelado') DEFAULT 'agendado',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campanha_id) REFERENCES campanhas(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    especie ENUM('Cachorro', 'Gato') NOT NULL,
    raca VARCHAR(50),
    idade INT,
    sexo ENUM('macho', 'femea'),
    foto_url VARCHAR(255),
    usuario_id INT NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS historico_medico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    tipo ENUM('vacina', 'consulta', 'cirurgia', 'outros') NOT NULL,
    descricao VARCHAR(200) NOT NULL,
    data_procedimento DATE NOT NULL,
    veterinario VARCHAR(100),
    observacoes TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
);

-- Inserir dados de exemplo
INSERT INTO usuarios (nome, email, senha, perfil, telefone, cidade, estado) VALUES
('Admin Sistema', 'admin@medvet.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'org', '(11) 99999-9999', 'São Paulo', 'SP'),
('Maria Silva', 'maria@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tutor', '(11) 88888-8888', 'São Paulo', 'SP'),
('Clínica PetCare', 'contato@petcare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'org', '(31) 77777-7777', 'Belo Horizonte', 'MG');

INSERT INTO animais (nome, especie, raca, idade, sexo, tamanho, cor, descricao, foto_url, cidade, estado, usuario_id) VALUES
('Thor', 'Cachorro', 'Labrador', 2, 'macho', 'medio', 'Dourado', 'Cachorro muito dócil e brincalhão', 'img/thor.jpg', 'Belo Horizonte', 'MG', 1),
('Luna', 'Gato', 'Siamês', 1, 'femea', 'pequeno', 'Branco e cinza', 'Gata carinhosa e tranquila', 'img/luna.jpg', 'Uberlândia', 'MG', 1),
('Bidu', 'Cachorro', 'Vira-lata', 3, 'macho', 'pequeno', 'Preto', 'Cachorro esperto e leal', 'img/bidu.jpg', 'São Paulo', 'SP', 1);

INSERT INTO campanhas (nome, descricao, tipo, data_evento, cidade, estado, endereco, usuario_id, vagas_disponiveis) VALUES
('Campanha de Vacinação', 'Vacinação antirrábica gratuita para cães e gatos', 'vacinacao', '2025-09-10', 'Uberlândia', 'MG', 'Praça Central', 1, 100),
('Mutirão de Castração', 'Agendamento online com vagas limitadas', 'castracao', '2025-08-30', 'Belo Horizonte', 'MG', 'Clínica Municipal', 1, 50),
('Feira de Adoção', 'Conheça pets incríveis e adote com responsabilidade', 'adocao', '2025-08-25', 'São Paulo', 'SP', 'Parque Ibirapuera', 1, 0);

INSERT INTO pets (nome, especie, raca, idade, sexo, usuario_id) VALUES
('Rex', 'Cachorro', 'Pastor Alemão', 3, 'macho', 2);

INSERT INTO historico_medico (pet_id, tipo, descricao, data_procedimento, veterinario) VALUES
(1, 'vacina', 'Antirrábica', '2025-04-10', 'Dr. João Silva'),
(1, 'consulta', 'Check-up geral', '2025-05-15', 'Dr. João Silva');
