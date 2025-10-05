# SGE - Sistema de Gerenciamento de Eventos (UNIFIO)

Sistema web completo para gerenciamento de agendamentos de quadras esportivas, administração de atléticas, cursos e usuários da UNIFIO. O sistema oferece funcionalidades abrangentes desde o agendamento de eventos até relatórios detalhados, com diferentes níveis de acesso e um calendário interativo.

Desenvolvido com **arquitetura MVC moderna** e completamente **containerizado com Docker**, garantindo um ambiente de desenvolvimento consistente, seguro, escalável e de fácil manutenção.

---

## 🎯 Funcionalidades Principais

### 👤 Sistema de Autenticação e Autorização
- **Login com Verificação em 2 Etapas**: Código de 6 dígitos enviado por email
- **Recuperação de Senha**: Sistema completo com token temporário
- **Cadastro de Usuários**: Com validação de RA, curso e tipo de usuário
- **3 Níveis de Acesso**:
  - **Usuário Comum**: Alunos, Professores, Comunidade Externa
  - **Admin de Atlética**: Gerencia membros e eventos da atlética
  - **Super Admin**: Controle total do sistema

### 📅 Gestão de Agendamentos
- **Calendário Interativo**: Navegação mensal com AJAX
- **2 Períodos por Dia**:
  - Primeiro período: 19:15 - 20:55
  - Segundo período: 21:10 - 22:50
- **Tipos de Eventos**:
  - Esportivos (treinos, campeonatos)
  - Não Esportivos (palestras, workshops, formaturas)
- **Validações Inteligentes**:
  - Antecedência mínima de 4 dias (exceto campeonatos)
  - Verificação de conflitos de horário
  - Restrição de datas passadas
- **Workflow de Aprovação**: Pendente → Aprovado/Rejeitado
- **Formulário Completo**:
  - Informações de responsável
  - Materiais necessários
  - Lista de participantes
  - Árbitro (para eventos esportivos)
  - Infraestrutura adicional

### 📊 Painel do Super Admin
- **Gerenciamento de Agendamentos**: Aprovar/Rejeitar solicitações
- **Gestão de Usuários**: CRUD completo com edição de perfis
- **Estrutura Acadêmica**: Gerenciar Cursos e Atléticas
- **Modalidades Esportivas**: Cadastro e edição
- **Gestão de Admins**: Promover/Rebaixar usuários
- **Relatórios Avançados**:
  - Agendamentos por período
  - Estatísticas de uso
  - Participação de atléticas
  - Eventos mais populares
  - Versão para impressão
- **Notificações Globais**: Enviar avisos para todos os usuários

### 🏃 Painel do Admin de Atlética
- **Dashboard Personalizado**: Estatísticas da atlética
- **Gestão de Membros**: Aprovar/Recusar solicitações de entrada
- **Gestão de Membros da Atlética**: Adicionar/Remover membros
- **Inscrições em Modalidades**: Aprovar atletas para competições
- **Inscrições em Eventos**: Gerenciar participação em eventos
- **Visualização de Eventos**: Calendário filtrado da atlética

### 👥 Painel do Usuário
- **Dashboard**: Visão geral de eventos e notificações
- **Perfil**: Editar dados pessoais, foto, senha
- **Solicitar Entrada em Atlética**: Sistema de requisição
- **Inscrições em Modalidades**: Escolher esportes de interesse
- **Meus Agendamentos**: Visualizar, editar e cancelar
- **Agenda Pública**: Ver todos os eventos aprovados
- **Marcar Presença**: Confirmar participação em eventos
- **Agendar Eventos**: Solicitar uso da quadra (Professores e Admins)

### 🔔 Sistema de Notificações
- **Notificações em Tempo Real**: Contador de não lidas no header
- **Tipos de Notificações**:
  - Agendamento aprovado/rejeitado/cancelado
  - Presença confirmada
  - Lembretes de eventos (1 dia antes)
  - Informações do sistema
  - Avisos importantes
- **Interface AJAX**: Atualização sem recarregar a página
- **Marcar como Lida**: Individual ou todas de uma vez
- **Limpeza Automática**: Notificações antigas removidas após 30 dias
- **Script Diário**: `daily_notifications.php` para lembretes automáticos

