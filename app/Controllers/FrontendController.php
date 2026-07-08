<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Atendimento;
use App\Models\Pessoa;
use App\Models\Tipo;

class FrontendController extends Controller
{
    public function __construct()
    {
        Auth::requireLogin();
    }

    /* ===================== PESSOAS ===================== */

    public function pessoas(): void
    {
        $this->view('pessoas/index', [
            'pessoas' => Pessoa::todos(),
            'flash'   => $this->consumeFlash(),
        ], 'Pessoas atendidas');
    }

    public function pessoa_form(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $pessoa = $id ? Pessoa::encontrar($id) : null;

        $this->view('pessoas/form', [
            'pessoa' => $pessoa,
        ], $pessoa ? 'Editar pessoa' : 'Nova pessoa');
    }

    public function pessoa_salvar(): void
    {
        $this->somentePost('frontend', 'pessoas');

        $id = (int) ($_POST['id'] ?? 0);
        $dados = [
            'nome'      => trim($_POST['nome'] ?? ''),
            'documento' => trim($_POST['documento'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'curso'     => trim($_POST['curso'] ?? ''),
            'periodo'   => trim($_POST['periodo'] ?? ''),
        ];

        if ($dados['nome'] === '' || $dados['email'] === '') {
            $this->flash('erro', 'Nome e e-mail são obrigatórios.');
            $this->redirect('frontend', 'pessoa_form', $id ? ['id' => $id] : []);
        }

        if ($id) {
            Pessoa::atualizar($id, $dados);
            $this->flash('sucesso', 'Pessoa atualizada com sucesso.');
        } else {
            $dados['ativo'] = 1;
            Pessoa::criar($dados);
            $this->flash('sucesso', 'Pessoa cadastrada com sucesso.');
        }
        $this->redirect('frontend', 'pessoas');
    }

    public function pessoa_status(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            Pessoa::alternarStatus($id);
            $this->flash('sucesso', 'Status da pessoa atualizado.');
        }
        $this->redirect('frontend', 'pessoas');
    }

    /* ===================== TIPOS ===================== */

    public function tipos(): void
    {
        $this->view('tipos/index', [
            'tipos' => Tipo::todos(),
            'flash' => $this->consumeFlash(),
        ], 'Tipos de atendimento');
    }

    public function tipo_form(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $tipo = $id ? Tipo::encontrar($id) : null;

        $this->view('tipos/form', [
            'tipo' => $tipo,
        ], $tipo ? 'Editar tipo' : 'Novo tipo');
    }

    public function tipo_salvar(): void
    {
        $this->somentePost('frontend', 'tipos');

        $id = (int) ($_POST['id'] ?? 0);
        $dados = [
            'nome'      => trim($_POST['nome'] ?? ''),
            'descricao' => trim($_POST['descricao'] ?? ''),
        ];

        if ($dados['nome'] === '') {
            $this->flash('erro', 'O nome do tipo é obrigatório.');
            $this->redirect('frontend', 'tipo_form', $id ? ['id' => $id] : []);
        }

        if ($id) {
            Tipo::atualizar($id, $dados);
            $this->flash('sucesso', 'Tipo atualizado com sucesso.');
        } else {
            $dados['ativo'] = 1;
            Tipo::criar($dados);
            $this->flash('sucesso', 'Tipo cadastrado com sucesso.');
        }
        $this->redirect('frontend', 'tipos');
    }

    public function tipo_status(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            Tipo::alternarStatus($id);
            $this->flash('sucesso', 'Status do tipo atualizado.');
        }
        $this->redirect('frontend', 'tipos');
    }

    /* ===================== ATENDIMENTOS ===================== */

    public function atendimentos(): void
    {
        $this->view('atendimentos/index', [
            'atendimentos' => Atendimento::todos(),
            'flash'        => $this->consumeFlash(),
        ], 'Atendimentos');
    }

    public function atendimento_form(): void
    {
        $this->view('atendimentos/form', [
            'pessoas' => Pessoa::ativas(),
            'tipos'   => Tipo::ativos(),
            'hoje'    => date('Y-m-d'),
        ], 'Novo atendimento');
    }

    public function atendimento_salvar(): void
    {
        $this->somentePost('frontend', 'atendimentos');

        $dados = [
            'pessoa_id'  => (int) ($_POST['pessoa_id'] ?? 0),
            'tipo_id'    => (int) ($_POST['tipo_id'] ?? 0),
            'usuario_id' => Auth::id(),
            'data'       => $_POST['data'] ?? date('Y-m-d'),
            'status'     => ($_POST['status'] ?? 'aberto') === 'concluido' ? 'concluido' : 'aberto',
            'observacao' => trim($_POST['observacao'] ?? ''),
        ];

        if (!$dados['pessoa_id'] || !$dados['tipo_id']) {
            $this->flash('erro', 'Selecione a pessoa e o tipo de atendimento.');
            $this->redirect('frontend', 'atendimento_form');
        }

        Atendimento::criar($dados);
        $this->flash('sucesso', 'Atendimento registrado com sucesso.');
        $this->redirect('frontend', 'atendimentos');
    }

    /** Alterna o status do atendimento (aberto <-> concluido). */
    public function atendimento_status(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $atual = $id ? Atendimento::encontrar($id) : null;
        if ($atual) {
            $novo = $atual['status'] === 'concluido' ? 'aberto' : 'concluido';
            Atendimento::definirStatus($id, $novo);
            $this->flash('sucesso', 'Status do atendimento atualizado.');
        }
        $this->redirect('frontend', 'atendimentos');
    }

    /* ===================== AUXILIARES ===================== */

    private function consumeFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    private function somentePost(string $c, string $a): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($c, $a);
        }
    }
}
