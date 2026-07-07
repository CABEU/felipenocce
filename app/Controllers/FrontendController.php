<?php
declare(strict_types=1);

/**
 * Controller responsável por renderizar as páginas visuais (HTML).
 * Não acessa o banco diretamente: quem busca dados é o JavaScript (api.js)
 * chamando os controllers pessoas, tipos e atendimentos.
 */
class FrontendController
{
    private string $baseUrl = '/atendelab/public/';

    public function pessoas(): void
    {
        $this->exigirSessao();
        $baseUrl = $this->baseUrl;
        require __DIR__ . '/../Views/pessoas/index.php';
    }

    public function tiposAtendimentos(): void
    {
        $this->exigirSessao();
        $baseUrl = $this->baseUrl;
        require __DIR__ . '/../Views/tipos-atendimentos/index.php';
    }

    public function atendimentos(): void
    {
        $this->exigirSessao();
        $baseUrl = $this->baseUrl;
        require __DIR__ . '/../Views/atendimentos/index.php';
    }

    private function exigirSessao(): void
    {
        if (empty($_SESSION['usuario']['id'])) {
            header('Location: ' . $this->baseUrl . '?controller=auth&action=login');
            exit;
        }
    }
}
