# SGE - Sistema de Gerenciamento de Eventos (UNIFIO)

Sistema web completo para gerenciamento de agendamentos de quadras esportivas, administração de atléticas, cursos e usuários da UNIFIO. O sistema oferece funcionalidades abrangentes desde o agendamento de eventos até relatórios detalhados, com diferentes níveis de acesso e um calendário interativo.

Desenvolvido com arquitetura MVC moderna e containerizado com **Docker**, garantindo um ambiente de desenvolvimento consistente, seguro, escalável e de fácil manutenção.

## 🏗️ Arquitetura e Tecnologias

### Stack Tecnológica

-   **Docker & Docker Compose:** Ambiente completamente containerizado com serviços isolados
    -   **Web Server:** PHP 8.2 + Apache com mod_rewrite habilitado
    -   **Database:** MySQL 9.4 com timezone configurado para America/Sao_Paulo  
    -   **Database Management:** phpMyAdmin para administração visual do banco
-   **PHP 8.2:** Linguagem principal com extensões PDO, PDO_MySQL e Intl
-   **MySQL:** Sistema de gerenciamento de banco de dados relacional
-   **Composer:** Gerenciador de dependências com autoloading PSR-4
-   **JavaScript/AJAX:** Interações assíncronas para calendário e notificações

### Arquitetura MVC

-   **Models (Repository Pattern - `src/Repository/`):** 
    -   Isolamento completo da lógica de banco de dados
    -   Repositories especializados por entidade (Usuario, Agendamento, Atletica, etc.)
-   **Views (Template Engine - `views/`):** 
    -   Templates PHP organizados por funcionalidade
    -   Partials reutilizáveis (header, footer, calendar)
    -   Layouts responsivos com CSS moderno
-   **Controllers (Business Logic - `src/Controller/`):** 
    -   Controladores especializados por domínio
    -   Sistema de autenticação e autorização integrado
    -   Validação de dados e tratamento de erros

### Funcionalidades Técnicas

-   **Roteamento:** Sistema de rotas RESTful com Router personalizado
-   **Autenticação:** Login com verificação em duas etapas (2FA simulado)
-   **Autorização:** Sistema de roles (usuario, admin, superadmin) e permissões granulares
-   **Sessões:** Gerenciamento seguro de sessões PHP
-   **API REST:** Endpoints JSON para notificações e interações AJAX
-   **Calendário Interativo:** Interface de agendamento com navegação assíncrona

## 📁 Estrutura do Projeto

Organização seguindo as melhores práticas de projetos web modernos, com separação clara entre código público e privado.

