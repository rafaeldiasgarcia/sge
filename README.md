# 📊 SGE - Sistema de Gerenciamento de Eventos UNIFIO

> Sistema web completo para gerenciamento de agendamentos de quadras esportivas, administração de atléticas, cursos e usuários da UNIFIO.

[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-9.4-orange.svg)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-green.svg)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-Academic-yellow.svg)](LICENSE)

---

## 📋 Índice

- [Visão Geral](#-visão-geral)
- [Novidades](#-novidades)
- [Funcionalidades](#-funcionalidades)
- [Arquitetura](#-arquitetura)
- [Tecnologias](#-tecnologias)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Uso](#-uso)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Banco de Dados](#-banco-de-dados)
- [API e Endpoints](#-api-e-endpoints)
- [Sistema de Permissões](#-sistema-de-permissões)
- [Desenvolvimento](#-desenvolvimento)
- [Contribuição](#-contribuição)
- [Solução de Problemas](#-solução-de-problemas)
- [Licença](#-licença)

---

## 🎯 Visão Geral

O **SGE (Sistema de Gerenciamento de Eventos)** é uma aplicação web desenvolvida para o Centro Universitário UNIFIO com o objetivo de modernizar e centralizar a gestão de eventos esportivos e acadêmicos na quadra poliesportiva da instituição.

### Características Principais

- **Arquitetura MVC Moderna**: Separação clara de responsabilidades
- **Containerizado com Docker**: Ambiente consistente e fácil deploy
- **Autenticação Segura**: Login com verificação em 2 etapas (2FA)
- **Sistema de Notificações**: Notificações em tempo real via AJAX
- **Interface Responsiva**: Design moderno e mobile-friendly
- **Calendário Interativo**: Navegação mensal com eventos dinâmicos
- **Gestão Multinível**: 3 níveis de acesso (Usuário, Admin, Super Admin)

### Problema que Resolve

Antes do SGE, o gerenciamento de eventos na quadra da UNIFIO era feito de forma manual e descentralizada, causando:
- Conflitos de agendamento
- Falta de transparência
- Dificuldade de controle de presença
- Ausência de relatórios e estatísticas
- Comunicação ineficiente

O SGE resolve todos esses problemas com uma plataforma centralizada, automatizada e transparente.

---

## 🆕 Novidades

Esta seção consolida atualizações recentes anteriormente descritas em documentos auxiliares. Qualquer conteúdo que existia em `NOVOREADME.md` foi integrado e organizado abaixo.

### Destaques

- ✅ Pop-ups de eventos agora exibem prompts de login para usuários não autenticados.
- ✅ Lógica de agendamento aprimorada para campeonatos (validando cenários específicos e comunicando melhor restrições).
- ✅ Nova seção de Termos e Políticas na página de agendamento, com links para documentos em `public/doc/` e um checkbox obrigatório de aceite.
- ✅ Novo esquema de banco de dados para Solicitações de Troca de Curso.
- ✅ Tipos de notificação expandidos para cobrir novos fluxos (troca de curso, termos e campeonatos).
- ✅ Melhorias de UI/UX em componentes de formulário, pop-ups e feedback visual.

## 🚀 Funcionalidades

### 🔐 Sistema de Autenticação e Autorização

#### Autenticação em 2 Etapas (2FA)
- **Etapa 1**: Login com email/RA e senha
- **Etapa 2**: Código de 6 dígitos enviado por e-mail
- **Expiração**: Código válido por 15 minutos
- **Segurança**: Proteção contra acesso não autorizado

#### Recuperação de Senha
- Link de recuperação enviado por e-mail
- Token único com validade de 1 hora
- Redefinição segura de senha
- Templates HTML profissionais

#### 3 Níveis de Acesso
1. **Usuário Comum**: Alunos, Professores, Comunidade Externa
2. **Admin de Atlética**: Gerencia membros e eventos da atlética
3. **Super Admin**: Controle total do sistema

### 📅 Gestão de Agendamentos

#### Calendário Interativo
- Navegação mensal com AJAX (sem recarregar página)
- Visualização de eventos por dia
- Cores diferenciadas por status (aprovado, pendente, rejeitado)
- Modal de detalhes ao clicar em qualquer evento

#### Pop-up de Evento com Prompt de Login
- Se o usuário não estiver autenticado, o pop-up orienta a realizar login antes de interagir (marcar presença, visualizar detalhes avançados ou iniciar agendamento a partir do evento).
- CTA direto para `login` com retorno à tela atual após autenticação.

#### Períodos de Agendamento
O sistema trabalha com **2 períodos fixos por dia**:
- **Primeiro Período**: 19:15 - 20:55 (1h40min)
- **Segundo Período**: 21:10 - 22:50 (1h40min)

#### Tipos de Eventos

**Eventos Esportivos:**
- Treinos de atléticas
- Campeonatos interatléticas
- Jogos amistosos
- Aulas de educação física

**Eventos Não Esportivos:**
- Palestras e workshops
- Formaturas e cerimônias
- Eventos institucionais
- Atividades culturais

#### Validações Inteligentes
- ✅ Antecedência mínima de 4 dias
- ✅ Verificação automática de conflitos de horário
- ✅ Bloqueio de datas passadas
- ✅ Limite de 1 agendamento por esporte por semana (por usuário)
- ✅ Validação de responsável e participantes
 - ✅ Regras específicas para campeonatos (mensagens claras, prevenção de conflitos e instruções de cadastro)

#### Workflow de Aprovação
```
Usuário Solicita → Pendente → Super Admin Analisa → Aprovado/Rejeitado
                                                    ↓
                                            Notificação Enviada
```

#### Formulário Completo de Agendamento
- **Informações Básicas**: Título, tipo, data, período
- **Responsável**: Nome, telefone, e-mail
- **Materiais**: Lista de materiais necessários
- **Participantes**: RAs dos participantes
- **Eventos Esportivos**: Árbitro, modalidade, atlética adversária
- **Eventos Não Esportivos**: Público-alvo, infraestrutura adicional
- **Observações**: Informações complementares

#### Termos e Políticas (Obrigatório)
- Nova seção no final do formulário de agendamento com links para:
  - Regulamento de Uso da Quadra (`public/doc/regulamento.pdf`)
  - Política de Privacidade (`public/doc/politica-privacidade.pdf`)
  - Termos do Usuário (`public/doc/termo-usuario.pdf`)
- Checkbox de aceite obrigatório para prosseguir com a criação/edição do agendamento.
- Mensagens de erro amigáveis quando o aceite não for marcado.

### 🏃 Sistema de Confirmação de Presença

#### Funcionalidades
- **Marcar Presença**: Botão em cada evento da agenda
- **Toggle Instantâneo**: Marcar/desmarcar com um clique
- **Feedback Visual**: Botão muda de cor ao confirmar
- **Contador Dinâmico**: Mostra quantas pessoas confirmaram
- **Notificação Automática**: Confirmação imediata + lembrete 1 dia antes

#### Armazenamento
- Tabela dedicada `presencas` no banco de dados
- Chave única: (usuario_id, agendamento_id)
- Timestamp de confirmação
- Integrado com sistema de notificações

#### Visualização (Admin/Super Admin)
- Lista completa de presenças confirmadas
- Nomes e informações de contato
- Contador público para todos os usuários
- Relatórios de participação

### 🔔 Sistema de Notificações

#### Tipos de Notificações
- **Agendamento Aprovado**: Quando sua solicitação é aprovada
- **Agendamento Rejeitado**: Com motivo da rejeição
- **Agendamento Cancelado**: Quando evento é cancelado
- **Presença Confirmada**: Confirmação de marcação
- **Lembrete de Evento**: 1 dia antes do evento (via script diário)
- **Notificações do Sistema**: Avisos importantes
- **Notificações Globais**: Enviadas pelo Super Admin

##### Novos tipos
- `solicitacao_troca_curso_criada`
- `solicitacao_troca_curso_aprovada`
- `solicitacao_troca_curso_rejeitada`
- `termos_aceitos`
- `campeonato_agendado`
- `campeonato_atualizado`

#### Interface em Tempo Real
- **Contador**: Badge com número de notificações não lidas
- **Dropdown**: Lista de notificações recentes no header
- **AJAX**: Atualização sem recarregar a página
- **Marcar como Lida**: Individual ou todas de uma vez
- **Limpeza Automática**: Notificações antigas removidas após 30 dias

#### Script Diário (Lembretes)
Execute diariamente para enviar lembretes automáticos:
```bash
# Linux/Mac (crontab)
0 20 * * * docker exec php php /var/www/html/scripts/daily_notifications.php

# Windows (Task Scheduler)
docker exec php php /var/www/html/scripts/daily_notifications.php
```

### 👥 Painel do Usuário

#### Dashboard
- Visão geral de próximos eventos
- Notificações recentes
- Links rápidos para funcionalidades

#### Gerenciamento de Perfil
- Editar dados pessoais (nome, telefone, data de nascimento)
- Upload de foto de perfil
- Alterar senha
- Visualizar informações de curso e atlética

#### Gerenciamento de Atlética
- **Solicitar Entrada**: Pedido para se juntar a uma atlética
- **Status de Solicitação**: Pendente/Aprovado/Recusado
- **Sair da Atlética**: Opção de desligamento

#### Inscrições em Modalidades
- Lista de modalidades disponíveis (Futsal, Vôlei, Basquete, etc.)
- Inscrever-se em modalidades de interesse
- Aguardar aprovação do admin da atlética
- Cancelar inscrições pendentes

#### Meus Agendamentos
- Lista completa de seus agendamentos
- Filtros por status (todos, pendentes, aprovados, rejeitados, cancelados)
- **Editar**: Agendamentos pendentes ou aprovados (antes da data)
- **Cancelar**: Eventos pendentes ou aprovados
- **Visualizar Detalhes**: Modal com informações completas

#### Agenda Pública
- Calendário com todos os eventos aprovados
- Marcar/desmarcar presença
- Visualizar detalhes de qualquer evento
- Navegação mensal

### 🏅 Painel do Admin de Atlética

#### Dashboard Personalizado
- Estatísticas da sua atlética
- Eventos próximos
- Pendências (solicitações de entrada, inscrições)

#### Gestão de Membros
**Solicitações de Entrada:**
- Lista de pedidos pendentes para entrar na atlética
- Aprovar ou recusar solicitações
- Notificação automática ao usuário

**Membros Ativos:**
- Lista de todos os membros da atlética
- Promover membros a administradores
- Remover membros (com notificação)

#### Gestão de Inscrições em Modalidades
- Lista de inscrições pendentes em modalidades esportivas
- Aprovar ou rejeitar inscrições
- Filtro por modalidade
- Notificação automática ao aluno

#### Gestão de Eventos
- Visualizar eventos da atlética
- Inscrever atletas em eventos específicos
- Remover atletas de eventos
- Calendário filtrado da atlética

### 🔧 Painel do Super Admin

#### Dashboard Completo
- **Estatísticas Gerais**:
  - Total de agendamentos (aprovados, pendentes, rejeitados)
  - Total de usuários ativos
  - Eventos nos próximos 7 dias
  - Uso da quadra por período
- **Gráficos e Métricas** (em desenvolvimento)
- **Links Rápidos**: Acesso a todas as funcionalidades

#### Gerenciamento de Agendamentos
- **Visualizar Todos**: Lista completa de agendamentos do sistema
- **Filtros**: Por status, data, tipo, atlética
- **Aprovar**: Libera evento para agenda pública
- **Rejeitar**: Com campo obrigatório de motivo
- **Editar**: Alterar eventos aprovados (data, horário, informações)
- **Cancelar**: Cancelar eventos aprovados (com motivo)
- **Notificações Automáticas**: Enviadas em todas as ações

#### Gerenciamento de Usuários (CRUD Completo)
- **Listar**: Todos os usuários do sistema
- **Filtros**: Por tipo, curso, atlética
- **Editar**: Alterar qualquer informação do usuário
- **Excluir**: Remover usuário (com confirmação)
- **Pesquisa**: Por nome, email ou RA

#### Gerenciamento de Estrutura Acadêmica

**Cursos:**
- Criar novos cursos
- Editar informações (nome, coordenador)
- Vincular a atléticas
- Excluir cursos

**Atléticas:**
- Criar novas atléticas
- Editar nome
- Excluir atléticas
- Visualizar cursos vinculados

#### Gerenciamento de Modalidades Esportivas
- **Criar**: Novas modalidades (ex: Futsal, Vôlei, Basquete, LoL, CS:GO)
- **Editar**: Nome da modalidade
- **Excluir**: Remover modalidades (com validação)
- **Listar**: Todas as modalidades cadastradas

#### Gerenciamento de Administradores
- **Promover a Admin**: Transformar usuário comum em admin de atlética
- **Rebaixar a Usuário**: Remover privilégios de admin
- **Filtros**: Por atlética
- **Notificações Automáticas**: Informam sobre mudanças de permissão

#### Sistema de Relatórios

**Tipos de Relatórios:**
- **Agendamentos por Período**: Filtrar por intervalo de datas
- **Eventos por Tipo**: Esportivos vs Não Esportivos
- **Participação de Atléticas**: Ranking de uso da quadra
- **Estatísticas Gerais**: Total de eventos, usuários ativos, etc.
- **Modalidades Mais Populares**: Esportes mais agendados

**Recursos:**
- Filtros avançados (data, tipo, status, atlética)
- Tabelas detalhadas com todas as informações
- **Versão para Impressão**: Layout otimizado sem navegação
- Exportação (em desenvolvimento)

#### Notificações Globais
- **Enviar para Todos**: Notifica todos os usuários do sistema
- **Título e Mensagem**: Personalizáveis
- **Tipos**: Informação, Aviso, Sistema
- **Usos**: Manutenções, avisos importantes, comunicados

---

## 🏗️ Arquitetura

### Padrão MVC (Model-View-Controller)

O SGE utiliza uma arquitetura MVC moderna e bem estruturada:

```
┌─────────────────────────────────────────────────────┐
│              FRONT CONTROLLER                       │
│             (public/index.php)                      │
│      ✓ Inicialização de sessão                     │
│      ✓ Autoloading PSR-4                           │
│      ✓ Tratamento global de erros                  │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│                  ROUTER                             │
│             (src/Core/Router.php)                   │
│      ✓ Mapeia URLs para Controllers                │
│      ✓ Suporta parâmetros dinâmicos                │
│      ✓ Method override (PUT via POST)              │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│               MIDDLEWARES                           │
│             (src/Core/Auth.php)                     │
│      ✓ Autenticação (Auth::protect)                │
│      ✓ Autorização (Admin, SuperAdmin)             │
│      ✓ Verificação de permissões                   │
└──────────────────┬──────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────┐
│              CONTROLLERS                            │
│          (src/Controller/*.php)                     │
│   - AuthController: Login, registro, 2FA           │
│   - UsuarioController: Dashboard, perfil           │
│   - AgendamentoController: CRUD agendamentos       │
│   - AdminAtleticaController: Gestão atlética       │
│   - SuperAdminController: Administração total      │
│   - NotificationController: API notificações       │
│   - AgendaController: Calendário público           │
└──────────────────┬──────────────────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
┌──────────────┐      ┌──────────────┐
│  SERVICES    │      │    VIEWS     │
│  (Business   │      │  (Templates) │
│   Logic)     │      │              │
│              │      │ - _partials  │
│- Notification│      │ - auth       │
│  Service     │      │ - usuario    │
│- EmailService│      │ - pages      │
│              │      │ - admin      │
│              │      │ - superadmin │
└──────┬───────┘      └──────────────┘
       │
       ▼
┌──────────────┐
│ REPOSITORIES │
│ (Data Layer) │
│              │
│ - Usuario    │
│ - Agendamento│
│ - Atletica   │
│ - Curso      │
│ - Modalidade │
│ - Notification│
│ - Relatorio  │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  CONNECTION  │
│  (Singleton  │
│    PDO)      │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   DATABASE   │
│  MySQL 9.4   │
│  UTF8MB4     │
└──────────────┘
```

### Camadas da Aplicação

#### 1. Front Controller (`public/index.php`)
**Responsabilidades:**
- Ponto de entrada único da aplicação
- Inicialização de sessão com parâmetros de segurança
- Configuração de timezone (America/Sao_Paulo)
- Carregamento do autoloader do Composer
- Tratamento global de exceções
- Diferenciação de requisições AJAX vs HTML

**Segurança:**
- Cookies httponly (previne XSS)
- Cookies secure (quando HTTPS disponível)
- SameSite=Lax (previne CSRF)
- Sessão expira ao fechar navegador

#### 2. Router (`src/Core/Router.php`)
**Funcionalidades:**
- Roteamento RESTful (GET, POST, PUT)
- Parâmetros dinâmicos na URL (ex: `/usuario/:id`)
- Method override para PUT via POST
- Despacho automático para controllers
- Extração de parâmetros via regex

**Exemplo de Uso:**
```php
Router::get('/perfil', 'UsuarioController@perfil');
Router::post('/perfil', 'UsuarioController@updatePerfil');
Router::get('/usuario/:id', 'UsuarioController@show');
```

#### 3. Controllers (`src/Controller/`)
**Classes Disponíveis:**

| Controller | Responsabilidades |
|-----------|-------------------|
| `BaseController` | Classe base com métodos reutilizáveis |
| `AuthController` | Login 2FA, registro, recuperação de senha |
| `HomeController` | Redirecionamento baseado em role |
| `UsuarioController` | Dashboard, perfil, inscrições |
| `AgendamentoController` | CRUD de agendamentos |
| `AgendaController` | Calendário público, presenças |
| `AdminAtleticaController` | Gestão de membros e eventos |
| `SuperAdminController` | Administração completa |
| `NotificationController` | API REST de notificações |

**BaseController - Métodos Úteis:**
```php
// Instanciar repository
$repo = $this->repository('UsuarioRepository');

// Renderizar view com dados
$this->view('usuario/perfil', ['usuario' => $data]);

// Redirecionar
$this->redirect('/dashboard');
```

#### 4. Repositories (`src/Repository/`)
**Padrão Repository:**
- Abstrai acesso ao banco de dados
- Isola queries SQL dos controllers
- Facilita testes e manutenção
- Reutilização de queries

**Classes Disponíveis:**

| Repository | Responsabilidades |
|-----------|-------------------|
| `UsuarioRepository` | CRUD de usuários, autenticação |
| `AgendamentoRepository` | Gestão de agendamentos, conflitos |
| `AtleticaRepository` | Gestão de atléticas |
| `CursoRepository` | Gestão de cursos acadêmicos |
| `ModalidadeRepository` | Gestão de modalidades esportivas |
| `NotificationRepository` | Sistema de notificações |
| `RelatorioRepository` | Geração de relatórios |
| `AdminAtleticaRepository` | Funcionalidades específicas admin |

**Exemplo de Repository:**
```php
class UsuarioRepository {
    private $db;
    
    public function __construct() {
        $this->db = Connection::getInstance();
    }
    
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
```

#### 5. Services (`src/Core/`)
**Lógica de Negócio:**

**NotificationService:**
- Criação de notificações automáticas
- Envio de lembretes diários
- Notificações em massa
- Integração com repositories

**EmailService:**
- Envio de e-mails via PHPMailer
- Templates HTML responsivos
- Códigos 2FA
- Links de recuperação de senha
- Configuração SMTP (Gmail)

**Auth (Middleware):**
- Verificação de autenticação
- Controle de permissões
- Proteção de rotas
- Helpers de sessão

#### 6. Connection (`src/Core/Connection.php`)
**Padrão Singleton:**
- Uma única instância PDO durante toda execução
- Economia de recursos
- Controle centralizado
- Configuração UTF8MB4
- Timezone GMT-3

**Configuração:**
```php
$db = Connection::getInstance();
// Host: db (container Docker)
// Database: application
// User: appuser
// Password: apppass
// Charset: UTF8MB4
// Timezone: America/Sao_Paulo
```

#### 7. Views (`views/`)
**Organização:**
```
views/
├── _partials/           # Componentes reutilizáveis
│   ├── header.php       # Navegação, notificações
│   ├── footer.php       # Scripts JavaScript
│   └── calendar.php     # Componente de calendário
├── auth/                # Autenticação
│   ├── login.view.php
│   ├── login-verify.view.php
│   ├── registro.view.php
│   ├── esqueci-senha.view.php
│   └── redefinir-senha.view.php
├── usuario/             # Painel usuário
│   ├── dashboard.view.php
│   └── perfil.view.php
├── pages/               # Páginas gerais
│   ├── agenda.view.php
│   ├── agendar-evento.view.php
│   ├── editar-evento.view.php
│   └── meus-agendamentos.view.php
├── admin_atletica/      # Painel admin atlética
│   ├── dashboard.view.php
│   ├── gerenciar-membros.view.php
│   ├── gerenciar-inscricoes.view.php
│   └── gerenciar-eventos.view.php
└── super_admin/         # Painel super admin
    ├── dashboard.view.php
    ├── gerenciar-usuarios.view.php
    ├── gerenciar-agendamentos.view.php
    ├── gerenciar-estrutura.view.php
    ├── gerenciar-modalidades.view.php
    ├── gerenciar-admins.view.php
    └── relatorios.view.php
```

### Helpers Globais (`src/Core/helpers.php`)

**Funções disponíveis em toda aplicação:**

```php
// Renderizar view com dados
view('usuario/perfil', ['nome' => 'João']);

// Redirecionar para URL
redirect('/dashboard');

// Formatar telefone brasileiro
formatarTelefone('11987654321'); // (11) 98765-4321
```

---

## 💻 Tecnologias

### Backend

| Tecnologia | Versão | Uso |
|-----------|--------|-----|
| **PHP** | 8.2 | Linguagem principal com recursos modernos |
| **MySQL** | 9.4 | Banco de dados relacional |
| **Composer** | Latest | Gerenciador de dependências |
| **PHPMailer** | 6.11 | Envio de e-mails |
| **PDO** | - | Abstração de banco de dados |

**Extensões PHP Necessárias:**
- `pdo` - Abstração de banco de dados
- `pdo_mysql` - Driver MySQL
- `intl` - Formatação de datas (opcional)

### Frontend

| Tecnologia | Uso |
|-----------|-----|
| **HTML5** | Estrutura semântica |
| **CSS3** | Estilos modernos e responsivos |
| **JavaScript Vanilla** | Interações dinâmicas |
| **AJAX** | Requisições assíncronas |
| **Fetch API** | Comunicação com backend |

**CSS Modular:**
```
public/css/
├── default.css          # Estilos globais, reset, variáveis
├── header.css           # Navegação, dropdown notificações
├── auth.css             # Páginas de autenticação
├── dashboard.css        # Painéis administrativos
├── calendar.css         # Calendário interativo
├── agenda.css           # Página de agenda
├── event-popup.css      # Modal de detalhes
├── notifications.css    # Sistema de notificações
└── usuario.css          # Perfil do usuário
```

**JavaScript Modular:**
```
public/js/
├── calendar.js          # Calendário com AJAX
├── event-form.js        # Validações de formulário
├── event-popup.js       # Modal de detalhes
├── header.js            # Navegação responsiva
└── notifications.js     # Sistema de notificações
```

**Documentos Públicos:**
```
public/doc/
├── regulamento.pdf
├── politica-privacidade.pdf
└── termo-usuario.pdf
```

### DevOps e Infraestrutura

#### Docker + Docker Compose

**Containers:**

| Container | Imagem | Porta | Descrição |
|-----------|--------|-------|-----------|
| `php` | php:8.2-apache | 80 | Aplicação + Apache |
| `mysql` | mysql:latest | 3306 | Banco de dados |
| `phpmyadmin` | phpmyadmin | 8080 | Admin visual do banco |

**Configuração (`docker-compose.yml`):**
```yaml
services:
  apache:
    build: .
    ports:
      - '80:80'
    volumes:
      - .:/var/www/html
    depends_on:
      - db
  
  db:
    image: mysql
    environment:
      MYSQL_DATABASE: application
      MYSQL_ROOT_PASSWORD: rootpass
    volumes:
      - ./assets/data:/docker-entrypoint-initdb.d
```

**Volumes Docker:**
- **Código**: `.:/var/www/html` (bind mount para desenvolvimento)
- **Banco**: `./assets/data:/docker-entrypoint-initdb.d` (auto-executa schema.sql)
- **Persistência**: MySQL data (removido com `docker-compose down -v`)

#### Dockerfile

**Imagem Base:** `php:8.2-apache`

**Instalações:**
- Composer (latest)
- Git (para dependências)
- Extensões: pdo, pdo_mysql
- Módulos Apache: rewrite, actions

**Autostart:**
- `composer install` executado automaticamente
- PHPMailer instalado via Composer

### Padrões e Boas Práticas

#### PSR-4 Autoloading
```json
{
    "autoload": {
        "psr-4": {
            "Application\\": "src/"
        },
        "files": [
            "src/Core/helpers.php"
        ]
    }
}
```

#### Padrões Utilizados

| Padrão | Onde | Benefício |
|--------|------|-----------|
| **Singleton** | Connection.php | Uma única conexão DB |
| **Repository** | Repository/* | Abstração de dados |
| **MVC** | Todo projeto | Separação de responsabilidades |
| **Front Controller** | index.php | Ponto único de entrada |
| **Dependency Injection** | Controllers | Testabilidade |
| **Service Layer** | Services | Lógica de negócio reutilizável |

#### Convenções de Código

**Nomenclatura:**
- Classes: `PascalCase` (ex: `UsuarioController`)
- Métodos: `camelCase` (ex: `findById`)
- Variáveis: `camelCase` (ex: `$userName`)
- Constantes: `UPPER_SNAKE_CASE` (ex: `ROOT_PATH`)
- Arquivos de view: `kebab-case.view.php` (ex: `login-verify.view.php`)

**Organização:**
- Um namespace por diretório
- Uma classe por arquivo
- Métodos públicos antes de privados
- Dependências injetadas via construtor

---

## 🚀 Instalação

### Pré-requisitos

- **Docker Desktop** (versão 20.10+)
- **Git** (para clonar o repositório)
- **Portas Livres**: 80, 3306, 8080

### Instalação Local (Docker)

#### 1. Clone o Repositório

```bash
git clone https://github.com/seu-usuario/sge.git
cd sge
```

#### 2. Inicie os Containers

```bash
docker-compose up -d
```

**O que acontece:**
- Container `php` inicia na porta 80
- Container `mysql` inicia na porta 3306
- Container `phpmyadmin` inicia na porta 8080
- ✅ Composer instala dependências automaticamente via `entrypoint.sh`
- MySQL executa `assets/data/0-schema.sql` criando a estrutura

#### 3. Popular o Banco de Dados

⚠️ **IMPORTANTE:** O banco é criado vazio (apenas estrutura). Você **precisa** popular com dados.

**Opção A - Via Terminal (Recomendado):**

**Windows PowerShell:**
```powershell
Get-Content assets/seeds/db_populate.sql | docker exec -i mysql mysql -uroot -prootpass application
```

**Linux/Mac:**
```bash
docker exec -i mysql mysql -uroot -prootpass application < assets/seeds/db_populate.sql
```

**Opção B - Via phpMyAdmin:**
1. Acesse http://localhost:8080
2. Login: `root` / Senha: `rootpass`
3. Selecione o banco `application`
4. Vá em "SQL"
5. Copie todo o conteúdo de `assets/seeds/db_populate.sql`
6. Cole e execute

#### 4. Acesse a Aplicação

🎉 **Pronto!** Acesse: http://localhost

### GitHub Codespaces (Alternativa)

**Instalação Automática:**

1. Clique em **Code** → **Codespaces** → **Create codespace**
2. Aguarde inicialização (containers sobem automaticamente)
   - ✅ Composer instala dependências automaticamente via `entrypoint.sh`
   - ✅ Não precisa rodar comandos manuais!
3. **Popular banco** (obrigatório):
   ```bash
   docker exec -i mysql mysql -uroot -prootpass application < assets/seeds/db_populate.sql
   ```
4. Acesse a porta 80 quando abrir automaticamente

**Portas Disponíveis:**
- Porta 80: Aplicação
- Porta 8080: phpMyAdmin
- Porta 3306: MySQL

---

## ⚙️ Configuração

### Credenciais de Acesso

Após popular o banco, você pode fazer login com as seguintes credenciais:

#### Super Admin (Acesso Total)
```
Email/RA: sadmin
Senha: sadmin
```
**Permissões:** Tudo

#### Admin de Atlética
```
Email: admin.atletica@sge.com
Senha: sadmin
Atlética: A.A.A. FURIOSA
```
**Permissões:** Gestão da atlética

#### Usuário Comum (Aluno)
```
Email: aluno@sge.com
Senha: sadmin
```
**Permissões:** Agendamentos, presença, perfil

#### Professor
```
Email: carlos.andrade@prof.sge.com
Senha: sadmin
Curso: Engenharia Civil (Coordenador)
```
**Permissões:** Pode agendar eventos

#### Membro de Atlética
```
Email: membro@sge.com
Senha: sadmin
Atlética: A.A.A. FURIOSA (aprovado)
```

#### Comunidade Externa
```
Email: comunidade@email.com
Senha: sadmin
```

**⚠️ Nota:** Todos os usuários de teste têm a senha `sadmin` (hash bcrypt).

### Configuração de E-mail (Opcional)

Para habilitar envio de e-mails (2FA e recuperação de senha):

#### 1. Gerar Senha de Aplicativo Gmail

1. Ative verificação em 2 etapas no Gmail
2. Acesse: https://myaccount.google.com/apppasswords
3. Gere uma senha para "Outro (SGE UNIFIO)"
4. Copie a senha gerada

#### 2. Configurar Variáveis de Ambiente

Edite `src/Core/EmailService.php`:

```php
// Linha 71-72
$this->mailer->Username = getenv('SMTP_EMAIL') ?: 'seu-email@gmail.com';
$this->mailer->Password = getenv('SMTP_PASSWORD') ?: 'sua-senha-app';

// Linha 79
$fromEmail = 'seu-email@gmail.com';
```

**Ou via Docker Compose:**

Adicione no `docker-compose.yml`:
```yaml
services:
  apache:
    environment:
      - SMTP_EMAIL=seu-email@gmail.com
      - SMTP_PASSWORD=sua-senha-app
```

### Configuração do Banco de Dados

As credenciais padrão estão em:
- `docker-compose.yml` (container MySQL)
- `src/Core/Connection.php` (aplicação)

**Padrão:**
```
Host: db
Database: application
User: appuser (root para admin)
Password: apppass (rootpass para root)
Charset: UTF8MB4
Timezone: America/Sao_Paulo (-03:00)
```

**Para alterar:**

1. **docker-compose.yml**:
```yaml
environment:
  MYSQL_DATABASE: novo_banco
  MYSQL_USER: novo_usuario
  MYSQL_PASSWORD: nova_senha
```

2. **src/Core/Connection.php** (linhas 70-73):
```php
$host = 'db';
$dbname = 'novo_banco';
$username = 'novo_usuario';
$password = 'nova_senha';
```

3. **Recriar containers**:
```bash
docker-compose down -v
docker-compose up -d
```

### Configuração de Portas

Para alterar portas expostas, edite `docker-compose.yml`:

```yaml
services:
  apache:
    ports:
      - '8000:80'  # Aplicação na porta 8000
  
  db:
    ports:
      - '3307:3306'  # MySQL na porta 3307
  
  phpmyadmin:
    ports:
      - '8081:80'  # phpMyAdmin na porta 8081
```

Reinicie os containers:
```bash
docker-compose down
docker-compose up -d
```

### Configuração de Timezone

O timezone está configurado para **America/Sao_Paulo** (GMT-3) em:

1. **PHP** (`public/index.php`, linha 89):
```php
date_default_timezone_set('America/Sao_Paulo');
```

2. **MySQL** (`src/Core/Connection.php`, linha 102):
```php
self::$instance->exec("SET time_zone = '-03:00'");
```

Para alterar, modifique ambos os arquivos.

### Configuração do Apache (.htaccess)

O arquivo `public/.htaccess` redireciona todas as requisições para `index.php`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

**Importante:**
- Arquivos estáticos (CSS, JS, imagens) são servidos diretamente
- Todas as outras requisições passam pelo Front Controller
- Mod_rewrite deve estar habilitado (já está no Docker)

---

## 🗄️ Banco de Dados

### Estrutura do Banco

O SGE utiliza MySQL 9.4 com charset **UTF8MB4** (suporte completo a acentos, emojis e caracteres especiais).

#### Principais Tabelas

| Tabela | Descrição | Registros Chave |
|--------|-----------|-----------------|
| `usuarios` | Dados de usuários do sistema | nome, email, senha, RA, role, tipo_usuario_detalhado |
| `agendamentos` | Solicitações de uso da quadra | titulo, tipo, data, período, status, responsável |
| `atleticas` | Organizações estudantis | nome |
| `cursos` | Cursos acadêmicos | nome, atletica_id, coordenador_id |
| `modalidades` | Esportes disponíveis | nome (Futsal, Vôlei, etc) |
| `presencas` | Confirmações de presença | usuario_id, agendamento_id, data_presenca |
| `inscricoes_modalidade` | Inscrições em esportes | aluno_id, modalidade_id, status, atletica_id |
| `inscricoes_eventos` | Participação em eventos | aluno_id, evento_id, atletica_id |
| `notificacoes` | Sistema de notificações | usuario_id, titulo, mensagem, tipo, lida |
| `solicitacoes_troca_curso` | Solicitações de mudança de curso | aluno_id, curso_atual_id, curso_destino_id, status |

#### Diagrama de Relacionamentos

```
┌──────────────┐         ┌──────────────┐
│  atleticas   │◄────────│    cursos    │
└──────┬───────┘         └──────┬───────┘
       │                        │
       │ 1                      │ N
       │                        │
       │               ┌────────▼────────┐
       └───────────────┤    usuarios     │
            N          └────────┬────────┘
                                │ 1
                                │
                                │ N
                       ┌────────▼──────────┐
                       │  agendamentos     │◄──────┐
                       └────────┬──────────┘       │
                                │ 1                │
                                │                  │ N
                                │ N          ┌─────┴────────┐
                       ┌────────▼────────┐   │  presencas   │
                       │  notificacoes   │   └──────────────┘
                       └─────────────────┘
```

#### Tabela `usuarios`

**Campos Principais:**

```sql
- id: INT (PK, AUTO_INCREMENT)
- nome: VARCHAR(255)
- email: VARCHAR(255) UNIQUE
- senha: VARCHAR(255) (hash bcrypt)
- ra: VARCHAR(20) UNIQUE
- telefone: VARCHAR(20)
- data_nascimento: DATE
- curso_id: INT (FK → cursos)
- atletica_id: INT (FK → atleticas)
- role: ENUM('usuario', 'admin', 'superadmin')
- tipo_usuario_detalhado: ENUM('Aluno', 'Professor', 'Membro das Atleticas', 'Comunidade Externa')
- is_coordenador: TINYINT(1)
- atletica_join_status: ENUM('none', 'pendente', 'aprovado')
- login_code: VARCHAR(6) -- Código 2FA
- login_code_expires: DATETIME
- reset_token: VARCHAR(255) -- Token recuperação
- reset_token_expires: DATETIME
```

#### Tabela `agendamentos`

**Campos Principais:**

```sql
- id: INT (PK, AUTO_INCREMENT)
- usuario_id: INT (FK → usuarios)
- titulo: VARCHAR(255)
- tipo_agendamento: ENUM('esportivo', 'nao_esportivo')
- esporte_tipo: VARCHAR(100)
- data_agendamento: DATE
- periodo: ENUM('primeiro', 'segundo')
- descricao: TEXT
- status: ENUM('aprovado', 'pendente', 'rejeitado', 'cancelado', 'finalizado')
- motivo_rejeicao: TEXT
- data_solicitacao: TIMESTAMP
- subtipo_evento: VARCHAR(100) -- treino/campeonato/palestra/workshop/formatura
- responsavel_evento: VARCHAR(255)
- possui_materiais: TINYINT(1)
- materiais_necessarios: TEXT
- lista_participantes: TEXT
- arbitro_partida: VARCHAR(255)
- estimativa_participantes: INT
- evento_aberto_publico: TINYINT(1)
- infraestrutura_adicional: TEXT
- observacoes: TEXT
- foi_editado: TINYINT(1)
- data_edicao: DATETIME
- observacoes_admin: TEXT
- alterado_por_admin: TINYINT(1)
- cancelado_por_admin: TINYINT(1)
```

#### Tabela `notificacoes`

**Campos Principais:**

```sql
- id: INT (PK, AUTO_INCREMENT)
- usuario_id: INT (FK → usuarios)
- titulo: VARCHAR(255)
- mensagem: TEXT
- tipo: ENUM(
    'agendamento_aprovado',
    'agendamento_rejeitado',
    'agendamento_cancelado',
    'agendamento_cancelado_admin',
    'agendamento_editado',
    'agendamento_alterado',
    'presenca_confirmada',
    'lembrete_evento',
    'info',
    'aviso',
    'sistema'
  )
- agendamento_id: INT (FK → agendamentos, nullable)
- lida: TINYINT(1) DEFAULT 0
- data_criacao: TIMESTAMP
```

#### Tabela `solicitacoes_troca_curso`

**Campos Principais:**

```sql
- id: INT (PK, AUTO_INCREMENT)
- aluno_id: INT (FK → usuarios)
- curso_atual_id: INT (FK → cursos)
- curso_destino_id: INT (FK → cursos)
- justificativa: TEXT
- status: ENUM('pendente', 'aprovada', 'rejeitada')
- data_solicitacao: TIMESTAMP
- data_decisao: TIMESTAMP NULL
```

#### Tabela `presencas`

**Campos Principais:**

```sql
- id: INT (PK, AUTO_INCREMENT)
- usuario_id: INT (FK → usuarios)
- agendamento_id: INT (FK → agendamentos)
- data_presenca: TIMESTAMP
- UNIQUE KEY (usuario_id, agendamento_id) -- Um usuário só pode marcar presença uma vez
```

### Dados de Exemplo

O arquivo `assets/seeds/db_populate.sql` contém:

#### Atléticas (10):
- A.A.A. FURIOSA (Engenharia Civil)
- A.A.A. PREDADORA (Direito)
- A.A.A. SANGUINÁRIA (Medicina)
- A.A.A. INSANA (Psicologia)
- A.A.A. MAGNA (Administração)
- A.A.A. ALFA (Eng. Software / Ciência da Computação)
- A.A.A. IMPÉRIO (Publicidade e Propaganda)
- A.A.A. VENENOSA (Farmácia)
- A.A.A. LETAL (Ciências Biológicas)
- A.A.A. ATÔMICA

#### Modalidades (15+):
**Esportes Tradicionais:**
- Futsal, Voleibol, Basquetebol, Handebol
- Natação, Atletismo, Vôlei de Praia, Queimada

**Artes Marciais:**
- Judô, Karatê

**Esportes de Raquete:**
- Tênis de Mesa, Tênis de Campo

**E-Sports:**
- League of Legends, CS:GO

**Outros:**
- Xadrez

#### Cursos (20+):
- Engenharias (Civil, Software, Produção, Elétrica, Mecânica)
- Saúde (Medicina, Enfermagem, Fisioterapia, Farmácia, Odontologia)
- Humanas (Direito, Psicologia, Pedagogia)
- Exatas (Ciência da Computação, Matemática, Física)
- Gestão (Administração, Contabilidade, Marketing)
- Comunicação (Jornalismo, Publicidade)

### Comandos Úteis do Banco

```bash
# Backup do banco
docker exec mysql mysqldump -uroot -prootpass application > backup.sql

# Restaurar backup
docker exec -i mysql mysql -uroot -prootpass application < backup.sql

# Acessar MySQL CLI
docker exec -it mysql mysql -uroot -prootpass application

# Ver tabelas
docker exec mysql mysql -uroot -prootpass application -e "SHOW TABLES"

# Ver estrutura de uma tabela
docker exec mysql mysql -uroot -prootpass application -e "DESCRIBE usuarios"

# Executar query
docker exec mysql mysql -uroot -prootpass application -e "SELECT COUNT(*) FROM usuarios"
```

---

## 📁 Estrutura do Projeto

```
sge/
├── 📂 .devcontainer/             # Configuração GitHub Codespaces / VS Code Dev Containers
│   ├── devcontainer.json        # Config: ports, postCreateCommand, workspace
│   └── Dockerfile               # Imagem customizada para dev
│
├── 📂 assets/                    # Recursos do banco de dados
│   ├── data/
│   │   └── 0-schema.sql         # Estrutura do banco (auto-executado)
│   └── seeds/
│       ├── db_populate.sql      # Dados de exemplo (execução manual)
│       └── README.md
│
├── 📂 documentation/             # Documentação UML
│   ├── diagrama-de-caso-de-uso.jpg
│   ├── diagrama-de-classes.jpg
│   ├── diagrama-de-sequencia.jpg
│   └── README.md
│
├── 📂 public/                    # DocumentRoot (ponto de entrada web)
│   ├── 📄 index.php             # ⭐ Front Controller
│   ├── 📄 .htaccess             # Regras de reescrita Apache
│   ├── 📂 css/                  # Estilos CSS
│   │   ├── default.css
│   │   ├── header.css
│   │   ├── auth.css
│   │   ├── dashboard.css
│   │   ├── calendar.css
│   │   ├── agenda.css
│   │   ├── event-popup.css
│   │   ├── notifications.css
│   │   └── usuario.css
│   ├── 📂 js/                   # Scripts JavaScript
│   │   ├── calendar.js
│   │   ├── event-form.js
│   │   ├── event-popup.js
│   │   ├── header.js
│   │   └── notifications.js
│   ├── 📂 img/                  # Imagens e logos
│       ├── logo-unifio-azul.webp
│       ├── logo-unifio-branco.webp
│       └── ...
│   └── 📂 doc/                  # Documentos públicos (Termos e Políticas)
│       ├── regulamento.pdf
│       ├── politica-privacidade.pdf
│       └── termo-usuario.pdf
│
├── 📂 src/                       # Código da aplicação
│   ├── 📄 routes.php            # Definição de todas as rotas
│   │
│   ├── 📂 Controller/           # Camada de controle (MVC)
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   ├── UsuarioController.php
│   │   ├── AgendamentoController.php
│   │   ├── AgendaController.php
│   │   ├── AdminAtleticaController.php
│   │   ├── SuperAdminController.php
│   │   └── NotificationController.php
│   │
│   ├── 📂 Repository/           # Camada de dados (Data Access Layer)
│   │   ├── UsuarioRepository.php
│   │   ├── AgendamentoRepository.php
│   │   ├── AtleticaRepository.php
│   │   ├── CursoRepository.php
│   │   ├── ModalidadeRepository.php
│   │   ├── NotificationRepository.php
│   │   ├── RelatorioRepository.php
│   │   └── AdminAtleticaRepository.php
│   │
│   └── 📂 Core/                 # Classes principais do framework
│       ├── Router.php           # Sistema de roteamento
│       ├── Auth.php             # Autenticação e autorização
│       ├── Connection.php       # Singleton PDO
│       ├── NotificationService.php  # Lógica de notificações
│       ├── EmailService.php     # Envio de e-mails
│       └── helpers.php          # Funções globais
│
├── 📂 views/                     # Templates (Views do MVC)
│   ├── 📂 _partials/            # Componentes reutilizáveis
│   │   ├── header.php
│   │   ├── footer.php
│   │   └── calendar.php
│   ├── 📂 auth/                 # Autenticação
│   │   ├── login.view.php
│   │   ├── login-verify.view.php
│   │   ├── registro.view.php
│   │   ├── esqueci-senha.view.php
│   │   └── redefinir-senha.view.php
│   ├── 📂 usuario/              # Painel do usuário
│   │   ├── dashboard.view.php
│   │   └── perfil.view.php
│   ├── 📂 pages/                # Páginas gerais
│   │   ├── agenda.view.php
│   │   ├── agendar-evento.view.php
│   │   ├── editar-evento.view.php
│   │   └── meus-agendamentos.view.php
│   ├── 📂 admin_atletica/       # Painel admin atlética
│   │   ├── dashboard.view.php
│   │   ├── gerenciar-membros.view.php
│   │   ├── gerenciar-membros-atletica.view.php
│   │   ├── gerenciar-inscricoes.view.php
│   │   └── gerenciar-eventos.view.php
│   └── 📂 super_admin/          # Painel super admin
│       ├── dashboard.view.php
│       ├── gerenciar-usuarios.view.php
│       ├── editar-usuario.view.php
│       ├── gerenciar-agendamentos.view.php
│       ├── gerenciar-estrutura.view.php
│       ├── editar-curso.view.php
│       ├── editar-atletica.view.php
│       ├── gerenciar-modalidades.view.php
│       ├── editar-modalidade.view.php
│       ├── gerenciar-admins.view.php
│       ├── relatorios.view.php
│       ├── relatorio-print.view.php
│       └── enviar-notificacao-global.view.php
│
├── 📂 vendor/                    # Dependências do Composer
│   └── autoload.php
│
├── 📂 scripts/                   # Scripts utilitários (criar se necessário)
│   └── daily_notifications.php  # Cron de lembretes
│
├── 📄 composer.json              # Configuração do Composer
├── 📄 composer.lock              # Lock de versões
├── 📄 docker-compose.yml         # Orquestração Docker
├── 📄 Dockerfile                 # Imagem PHP + Apache
├── 📄 README.md                  # Este arquivo
└── 📄 CONTRIBUTING.md            # Guia de contribuição

```

### Fluxo de uma Requisição

```
1. Usuário acessa: http://localhost/perfil

2. Apache (.htaccess) redireciona para:
   public/index.php?url=perfil

3. Front Controller (index.php):
   - Inicia sessão
   - Carrega autoloader
   - Carrega rotas (routes.php)
   - Chama Router::dispatch('/perfil', 'GET')

4. Router:
   - Busca rota GET '/perfil'
   - Encontra: 'UsuarioController@perfil'
   - Instancia UsuarioController
   - Chama método perfil()

5. Controller (UsuarioController@perfil):
   - Auth::protect() (verifica login)
   - Busca dados via Repository
   - view('usuario/perfil', $dados)

6. Helper view():
   - Inclui header.php
   - Inclui usuario/perfil.view.php
   - Inclui footer.php

7. Resposta HTML enviada ao navegador
```

---

## 🔒 Sistema de Permissões

### Níveis de Acesso

| Funcionalidade | Usuário | Admin Atlética | Super Admin |
|----------------|---------|----------------|-------------|
| Ver agenda pública | ✅ | ✅ | ✅ |
| Marcar presença | ✅ | ✅ | ✅ |
| Editar próprio perfil | ✅ | ✅ | ✅ |
| Solicitar entrada em atlética | ✅ | ❌ | ❌ |
| Inscrever-se em modalidades | ✅ | ✅ | ✅ |
| **Agendar eventos** | ⚠️¹ | ✅² | ✅ |
| Editar próprios agendamentos | ✅ | ✅ | ✅ |
| **Gerenciar membros atlética** | ❌ | ✅ | ✅ |
| **Aprovar inscrições modalidades** | ❌ | ✅³ | ✅ |
| **Gerenciar eventos atlética** | ❌ | ✅³ | ✅ |
| **Aprovar/Rejeitar agendamentos** | ❌ | ❌ | ✅ |
| **Gerenciar todos os usuários** | ❌ | ❌ | ✅ |
| **Gerenciar estrutura (cursos/atléticas)** | ❌ | ❌ | ✅ |
| **Gerenciar modalidades** | ❌ | ❌ | ✅ |
| **Promover/Rebaixar admins** | ❌ | ❌ | ✅ |
| **Gerar relatórios** | ❌ | ❌ | ✅ |
| **Enviar notificação global** | ❌ | ❌ | ✅ |

**Notas:**
- ⚠️¹ Usuários comuns só podem agendar se forem **Professores**
- ² Admin de Atlética só pode agendar se for também **"Membro das Atléticas"**
- ³ Apenas para sua própria atlética

### Middlewares de Proteção

**Uso nos Controllers:**

```php
// Proteger rota (requer login)
Auth::protect();

// Proteger rota de admin de atlética
Auth::protectAdmin();

// Proteger rota de super admin
Auth::protectSuperAdmin();

// Verificar role manualmente
if (Auth::role() === 'superadmin') {
    // Código restrito a super admins
}

// Obter dados do usuário logado
$userId = Auth::id();
$userName = Auth::name();
$userRole = Auth::role();
$atleticaId = Auth::get('atletica_id');
```

### Controle de Acesso por Role

**Redirecionamento Automático (HomeController):**

```php
public function index() {
    if (!Auth::check()) {
        redirect('/login');
    }
    
    switch (Auth::role()) {
        case 'superadmin':
            redirect('/superadmin/dashboard');
        case 'admin':
            redirect('/admin/atletica/dashboard');
        default:
            redirect('/dashboard');
    }
}
```

---

## 🛠️ Comandos Úteis do Docker

### Gerenciamento de Containers

```bash
# Iniciar todos os containers
docker-compose up -d

# Parar containers (mantém volumes)
docker-compose down

# Parar e remover volumes (apaga banco de dados)
docker-compose down -v

# Reiniciar containers
docker-compose restart

# Reconstruir imagens
docker-compose up -d --build

# Ver status dos containers
docker ps

# Ver logs
docker logs php -f
docker logs mysql -f

# Parar container específico
docker stop php
docker stop mysql
```

### Acesso aos Containers

```bash
# Acessar terminal do container PHP
docker exec -it php bash

# Verificar versão do PHP
docker exec php php -v

# Verificar extensões instaladas
docker exec php php -m

# Acessar terminal do MySQL
docker exec -it mysql bash

# MySQL CLI
docker exec -it mysql mysql -uroot -prootpass application
```

### Composer

```bash
# Instalar dependências
docker exec php composer install

# Atualizar dependências
docker exec php composer update

# Adicionar nova dependência
docker exec php composer require nome/pacote

# Atualizar autoload
docker exec php composer dump-autoload
```

### Permissões (Linux/Mac)

Se enfrentar problemas de permissão:

```bash
# Ajustar permissões
sudo chown -R $USER:$USER .
chmod -R 755 .

# Permissão de escrita em diretórios específicos
chmod -R 777 public/uploads  # Se criar pasta de uploads
```

### Limpeza

```bash
# Remover containers parados
docker container prune

# Remover imagens não usadas
docker image prune -a

# Limpar tudo (cuidado!)
docker system prune -a --volumes
```

---

## 💡 Uso e Exemplos

### Fluxo de Agendamento

#### 1. Professor Solicita Agendamento

```
1. Login como professor
2. Ir em "Agendar Evento"
3. Preencher formulário:
   - Título: "Treino de Futsal"
   - Tipo: Esportivo
   - Modalidade: Futsal
   - Data: (mínimo 4 dias no futuro)
   - Período: Primeiro ou Segundo
   - Responsável: Nome, telefone, email
   - Participantes: Lista de RAs
4. Submeter
5. Status: PENDENTE
```

#### 2. Super Admin Aprova

```
1. Login como super admin
2. Ir em "Gerenciar Agendamentos"
3. Ver lista de pendentes
4. Clicar em "Detalhes"
5. Analisar informações
6. Clicar em "Aprovar"
7. Sistema envia notificação automática
```

#### 3. Aluno Marca Presença

```
1. Login como aluno
2. Ir em "Agenda"
3. Ver evento aprovado no calendário
4. Clicar em "Marcar Presença"
5. Receber notificação de confirmação
6. 1 dia antes: Receber lembrete automático
```

### Criar Nova Rota

**1. Definir rota em `src/routes.php`:**

```php
Router::get('/minha-rota', 'MeuController@minhaAction');
```

**2. Criar Controller:**

```php
<?php
namespace Application\Controller;

class MeuController extends BaseController
{
    public function minhaAction()
    {
        // Proteger rota (opcional)
        Auth::protect();
        
        // Buscar dados
        $dados = $this->repository('MeuRepository')->findAll();
        
        // Renderizar view
        view('minha-view', ['dados' => $dados]);
    }
}
```

**3. Criar Repository (se necessário):**

```php
<?php
namespace Application\Repository;

use Application\Core\Connection;

class MeuRepository
{
    private $db;
    
    public function __construct()
    {
        $this->db = Connection::getInstance();
    }
    
    public function findAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM minha_tabela");
        return $stmt->fetchAll();
    }
}
```

**4. Criar View:**

```php
<!-- views/minha-view.view.php -->
<div class="container">
    <h1>Minha Página</h1>
    <?php foreach ($dados as $item): ?>
        <p><?= htmlspecialchars($item['nome']) ?></p>
    <?php endforeach; ?>
</div>
```

### Enviar Notificação

```php
use Application\Core\NotificationService;

$notificationService = new NotificationService();

// Notificação individual
$notificationService->notifyAgendamentoAprovado($agendamentoId);

// Notificação global (Super Admin)
$notificationRepo = $this->repository('NotificationRepository');
$notificationRepo->createGlobalNotification(
    'Título',
    'Mensagem',
    'sistema' // Tipo: info, aviso, sistema
);
```

---

## 🐛 Solução de Problemas

### Porta 80 Ocupada

**Windows:**
```cmd
netstat -ano | findstr :80
taskkill /PID <PID> /F
```

**Linux/Mac:**
```bash
sudo lsof -i :80
sudo kill -9 <PID>
```

Ou altere a porta em `docker-compose.yml`:
```yaml
ports:
  - '8000:80'  # Usar porta 8000
```

### Erro de Conexão com Banco

**1. Verificar se container está rodando:**
```bash
docker ps
```

**2. Ver logs do MySQL:**
```bash
docker logs mysql
```

**3. Testar conexão:**
```bash
docker exec mysql mysql -uroot -prootpass -e "SELECT 1"
```

**4. Recriar banco:**
```bash
docker-compose down -v
docker-compose up -d
# Aguardar inicialização
docker exec -i mysql mysql -uroot -prootpass application < assets/seeds/db_populate.sql
```

### Composer Não Instala Dependências

```bash
# Instalar manualmente
docker exec php composer install

# Se persistir, limpar cache
docker exec php composer clear-cache
docker exec php composer install
```

### Erros de Sessão

```bash
# Limpar cookies do navegador
# Ou usar modo anônimo

# Verificar sessão no PHP
docker exec php php -i | grep session
```

### Página em Branco (500 Error)

**1. Ver logs do Apache:**
```bash
docker logs php
```

**2. Ativar exibição de erros (desenvolvimento):**

Adicione em `public/index.php` (início):
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

**3. Verificar logs do PHP:**
```bash
docker exec php tail -f /var/log/apache2/error.log
```

### UTF-8 / Acentos Quebrados

Verifique charset em:

**1. HTML (views/_partials/header.php):**
```html
<meta charset="UTF-8">
```

**2. MySQL (src/Core/Connection.php):**
```php
self::$instance->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
```

**3. Headers PHP (views/_partials/header.php):**
```php
header('Content-Type: text/html; charset=UTF-8');
```

### Performance Lenta

**1. Verificar recursos do Docker:**
- Docker Desktop → Settings → Resources
- Aumentar CPU e RAM alocados

**2. Otimizar queries:**
```sql
-- Adicionar índices
CREATE INDEX idx_usuario_id ON agendamentos(usuario_id);
CREATE INDEX idx_data ON agendamentos(data_agendamento);
```

**3. Limpar containers não usados:**
```bash
docker system prune -a
```

---

## 👥 Contribuição

### Como Contribuir

1. **Fork** o projeto
2. Crie uma **branch** para sua feature:
   ```bash
   git checkout -b feature/minha-feature
   ```
3. **Commit** suas mudanças:
   ```bash
   git commit -m "feat: adiciona minha feature"
   ```
4. **Push** para a branch:
   ```bash
   git push origin feature/minha-feature
   ```
5. Abra um **Pull Request**

### Padrões de Commit

Use [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: adiciona nova funcionalidade
fix: corrige bug
docs: atualiza documentação
style: formatação de código
refactor: refatoração sem mudar funcionalidade
test: adiciona testes
chore: tarefas de manutenção
```

### Padrões de Código

- **PSR-4**: Autoloading
- **PSR-12**: Estilo de código
- **Comentários**: Documente classes e métodos complexos
- **Nomes descritivos**: Variáveis e funções claras
- **DRY**: Don't Repeat Yourself
- **SOLID**: Princípios de design

### Checklist antes de PR

- [ ] Código funciona localmente
- [ ] Sem erros de linter
- [ ] Comentários e documentação atualizados
- [ ] Testado em diferentes navegadores
- [ ] Commit messages descritivas
- [ ] Sem credenciais ou dados sensíveis no código

---

## 📊 Estatísticas do Projeto

- **Linhas de Código**: ~15.000+ linhas
- **Arquivos PHP**: 50+ arquivos
- **Tabelas do Banco**: 9 tabelas
- **Controllers**: 9 controllers
- **Repositories**: 8 repositories
- **Views**: 40+ views
- **JavaScript**: 5 arquivos modulares
- **CSS**: 9 folhas de estilo

---

## 📝 Licença

Este projeto foi desenvolvido para uso acadêmico no **Centro Universitário UNIFIO**.

**Uso Educacional:** Permitido para fins de estudo e aprendizado.  
**Uso Comercial:** Não autorizado sem permissão.

---

## 🙏 Agradecimentos

Desenvolvido com ❤️ para o **Centro Universitário UNIFIO** com o objetivo de modernizar e centralizar a gestão de eventos esportivos e acadêmicos.

**Equipe de Desenvolvimento:**
- Arquitetura MVC moderna
- Docker para ambiente consistente
- PHPMailer para e-mails
- MySQL para persistência

**Stack Principal:**
- PHP 8.2
- MySQL 9.4
- Docker + Docker Compose
- JavaScript Vanilla
- CSS3

**Padrões de Projeto:**
- MVC (Model-View-Controller)
- Repository Pattern
- Singleton
- Front Controller
- PSR-4 Autoloading

---

## 📞 Suporte

Para dúvidas ou problemas:

1. Verifique a seção de **Solução de Problemas**
2. Consulte os **logs dos containers**
3. Abra uma **issue** no repositório

---

## 🔗 Links Úteis

- [PHP 8.2 Documentation](https://www.php.net/docs.php)
- [MySQL 9.4 Reference](https://dev.mysql.com/doc/)
- [Docker Documentation](https://docs.docker.com/)
- [PHPMailer GitHub](https://github.com/PHPMailer/PHPMailer)
- [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
- [Conventional Commits](https://www.conventionalcommits.org/)

---

<div align="center">

**SGE - Sistema de Gerenciamento de Eventos UNIFIO**

Versão 1.0 | Outubro 2025

[![Made with PHP](https://img.shields.io/badge/Made%20with-PHP-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-9.4-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker&logoColor=white)](https://www.docker.com/)

</div>

