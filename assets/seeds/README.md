# 🌱 Seeds - Dados de Exemplo

Esta pasta contém arquivos SQL com dados de exemplo para popular o banco de dados.

## 📄 Arquivos

### `db_populate.sql`
Contém dados de exemplo para todas as tabelas:
- **10 Atléticas** (A.A.A. FURIOSA, PREDADORA, etc)
- **15 Modalidades** (Futsal, Vôlei, Basquete, LoL, CS:GO, etc)
- **20+ Cursos** (Engenharias, Medicina, Direito, etc)
- **25+ Usuários** (Super Admin, Admins, Alunos, Professores)
- **Agendamentos de exemplo**
- **Inscrições em modalidades**
- **Notificações**

## 🚀 Como Usar

### Via Terminal (Recomendado)

```bash
docker exec -i mysql mysql -uroot -prootpass application < assets/seeds/db_populate.sql
```

### Via phpMyAdmin

1. Acesse http://localhost:8080 (ou a porta 8080 no Codespaces)
2. Login: `root` / Senha: `rootpass`
3. Selecione o banco `application` na barra lateral esquerda
4. Clique na aba **SQL**
5. Copie todo o conteúdo do arquivo `db_populate.sql`
6. Cole no editor SQL
7. Clique em **Executar** (botão "Go")

## ⚠️ Importante

- O banco é criado **vazio** por padrão (apenas estrutura)
- Você **precisa** popular manualmente com este arquivo
- Os dados são apenas para **desenvolvimento/testes**
- Em **produção**, não use estes dados!

## 📌 Nota sobre Encoding

Para compatibilidade, o enum `tipo_usuario_detalhado` usa:
- ✅ `"Membro das Atleticas"` (sem acento)
- ✅ `"Professor"`
- ✅ `"Aluno"`  
- ✅ `"Comunidade Externa"`

## 🔑 Credenciais Após Popular

### Super Admin
- Email: `sadmin`
- Senha: `sadmin`

### Admin de Atlética
- Email: `admin.atletica@sge.com`
- Senha: `sadmin`

### Usuário Comum
- Email: `aluno@sge.com`
- Senha: `sadmin`

*Veja mais credenciais no README.md principal*