```
sge/
├── assets/                     # Recursos e dados do projeto
│   └── data/
│       ├── 0-schema.sql        # Schema completo do banco de dados
│       ├── db_populate.sql     # Dados de exemplo para desenvolvimento
│       └── db_vazia.sql        # Schema limpo para produção
├── public/                     # 🌐 Raiz pública do site (DocumentRoot)
│   ├── index.php               # 🎯 Front Controller (ponto de entrada único)
│   ├── css/                    # Estilos da aplicação
│   │   ├── auth.css            # Estilos para autenticação
│   │   ├── calendar.css        # Estilos do calendário interativo
│   │   └── default.css         # Estilos globais da aplicação
│   ├── js/                     # Scripts JavaScript
│   │   └── calendar.js         # Lógica do calendário AJAX
│   └── img/                    # Imagens e assets visuais
├── src/                        # 🔒 Código da aplicação (não acessível via web)
│   ├── Controller/             # 🎮 Controladores MVC
│   │   ├── AuthController.php          # Autenticação e registro
│   │   ├── HomeController.php          # Página inicial e redirecionamentos
│   │   ├── UsuarioController.php       # Dashboard e perfil do usuário
│   │   ├── AgendamentoController.php   # Gestão de agendamentos
│   │   ├── AgendaController.php        # Visualização de eventos públicos
│   │   ├── AdminAtleticaController.php # Painel do admin de atlética
│   │   ├── SuperAdminController.php    # Painel do super administrador
│   │   ├── NotificationController.php  # API de notificações
│   │   └── BaseController.php          # Controlador base
│   ├── Core/                   # 🔧 Classes centrais do sistema
│   │   ├── Auth.php            # Sistema de autenticação e autorização
│   │   ├── Connection.php      # Conexão PDO com MySQL
│   │   ├── Router.php          # Roteador de URLs
│   │   └── helpers.php         # Funções utilitárias globais
│   ├── Repository/             # 🗄️ Camada de acesso aos dados
│   │   ├── UsuarioRepository.php       # Gestão de usuários
│   │   ├── AgendamentoRepository.php   # Gestão de agendamentos
│   │   ├── AtleticaRepository.php      # Gestão de atléticas
│   │   ├── CursoRepository.php         # Gestão de cursos
│   │   ├── ModalidadeRepository.php    # Gestão de modalidades esportivas
│   │   ├── RelatorioRepository.php     # Geração de relatórios
│   │   ├── NotificationRepository.php  # Sistema de notificações
│   │   └── AdminAtleticaRepository.php # Funcionalidades de admin
│   └── routes.php              # 🗺️ Definição de todas as rotas da aplicação
├── views/                      # 🎨 Templates e interfaces
│   ├── _partials/              # Componentes reutilizáveis
│   │   ├── header.php          # Cabeçalho com navegação
│   │   ├── footer.php          # Rodapé da aplicação
│   │   └── calendar.php        # Componente do calendário
│   ├── auth/                   # Interfaces de autenticação
│   │   ├── login.view.php              # Tela de login
│   │   ├── login-verify.view.php       # Verificação 2FA
│   │   ├── registro.view.php           # Cadastro de usuários
│   │   ├── esqueci-senha.view.php      # Recuperação de senha
│   │   └── redefinir-senha.view.php    # Redefinição de senha
│   ├── usuario/                # Painel do usuário comum
│   │   ├── dashboard.view.php          # Dashboard principal
│   │   └── perfil.view.php             # Gestão de perfil
│   ├── pages/                  # Páginas principais
│   │   ├── agenda.view.php             # Agenda pública de eventos
│   │   ├── agendar-evento.view.php     # Formulário de agendamento
│   │   ├── editar-agendamento.view.php # Edição de agendamentos
│   │   └── meus-agendamentos.view.php  # Lista de agendamentos do usuário
│   ├── admin_atletica/         # Painel do admin de atlética
│   │   ├── dashboard.view.php          # Dashboard do admin
│   │   ├── gerenciar-membros.view.php  # Aprovação de membros
│   │   ├── gerenciar-inscricoes.view.php # Gestão de inscrições
│   │   └── gerenciar-eventos.view.php  # Gestão de participação em eventos
│   └── super_admin/            # Painel do super administrador
│       ├── dashboard.view.php          # Dashboard administrativo
│       ├── gerenciar-usuarios.view.php # CRUD de usuários
│       ├── gerenciar-agendamentos.view.php # Aprovação de agendamentos
│       ├── gerenciar-estrutura.view.php    # Gestão de cursos e atléticas
│       ├── gerenciar-modalidades.view.php  # CRUD de modalidades
│       ├── gerenciar-admins.view.php       # Promoção de administradores
│       ├── relatorios.view.php             # Sistema de relatórios
│       └── relatorio-print.view.php        # Versão para impressão
├── vendor/                     # 📦 Dependências do Composer
├── .env                        # ⚙️ Variáveis de ambiente
├── .gitignore                  # 🚫 Arquivos ignorados pelo Git
├── composer.json               # 📋 Configuração do Composer
├── composer.lock               # 🔒 Lock das versões das dependências
├── Dockerfile                  # 🐳 Imagem Docker da aplicação
├── docker-compose.yml          # 🐙 Orquestração dos contêineres
└── README.md                   # 📖 Documentação do projeto
```

## 🚀 Como Rodar o Projeto

### Pré-requisitos

1.  **Docker Desktop** instalado e em execução
2.  **Git** para clonar o repositório
3.  **VS Code** com a extensão **"Dev Containers"** da Microsoft (opcional, mas recomendado)

### Serviços Docker

O projeto utiliza 3 contêineres Docker:

- **sge-php**: Aplicação PHP 8.2 + Apache (porta 80)
- **sge-db**: MySQL 9.4 (porta 3306) 
- **phpmyadmin**: Interface web para MySQL (porta 8080)

### Passos para a Instalação

1.  **Clonar o Repositório:**
    ```bash
    git clone https://github.com/rafaeldiasgarcia/sge.git
    cd sge
    ```

2.  **Opção A - Usando Dev Container (Recomendado):**
    -   Abra a pasta do projeto no VS Code.
    -   O VS Code detectará a pasta `.devcontainer` e mostrará uma notificação no canto inferior direito. Clique em **"Reopen in Container"**.
    -   Aguarde o VS Code construir a imagem e iniciar o ambiente. O terminal integrado agora estará dentro do contêiner.
    -   Execute o Composer para gerar o autoloader:
        ```bash
        composer install
        ```

