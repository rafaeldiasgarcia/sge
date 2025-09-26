# SGE - Sistema de Gerenciamento de Eventos (UNIFIO)

Este projeto é um sistema web completo para o gerenciamento de agendamentos de quadras, atléticas, cursos e usuários, desenvolvido para a UNIFIO.

O sistema foi estruturado em uma arquitetura moderna MVC (Model-View-Controller) e containerizado com **Docker**, garantindo um ambiente de desenvolvimento consistente, seguro, escalável e de fácil manutenção.

## Arquitetura e Tecnologias

O projeto utiliza uma stack moderna para desenvolvimento PHP:

-   **Docker & Docker Compose:** Todo o ambiente (servidor web, PHP e banco de dados) é gerenciado por contêineres, eliminando problemas de configuração entre diferentes máquinas.
-   **PHP 8.2 + Apache:** A base da aplicação, rodando em um contêiner Apache otimizado.
-   **MySQL:** O banco de dados, rodando em um contêiner separado e persistindo os dados em um volume.
-   **Composer:** Gerenciador de dependências responsável pelo autoloading de classes (padrão PSR-4), eliminando a necessidade de `require_once` manuais.
-   **Arquitetura MVC:**
    -   **Models (Camada de Dados - `src/Repository/`):** Classes que isolam toda a interação com o banco de dados.
    -   **Views (Camada de Apresentação - `views/`):** Templates responsáveis apenas por exibir os dados.
    -   **Controllers (Camada de Controle - `src/Controller/`):** Classes que orquestram a lógica, recebendo requisições, interagindo com os repositórios e renderizando as views.

## Estrutura do Projeto

A estrutura foi organizada para seguir as melhores práticas de projetos web modernos, com uma pasta `public` como ponto de entrada e o código da aplicação isolado.

```
sge/
├── .devcontainer/              # Configuração para desenvolvimento em contêiner (VS Code, Codespaces)
├── .vscode/                    # Configurações específicas do VS Code
├── arquivos/                   # Documentação e arquivos auxiliares
├── assets/
│   └── data/
│       └── 0-schema.sql        # Script SQL para inicialização do banco de dados
├── public/                     # <-- Raiz do site (DocumentRoot)
│   ├── .htaccess               # Redireciona tudo para o index.php
│   ├── index.php               # Ponto de Entrada Único (Front Controller)
│   ├── css/                    # Arquivos de estilo (CSS)
│   │   ├── calendar.css        # Estilos para o calendário
│   │   └── default.css         # Estilos padrão da aplicação
│   └── js/                     # Arquivos de script (JavaScript)
│       └── calendar.js         # Script do calendário interativo
├── src/                        # <-- Coração da aplicação (não acessível via web)
│   ├── Controller/             # Controladores (lógica da aplicação)
│   │   ├── AdminAtleticaController.php
│   │   ├── AgendaController.php
│   │   ├── AgendamentoController.php
│   │   ├── AuthController.php
│   │   ├── BaseController.php
│   │   ├── HomeController.php
│   │   ├── NotificationController.php
│   │   ├── SuperAdminController.php
│   │   └── UsuarioController.php
│   ├── Core/                   # Classes centrais do sistema
│   │   ├── Auth.php            # Sistema de autenticação
│   │   ├── Connection.php      # Conexão com banco de dados
│   │   ├── helpers.php         # Funções auxiliares
│   │   └── Router.php          # Roteador da aplicação
│   ├── Repository/             # Camada de acesso a dados (SQL)
│   │   ├── AdminAtleticaRepository.php
│   │   ├── AgendamentoRepository.php
│   │   ├── AtleticaRepository.php
│   │   ├── CursoRepository.php
│   │   ├── ModalidadeRepository.php
│   │   ├── NotificationRepository.php
│   │   ├── RelatorioRepository.php
│   │   └── UsuarioRepository.php
│   └── routes.php              # Mapa de todas as URLs da aplicação
├── vendor/                     # <-- Pasta gerenciada pelo Composer (autoloader, dependências)
├── views/                      # <-- Arquivos de template (HTML com PHP)
│   ├── _partials/              # Componentes reutilizáveis
│   │   ├── calendar.php
│   │   ├── footer.php
│   │   └── header.php
│   ├── admin_atletica/         # Views para administradores de atlética
│   ├── auth/                   # Views para autenticação
│   ├── pages/                  # Views para páginas gerais
│   ├── super_admin/            # Views para super administrador
│   └── usuario/                # Views para usuários comuns
├── .env                        # <-- Arquivo de variáveis de ambiente (credenciais do DB)
├── .gitignore                  # Arquivos ignorados pelo Git
├── .htaccess                   # Redireciona requisições da raiz para a pasta /public
├── Dockerfile                  # "Planta" para construir a imagem do contêiner PHP/Apache
├── composer.json               # Define as dependências e o autoloading do projeto
├── composer.lock               # Versões fixas das dependências
└── docker-compose.yml          # Orquestra a inicialização de todos os contêineres
```

## Como Rodar o Projeto

### Pré-requisitos

1.  **Docker Desktop** instalado e em execução.
2.  **Git** para clonar o repositório.
3.  **VS Code** com a extensão **"Dev Containers"** da Microsoft (opcional, mas recomendado).

