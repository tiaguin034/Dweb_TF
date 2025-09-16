# MedVet Connect

Sistema web para adoção de animais e campanhas veterinárias, hospedado no **Vercel** com banco de dados **Railway**.

## 🚀 Deploy Online

**URL**: [https://medvet-connect.vercel.app](https://medvet-connect.vercel.app)

## ✨ Funcionalidades

### Área Pública
- Página inicial responsiva
- Sistema de login e cadastro
- Listagem de animais para adoção com filtros
- Listagem de campanhas e eventos
- Mapa de clínicas e ONGs

### Área Privada
- **Tutores**: Gerenciamento de pets, agendamentos, histórico médico
- **Organizações**: Criação de campanhas, publicação de animais, relatórios
- Edição de perfil e exclusão de conta
- Sistema de busca AJAX

## 🛠️ Tecnologias

- **Frontend**: HTML5, CSS3, JavaScript (jQuery), Bootstrap 5
- **Backend**: PHP 8+ com classes organizadas
- **Banco de Dados**: MySQL (Railway)
- **Hospedagem**: Vercel
- **APIs**: REST com JSON

## 📋 Deploy Rápido

### 1. Configurar Railway (Banco de Dados)
1. Acesse [railway.app](https://railway.app)
2. Crie um banco MySQL
3. Execute o script `database_mysql.sql`
4. Anote as credenciais

### 2. Configurar Vercel (Hospedagem)
1. Acesse [vercel.com](https://vercel.com)
2. Importe este repositório
3. Configure as variáveis de ambiente:
   ```
   DB_HOST = [host do Railway]
   DB_NAME = [nome do banco]
   DB_USER = [usuário]
   DB_PASS = [senha]
   ```

### 3. Deploy
- O Vercel fará deploy automático
- Acesse: `https://seuprojeto.vercel.app`
- Teste: `https://seuprojeto.vercel.app/teste.php`

## Estrutura do Projeto

```
medvet-connect/
├── backend/
│   ├── api/           # APIs REST
│   ├── classes/       # Classes PHP
│   ├── config/        # Configurações
│   └── sql/          # Scripts SQL
├── img/              # Imagens
├── *.html            # Páginas frontend
├── app.js            # JavaScript principal
├── style.css         # Estilos CSS
└── README.md         # Este arquivo
```

## APIs Disponíveis

- `backend/api/auth.php` - Autenticação (login/cadastro)
- `backend/api/animais.php` - CRUD de animais
- `backend/api/campanhas.php` - CRUD de campanhas
- `backend/api/usuarios.php` - Gerenciamento de usuários
- `backend/api/busca.php` - Busca geral

## Banco de Dados

O sistema utiliza as seguintes tabelas principais:
- `usuarios` - Dados dos usuários
- `animais` - Animais para adoção
- `campanhas` - Campanhas e eventos
- `solicitacoes_adocao` - Solicitações de adoção
- `agendamentos` - Agendamentos de consultas
- `pets` - Pets dos tutores
- `historico_medico` - Histórico médico/vacinal

## Backup do Banco

Para gerar o backup do banco de dados:
```bash
mysqldump -u [usuario] -p medvet_connect > bd_medvet_connect.sql
```

## Desenvolvimento

O sistema foi desenvolvido seguindo as especificações:
- Backend em PHP com classes para separação de responsabilidades
- Banco de dados MySQL com PDO
- Frontend responsivo com Bootstrap
- Integração via AJAX
- Sistema de autenticação com sessões
- Área pública e privada
- Funcionalidade de busca

## Tecnologias Utilizadas

- **Backend**: PHP 7.4+, MySQL
- **Frontend**: HTML5, CSS3, JavaScript (jQuery), Bootstrap 5
- **APIs**: REST com JSON
- **Autenticação**: Sessões PHP