### 🎯 Popup de Detalhes do Evento
- **Visualização Completa de Eventos**: Modal dinâmico com todas as informações
- **Clique em Qualquer Evento**: Abre popup instantâneo via AJAX
- **Informações Detalhadas**:
  - Título, data e horário do evento
  - Tipo e subtipo (esportivo/não esportivo)
  - Status com badges coloridos (aprovado, pendente, rejeitado, cancelado)
  - Responsável pelo evento
  - Descrição e observações
  - Detalhes específicos:
    - **Eventos Esportivos**: Modalidade, árbitro, atlética adversária, materiais
    - **Eventos Não Esportivos**: Público-alvo, aberto ao público, infraestrutura
  - Lista de participantes (RAs)
  - Motivo de rejeição (quando aplicável)
- **Lista de Presenças Confirmadas** (apenas para Admins e Super Admins):
  - Contador de pessoas confirmadas
  - Nomes dos participantes
  - Informações de contato
- **Interface Moderna**:
  - Design responsivo
  - Animações suaves
  - Fechamento ao clicar fora ou no X
  - CSS dedicado em `public/css/event-popup.css`
- **Implementação Técnica**:
  - Classe JavaScript `EventPopup` em `public/js/event-popup.js`
  - Endpoint AJAX: `GET /agendamento/detalhes?id={eventId}`
  - Controller: `AgendamentoController@getEventDetails`
  - Integrado com sistema de permissões

### ✅ Sistema de Confirmação de Presença
- **Marcar Presença em Eventos**: Usuários podem confirmar participação em eventos aprovados
- **Funcionalidades**:
  - Botão "Marcar Presença" em cada evento da agenda
  - Toggle instantâneo (marcar/desmarcar)
  - Feedback visual imediato (botão muda de cor)
  - Contador dinâmico de pessoas confirmadas
  - Validação de eventos aprovados
- **Notificações Automáticas**:
  - Confirmação imediata ao marcar presença
  - Lembrete enviado 1 dia antes do evento (via script diário)
  - Notificação de cancelamento (se evento for cancelado)
- **Armazenamento**:
  - Tabela `agendamento_presencas` no banco de dados
  - Chave única: (usuario_id, agendamento_id)
  - Timestamp de confirmação
- **Implementação Técnica**:
  - Endpoint AJAX: `POST /agenda/presenca`
  - Controller: `AgendaController@handlePresenca`
  - JavaScript: `public/js/calendar.js` e `public/js/event-popup.js`
  - Método Repository: `AgendamentoRepository->togglePresenca()`
- **Visualização de Presenças**:
  - Admins e Super Admins visualizam lista completa no popup
  - Contador público para todos os usuários
  - Relatórios de participação disponíveis

### 📋 Sistema de Controle de Agendamentos
- **Limite de Agendamentos por Esporte**: Cada usuário pode agendar apenas 1 evento por tipo de esporte por semana
- **Validação Automática**: Sistema verifica se já existe agendamento do mesmo esporte na mesma semana
- **Feedback Imediato**: Mensagem clara informando o limite quando atingido
- **Controle por Tipo de Esporte**: Limite aplicado individualmente para cada modalidade (Futsal, Vôlei, Basquete, etc.)
- **Aplicado a Eventos Esportivos**: Validação apenas para agendamentos do tipo "esportivo"

### 👥 Meus Agendamentos
- **Visualização Completa**: Lista todos os agendamentos do usuário
- **Status em Tempo Real**: Acompanhe pendentes, aprovados, rejeitados e cancelados
- **Edição Flexível**: Editar agendamentos pendentes ou aprovados (antes da data)
- **Cancelamento de Eventos**: Cancelar eventos pendentes ou aprovados
- **Super Admin - Visão Global**: Super Admin pode visualizar e editar TODOS os agendamentos do sistema
- **Detalhes Completos**: Clique em qualquer evento para ver informações detalhadas
- **Atualização Automática**: Eventos passados marcados como "finalizado" automaticamente

---

## 🏗️ Arquitetura e Tecnologias

### Stack Tecnológica

#### Backend
- **PHP 8.2**: Linguagem principal com recursos modernos
- **Extensões PHP**: PDO, PDO_MySQL, Intl (formatação de datas)
- **Composer**: Gerenciador de dependências com autoloading PSR-4
- **MySQL 9.4**: Banco de dados relacional

