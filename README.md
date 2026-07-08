# AtendeLab

Clone funcional do sistema **AtendeLab – Controle de atendimentos acadêmicos**,
feito em **PHP puro (padrão MVC) + MySQL**, para rodar no **XAMPP**.

## Recursos

- Login com sessão (usuário administrador criado automaticamente)
- Dashboard com totais e atendimentos recentes
- **Pessoas atendidas** – cadastro, edição e inativação (sem excluir histórico)
- **Tipos de atendimento** – cadastro, edição e inativação
- **Atendimentos** – registro e alternância de status (aberto ↔ concluido)
- Banco de dados, tabelas e dados iniciais criados **automaticamente** na 1ª execução

## Estrutura

```
atendelab/
├── config/config.php        # dados de conexão (host, usuário, senha)
├── public/                  # raiz web (entrada da aplicação)
│   ├── index.php            # front controller / roteador
│   └── assets/css/style.css
├── app/
│   ├── Core/                # Database, Controller, Auth, helpers
│   ├── Controllers/         # AuthController, FrontendController
│   ├── Models/              # Usuario, Pessoa, Tipo, Atendimento
│   └── Views/               # login, dashboard, pessoas, tipos, atendimentos
└── sql/schema.sql           # schema de referência (import manual opcional)
```

## Como rodar (XAMPP)

1. Copie a pasta `atendelab` para `C:\xampp\htdocs\`
   (fica `C:\xampp\htdocs\atendelab\`).
2. Abra o **XAMPP Control Panel** e inicie **Apache** e **MySQL**.
3. Acesse no navegador:

   ```
   http://localhost/atendelab/public/
   ```

   Na primeira vez, o banco `atendelab`, as tabelas e os dados de exemplo
   são criados automaticamente.

## Login padrão

| Campo   | Valor                  |
|---------|------------------------|
| E-mail  | `admin@atendelab.com`  |
| Senha   | `admin123`             |

> Para trocar o login padrão, edite `config/config.php` **antes** da primeira
> execução (ou altere direto na tabela `usuarios`).

## Configuração do banco

Ajuste em `config/config.php` se o seu MySQL usa outra senha/porta:

```php
'db' => [
    'host' => '127.0.0.1',
    'port' => 3306,
    'name' => 'atendelab',
    'user' => 'root',
    'pass' => '',        // no XAMPP padrão o root não tem senha
],
```

## Requisitos

- PHP 8.0+ (com extensão **PDO MySQL**)
- MySQL / MariaDB