3.  **Opção B - Usando Docker Compose Diretamente:**
    -   Instale as dependências do PHP:
        ```bash
        docker-compose run --rm sge-php composer install
        ```
    -   Inicie os serviços:
        ```bash
        docker-compose up -d --build
        ```

4.  **Acessar a Aplicação:**
    - **Aplicação Principal:** [http://localhost](http://localhost)
    - **phpMyAdmin:** [http://localhost:8080](http://localhost:8080)
    
    ### Credenciais de Acesso Padrão
    
    - **Super Admin:** `sadmin` / `sadmin`
    - **Admin Atlética:** `admin.atletica@sge.com` / `sadmin`
    - **Aluno:** `aluno@sge.com` / `sadmin`
    - **Membro das Atléticas:** `membro@sge.com` / `sadmin`
    - **Comunidade Externa:** `comunidade@email.com` / `sadmin`

## ⚡ Funcionalidades Implementadas

Sistema completo com três níveis de acesso e funcionalidades especializadas para cada perfil de usuário.

### 🔐 Sistema de Autenticação

- **Login com 2FA:** Verificação em duas etapas com código temporário (simulado)
- **Registro Inteligente:** Validação de e-mail institucional e associação automática com atléticas
- **Recuperação de Senha:** Sistema completo com tokens seguros
- **Gestão de Sessões:** Controle seguro de sessões com regeneração de ID

### 👤 Painel do Usuário

#### Tipos de Usuário
- **Aluno:** Acesso básico à agenda e perfil
- **Membro de Atlética:** Funcionalidades de inscrição em modalidades
- **Professor:** Permissões de agendamento de eventos
- **Comunidade Externa:** Acesso limitado ao sistema

#### Funcionalidades Principais
- **Dashboard Personalizado:** Visão geral das atividades e notificações
- **Gestão de Perfil:** Edição de dados pessoais e solicitação de entrada em atléticas
- **Agenda Pública:** Visualização de todos os eventos aprovados com sistema de presenças
- **Sistema de Inscrições:** Solicitação de participação em modalidades esportivas

### 📅 Sistema de Agendamentos (Professores e Admins)

#### Calendário Interativo
- **Visualização em Tempo Real:** Status de ocupação por cores (livre/ocupado/indisponível)
- **Navegação AJAX:** Troca de meses sem recarregamento da página
- **Seleção Intuitiva:** Clique direto nos horários disponíveis
- **Responsivo:** Funciona perfeitamente em dispositivos móveis

#### Gestão de Eventos
- **Tipos de Evento:** Esportivos e não-esportivos com campos específicos
- **Validações Inteligentes:** 
  - Antecedência mínima de 4 dias (exceto campeonatos)
  - Verificação de conflitos de horário
  - Limite de treinos por atlética por semana
- **Estados de Solicitação:** Pendente, Aprovado, Rejeitado, Cancelado
- **Edição e Cancelamento:** Controle completo das solicitações próprias

### 🏆 Painel do Admin de Atlética

#### Dashboard Administrativo
- **Indicadores Visuais:** Estatísticas de membros pendentes, aprovados e modalidades ativas
- **Visão Geral:** Resumo das atividades da atlética

#### Gestão de Membros
- **Aprovação de Solicitações:** Controle de entrada de novos membros na atlética
- **Gerenciamento Ativo:** Visualização e gestão de todos os membros ativos
- **Histórico de Ações:** Registro de todas as aprovações e recusas

#### Gestão de Modalidades
- **Controle de Inscrições:** Aprovação/recusa de inscrições em modalidades esportivas
- **Gestão de Atletas:** Organização dos membros por modalidade
- **Acompanhamento de Performance:** Visualização da participação em eventos

#### Gestão de Eventos
- **Inscrição em Massa:** Inscrever membros da atlética em eventos aprovados
- **Controle de Participação:** Adicionar/remover participantes de eventos
- **Relatórios de Presença:** Acompanhamento da participação dos membros

### 👑 Painel do Super Administrador

#### Gestão de Agendamentos
- **Aprovação Final:** Controle absoluto sobre todas as solicitações de agendamento
- **Verificação de Conflitos:** Sistema automático de detecção de sobreposições
- **Gestão de Rejeições:** Possibilidade de adicionar motivos para rejeições
- **Histórico Completo:** Visualização de todos os agendamentos do sistema

#### Administração de Usuários
- **CRUD Completo:** Criar, visualizar, editar e excluir usuários
- **Gestão de Perfis:** Edição de informações pessoais e acadêmicas
- **Controle de Permissões:** Alteração de roles e tipos de usuário
- **Associações:** Gerenciamento de vínculos com cursos e atléticas

#### Estrutura Acadêmica
- **Gestão de Cursos:** CRUD completo com associação a atléticas e coordenadores
- **Administração de Atléticas:** Controle total das organizações atléticas
- **Vínculos Inteligentes:** Sistema de associação automática curso-atlética
- **Coordenadores:** Designação de professores como coordenadores de curso

#### Modalidades Esportivas
- **Catálogo Completo:** Gestão de todas as modalidades disponíveis
- **Modalidades Tradicionais:** Futsal, Vôlei, Basquete, Handebol, Natação, etc.
- **E-Sports:** League of Legends, CS:GO, Valorant
- **Modalidades Especiais:** Xadrez, Queimada, Tênis de Mesa

#### Gestão de Administradores
- **Promoção de Usuários:** Transformar membros em admins de suas atléticas
- **Controle Hierárquico:** Rebaixar administradores quando necessário
- **Auditoria:** Registro de todas as mudanças de permissão

#### Sistema de Relatórios Avançados

##### Tipos de Relatório
1. **Relatório por Período**
   - Estatísticas gerais de eventos no período
   - Lista detalhada de todos os eventos
   - Métricas de ocupação da quadra

2. **Relatório por Evento Específico**
   - Detalhes completos do evento
   - Lista de participantes com dados formatados
   - Controle de presenças confirmadas
   - Informações de responsáveis e materiais

3. **Relatório por Usuário**
   - Histórico completo de agendamentos do usuário
   - Participações em eventos
   - Estatísticas de presenças

##### Funcionalidades dos Relatórios
- **Filtros Inteligentes:** Seleção por data, evento ou usuário específico
- **Dados Detalhados:** Informações completas incluindo participantes e presenças
- **Versão para Impressão:** Layout otimizado para impressão/PDF
- **Exportação:** Relatórios prontos para documentação oficial

## 🗄️ Banco de Dados

### Estrutura Principal

O sistema utiliza um banco de dados MySQL com as seguintes tabelas principais:

#### Entidades Principais
- **`usuarios`**: Dados dos usuários com roles, vínculos acadêmicos e status de atlética
- **`cursos`**: Cursos da instituição com coordenadores e atléticas associadas
- **`atleticas`**: Organizações atléticas dos cursos
- **`modalidades`**: Modalidades esportivas disponíveis (15 modalidades cadastradas)

#### Sistema de Agendamentos
- **`agendamentos`**: Solicitações de eventos com dados completos e status
- **`presencas`**: Sistema de controle de presença em eventos
- **`inscricoes_eventos`**: Inscrições de membros de atléticas em eventos
- **`inscricoes_modalidade`**: Inscrições de membros em modalidades esportivas

#### Funcionalidades Especiais
- **Códigos de Verificação**: Campos para 2FA e recuperação de senha
- **Relacionamentos Complexos**: FKs com cascade e set null apropriados
- **Dados de Exemplo**: 36+ agendamentos, 21 usuários, 10 atléticas
- **Timezone**: Configurado para America/Sao_Paulo

### Scripts Disponíveis
- **`0-schema.sql`**: Schema completo com dados de exemplo
- **`db_populate.sql`**: Apenas dados para popular o banco
- **`db_vazia.sql`**: Schema limpo para produção

## 🛠️ Desenvolvimento

### Padrões de Código
- **PSR-4**: Autoloading de classes
- **MVC**: Separação clara de responsabilidades
- **Repository Pattern**: Isolamento da lógica de banco
- **RESTful Routes**: URLs semânticas e organizadas

### Funcionalidades Técnicas Avançadas
- **AJAX**: Calendário e notificações assíncronas
- **Validação Robusta**: Validações client-side e server-side
- **Segurança**: Proteção contra SQL Injection, XSS e CSRF
- **Sessões Seguras**: Regeneração de ID e controle de timeout
- **Notificações**: Sistema de notificações em tempo real

## 🤝 Contribuindo

Para contribuir com o projeto:

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Faça commit das suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Faça push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

### Diretrizes de Desenvolvimento
- Siga os padrões PSR estabelecidos
- Documente adequadamente o código
- Teste as funcionalidades antes do commit
- Mantenha a compatibilidade com PHP 8.2+

## 📄 Licença

Este projeto foi desenvolvido para fins educacionais e institucionais da UNIFIO.

---

**Desenvolvido com ❤️ para a UNIFIO**
