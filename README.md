# MedVet Connect

Sistema web para adoção de animais e campanhas veterinárias, desenvolvido com PHP, MySQL e JavaScript.

## Funcionalidades

### Área Pública
- Página inicial com informações sobre o sistema
- Página de login e cadastro de usuários
- Listagem de animais para adoção com filtros
- Listagem de campanhas e eventos
- Mapa de clínicas e ONGs

### Área Privada
- **Tutores**: Gerenciamento de pets, agendamentos, histórico médico
- **Organizações**: Criação de campanhas, publicação de animais, relatórios
- Edição de perfil e exclusão de conta
- Sistema de busca AJAX

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL

## Instalação

1. **Clone ou baixe o projeto**
   ```bash
   git clone [url-do-repositorio]
   cd medvet-connect
   ```

2. **Configure o banco de dados**
   - Crie um banco de dados MySQL
   - Execute o script `backend/sql/database.sql` para criar as tabelas
   - Configure as credenciais em `backend/config/database.php`

3. **Configure o servidor web**
   - Coloque os arquivos em um diretório acessível pelo servidor web
   - Certifique-se de que o PHP está configurado corretamente
   - O arquivo `.htaccess` já está configurado para CORS

4. **Acesse o sistema**
   - Abra o navegador e acesse o diretório do projeto
   - A página inicial será carregada automaticamente

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