#### Frontend
- **HTML5 + CSS3**: Layouts responsivos e modernos
- **JavaScript Vanilla**: Interações dinâmicas sem frameworks
- **AJAX**: Requisições assíncronas para calendário e notificações
- **CSS Modular**:
  - `auth.css` - Estilos de autenticação
  - `calendar.css` - Calendário interativo
  - `dashboard.css` - Painéis administrativos
  - `default.css` - Estilos globais
  - `header.css` - Navegação e header
  - `notifications.css` - Sistema de notificações

#### DevOps e Infraestrutura
- **Docker + Docker Compose**: Ambiente completamente containerizado
  - **Container Web (sge-php)**: PHP 8.2 + Apache com mod_rewrite
  - **Container DB (sge-db)**: MySQL 9.4 com timezone America/Sao_Paulo
  - **Container phpMyAdmin**: Administração visual do banco
- **Volumes Docker**: Persistência de dados e código
- **Variáveis de Ambiente**: Configuração via `.env`

### Arquitetura MVC

```
┌─────────────────────────────────────────────────────────┐
│                    FRONT CONTROLLER                     │
│                   (public/index.php)                    │
│           Todas as requisições passam aqui             │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│                      ROUTER                             │
│                  (src/Core/Router.php)                  │
│         Mapeia URLs para Controllers/Actions            │
└──────────────────┬──────────────────────────────────────┘
                   │
                   ▼
┌─────────────────────────────────────────────────────────┐
│                   CONTROLLERS                           │
│              (src/Controller/*.php)                     │
│       - AuthController (login, registro)                │
│       - UsuarioController (dashboard, perfil)           │
│       - AgendamentoController (criar, editar)           │
│       - AdminAtleticaController (gestão atlética)       │
│       - SuperAdminController (admin completo)           │
│       - NotificationController (API de notificações)    │
└──────────────────┬──────────────────────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
┌──────────────┐      ┌──────────────┐
│  REPOSITORIES│      │    VIEWS     │
│ (Data Layer) │      │  (Templates) │
│              │      │              │
│ - Isolamento │      │ - Partials   │
│ - PDO        │      │ - Layouts    │
│ - Queries    │      │ - CSS/JS     │
└──────┬───────┘      └──────────────┘
       │
       ▼
┌──────────────┐
│   DATABASE   │
│  MySQL 9.4   │
└──────────────┘
```

#### Camadas da Aplicação

**1. Controllers (src/Controller/)**
- `BaseController.php`: Classe base com métodos reutilizáveis
- `AuthController.php`: Autenticação, registro, recuperação de senha
- `HomeController.php`: Redirecionamento baseado em role
- `UsuarioController.php`: Dashboard e perfil do usuário
- `AgendamentoController.php`: CRUD de agendamentos
- `AgendaController.php`: Visualização pública de eventos
- `AdminAtleticaController.php`: Painel administrativo da atlética
- `SuperAdminController.php`: Painel do super administrador
- `NotificationController.php`: API REST para notificações

**2. Repositories (src/Repository/)**
- `UsuarioRepository.php`: Gestão de usuários
- `AgendamentoRepository.php`: Gestão de agendamentos
- `AtleticaRepository.php`: Gestão de atléticas
- `CursoRepository.php`: Gestão de cursos
- `ModalidadeRepository.php`: Gestão de modalidades esportivas
- `NotificationRepository.php`: Sistema de notificações
- `RelatorioRepository.php`: Geração de relatórios
- `AdminAtleticaRepository.php`: Funcionalidades específicas de admin

**3. Core (src/Core/)**
- `Connection.php`: Singleton PDO com MySQL
- `Router.php`: Sistema de roteamento RESTful
- `Auth.php`: Autenticação e autorização (middleware)
- `NotificationService.php`: Lógica de negócio de notificações
- `helpers.php`: Funções globais (view, redirect)

**4. Views (views/)**
- `_partials/`: Componentes reutilizáveis (header, footer, calendar)
- `auth/`: Telas de login, registro, recuperação
- `usuario/`: Dashboard e perfil do usuário
- `pages/`: Agenda, agendamentos, edição
- `admin_atletica/`: Painel do admin da atlética
- `super_admin/`: Painel do super administrador

---

## 📁 Estrutura Completa do Projeto

