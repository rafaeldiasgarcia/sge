# 📊 SGE - Sistema de Gerenciamento de Eventos UNIFIO

> Sistema web completo para gerenciamento de agendamentos de quadras esportivas, administração de atléticas, cursos e usuários da UNIFIO.

[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-9.4-orange.svg)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-green.svg)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-Academic-yellow.svg)](LICENSE)

---

## 📋 Índice

- [Visão Geral](#-visão-geral)
- [Arquitetura Técnica](#-arquitetura-técnica)
- [Funcionalidades Detalhadas](#-funcionalidades-detalhadas)
- [Sistema de Autenticação](#-sistema-de-autenticação)
- [Estrutura do Banco de Dados](#-estrutura-do-banco-de-dados)
- [Sistema de Roteamento](#-sistema-de-roteamento)
- [Controllers e Repositories](#-controllers-e-repositories)
- [Frontend e JavaScript](#-frontend-e-javascript)
- [Sistema de Notificações](#-sistema-de-notificações)
- [Instalação e Configuração](#-instalação-e-configuração)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Padrões de Design](#-padrões-de-design)
- [Fluxos de Negócio](#-fluxos-de-negócio)
- [API e Endpoints](#-api-e-endpoints)
- [Sistema de Permissões](#-sistema-de-permissões)
- [Desenvolvimento](#-desenvolvimento)
- [Solução de Problemas](#-solução-de-problemas)
- [Contribuição](#-contribuição)

---

## 🎯 Visão Geral

O **SGE (Sistema de Gerenciamento de Eventos)** é uma aplicação web desenvolvida para o Centro Universitário UNIFIO com o objetivo de modernizar e centralizar a gestão de eventos esportivos e acadêmicos na quadra poliesportiva da instituição.

### Características Principais

- **Arquitetura MVC Moderna**: Separação clara de responsabilidades com padrões de design bem definidos
- **Containerizado com Docker**: Ambiente consistente e fácil deploy
- **Autenticação Segura**: Login com verificação em 2 etapas (2FA) via e-mail
- **Sistema de Notificações**: Notificações em tempo real via AJAX com polling automático
- **Interface Responsiva**: Design moderno e mobile-friendly
- **Calendário Interativo**: Navegação mensal com eventos dinâmicos via AJAX
- **Gestão Multinível**: 3 níveis de acesso (Usuário, Admin, Super Admin)
- **Sistema de Presenças**: Confirmação de presença em eventos com contadores dinâmicos
- **Relatórios Avançados**: Sistema completo de relatórios com filtros e exportação

### Problema que Resolve

Antes do SGE, o gerenciamento de eventos na quadra da UNIFIO era feito de forma manual e descentralizada, causando:
- Conflitos de agendamento
- Falta de transparência
- Dificuldade de controle de presença
- Ausência de relatórios e estatísticas
- Comunicação ineficiente

O SGE resolve todos esses problemas com uma plataforma centralizada, automatizada e transparente.

---

## 🏗️ Arquitetura Técnica

### Stack Tecnológica

```
┌─────────────────────────────────────────────────────────┐
│                    FRONTEND                             │
├─────────────────────────────────────────────────────────┤
│  HTML5 + CSS3 + JavaScript Vanilla + AJAX              │
│  • Interface responsiva e moderna                      │
│  • Comunicação assíncrona com backend                  │
│  • Componentes modulares (calendar, notifications)     │
└─────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────┐
│                    BACKEND                              │
├─────────────────────────────────────────────────────────┤
│  PHP 8.2 + Apache + MySQL 9.4                         │
│  • Arquitetura MVC com PSR-4 autoloading              │
│  • Padrão Repository para acesso a dados              │
│  • Sistema de roteamento customizado                  │
│  • Autenticação 2FA com PHPMailer                     │
└─────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────┐
│                 INFRAESTRUTURA                          │
├─────────────────────────────────────────────────────────┤
│  Docker + Docker Compose                               │
│  • Containerização completa                            │
│  • Ambiente de desenvolvimento consistente             │
│  • Deploy simplificado                                 │
└─────────────────────────────────────────────────────────┘
```

### Princípios Arquiteturais

- **Separação de Responsabilidades**: Cada camada tem uma função específica
- **Inversão de Dependência**: Controllers dependem de abstrações (Repositories)
- **Single Responsibility**: Cada classe tem uma única responsabilidade
- **DRY (Don't Repeat Yourself)**: Código reutilizável através de helpers e base classes
- **SOLID**: Princípios de design orientado a objetos aplicados

### Fluxo de Requisições

#### 1. Front Controller (`public/index.php`)

**Ponto de entrada único** - Todas as requisições passam por aqui:

```php
// 1. Configuração da sessão com parâmetros de segurança
session_start([
    'cookie_lifetime' => 0,           // Expira ao fechar navegador
    'cookie_httponly' => true,        // Previne XSS
    'cookie_secure' => isset($_SERVER['HTTPS']), // HTTPS se disponível
    'cookie_samesite' => 'Lax'        // Previne CSRF
]);

// 2. Configuração do ambiente
date_default_timezone_set('America/Sao_Paulo');
define('ROOT_PATH', __DIR__);

// 3. Autoloading PSR-4
require_once ROOT_PATH . '/vendor/autoload.php';

// 4. Carregamento de rotas
require_once ROOT_PATH . '/src/routes.php';

// 5. Roteamento e despacho
try {
    $url = $_GET['url'] ?? '/';
    $method = $_SERVER['REQUEST_METHOD'];
    Router::dispatch($url, $method);
} catch (Exception $e) {
    // Tratamento global de erros
    if (is_ajax_request()) {
        json_error($e->getMessage(), 500);
    } else {
        // Exibe página de erro
    }
}
```

#### 2. Sistema de Roteamento (`src/Core/Router.php`)

**Mapeamento dinâmico** de URLs para Controllers:

```php
// Registro de rotas
Router::get('/usuario/:id', 'UsuarioController@show');
Router::post('/agendamento', 'AgendamentoController@store');
Router::put('/agendamento/:id', 'AgendamentoController@update');

// Despacho da requisição
public static function dispatch(string $uri, string $method): void
{
    // 1. Verifica method override (PUT via POST)
    if ($method === 'POST' && $_POST['_method'] === 'PUT') {
        $method = 'PUT';
    }
    
    // 2. Busca rota correspondente
    foreach (self::$routes[$method] as $route => $action) {
        $params = self::extractParams($route, $uri);
        if ($params !== null) {
            // 3. Instancia controller e chama método
            [$controllerName, $methodName] = explode('@', $action);
            $controllerClass = "Application\\Controller\\" . $controllerName;
            $controller = new $controllerClass();
            call_user_func_array([$controller, $methodName], array_values($params));
            return;
        }
    }
    
    throw new Exception("Rota não encontrada: [{$method}] {$uri}");
}
```

#### 3. Controllers (`src/Controller/`)

**Orquestração da lógica de negócio**:

```php
class AgendamentoController extends BaseController
{
    public function store()
    {
        // 1. Autenticação obrigatória
        Auth::protect();
        
        // 2. Validação de dados
        $this->requireFieldsOrRedirect(['titulo', 'data'], $_POST, function() {
            $this->setErrorAndRedirect('Campos obrigatórios', '/agendar-evento');
        });
        
        // 3. Lógica de negócio
        $repo = $this->repository('AgendamentoRepository');
        $agendamentoId = $repo->create($_POST, Auth::id());
        
        // 4. Notificação automática
        $notificationService = new NotificationService();
        $notificationService->notifyAgendamentoAprovado($agendamentoId);
        
        // 5. Resposta
        $this->setSuccessAndRedirect('Agendamento criado com sucesso!', '/meus-agendamentos');
    }
}
```

---

## 🚀 Funcionalidades Detalhadas

### 🔐 Sistema de Autenticação e Autorização

#### Autenticação em 2 Etapas (2FA)

**Fluxo de login em duas etapas**:

```php
class AuthController
{
    public function authenticate()
    {
        // Etapa 1: Verificar email/senha
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        
        $usuario = $this->repository('UsuarioRepository')->findByEmail($email);
        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            $this->setErrorAndRedirect('Credenciais inválidas', '/login');
        }
        
        // Gerar código 2FA
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->repository('UsuarioRepository')->setLoginCode($usuario['id'], $codigo);
        
        // Enviar por email
        $emailService = new EmailService();
        $emailService->sendVerificationCode($usuario['email'], $usuario['nome'], $codigo);
        
        // Redirecionar para verificação
        $_SESSION['pending_user_id'] = $usuario['id'];
        redirect('/login-verify');
    }
    
    public function verifyCode()
    {
        $codigo = $_POST['codigo'];
        $userId = $_SESSION['pending_user_id'];
        
        $usuario = $this->repository('UsuarioRepository')->findById($userId);
        if (!$usuario || $usuario['login_code'] !== $codigo) {
            $this->setErrorAndRedirect('Código inválido', '/login-verify');
        }
        
        // Login bem-sucedido
        $this->createSession($usuario);
        redirect('/dashboard');
    }
}
```

#### Middlewares de Autorização

**Controle de acesso por roles**:

```php
class Auth
{
    // Verificação básica de login
    public static function protect()
    {
        if (!self::check()) {
            $_SESSION['error_message'] = 'Você precisa estar logado.';
            redirect('/login');
        }
    }
    
    // Verificação de admin de atlética
    public static function protectAdmin()
    {
        self::protect(); // Primeiro verifica login
        if (self::role() !== 'admin') {
            http_response_code(403);
            die('Acesso negado. Área restrita para Administradores.');
        }
    }
    
    // Verificação de super admin
    public static function protectSuperAdmin()
    {
        self::protect();
        if (self::role() !== 'superadmin') {
            http_response_code(403);
            die('Acesso negado. Área restrita para Super Administradores.');
        }
    }
}
```

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

- Se o usuário não estiver autenticado, o pop-up orienta a realizar login antes de interagir
- CTA direto para `login` com retorno à tela atual após autenticação

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
- Checkbox de aceite obrigatório para prosseguir com a criação/edição do agendamento
- Mensagens de erro amigáveis quando o aceite não for marcado

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

## 🗄️ Estrutura do Banco de Dados

### Padrão de Nomenclatura

**Convenções utilizadas**:

- **Tabelas**: Nome no plural (`usuarios`, `agendamentos`, `atleticas`)
- **Chaves primárias**: `id` (INT AUTO_INCREMENT)
- **Chaves estrangeiras**: `{tabela}_id` (ex: `usuario_id`, `curso_id`)
- **Campos de auditoria**: `data_criacao`, `data_atualizacao`
- **Status**: ENUM com valores descritivos (`'aprovado'`, `'pendente'`, `'rejeitado'`)

### Relacionamentos Principais

```sql
-- Estrutura simplificada das principais tabelas

-- Usuários do sistema
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    ra VARCHAR(20) UNIQUE,
    telefone VARCHAR(20),
    data_nascimento DATE,
    curso_id INT,
    atletica_id INT,
    role ENUM('usuario', 'admin', 'superadmin') DEFAULT 'usuario',
    tipo_usuario_detalhado ENUM('Aluno', 'Professor', 'Membro das Atleticas', 'Comunidade Externa'),
    is_coordenador TINYINT(1) DEFAULT 0,
    atletica_join_status ENUM('none', 'pendente', 'aprovado') DEFAULT 'none',
    login_code VARCHAR(6),
    login_code_expires DATETIME,
    reset_token VARCHAR(255),
    reset_token_expires DATETIME,
    FOREIGN KEY (curso_id) REFERENCES cursos(id),
    FOREIGN KEY (atletica_id) REFERENCES atleticas(id)
);

-- Agendamentos de eventos
CREATE TABLE agendamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    tipo_agendamento ENUM('esportivo', 'nao_esportivo') NOT NULL,
    esporte_tipo VARCHAR(100),
    data_agendamento DATE NOT NULL,
    periodo ENUM('primeiro', 'segundo') NOT NULL,
    descricao TEXT,
    status ENUM('aprovado', 'pendente', 'rejeitado', 'cancelado', 'finalizado') DEFAULT 'pendente',
    motivo_rejeicao TEXT,
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Sistema de presenças
CREATE TABLE presencas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    agendamento_id INT NOT NULL,
    data_presenca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_presence (usuario_id, agendamento_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id)
);

-- Notificações do sistema
CREATE TABLE notificacoes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    tipo ENUM('agendamento_aprovado', 'agendamento_rejeitado', 'presenca_confirmada', 'lembrete_evento', 'info', 'aviso', 'sistema') NOT NULL,
    agendamento_id INT NULL,
    lida TINYINT(1) DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id)
);
```

### Índices e Performance

**Otimizações implementadas**:

```sql
-- Índices para consultas frequentes
CREATE INDEX idx_agendamentos_data ON agendamentos(data_agendamento);
CREATE INDEX idx_agendamentos_status ON agendamentos(status);
CREATE INDEX idx_agendamentos_usuario ON agendamentos(usuario_id);
CREATE INDEX idx_presencas_agendamento ON presencas(agendamento_id);
CREATE INDEX idx_notificacoes_usuario ON notificacoes(usuario_id);
CREATE INDEX idx_notificacoes_lida ON notificacoes(lida);

-- Índices compostos para consultas complexas
CREATE INDEX idx_agendamentos_data_status ON agendamentos(data_agendamento, status);
CREATE INDEX idx_notificacoes_usuario_lida ON notificacoes(usuario_id, lida);
```

---

## 🛣️ Sistema de Roteamento

### Estrutura de Rotas

**Definição em `src/routes.php`**:

```php
// Rotas públicas (não requerem autenticação)
Router::get('/', 'HomeController@index');
Router::get('/login', 'AuthController@login');
Router::post('/login', 'AuthController@authenticate');

// Rotas protegidas (requerem login)
Router::get('/dashboard', 'UsuarioController@dashboard');
Router::get('/agenda', 'AgendaController@index');
Router::post('/agendamento', 'AgendamentoController@store');

// Rotas de admin (requerem role 'admin')
Router::get('/admin/atletica/dashboard', 'AdminAtleticaController@dashboard');
Router::post('/admin/atletica/aprovar-membro', 'AdminAtleticaController@aprovarMembro');

// Rotas de super admin (requerem role 'superadmin')
Router::get('/superadmin/dashboard', 'SuperAdminController@dashboard');
Router::post('/superadmin/aprovar-agendamento', 'SuperAdminController@aprovarAgendamento');
```

### Parâmetros Dinâmicos

**Suporte a parâmetros na URL**:

```php
// Rota com parâmetro
Router::get('/usuario/:id', 'UsuarioController@show');

// Extração de parâmetros
private static function extractParams(string $route, string $uri): ?array
{
    // Converte :id para regex (\d+)
    $pattern = preg_replace('/:(\w+)/', '(\d+)', $route);
    $pattern = '#^' . $pattern . '$#';
    
    if (preg_match($pattern, $uri, $matches)) {
        array_shift($matches); // Remove match completo
        return $matches;
    }
    
    return null;
}

// Uso no controller
public function show(int $id)
{
    $repo = $this->repository('UsuarioRepository');
    $usuario = $repo->findById($id);
    view('usuario/detalhes', ['usuario' => $usuario]);
}
```

### Method Override

**Suporte a PUT via POST** (necessário para formulários HTML):

```php
// No formulário HTML
<form method="POST">
    <input type="hidden" name="_method" value="PUT">
    <!-- campos do formulário -->
</form>

// No Router
if ($method === 'POST' && $_POST['_method'] === 'PUT') {
    $method = 'PUT';
}
```

---

## 🎨 Padrões de Design Utilizados

### 1. Singleton (Connection)

**Uma única instância de conexão com o banco**:

```php
class Connection
{
    private static ?PDO $instance = null;
    
    private function __construct() {} // Construtor privado
    private function __clone() {}     // Previne clonagem
    
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new PDO($dsn, $username, $password, $options);
        }
        return self::$instance;
    }
}
```

### 2. Factory Method (BaseController)

**Criação dinâmica de repositories**:

```php
abstract class BaseController
{
    protected function repository(string $repositoryName)
    {
        $className = "Application\\Repository\\" . $repositoryName;
        return new $className();
    }
}
```

### 3. Template Method (Layout System)

**Estrutura comum para todas as views**:

```php
// views/layout.php
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'SGE UNIFIO' ?></title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/default.css">
</head>
<body>
    <?php if (!$isAuthPage): ?>
        <?php include ROOT_PATH . '/views/_partials/header.php'; ?>
    <?php endif; ?>
    
    <main>
        <?= $content ?>
    </main>
    
    <?php if (!$isAuthPage): ?>
        <?php include ROOT_PATH . '/views/_partials/footer.php'; ?>
    <?php endif; ?>
</body>
</html>
```

### 4. Strategy Pattern (Notification Types)

**Diferentes tipos de notificações**:

```php
class NotificationService
{
    public function notifyAgendamentoAprovado(int $agendamentoId): bool
    {
        return $this->createNotification($agendamentoId, 'agendamento_aprovado');
    }
    
    public function notifyAgendamentoRejeitado(int $agendamentoId, string $motivo): bool
    {
        return $this->createNotification($agendamentoId, 'agendamento_rejeitado', $motivo);
    }
    
    private function createNotification(int $agendamentoId, string $tipo, string $extra = ''): bool
    {
        // Lógica comum para criação de notificações
    }
}
```

---

## 🔄 Fluxos de Negócio

### 1. Fluxo de Agendamento

```mermaid
graph TD
    A[Usuário acessa /agendar-evento] --> B[Auth::protect verifica login]
    B --> C[Exibe formulário de agendamento]
    C --> D[Usuário preenche e submete]
    D --> E[AgendamentoController@store]
    E --> F[Validação de dados]
    F --> G[Verificação de conflitos]
    G --> H[AgendamentoRepository@create]
    H --> I[Status: 'pendente']
    I --> J[NotificationService notifica Super Admin]
    J --> K[Redireciona para /meus-agendamentos]
```

### 2. Fluxo de Aprovação

```mermaid
graph TD
    A[Super Admin acessa /superadmin/agendamentos] --> B[Auth::protectSuperAdmin]
    B --> C[Lista agendamentos pendentes]
    C --> D[Super Admin clica em 'Aprovar']
    D --> E[SuperAdminController@aprovarAgendamento]
    E --> F[AgendamentoRepository@updateStatus]
    F --> G[Status: 'aprovado']
    G --> H[NotificationService notifica usuário]
    H --> I[Evento aparece na agenda pública]
```

### 3. Fluxo de Presença

```mermaid
graph TD
    A[Usuário acessa /agenda] --> B[AgendaController@index]
    B --> C[Exibe calendário com eventos aprovados]
    C --> D[Usuário clica em 'Marcar Presença']
    D --> E[AJAX: /api/presenca/marcar]
    E --> F[AgendaController@marcarPresenca]
    F --> G[Verifica se evento é aprovado]
    G --> H[PresencaRepository@create]
    H --> I[NotificationService notifica confirmação]
    I --> J[Atualiza contador de presenças]
```

---

## ⚙️ Configurações e Dependências

### Autoloading PSR-4

**Configuração no `composer.json`**:

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

**Estrutura de namespaces**:

```
Application\
├── Controller\     # Controllers da aplicação
├── Repository\     # Camada de acesso a dados
├── Core\          # Classes principais (Router, Auth, Connection)
└── Models\        # Modelos de dados (se necessário)
```

### Configuração de Sessão

**Parâmetros de segurança**:

```php
session_start([
    'cookie_lifetime' => 0,                    // Expira ao fechar navegador
    'cookie_httponly' => true,                 // Previne XSS
    'cookie_secure' => isset($_SERVER['HTTPS']), // HTTPS se disponível
    'cookie_samesite' => 'Lax',                // Previne CSRF
    'use_strict_mode' => true,                 // Modo estrito de sessão
    'use_only_cookies' => true                 // Apenas cookies, não URL
]);
```

### Configuração do Banco

**Connection Singleton**:

```php
// Configurações do banco
$host = 'db';              // Nome do serviço Docker
$dbname = 'application';   // Nome do banco
$username = 'appuser';     // Usuário
$password = 'apppass';     // Senha
$charset = 'utf8mb4';      // Charset completo

// Opções PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
```

### Configuração de E-mail

**PHPMailer com Gmail**:

```php
class EmailService
{
    private function configureSMTP(): bool
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = getenv('SMTP_EMAIL') ?: 'seu-email@gmail.com';
        $this->mailer->Password = getenv('SMTP_PASSWORD') ?: 'sua-senha-app';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->CharSet = 'UTF-8';
        
        return true;
    }
}
```

---

## 🚀 Performance e Otimizações

### 1. Lazy Loading

**Conexão com banco criada apenas quando necessário**:

```php
public static function getInstance(): PDO
{
    if (self::$instance === null) {  // Lazy initialization
        self::$instance = new PDO($dsn, $username, $password, $options);
    }
    return self::$instance;
}
```

### 2. Prepared Statements

**Prevenção de SQL Injection**:

```php
public function findByEmail(string $email): ?array
{
    $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);  // Parâmetros seguros
    return $stmt->fetch() ?: null;
}
```

### 3. Índices de Banco

**Otimização de consultas frequentes**:

```sql
-- Consultas otimizadas com índices
CREATE INDEX idx_agendamentos_data_status ON agendamentos(data_agendamento, status);
CREATE INDEX idx_notificacoes_usuario_lida ON notificacoes(usuario_id, lida);
```

### 4. Cache de Sessão

**Dados do usuário em sessão**:

```php
// Evita consultas repetidas ao banco
$_SESSION['user_data'] = [
    'id' => $usuario['id'],
    'nome' => $usuario['nome'],
    'role' => $usuario['role']
];
```

---

## 🔧 Manutenção e Debugging

### Logs de Erro

**Tratamento global de exceções**:

```php
// public/index.php
try {
    Router::dispatch($url, $method);
} catch (Exception $e) {
    error_log("Erro na aplicação: " . $e->getMessage());
    
    if (is_ajax_request()) {
        json_error($e->getMessage(), 500);
    } else {
        // Exibe página de erro amigável
        view('errors/500', ['error' => $e->getMessage()]);
    }
}
```

### Debug Mode

**Desenvolvimento vs Produção**:

```php
// Configuração de ambiente
if (getenv('APP_ENV') === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
```

### Validação de Dados

**Sanitização e validação**:

```php
// Helpers de validação
function post_string(string $key, string $default = ''): string
{
    if (!isset($_POST[$key])) {
        return $default;
    }
    $value = is_string($_POST[$key]) ? $_POST[$key] : $default;
    return trim($value);  // Remove espaços
}

function post_int(string $key, int $default = 0): int
{
    return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
}
```

---

## 📊 Métricas e Monitoramento

### Queries de Performance

**Análise de uso do sistema**:

```sql
-- Eventos mais populares
SELECT titulo, COUNT(*) as total_agendamentos
FROM agendamentos 
WHERE status = 'aprovado'
GROUP BY titulo 
ORDER BY total_agendamentos DESC;

-- Taxa de aprovação
SELECT 
    COUNT(*) as total_solicitacoes,
    COUNT(CASE WHEN status = 'aprovado' THEN 1 END) as aprovadas,
    ROUND(COUNT(CASE WHEN status = 'aprovado' THEN 1 END) * 100.0 / COUNT(*), 2) as taxa_aprovacao
FROM agendamentos;

-- Uso por período
SELECT 
    periodo,
    COUNT(*) as total_eventos
FROM agendamentos 
WHERE status = 'aprovado'
GROUP BY periodo;
```

### Logs de Auditoria

**Rastreamento de ações importantes**:

```php
// Log de ações administrativas
private function logAdminAction(string $action, array $data): void
{
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => Auth::id(),
        'action' => $action,
        'data' => $data
    ];
    
    error_log("Admin Action: " . json_encode($log));
}
```

---

## 🎯 Conclusão

O SGE implementa uma arquitetura robusta e escalável, seguindo boas práticas de desenvolvimento e padrões de design estabelecidos. A separação clara de responsabilidades, o uso de padrões como Repository e Singleton, e a implementação de um sistema de roteamento flexível tornam o código maintível e extensível.

### Pontos Fortes da Arquitetura

- ✅ **Separação clara de responsabilidades** (MVC)
- ✅ **Padrões de design bem implementados** (Repository, Singleton, Factory)
- ✅ **Sistema de autenticação robusto** (2FA)
- ✅ **Roteamento flexível e dinâmico**
- ✅ **Tratamento de erros centralizado**
- ✅ **Código reutilizável** (BaseController, helpers)
- ✅ **Segurança implementada** (prepared statements, validação)

### Áreas de Melhoria

- 🔄 **Implementar cache** para consultas frequentes
- 🔄 **Adicionar testes automatizados** (PHPUnit)
- 🔄 **Implementar logs estruturados** (Monolog)
- 🔄 **Adicionar validação de entrada** mais robusta
- 🔄 **Implementar rate limiting** para APIs

Esta arquitetura fornece uma base sólida para futuras expansões e melhorias do sistema, mantendo a qualidade e a manutenibilidade do código.

---

## 🎮 Controllers e Repositories Detalhados

### Controllers Principais

#### 1. **AuthController** - Autenticação e Autorização

**Responsabilidades:**
- Gerenciar todo o fluxo de autenticação 2FA
- Registro de novos usuários
- Recuperação de senha via e-mail
- Logout e limpeza de sessão

**Métodos Principais:**
```php
class AuthController extends BaseController
{
    // Exibe formulário de login
    public function showLoginForm()
    
    // Processa login e envia código 2FA
    public function login()
    
    // Exibe formulário de verificação 2FA
    public function showVerifyForm()
    
    // Valida código 2FA e cria sessão
    public function verifyCode()
    
    // Exibe formulário de registro
    public function showRegistrationForm()
    
    // Processa cadastro de novo usuário
    public function register()
    
    // Destrói sessão e redireciona
    public function logout()
    
    // Recuperação de senha
    public function showForgotPasswordForm()
    public function sendRecoveryLink()
    public function showResetPasswordForm()
    public function resetPassword()
}
```

#### 2. **UsuarioController** - Painel do Usuário

**Responsabilidades:**
- Dashboard do usuário
- Gerenciamento de perfil
- Solicitações de atlética
- Inscrições em modalidades
- Troca de curso

**Métodos Principais:**
```php
class UsuarioController extends BaseController
{
    // Dashboard com próximos eventos
    public function dashboard()
    
    // Exibe e atualiza perfil
    public function perfil()
    public function updatePerfil()
    
    // Gerenciamento de atlética
    public function solicitarEntradaAtletica()
    public function sairAtletica()
    
    // Troca de curso
    public function solicitarTrocaCurso()
    
    // Inscrições em modalidades
    public function showInscricoes()
    public function inscreverEmModalidade()
    public function cancelarInscricao()
}
```

#### 3. **AgendamentoController** - Gestão de Agendamentos

**Responsabilidades:**
- Criação de agendamentos
- Edição e cancelamento
- Validações de negócio
- Calendário AJAX

**Métodos Principais:**
```php
class AgendamentoController extends BaseController
{
    // Formulário de novo agendamento
    public function showForm()
    
    // Cria agendamento (pendente)
    public function create()
    
    // Lista agendamentos do usuário
    public function showMeusAgendamentos()
    
    // Edição de agendamentos
    public function showEditForm(int $id)
    public function update(int $id)
    
    // Cancelamento
    public function cancel()
    
    // Endpoints AJAX para calendário
    public function getCalendarPartial()
    public function getCalendarGrid()
    public function getCalendarStats()
    public function getEventDetails()
}
```

#### 4. **AgendaController** - Calendário Público

**Responsabilidades:**
- Exibição do calendário público
- Sistema de presenças
- Filtros de eventos

**Métodos Principais:**
```php
class AgendaController extends BaseController
{
    // Exibe calendário com eventos aprovados
    public function index()
    
    // Marcar/desmarcar presença
    public function handlePresenca()
}
```

#### 5. **AdminAtleticaController** - Admin de Atlética

**Responsabilidades:**
- Dashboard da atlética
- Gestão de membros
- Inscrições em modalidades
- Eventos da atlética

**Métodos Principais:**
```php
class AdminAtleticaController extends BaseController
{
    // Dashboard com estatísticas
    public function dashboard()
    
    // Gerenciamento de membros
    public function gerenciarMembros()
    public function handleMembroAction()
    
    // Inscrições em modalidades
    public function gerenciarInscricoes()
    public function handleInscricaoAction()
}
```

#### 6. **SuperAdminController** - Super Administrador

**Responsabilidades:**
- Dashboard completo
- Gerenciamento de usuários
- Aprovação de agendamentos
- Estrutura acadêmica
- Relatórios

**Métodos Principais:**
```php
class SuperAdminController extends BaseController
{
    // Dashboard com estatísticas gerais
    public function dashboard()
    
    // Gerenciamento de agendamentos
    public function gerenciarAgendamentos()
    public function aprovarAgendamento()
    public function rejeitarAgendamento()
    
    // Gerenciamento de usuários
    public function gerenciarUsuarios()
    public function showEditUserForm()
    public function updateUser()
    public function deleteUser()
    
    // Estrutura acadêmica
    public function gerenciarEstrutura()
    public function createCurso()
    public function updateCurso()
    public function deleteCurso()
    
    // Relatórios
    public function showRelatorios()
    public function gerarRelatorio()
    public function imprimirRelatorio()
}
```

#### 7. **NotificationController** - API de Notificações

**Responsabilidades:**
- API REST para notificações
- Polling em tempo real
- Marcar como lida

**Métodos Principais:**
```php
class NotificationController extends BaseController
{
    // Lista notificações (JSON)
    public function getNotifications()
    
    // Marcar como lida (JSON)
    public function markAsRead()
}
```

### Repositories Principais

#### 1. **UsuarioRepository** - Gestão de Usuários

**Responsabilidades:**
- CRUD completo de usuários
- Autenticação e códigos 2FA
- Gerenciamento de atléticas
- Inscrições em modalidades

**Métodos Principais:**
```php
class UsuarioRepository
{
    // Busca usuário por email
    public function findByEmail(string $email): ?array
    
    // Busca usuário por ID
    public function findById(int $id): ?array
    
    // Cria novo usuário
    public function create(array $data): int
    
    // Atualiza dados do usuário
    public function update(int $id, array $data): bool
    
    // Gerenciamento de códigos 2FA
    public function setLoginCode(int $userId, string $code): bool
    public function verifyLoginCode(int $userId, string $code): bool
    
    // Gerenciamento de atlética
    public function solicitarEntradaAtletica(int $userId, int $atleticaId): bool
    public function aprovarEntradaAtletica(int $userId): bool
    public function sairAtletica(int $userId): bool
    
    // Inscrições em modalidades
    public function inscreverEmModalidade(int $userId, int $modalidadeId, int $atleticaId): bool
    public function cancelarInscricao(int $inscricaoId): bool
}
```

#### 2. **AgendamentoRepository** - Gestão de Agendamentos

**Responsabilidades:**
- CRUD de agendamentos
- Verificação de disponibilidade
- Validações de negócio
- Presenças em eventos

**Métodos Principais:**
```php
class AgendamentoRepository
{
    // Busca agendamentos por período
    public function findAgendaEvents(?int $usuarioId): array
    
    // Cria novo agendamento
    public function create(array $data, int $usuarioId): int
    
    // Atualiza agendamento
    public function update(int $id, array $data): bool
    
    // Verifica disponibilidade
    public function isHorarioDisponivel(string $data, string $periodo): bool
    
    // Aprova/rejeita agendamento
    public function aprovarAgendamento(int $id): bool
    public function rejeitarAgendamento(int $id, string $motivo): bool
    
    // Sistema de presenças
    public function marcarPresenca(int $usuarioId, int $agendamentoId): bool
    public function desmarcarPresenca(int $usuarioId, int $agendamentoId): bool
    public function getPresencasByAgendamento(int $agendamentoId): array
}
```

#### 3. **NotificationRepository** - Sistema de Notificações

**Responsabilidades:**
- CRUD de notificações
- Contadores de não lidas
- Notificações globais

**Métodos Principais:**
```php
class NotificationRepository
{
    // Cria nova notificação
    public function create(int $usuarioId, string $titulo, string $mensagem, string $tipo, ?int $agendamentoId = null): bool
    
    // Busca notificações do usuário
    public function findByUsuario(int $usuarioId, int $limit = 10): array
    
    // Conta notificações não lidas
    public function getUnreadCount(int $usuarioId): int
    
    // Marca como lida
    public function markAsRead(int $notificationId, int $usuarioId): bool
    
    // Notificação global
    public function createGlobalNotification(string $titulo, string $mensagem, string $tipo): bool
}
```

#### 4. **AtleticaRepository** - Gestão de Atléticas

**Responsabilidades:**
- CRUD de atléticas
- Busca de atléticas sem vínculos
- Relacionamentos com cursos

**Métodos Principais:**
```php
class AtleticaRepository
{
    // Lista todas as atléticas
    public function findAll(): array
    
    // Busca por ID
    public function findById(int $id): ?array
    
    // Cria nova atlética
    public function create(string $nome): bool
    
    // Atualiza atlética
    public function update(int $id, string $nome): bool
    
    // Exclui atlética
    public function delete(int $id): bool
    
    // Busca atléticas sem vínculos
    public function findUnlinked(): array
}
```

#### 5. **CursoRepository** - Gestão de Cursos

**Responsabilidades:**
- CRUD de cursos
- Relacionamentos com atléticas
- Coordenadores

**Métodos Principais:**
```php
class CursoRepository
{
    // Lista todos os cursos
    public function findAll(): array
    
    // Busca por ID
    public function findById(int $id): ?array
    
    // Cria novo curso
    public function create(string $nome, ?int $atleticaId): bool
    
    // Atualiza curso
    public function update(int $id, string $nome, ?int $atleticaId): bool
    
    // Exclui curso
    public function delete(int $id): bool
    
    // Busca ID da atlética do curso
    public function getAtleticaIdByCurso(int $cursoId): ?int
}
```

#### 6. **ModalidadeRepository** - Gestão de Modalidades

**Responsabilidades:**
- CRUD de modalidades esportivas
- Lista de esportes disponíveis

**Métodos Principais:**
```php
class ModalidadeRepository
{
    // Lista todas as modalidades
    public function findAll(): array
    
    // Busca por ID
    public function findById(int $id): ?array
    
    // Cria nova modalidade
    public function create(string $nome): bool
    
    // Atualiza modalidade
    public function update(int $id, string $nome): bool
    
    // Exclui modalidade
    public function delete(int $id): bool
}
```

#### 7. **RelatorioRepository** - Sistema de Relatórios

**Responsabilidades:**
- Relatórios gerais
- Estatísticas por período
- Dados de participação

**Métodos Principais:**
```php
class RelatorioRepository
{
    // Relatório geral por período
    public function getRelatorioGeral(string $dataInicio, string $dataFim): array
    
    // Presenças por agendamento
    public function getPresencasByAgendamento(int $agendamentoId): array
    
    // Estatísticas de uso
    public function getEstatisticasUso(): array
    
    // Modalidades mais populares
    public function getModalidadesPopulares(): array
}
```

---

## 🎨 Frontend e JavaScript Modules

### Estrutura JavaScript Modular

```
public/js/
├── app.js                    # Arquivo principal
├── modules/
│   ├── _partials/           # Componentes reutilizáveis
│   │   ├── calendar.js       # Calendário interativo
│   │   ├── notifications.js  # Sistema de notificações
│   │   ├── header.js         # Navegação responsiva
│   │   └── dashboard-calendar.js # Calendário do dashboard
│   ├── auth/                # Autenticação
│   │   ├── login.js         # Validações de login
│   │   └── register.js      # Validações de registro
│   ├── events/              # Eventos e agendamentos
│   │   ├── agenda.js        # Página de agenda
│   │   ├── event-form.js    # Formulário de agendamento
│   │   ├── event-popup.js   # Modal de detalhes
│   │   └── meus-agendamentos.js # Lista de agendamentos
│   ├── super_admin/         # Painel super admin
│   │   ├── gerenciar-usuarios.js # Gestão de usuários
│   │   ├── gerenciar-agendamentos.js # Gestão de agendamentos
│   │   ├── relatorios.js    # Sistema de relatórios
│   │   └── enviar-notificacao-global.js # Notificações globais
│   └── users/               # Painel do usuário
│       ├── perfil-page.js   # Página de perfil
│       └── profile.js       # Validações de perfil
```

### 1. **Calendário Interativo** (`calendar.js`)

**Funcionalidades:**
- Seleção visual de data e período
- Navegação entre meses via AJAX
- Indicadores visuais de disponibilidade
- Validação de datas passadas
- Sincronização com formulário

**Características:**
```javascript
/**
 * Calendário Interativo de Agendamentos
 * 
 * Funcionalidades:
 * - Seleção visual de data e período (primeiro/segundo)
 * - Navegação entre meses via AJAX (sem recarregar página)
 * - Indicadores visuais de disponibilidade:
 *   * Verde: Horário disponível
 *   * Vermelho: Horário ocupado
 *   * Cinza: Data passada (desabilitado)
 * - Validação de datas passadas (não permite seleção)
 * - Sincronização com campos hidden do formulário
 * - Feedback visual da seleção atual
 * 
 * Períodos:
 * - Primeiro: 19:15 - 20:55
 * - Segundo: 21:10 - 22:50
 * 
 * Integração:
 * - Endpoint AJAX: /calendario-partial
 * - Campos do formulário: data_agendamento, periodo
 * - Botão de envio habilitado apenas após seleção completa
 */
```

### 2. **Sistema de Notificações** (`notifications.js`)

**Funcionalidades:**
- Polling automático a cada 30 segundos
- Badge com contador de não lidas
- Dropdown com lista de notificações
- Marcar como lida individual ou em massa
- Ícones personalizados por tipo

**Características:**
```javascript
/**
 * Sistema de Notificações em Tempo Real
 * 
 * Funcionalidades:
 * - Polling automático a cada 30 segundos
 * - Badge com contador de não lidas
 * - Dropdown com lista de notificações
 * - Marcar notificações como lidas individualmente ou em massa
 * - Ícones personalizados por tipo de notificação
 * - Som de notificação (opcional)
 * 
 * Tipos de Notificação Suportados:
 * - agendamento_aprovado: ✅ Seu agendamento foi aprovado
 * - agendamento_rejeitado: ❌ Agendamento rejeitado
 * - agendamento_cancelado: ⚠️ Evento cancelado
 * - presenca_confirmada: ✅ Presença confirmada
 * - lembrete_evento: 📅 Lembrete de evento
 * - info: ℹ️ Informação geral
 * - aviso: ⚠️ Aviso importante
 * 
 * Integração Backend:
 * - GET /notifications - Busca notificações
 * - POST /notifications/read - Marca como lida
 */
```

### 3. **Formulário de Agendamento** (`event-form.js`)

**Funcionalidades:**
- Controle dinâmico de campos
- Validação condicional
- Campos "Outro" personalizados
- Feedback visual das seleções

**Características:**
```javascript
/**
 * Controle Dinâmico do Formulário de Agendamento
 * 
 * Funcionalidades:
 * - Mostrar/ocultar campos conforme tipo de evento (esportivo/não-esportivo)
 * - Mostrar/ocultar campos de materiais conforme necessidade
 * - Validação de campos condicionais
 * - Controle de campos "Outro" personalizados
 * - Feedback visual das seleções
 * 
 * Lógica Condicional:
 * - Se Esportivo: mostra subtipo, esporte, participantes
 * - Se Não Esportivo: mostra subtipo alternativo, público alvo
 * - Se Possui Materiais: mostra lista de materiais e responsabilização
 * - Se Evento Aberto ao Público: mostra descrição do público alvo
 * - Se subtipo "Outro": mostra campo de texto para especificar
 */
```

### 4. **Modal de Eventos** (`event-popup.js`)

**Funcionalidades:**
- Busca detalhes via AJAX
- Exibe informações formatadas
- Lista de participantes
- Botão de imprimir/PDF
- Design responsivo

**Características:**
```javascript
/**
 * Sistema de Popup de Detalhes do Evento
 * 
 * Funcionalidades:
 * - Busca detalhes do evento via AJAX
 * - Exibe todas as informações formatadas
 * - Lista de participantes confirmados
 * - Botão de imprimir/salvar PDF
 * - Design responsivo
 * - Fechamento por overlay ou botão X
 * - Animações suaves de abertura/fechamento
 * 
 * Informações Exibidas:
 * - Tipo e subtipo do evento
 * - Data, horário e período
 * - Responsável e solicitante
 * - Descrição completa
 * - Materiais necessários
 * - Lista de participantes (se houver)
 * - Infraestrutura adicional
 * - Observações administrativas
 * 
 * Integração:
 * - Endpoint: GET /agendamento/detalhes?id=X
 * - Retorna JSON com todos os dados do evento
 */
```

### 5. **Página de Agenda** (`agenda.js`)

**Funcionalidades:**
- Toggle entre eventos esportivos e não esportivos
- Toggle de eventos passados
- Gerenciamento de presenças via AJAX
- Integração com popup de detalhes

**Características:**
```javascript
/**
 * JavaScript para a página da Agenda
 * 
 * Funcionalidades:
 * - Toggle entre eventos esportivos e não esportivos
 * - Toggle de eventos passados
 * - Gerenciamento de presenças via AJAX
 * - Integração com popup de detalhes do evento
 */
```

### 6. **Sistema de Relatórios** (`relatorios.js`)

**Funcionalidades:**
- Filtros avançados
- Geração de relatórios
- Exportação para impressão
- Validação de formulários

**Características:**
```javascript
/**
 * Sistema de Relatórios Avançados
 * 
 * Funcionalidades:
 * - Filtros por período, tipo, status
 * - Geração de relatórios em tempo real
 * - Exportação para impressão
 * - Validação de formulários
 * - Feedback visual de carregamento
 */
```

---

## 🎨 Sistema CSS e Styling

### Estrutura CSS Modular

```
public/css/
├── default.css                    # Estilos globais e variáveis
├── components/                    # Componentes reutilizáveis
│   ├── agenda.css                # Página de agenda
│   ├── calendar.css              # Calendário interativo
│   ├── event-popup.css           # Modal de eventos
│   └── notifications.css         # Sistema de notificações
├── pages/                        # Páginas específicas
│   ├── auth/                     # Autenticação
│   │   ├── login.css             # Página de login
│   │   └── register.css          # Página de registro
│   ├── dashboard.css             # Painéis administrativos
│   ├── profile.css                # Página de perfil
│   └── super_admin/              # Painel super admin
│       └── gerenciar-usuarios.css # Gestão de usuários
└── partials/                     # Componentes de layout
    ├── header.css                # Navegação
    └── footer.css                # Rodapé
```

### 1. **Estilos Globais** (`default.css`)

**Variáveis CSS:**
```css
:root {
    --blue-color: #0900FF;
    --textcolor-blue: #004386;
    --orange-color: #F28100;
    --background-color: #ffffff;
    --text-color: #333333;
    --gradient-color-login: linear-gradient(90deg,rgba(9, 0, 255, 1) 0%, rgba(0, 0, 0, 1) 50%, rgba(242, 129, 0, 1) 100%);
    --gradient-color-text: linear-gradient(90deg,rgba(9, 0, 255, 1) 0%, rgba(242, 129, 0, 1) 100%);
    --orange-color-opacity: rgba(242, 129, 0, 0.20);
    --blue-color-opacity: rgba(9, 0, 255, 0.20);
    --shadow-carousel: 1px 10px 39px -8px rgba(0,0,0,0.75);
    --shadow-card: 0 4px 12px rgba(0,0,0,0.4);
    --shadow-card-hover: 0 6px 20px rgba(0,0,0,0.45);
    --shadow-light: 0 2px 6px rgba(0,0,0,0.08);
}
```

**Tipografia:**
```css
body {
    font-family: 'Montserrat', Arial, sans-serif;
    min-height: 100vh;
}
```

### 2. **Páginas de Autenticação** (`login.css`)

**Características:**
- Background com imagem
- Cards com glassmorphism
- Formulários responsivos
- Validação visual

```css
body.auth-body {
    background: #0b3aa7;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
}

.auth-background {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    background-image: url('/img/bg-auth-desktop.webp');
    background-repeat: no-repeat;
    background-position: center center;
    background-size: cover;
    opacity: 1;
    z-index: 1;
}

.auth-card {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}
```

### 3. **Dashboard** (`dashboard.css`)

**Características:**
- Grid de atalhos responsivo
- Cards com hover effects
- Animações suaves
- Layout flexível

```css
.dashboard-shortcuts {
    display: flex;
    flex-direction: column;
    gap: 24px;
    margin-bottom: 32px;
}

.shortcut-card {
    flex: 1;
    background: #fff;
    border-radius: 12px;
    box-shadow: var(--shadow-card);
    padding: 32px 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: box-shadow 0.3s;
}

.shortcut-card:hover {
    box-shadow: var(--shadow-card-hover);
}
```

### 4. **Página de Perfil** (`profile.css`)

**Características:**
- Cards com gradientes
- Formulários estilizados
- Botões personalizados
- Responsividade

```css
.profile-card {
    border: none;
    border-radius: 15px;
    box-shadow: var(--shadow-card);
    transition: transform .3s ease, box-shadow .3s ease;
    margin-bottom: 25px;
    overflow: hidden;
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-card-hover);
}

.profile-card .card-header {
    background: linear-gradient(135deg, var(--blue-color), var(--orange-color));
    color: #fff;
    font-weight: 700;
    font-size: 1.1rem;
    border: none;
    padding: 15px 20px;
}
```

### 5. **Calendário** (`calendar.css`)

**Características:**
- Grid responsivo
- Indicadores visuais
- Hover effects
- Estados de disponibilidade

```css
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.calendar-day {
    background: #fff;
    padding: 12px 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.calendar-day.available {
    background: #d4edda;
    color: #155724;
}

.calendar-day.occupied {
    background: #f8d7da;
    color: #721c24;
}

.calendar-day.disabled {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}
```

### 6. **Sistema de Notificações** (`notifications.css`)

**Características:**
- Dropdown animado
- Badge com contador
- Ícones por tipo
- Estados de leitura

```css
.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    min-width: 300px;
    max-height: 400px;
    overflow-y: auto;
    z-index: 1000;
}
```

---

## 🐳 Docker e Infraestrutura

### Docker Compose

**Configuração completa:**
```yaml
services:
  apache:
    build: .
    image: 'php-8-apache-mc'
    container_name: php
    restart: always
    ports:
      - '80:80'
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    
  db:
    image: mysql
    container_name: mysql
    restart: always
    command: --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci
    environment:
      MYSQL_DATABASE: application
      MYSQL_USER: appuser
      MYSQL_PASSWORD: apppass
      MYSQL_ROOT_PASSWORD: rootpass
    volumes:
      - ./assets/data:/docker-entrypoint-initdb.d
    ports:
      - '3306:3306'

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: phpmyadmin
    restart: always
    ports:
      - '8080:80'
    environment:
      PMA_HOST: db
      PMA_USER: root
      PMA_PASSWORD: rootpass
    depends_on:
      - db
```

### Dockerfile

**Imagem PHP 8.2 + Apache:**
```dockerfile
FROM php:8.2-apache as final

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN apt-get update && apt-get install -y git unzip && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql && a2enmod rewrite && a2enmod actions

# Allow Composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy the rest of the application
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
```

### DevContainer

**Configuração para desenvolvimento:**
```json
{
    "name": "SGE Development",
    "dockerFile": "Dockerfile",
    "forwardPorts": [80, 3306, 8080],
    "postCreateCommand": "composer install",
    "customizations": {
        "vscode": {
            "extensions": [
                "ms-vscode.vscode-json",
                "bradlc.vscode-tailwindcss",
                "formulahendry.auto-rename-tag"
            ]
        }
    }
}
```

---

## 🚀 Instalação e Configuração

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
- Composer instala dependências automaticamente
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

### Credenciais de Acesso

Após popular o banco, você pode fazer login com as seguintes credenciais:

#### Super Admin (Acesso Total)
```
Email/RA: sadmin
Senha: sadmin
```

#### Admin de Atlética
```
Email: admin.atletica@sge.com
Senha: sadmin
```

#### Usuário Comum (Aluno)
```
Email: aluno@sge.com
Senha: sadmin
```

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
```

---

## 🔌 API e Endpoints

### Endpoints AJAX

#### 1. **Sistema de Notificações**

**GET `/notifications`**
- **Descrição**: Lista notificações do usuário
- **Autenticação**: Obrigatória
- **Resposta**:
```json
{
    "success": true,
    "notifications": [
        {
            "id": 1,
            "titulo": "Agendamento Aprovado! ✅",
            "mensagem": "Seu agendamento 'Treino de Futsal' foi aprovado.",
            "tipo": "agendamento_aprovado",
            "lida": false,
            "data_criacao": "2025-01-15 10:30:00"
        }
    ]
}
```

**POST `/notifications/read`**
- **Descrição**: Marca notificação como lida
- **Parâmetros**: `notification_id`
- **Resposta**:
```json
{
    "success": true,
    "message": "Notificação marcada como lida"
}
```

#### 2. **Calendário e Agendamentos**

**GET `/calendario-partial`**
- **Descrição**: HTML do calendário para AJAX
- **Parâmetros**: `mes`, `ano`
- **Resposta**: HTML do calendário

**GET `/agendamento/detalhes`**
- **Descrição**: Detalhes completos de um evento
- **Parâmetros**: `id`
- **Resposta**:
```json
{
    "success": true,
    "evento": {
        "id": 1,
        "titulo": "Treino de Futsal",
        "tipo": "esportivo",
        "data": "2025-01-20",
        "periodo": "primeiro",
        "responsavel": "João Silva",
        "descricao": "Treino da atlética",
        "participantes": ["123456", "789012"]
    }
}
```

#### 3. **Sistema de Presenças**

**POST `/agenda/presenca`**
- **Descrição**: Marcar/desmarcar presença
- **Parâmetros**: `agendamento_id`, `acao`
- **Resposta**:
```json
{
    "success": true,
    "message": "Presença confirmada",
    "total_presencas": 15
}
```

### Estrutura de Respostas JSON

#### Sucesso
```json
{
    "success": true,
    "data": { ... },
    "message": "Operação realizada com sucesso"
}
```

#### Erro
```json
{
    "success": false,
    "message": "Erro ao processar solicitação",
    "error": "Detalhes do erro"
}
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

## 🛠️ Desenvolvimento

### Estrutura do Projeto

```
sge/
├── 📂 .devcontainer/             # Configuração GitHub Codespaces
│   ├── devcontainer.json        # Config: ports, postCreateCommand
│   └── Dockerfile               # Imagem customizada para dev
│
├── 📂 assets/                    # Recursos do banco de dados
│   ├── data/
│   │   └── 0-schema.sql         # Estrutura do banco (auto-executado)
│   └── seeds/
│       ├── db_populate.sql      # Dados de exemplo (execução manual)
│       └── README.md
│
├── 📂 public/                    # DocumentRoot (ponto de entrada web)
│   ├── 📄 index.php             # ⭐ Front Controller
│   ├── 📄 .htaccess             # Regras de reescrita Apache
│   ├── 📂 css/                  # Estilos CSS modulares
│   ├── 📂 js/                   # Scripts JavaScript modulares
│   ├── 📂 img/                  # Imagens e logos
│   └── 📂 doc/                  # Documentos públicos
│
├── 📂 src/                       # Código da aplicação
│   ├── 📄 routes.php            # Definição de todas as rotas
│   ├── 📂 Controller/           # Camada de controle (MVC)
│   ├── 📂 Repository/           # Camada de dados (Data Access Layer)
│   └── 📂 Core/                 # Classes principais do framework
│
├── 📂 views/                     # Templates (Views do MVC)
│   ├── 📂 _partials/            # Componentes reutilizáveis
│   ├── 📂 auth/                 # Autenticação
│   ├── 📂 usuario/              # Painel do usuário
│   ├── 📂 pages/                # Páginas gerais
│   ├── 📂 admin_atletica/       # Painel admin atlética
│   └── 📂 super_admin/          # Painel super admin
│
├── 📂 vendor/                    # Dependências do Composer
├── 📄 composer.json              # Configuração do Composer
├── 📄 docker-compose.yml         # Orquestração Docker
├── 📄 Dockerfile                 # Imagem PHP + Apache
└── 📄 README.md                  # Documentação
```

### Convenções de Código

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

### Padrões Utilizados

| Padrão | Onde | Benefício |
|--------|------|-----------|
| **Singleton** | Connection.php | Uma única conexão DB |
| **Repository** | Repository/* | Abstração de dados |
| **MVC** | Todo projeto | Separação de responsabilidades |
| **Front Controller** | index.php | Ponto único de entrada |
| **Dependency Injection** | Controllers | Testabilidade |
| **Service Layer** | Services | Lógica de negócio reutilizável |

### Comandos Úteis do Docker

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

# Acessar terminal do container PHP
docker exec -it php bash

# Acessar terminal do MySQL
docker exec -it mysql mysql -uroot -prootpass application
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

*Documentação Técnica | Versão 1.0 | Outubro 2025*

[![Made with PHP](https://img.shields.io/badge/Made%20with-PHP-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-9.4-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker&logoColor=white)](https://www.docker.com/)

</div>