### Passos para a Instalação

1.  **Clonar o Repositório:**
    ```bash
    git clone https://github.com/rafaeldiasgarcia/sge.git
    cd sge
    ```

2.  **Criar o Arquivo de Ambiente (`.env`):**
    Crie um arquivo chamado `.env` na raiz do projeto (se não existir). Ele conterá as credenciais e a URL da aplicação. Copie e cole o conteúdo abaixo, **ajustando a `APP_URL` se necessário** (para o Codespaces, use a URL fornecida; para rodar localmente, use `http://localhost`).

    ```env
    # Credenciais do Banco de Dados
    DB_HOST=sge-db
    DB_NAME=sge_db
    DB_USER=root
    DB_PASS=rootpass

    # URL pública da aplicação (essencial para gerar links corretos)
    APP_URL=http://localhost
    ```

3.  **Opção A - Usando Dev Container (Recomendado):**
    -   Abra a pasta do projeto no VS Code.
    -   O VS Code detectará a pasta `.devcontainer` e mostrará uma notificação no canto inferior direito. Clique em **"Reopen in Container"**.
    -   Aguarde o VS Code construir a imagem e iniciar o ambiente. O terminal integrado agora estará dentro do contêiner.
    -   Execute o Composer para gerar o autoloader:
        ```bash
        composer install
        ```

4.  **Opção B - Usando Docker Compose Diretamente:**
    -   Instale as dependências do PHP:
        ```bash
        docker-compose run --rm web composer install
        ```
    -   Inicie os serviços:
        ```bash
        docker-compose up -d --build
        ```

5.  **Acessar a Aplicação:**
    Abra seu navegador e acesse: **[http://localhost](http://localhost)**

## Funcionalidades Implementadas

O sistema conta com três níveis de acesso principais, cada um com seu conjunto de funcionalidades.

### 1. Painel do Usuário (Comum, Membro de Atlética, Professor)

-   **Autenticação e Gestão de Conta:** Sistema robusto de Registro, Login com verificação em dois passos (2FA simulado), e Recuperação de Senha. Os usuários podem gerenciar seus dados de perfil e alterar a senha de forma segura.
-   **Agendamento de Eventos com Calendário Interativo:** A funcionalidade central para usuários com permissão (Professores, Admins) foi aprimorada com um calendário dinâmico, proporcionando uma experiência de usuário moderna e intuitiva.
    -   **Visualização de Disponibilidade:** O calendário exibe em tempo real, através de um sistema de cores, os dias e horários que estão livres, parcialmente ocupados ou totalmente indisponíveis.
    -   **Navegação Assíncrona (AJAX):** O usuário pode navegar entre os meses sem recarregar a página.
    -   **Seleção Intuitiva:** Com um único clique em um horário vago, o sistema preenche os dados de data e período e direciona o usuário para o formulário.
-   **Agenda Pública da Quadra:** Todos os usuários podem visualizar a agenda de eventos já aprovados, permitindo que se programem e marquem ou desmarquem sua presença.
-   **Gerenciamento de Solicitações:** Usuários que podem agendar eventos têm uma área dedicada para acompanhar o status de suas solicitações (Pendente, Aprovado, Rejeitado), com opções para editar ou cancelar pedidos.
-   **Interação com Atléticas:** Membros de atléticas podem solicitar inscrição em diferentes modalidades esportivas, com o pedido sendo encaminhado para a aprovação do administrador de sua atlética.

### 2. Painel do Admin de Atlética

-   **Dashboard de Gestão:** Painel com indicadores visuais, como o número de membros pendentes e atletas já aprovados.
-   **Gerenciamento de Membros:** Ferramentas para aprovar ou recusar solicitações de alunos que desejam fazer parte da atlética.
-   **Gerenciamento de Inscrições:** Controle total sobre as inscrições dos membros em modalidades esportivas.
-   **Gestão de Participação em Eventos:** Capacidade de inscrever ou remover membros da atlética em eventos esportivos que já foram aprovados.

### 3. Painel do Super Admin

-   **Controle Total de Agendamentos:** O Super Admin tem a palavra final sobre o uso da quadra, podendo aprovar ou rejeitar solicitações, com verificação automática de conflitos.
-   **Gerenciamento Completo de Usuários:** Acesso para visualizar, editar (incluindo perfil e permissões) e excluir qualquer usuário do sistema.
-   **Administração da Estrutura Acadêmica:** Uma página unificada permite o gerenciamento completo (CRUD) de Cursos, Atléticas e a associação entre eles.
-   **Gestão de Modalidades:** Ferramentas para criar, editar e excluir os esportes disponíveis no sistema.
-   **Controle de Permissões:** Capacidade de promover membros de atléticas a administradores de suas respectivas atléticas.
-   **Sistema de Relatórios Avançados:**
    -   **Filtros Flexíveis:** Geração de relatórios por período, por evento específico ou por usuário.
    -   **Dados Detalhados:** Visualização de informações como público previsto, presenças confirmadas e listas de participantes.
    -   **Exportação e Impressão:** Todos os relatórios gerados possuem uma versão otimizada para impressão ou para salvar como PDF.

## Contribuindo

Para contribuir com o projeto:

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Faça commit das suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Faça push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.