```
sge/
├── 🐳 Docker Configuration
│   ├── docker-compose.yml        # Orquestração de containers
│   ├── Dockerfile                # Imagem PHP 8.2 + Apache
│   └── .env                      # Variáveis de ambiente
│
├── 📦 Dependencies
│   ├── composer.json             # Configuração PSR-4
│   ├── composer.lock             # Lock de versões
│   └── vendor/                   # Dependências do Composer
│
├── 🗄️ Database
│   └── assets/
│       ├── data/
│       │   └── 0-schema.sql      # Estrutura do banco (auto-executado)
│       └── seeds/
│           └── db_populate.sql   # Dados de exemplo (manual)
│
├── 🌐 Public (DocumentRoot)
│   └── public/
│       ├── index.php             # 🎯 Front Controller
│       ├── .htaccess             # Rewrite rules
│       ├── css/                  # Estilos CSS
│       ├── js/                   # Scripts JavaScript
│       └── img/                  # Imagens e logos
│
├── 💻 Application Code
│   └── src/
│       ├── routes.php            # Definição de rotas
│       ├── Controller/           # Camada de controle
│       ├── Repository/           # Camada de dados
│       └── Core/                 # Classes principais
│
├── 🎨 Views
│   └── views/
│       ├── _partials/            # Componentes reutilizáveis
│       ├── auth/                 # Autenticação
│       ├── usuario/              # Painel usuário
│       ├── pages/                # Páginas gerais
│       ├── admin_atletica/       # Painel do admin da atlética
│       └── super_admin/          # Painel do super administrador
│
├── ⚙️ Scripts
│   └── scripts/
│       └── daily_notifications.php  # Cron de lembretes
│
└── 📖 Documentation
    └── README.md                 # Este arquivo
```

---

## 🚀 Instalação e Configuração

### 🎉 GitHub Codespaces (Recomendado)

A forma mais rápida de começar! Tudo é configurado automaticamente:

1. **Abra no Codespaces**
   - Clique no botão verde "Code" no GitHub
   - Selecione "Codespaces" → "Create codespace on main"

2. **Aguarde a Inicialização** (1-2 minutos)
   - O ambiente será criado automaticamente
   - Docker Compose subirá todos os containers
   - O banco de dados será criado com a estrutura vazia

3. **Popular o Banco de Dados** (Obrigatório)
   
   **Opção 1 - Via phpMyAdmin (porta 8080)**:
   - Acesse o phpMyAdmin quando a porta 8080 abrir
   - Login: `root` / Senha: `rootpass`
   - Selecione o banco `application`
   - Vá em "SQL" e cole o conteúdo de `assets/seeds/db_populate.sql`
   - Execute

   **Opção 2 - Via Terminal**:
   ```bash
   docker exec -i mysql mysql -uroot -prootpass application < assets/seeds/db_populate.sql
   ```

4. **Pronto!** 🎉
   - Aplicação: Porta 80
   - phpMyAdmin: Porta 8080
   - MySQL: Porta 3306

---

### 💻 Instalação Local (Docker)

#### Pré-requisitos

- **Docker Desktop**: Versão mais recente instalada
- **Git**: Para clonar o repositório
- **Portas 80, 3306 e 8080**: Devem estar disponíveis

#### Passo a Passo

**1. Clone o Repositório**

```bash
git clone <url-do-repositorio>
cd sge
```

**2. Inicie os Containers Docker**

```bash
docker compose up -d
```

Isso iniciará 3 containers:
- **php**: Aplicação PHP + Apache (porta 80)
- **mysql**: MySQL (porta 3306)
- **phpmyadmin**: Interface de administração (porta 8080)

**3. Instale as dependências do Composer**
   - Execute:
     ```bash
     docker exec -it php composer install
     ```
   - Se aparecer o erro `composer: executable file not found in $PATH`, instale o Composer manualmente dentro do container:
     1. Entre no container como root:
        ```bash
        docker exec -it --user root php bash
        ```
     2. Instale o Composer:
        ```bash
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
        exit
        ```
     3. Execute novamente:
        ```bash
        docker exec -it php composer install
        ```

**4. Popular o Banco de Dados**

O banco é criado automaticamente **vazio** (somente estrutura).
Para adicionar dados de exemplo:

**Via phpMyAdmin**:
1. Acesse http://localhost:8080
2. Login: `root` / Senha: `rootpass`
3. Selecione o banco `application`
4. Importe o arquivo `assets/seeds/db_populate.sql`

**Via Terminal**:
```bash
docker exec -i mysql mysql -uroot -prootpass application < assets/seeds/db_populate.sql
```

**5. Acesse a Aplicação**

Abra o navegador em: **http://localhost**

---

## 🔑 Credenciais de Acesso

