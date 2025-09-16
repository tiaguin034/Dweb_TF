-- Script para configurar banco no Vercel Postgres
-- Execute este script no Vercel Postgres

-- Criar tabelas
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil VARCHAR(20) NOT NULL CHECK (perfil IN ('tutor', 'org')),
    telefone VARCHAR(20),
    endereco TEXT,
    cidade VARCHAR(50),
    estado VARCHAR(2),
    cep VARCHAR(10),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS animais (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    especie VARCHAR(20) NOT NULL CHECK (especie IN ('Cachorro', 'Gato')),
    raca VARCHAR(50),
    idade INTEGER,
    sexo VARCHAR(10) NOT NULL CHECK (sexo IN ('macho', 'femea')),
    tamanho VARCHAR(20) CHECK (tamanho IN ('pequeno', 'medio', 'grande')),
    cor VARCHAR(30),
    descricao TEXT,
    foto_url VARCHAR(255),
    cidade VARCHAR(50) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    data_publicacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    adotado BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS solicitacoes_adocao (
    id SERIAL PRIMARY KEY,
    animal_id INTEGER NOT NULL REFERENCES animais(id) ON DELETE CASCADE,
    solicitante_nome VARCHAR(100) NOT NULL,
    solicitante_email VARCHAR(100) NOT NULL,
    solicitante_telefone VARCHAR(20),
    mensagem TEXT,
    status VARCHAR(20) DEFAULT 'pendente' CHECK (status IN ('pendente', 'aprovada', 'rejeitada')),
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS campanhas (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('vacinacao', 'castracao', 'adocao', 'outros')),
    data_evento DATE NOT NULL,
    hora_inicio TIME,
    hora_fim TIME,
    cidade VARCHAR(50) NOT NULL,
    estado VARCHAR(2) NOT NULL,
    endereco TEXT,
    usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    vagas_disponiveis INTEGER DEFAULT 0,
    vagas_preenchidas INTEGER DEFAULT 0,
    ativa BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS agendamentos (
    id SERIAL PRIMARY KEY,
    campanha_id INTEGER REFERENCES campanhas(id) ON DELETE SET NULL,
    usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    animal_nome VARCHAR(50),
    animal_especie VARCHAR(20) CHECK (animal_especie IN ('Cachorro', 'Gato')),
    data_agendamento DATE NOT NULL,
    hora_agendamento TIME,
    status VARCHAR(20) DEFAULT 'agendado' CHECK (status IN ('agendado', 'confirmado', 'realizado', 'cancelado')),
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pets (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(50) NOT NULL,
    especie VARCHAR(20) NOT NULL CHECK (especie IN ('Cachorro', 'Gato')),
    raca VARCHAR(50),
    idade INTEGER,
    sexo VARCHAR(10) CHECK (sexo IN ('macho', 'femea')),
    foto_url VARCHAR(255),
    usuario_id INTEGER NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS historico_medico (
    id SERIAL PRIMARY KEY,
    pet_id INTEGER NOT NULL REFERENCES pets(id) ON DELETE CASCADE,
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('vacina', 'consulta', 'cirurgia', 'outros')),
    descricao VARCHAR(200) NOT NULL,
    data_procedimento DATE NOT NULL,
    veterinario VARCHAR(100),
    observacoes TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
