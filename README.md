# AtendeLab — Sistema de Controle de Atendimentos Acadêmicos

Projeto PHP 8.x + PDO + MySQL + Sessões + Bootstrap + JavaScript (Fetch),
seguindo o fluxo view → `api.js` → `routes.php` → controller → PDO → banco,
conforme o material da Aula 05 (Frontend integrado ao backend).

## Estrutura

```
atendelab/
├── app/
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── FrontendController.php
│   │   ├── DashboardController.php
│   │   ├── PessoasController.php
│   │   ├── TiposAtendimentosController.php
│   │   └── AtendimentosController.php
│   ├── Middleware/
│   │   └── auth.php
│   └── Views/
│       ├── auth/login.php
│       ├── dashboard/index.php
│       ├── pessoas/index.php
│       ├── tipos-atendimentos/index.php
│       ├── atendimentos/index.php
│       └── layouts/{config-view,header,footer}.php
├── config/
│   └── database.php
├── database/
│   └── atendelab.sql
├── public/
│   ├── index.php
│   └── assets/{css/style.css,js/api.js}
└── routes.php
```

## Instalação (XAMPP)

1. Copie a pasta `atendelab` para dentro de `htdocs`.
2. Ative Apache e MySQL no XAMPP.
3. Importe o banco: abra o phpMyAdmin e execute `database/atendelab.sql`
   (ele cria o banco `atendelab`, as tabelas e os dados de teste).
4. Confira `config/database.php` — por padrão aponta para
   `host=127.0.0.1`, `usuário=root`, `senha=` (vazio).
5. Acesse `http://localhost/atendelab/public/`.
6. Entre com `admin@atendelab.com` / `123456`.

## Rotas principais

| Finalidade        | URL                                                                   |
|--------------------|-----------------------------------------------------------------------|
| Login              | `?controller=auth&action=login`                                       |
| Autenticar         | `?controller=auth&action=entrar` (POST)                               |
| Dashboard          | `?controller=auth&action=dashboard`                                   |
| Logout             | `?controller=auth&action=logout`                                      |
| Pessoas (tela)     | `?controller=frontend&action=pessoas`                                 |
| Tipos (tela)       | `?controller=frontend&action=tipos`                                   |
| Atendimentos (tela)| `?controller=frontend&action=atendimentos`                            |
| Resumo (JSON)      | `?controller=dashboard&action=resumo`                                 |
| Pessoas (JSON)     | `?controller=pessoas&action=listar\|buscar\|criar\|atualizar\|inativar` |
| Tipos (JSON)       | `?controller=tipos&action=listar\|buscar\|criar\|atualizar\|inativar`   |
| Atendimentos (JSON)| `?controller=atendimentos&action=listar\|criar\|alterarStatus\|visualizar` |

## Regras de negócio implementadas

- `usuario_id` do atendimento sempre vem de `$_SESSION['usuario']['id']`
  (nunca de um campo livre do formulário).
- "Inativar" pessoas e tipos apenas muda o `status`, nunca apaga o registro
  (o histórico de atendimentos depende de `pessoa_id`/`tipo_atendimento_id`).
- `routes.php` aceita tanto `buscar` quanto `buscarPorId` em `pessoas` e `tipos`,
  compatível com o que as views chamam via `api.js`.
- `footer.php` carrega o Bootstrap JS apenas uma vez; `api.js` é carregado
  uma única vez em `header.php` (evita o carregamento duplicado citado na aula).
- Perfil de usuário usa `admin`/`atendente` (nunca `administrador`).

## Banco de dados

Ver `database/atendelab.sql`. Já inclui:
- 2 usuários (`admin@atendelab.com` e `atendente@atendelab.com`, senha `123456`)
- 9 tipos de atendimento
- 16 pessoas (algumas inativas, para testar a preservação de histórico)
- 6 atendimentos de exemplo em diferentes status

## Observação

O layout `layouts/sidebar.php` citado no material (rotas antigas e perfil
`administrador`) foi propositalmente **omitido** deste projeto: o layout ativo
é `header.php` com navbar, conforme orientado na Aula 05.