> ⚠️ **Importante**: As credenciais abaixo só funcionarão **após popular o banco** com o arquivo `assets/seeds/db_populate.sql`

### Super Admin (Acesso Total)
- **Email**: `sadmin`
- **Senha**: `sadmin`

### Admin de Atlética
- **Email**: `admin.atletica@sge.com`
- **Senha**: `sadmin`
- **Atlética**: A.A.A. FURIOSA

### Usuário Comum (Aluno)
- **Email**: `aluno@sge.com`
- **Senha**: `sadmin`

### Membro de Atlética
- **Email**: `membro@sge.com`
- **Senha**: `sadmin`
- **Atlética**: A.A.A. FURIOSA (aprovado)

### Professor (Pode Agendar Eventos)
- **Email**: `carlos.andrade@prof.sge.com`
- **Senha**: `sadmin`
- **Curso**: Engenharia Civil (Coordenador)

### Comunidade Externa
- **Email**: `comunidade@email.com`
- **Senha**: `sadmin`

**Nota**: Todos os usuários de teste têm a senha `sadmin` (hash: `$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O`)

---

## 📖 Como Usar

### Fluxo de Agendamento de Eventos

#### 1. Usuário Solicita Agendamento
- Professor ou Admin de Atlética acessa "Agendar Evento"
- Preenche formulário completo:
  - Título e tipo (esportivo/não esportivo)
  - Data e período
  - Informações do responsável
  - Materiais necessários
  - Lista de participantes
  - Observações
- Sistema valida:
  - Data futura
  - Antecedência mínima de 4 dias
  - Disponibilidade do horário
- Agendamento fica com status **"Pendente"**

#### 2. Super Admin Aprova/Rejeita
- Acessa "Gerenciar Agendamentos"
- Visualiza detalhes completos
- Aprova ou rejeita (com motivo)
- Sistema envia **notificação automática** ao solicitante

#### 3. Evento Aprovado
- Aparece na agenda pública
- Usuários podem marcar presença
- Sistema envia **lembrete 1 dia antes** (via script diário)

#### 4. Confirmação de Presença
- Usuários acessam "Agenda"
- Clicam em "Marcar Presença"
- Recebem notificação de confirmação

#### 5. Gestão de Agendamentos
- Usuário pode editar/cancelar seus agendamentos
- Admin pode visualizar estatísticas
- Super Admin gera relatórios

### Sistema de Notificações

#### Backend (Automático)
```php
// Criar notificação para aprovação
$notificationService->notifyAgendamentoAprovado($agendamentoId);

// Criar notificação para rejeição
$notificationService->notifyAgendamentoRejeitado($agendamentoId, $motivo);

// Criar notificação de presença
$notificationService->notifyPresencaConfirmada($userId, $agendamentoId);

// Notificação global (Super Admin)
$notificationRepo->createGlobalNotification($titulo, $mensagem, 'sistema');
```

#### Frontend (AJAX)
- Contador atualizado automaticamente no header
- Dropdown com notificações recentes
- Marcar como lida sem recarregar página

#### Script Diário (Cron Job)
Execute diariamente para enviar lembretes:

**Linux/Mac**:
```bash
# Adicionar ao crontab
0 20 * * * docker exec sge-php php /var/www/html/scripts/daily_notifications.php
```

**Windows (Task Scheduler)**:
```cmd
docker exec sge-php php /var/www/html/scripts/daily_notifications.php
```

---

## 🔒 Sistema de Permissões

### Níveis de Acesso

| Funcionalidade | Usuário | Admin Atlética | Super Admin |
|----------------|---------|----------------|-------------|
| Ver agenda pública | ✅ | ✅ | ✅ |
| Marcar presença | ✅ | ✅ | ✅ |
| Editar perfil | ✅ | ✅ | ✅ |
| Solicitar entrada em atlética | ✅ | ❌ | ❌ |
| Inscrever-se em modalidades | ✅ | ✅ | ✅ |
| **Agendar eventos** | ❌* | ✅** | ✅ |
| Editar próprios agendamentos | ✅ | ✅ | ✅ |
| Gerenciar membros atlética | ❌ | ✅ | ✅ |
| Aprovar inscrições modalidades | ❌ | ✅ | ✅ |
| Gerenciar eventos atlética | ❌ | ✅ | ✅ |
| **Aprovar/Rejeitar agendamentos** | ❌ | ❌ | ✅ |
| Gerenciar usuários | ❌ | ❌ | ✅ |
| Gerenciar estrutura (cursos/atléticas) | ❌ | ❌ | ✅ |
| Gerenciar modalidades | ❌ | ❌ | ✅ |
| Promover/Rebaixar admins | ❌ | ❌ | ✅ |
| Gerar relatórios | ❌ | ❌ | ✅ |
| Enviar notificação global | ❌ | ❌ | ✅ |

