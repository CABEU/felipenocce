<?php
declare(strict_types=1);

/**
 * Ponto central que transforma parâmetros de URL em chamadas de controller.
 * Páginas visuais usam controller=frontend.
 * Operações de dados usam controller=pessoas, tipos, atendimentos ou dashboard.
 */

require_once __DIR__ . '/app/Middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action     = $_GET['action'] ?? 'login';

switch ($controller) {

    case 'auth':
        require_once __DIR__ . '/app/Controllers/AuthController.php';
        $authController = new AuthController();

        switch ($action) {
            case 'login':
                $authController->exibirLogin();
                break;
            case 'entrar':
                $authController->entrar();
                break;
            case 'dashboard':
                $authController->dashboard();
                break;
            case 'logout':
                $authController->logout();
                break;
            default:
                responderRotaNaoEncontrada('Ação de autenticação não encontrada.');
        }
        break;

    case 'frontend':
        require_once __DIR__ . '/app/Controllers/FrontendController.php';
        $frontendController = new FrontendController();

        switch ($action) {
            case 'pessoas':
                $frontendController->pessoas();
                break;
            case 'tipos':
                $frontendController->tiposAtendimentos();
                break;
            case 'atendimentos':
                $frontendController->atendimentos();
                break;
            default:
                responderRotaNaoEncontrada('Página de frontend não encontrada.');
        }
        break;

    case 'dashboard':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController();

        switch ($action) {
            case 'resumo':
                $dashboardController->resumo();
                break;
            default:
                responderRotaNaoEncontrada('Ação de dashboard não encontrada.');
        }
        break;

    case 'pessoas':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/PessoasController.php';
        $pessoasController = new PessoasController();

        switch ($action) {
            case 'listar':
                $pessoasController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $pessoasController->buscarPorId();
                break;
            case 'criar':
                $pessoasController->criar();
                break;
            case 'atualizar':
                $pessoasController->atualizar();
                break;
            case 'inativar':
                $pessoasController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de pessoas não encontrada.');
        }
        break;

    case 'tipos':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
        $tiposController = new TiposAtendimentosController();

        // Correção da Aula 006: a view chama AtendeLabApi.get("tipos", "buscar", { id }),
        // então o roteador aceita tanto "buscar" quanto "buscarPorId".
        switch ($action) {
            case 'listar':
                $tiposController->listar();
                break;
            case 'buscar':
            case 'buscarPorId':
                $tiposController->buscarPorId();
                break;
            case 'criar':
                $tiposController->criar();
                break;
            case 'atualizar':
                $tiposController->atualizar();
                break;
            case 'inativar':
                $tiposController->inativar();
                break;
            default:
                responderRotaNaoEncontrada('Ação de tipos de atendimento não encontrada.');
        }
        break;

    case 'atendimentos':
        exigirAutenticacao();
        require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
        $atendimentosController = new AtendimentosController();

        switch ($action) {
            case 'listar':
                $atendimentosController->listar();
                break;
            case 'visualizar':
                $atendimentosController->visualizar();
                break;
            case 'criar':
                $atendimentosController->criar();
                break;
            case 'alterarStatus':
            case 'atualizarStatus':
                $atendimentosController->atualizarStatus();
                break;
            case 'opcoesFormulario':
                $atendimentosController->opcoesFormulario();
                break;
            default:
                responderRotaNaoEncontrada('Ação de atendimentos não encontrada.');
        }
        break;

    default:
        header('Location: /atendelab/public/?controller=auth&action=login');
        exit;
}