**Observações**:
- *Usuários comuns só podem agendar se forem **Professores**
- **Admin de Atlética só pode agendar se for também **"Membro das Atléticas"**

### Middleware de Proteção

```php
// Proteger rota (requer login)
Auth::protect();

// Proteger rota de admin de atlética
Auth::protectAdmin();

// Proteger rota de super admin
Auth::protectSuperAdmin();

// Verificar permissão personalizada
if (Auth::role() === 'superadmin') {
    // Código restrito
}
```

---

## 🗃️ Estrutura do Banco de Dados

### Principais Tabelas

#### `usuarios`
- Dados pessoais (nome, email, senha, RA, telefone)
- Relacionamento com `cursos` e `atleticas`
- `role`: usuario, admin, superadmin
- `tipo_usuario_detalhado`: Aluno, Professor, Membro das Atléticas, Comunidade Externa
- `is_coordenador`: Se é coordenador de curso
- `atletica_join_status`: Status de solicitação de entrada na atlética
- Campos para 2FA: `login_code`, `login_code_expires`
- Campos para recuperação: `reset_token`, `reset_token_expires`

#### `agendamentos`
- Informações completas do evento
- `status`: pendente, aprovado, rejeitado, cancelado
- `tipo_agendamento`: esportivo, nao_esportivo
- `subtipo_evento`: treino, campeonato, palestra, workshop, formatura
- `periodo`: primeiro (19:15-20:55), segundo (21:10-22:50)
- Campos detalhados: materiais, participantes, árbitro, infraestrutura
- Relacionamento com `usuarios` (solicitante) e `atleticas`

#### `notificacoes`
- Sistema completo de notificações
- `tipo`: agendamento_aprovado, agendamento_rejeitado, agendamento_cancelado, presenca_confirmada, lembrete_evento, info, aviso
- `lida`: Status de leitura (0 ou 1)
- Relacionamento opcional com `agendamentos`

#### `presencas`
- Confirmação de participação em eventos
- Chave única: (usuario_id, agendamento_id)
- Timestamp de confirmação

#### `atleticas`
- Cadastro das atléticas da UNIFIO
- Exemplos: FURIOSA, PREDADORA, SANGUINÁRIA, ALFA, MAGNA, etc.

#### `cursos`
- Cursos acadêmicos
- Relacionamento com `atleticas` e `coordenador_id` (professor)

#### `modalidades`
- Esportes e competições
- Exemplos: Futsal, Vôlei, Basquete, Handebol, League of Legends, CS:GO, etc.

#### `inscricoes_modalidade`
- Inscrições de alunos em modalidades esportivas
- `status`: pendente, aprovado, recusado
- Aprovação pelo admin da atlética

#### `inscricoes_eventos`
- Inscrições de alunos em eventos específicos
- Gestão pelo admin da atlética

---

## 🛠️ Comandos Úteis do Docker

### Gerenciamento de Containers

```bash
# Iniciar containers
docker compose up -d

# Parar containers
docker compose down

# Reiniciar containers
docker compose restart

# Recriar do zero (apaga todos os dados)
docker compose down -v
docker compose up -d

# Ver logs
docker logs php -f

# Ver status
docker ps
```

### Banco de Dados

```bash
# Popular banco com dados de exemplo
docker exec -i mysql mysql -uroot -prootpass application < assets/seeds/db_populate.sql

# Acessar MySQL via terminal
docker exec -it mysql mysql -uroot -prootpass application

# Backup do banco
docker exec mysql mysqldump -uroot -prootpass application > backup.sql

# Restaurar backup
docker exec -i mysql mysql -uroot -prootpass application < backup.sql
```

### Acesso aos Containers

```bash
# Entrar no container PHP
docker exec -it php bash

# Executar comandos PHP
docker exec -it php php -v

# Executar Composer
docker exec -it php composer install

# Atualizar autoload
docker exec -it php composer dump-autoload
```

### 🚀 Quick Start (Codespaces)

```bash
# 1. Abra no GitHub Codespaces (tudo sobe automaticamente)

# 2. Aguarde containers subirem (automático via postCreateCommand)

# 3. Popular banco de dados (OBRIGATÓRIO)
docker exec -i mysql mysql -uroot -prootpass application < assets/seeds/db_populate.sql

# 4. Acesse a aplicação (porta 80 será aberta automaticamente)

# 5. Login como Super Admin
# Email: sadmin
# Senha: sadmin
```

### Banco de Dados

```bash
# Backup do banco
docker exec sge-db mysqldump -uroot -prootpass sge_db > backup.sql

# Restaurar banco
docker exec -i sge-db mysql -uroot -prootpass sge_db < backup.sql

# Acessar MySQL CLI
docker exec -it sge-db mysql -uroot -prootpass sge_db
```

### Limpeza

```bash
# Remover containers e volumes
docker-compose down -v

# Limpar cache do Docker
docker system prune -a
```

---

## 🔧 Configuração Avançada

### Modificar Variáveis de Ambiente

Edite o arquivo `.env`:

```env
DB_HOST=sge-db
DB_NAME=sge_db
DB_USER=root
DB_PASS=rootpass
```

Após alterações, recrie os containers:
```bash
docker-compose down
docker-compose up -d
```

### Alterar Portas

Edite `docker-compose.yml`:

```yaml
services:
  sge-php:
    ports:
      - '8080:80'  # Altere 80 para outra porta

  sge-db:
    ports:
      - '3308:3306'  # Altere 3307 para outra porta
```

### Configurar Timezone

O timezone já está configurado para `America/Sao_Paulo` em:
- `docker-compose.yml` (MySQL)
- `public/index.php` (PHP)

Para alterar, modifique ambos os arquivos.

---

## 📊 Recursos Adicionais

### Relatórios Disponíveis

O Super Admin pode gerar relatórios de:
- **Agendamentos por período**: Filtrar por intervalo de datas
- **Eventos por tipo**: Esportivos vs Não Esportivos
- **Participação de atléticas**: Ranking de uso da quadra
- **Estatísticas gerais**: Total de eventos, usuários ativos, etc.
- **Versão para impressão**: Layout otimizado

### phpMyAdmin

Interface web para administração do MySQL:
- **URL**: http://localhost:8080
- **Servidor**: sge-db
- **Usuário**: root
- **Senha**: rootpass

Funcionalidades:
- Executar queries SQL
- Importar/Exportar dados
- Visualizar estrutura das tabelas
- Editar registros manualmente

### Logs e Debugging

**Logs do Apache**:
```bash
docker exec -it sge-php tail -f /var/log/apache2/error.log
```

**Logs do PHP**:
Configure `display_errors` no Dockerfile se necessário.

**Logs do MySQL**:
```bash
docker logs sge-db
```

---

## 🐛 Solução de Problemas

### Porta 80 já está em uso

**Windows**:
```cmd
netstat -ano | findstr :80
taskkill /PID <PID> /F
```

**Linux/Mac**:
```bash
sudo lsof -i :80
sudo kill -9 <PID>
```

Ou altere a porta no `docker-compose.yml`.

### Containers não iniciam

```bash
# Ver logs de erro
docker-compose logs

# Remover e recriar
docker-compose down -v
docker-compose up -d --build
```

### Erro de conexão com banco

1. Verifique se o container do banco está rodando:
   ```bash
   docker-compose ps
   ```

2. Teste a conexão:
   ```bash
   docker exec -it sge-db mysql -uroot -prootpass -e "SELECT 1"
   ```

3. Verifique as variáveis de ambiente no `.env`

### Permissões de arquivo (Linux/Mac)

```bash
sudo chown -R $USER:$USER .
chmod -R 755 .
```

### Composer não instalado

```bash
docker exec -it sge-php composer --version

# Se não estiver instalado
docker exec -it sge-php curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

---

## 📚 Boas Práticas de Desenvolvimento

### Adicionar Nova Rota

1. Defina a rota em `src/routes.php`:
```php
Router::get('/nova-rota', 'MeuController@minhaAction');
```

2. Crie o método no Controller:
```php
public function minhaAction() {
    Auth::protect(); // Proteger se necessário
    view('minha-view', ['data' => $dados]);
}
```

3. Crie a view em `views/minha-view.view.php`

### Adicionar Nova Tabela

1. Crie a estrutura SQL em `assets/data/0-schema.sql`
2. Crie um Repository em `src/Repository/MinhaRepository.php`
3. Use no Controller:
```php
$repo = $this->repository('MinhaRepository');
$dados = $repo->findAll();
```

### Sistema de Notificações

```php
use Application\Core\NotificationService;

$notificationService = new NotificationService();

// Notificação individual
$notificationService->notifyAgendamentoAprovado($agendamentoId);

// Notificação global (Super Admin)
$notificationRepo = new NotificationRepository();
$notificationRepo->createGlobalNotification(
    'Título da Notificação',
    'Mensagem completa',
    'sistema'
);
```

---

## 📊 Estrutura de Dados Importantes

### Tipos de Usuário (`tipo_usuario_detalhado`)
- `Aluno`: Estudante da UNIFIO
- `Professor`: Docente da UNIFIO
- `Membro das Atléticas`: Participante ativo de atlética
- `Comunidade Externa`: Visitante externo

### Tipos de Agendamento
- `esportivo`: Treinos, campeonatos, jogos
  - Subtipos: treino, campeonato
- `nao_esportivo`: Palestras, workshops, formaturas
  - Subtipos: palestra, workshop, formatura

### Status de Agendamento
- `pendente`: Aguardando aprovação
- `aprovado`: Confirmado pelo Super Admin
- `rejeitado`: Negado com motivo
- `cancelado`: Cancelado pelo usuário ou admin

### Períodos
- `primeiro`: 19:15 - 20:55
- `segundo`: 21:10 - 22:50

---

## 🎓 Atléticas Cadastradas

1. **A.A.A. FURIOSA** - Engenharia Civil
2. **A.A.A. PREDADORA** - Direito
3. **A.A.A. SANGUINÁRIA** - Medicina
4. **A.A.A. INSANA** - Psicologia
5. **A.A.A. MAGNA** - Administração
6. **A.A.A. ALFA** - Eng. Software / Ciência da Computação
7. **A.A.A. IMPÉRIO** - Publicidade e Propaganda
8. **A.A.A. VENENOSA** - Farmácia
9. **A.A.A. LETAL** - Ciências Biológicas
10. **A.A.A. ATÔMICA** - (Sem curso vinculado)

---

## 🏆 Modalidades Esportivas

### Esportes Tradicionais
- Futsal
- Voleibol
- Basquetebol
- Handebol
- Natação
- Atletismo
- Vôlei de Praia
- Queimada

### Artes Marciais
- Judô
- Karatê

### Esportes de Raquete
- Tênis de Mesa
- Tênis de Campo

### E-Sports
- League of Legends
- CS:GO

### Outros
- Xadrez

---

## 🚀 Roadmap e Melhorias Futuras

### Funcionalidades Planejadas
- [ ] Upload real de arquivos (lista de participantes)
- [ ] Integração com email para notificações
- [ ] Sistema de pontuação de atléticas
- [ ] Calendário público com filtros avançados
- [ ] API REST completa para mobile
- [ ] Dashboard com gráficos interativos
- [ ] Sistema de chat entre membros
- [ ] Galeria de fotos de eventos
- [ ] QR Code para check-in de presença

### Melhorias Técnicas
- [ ] Testes automatizados (PHPUnit)
- [ ] CI/CD com GitHub Actions
- [ ] Cache com Redis
- [ ] Logs estruturados (Monolog)
- [ ] Documentação da API (Swagger)
- [ ] Migração para PHP 8.3
- [ ] Containerização com Kubernetes

---

## 👥 Contribuindo

### Como Contribuir

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

### Padrões de Código

- **PSR-4**: Autoloading
- **PSR-12**: Estilo de código
- **Comentários**: Documentar classes e métodos complexos
- **Commits**: Mensagens descritivas em português

---

## 📄 Licença

Este projeto foi desenvolvido para uso acadêmico na UNIFIO.

---

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique a seção de **Solução de Problemas**
2. Consulte os logs dos containers
3. Abra uma issue no repositório

---

## 🙏 Agradecimentos

Desenvolvido para o **Centro Universitário UNIFIO** com o objetivo de modernizar e centralizar a gestão de eventos esportivos e acadêmicos.

**Stack Principal**: PHP 8.2 | MySQL 9.4 | Docker | JavaScript | CSS3

**Padrões**: MVC | Repository Pattern | Singleton | PSR-4

---

**Versão**: 1.0.0
**Última Atualização**: Outubro 2025
**Status**: ✅ Produção
