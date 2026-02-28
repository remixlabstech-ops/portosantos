<?php
/**
 * Cadastros Controller (Clientes, Fornecedores, Centros de Custo, Categorias)
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Fornecedor.php';
require_once __DIR__ . '/../models/CentroCusto.php';
require_once __DIR__ . '/../models/Categoria.php';

class CadastrosController extends BaseController
{
    private Cliente $cliente;
    private Fornecedor $fornecedor;
    private CentroCusto $centroCusto;
    private Categoria $categoria;

    public function __construct()
    {
        $this->cliente     = new Cliente();
        $this->fornecedor  = new Fornecedor();
        $this->centroCusto = new CentroCusto();
        $this->categoria   = new Categoria();
    }

    public function clientes(): void
    {
        $page      = max(1, (int) $this->get('page', 1));
        $resultado = $this->cliente->paginate($page);
        $this->render('clientes', [
            'clientes' => $resultado['data'],
            'total'    => $resultado['total'],
            'pages'    => $resultado['pages'],
            'page'     => $page,
        ]);
    }

    public function fornecedores(): void
    {
        $page      = max(1, (int) $this->get('page', 1));
        $resultado = $this->fornecedor->paginate($page);
        $this->render('fornecedores', [
            'fornecedores' => $resultado['data'],
            'total'        => $resultado['total'],
            'pages'        => $resultado['pages'],
            'page'         => $page,
        ]);
    }

    public function centrosCusto(): void
    {
        $page      = max(1, (int) $this->get('page', 1));
        $resultado = $this->centroCusto->paginate($page);
        $this->render('centros_custo', [
            'centros' => $resultado['data'],
            'total'   => $resultado['total'],
            'pages'   => $resultado['pages'],
            'page'    => $page,
        ]);
    }

    public function categorias(): void
    {
        $page      = max(1, (int) $this->get('page', 1));
        $resultado = $this->categoria->paginate($page);
        $this->render('cadastros', [
            'categorias' => $resultado['data'],
            'total'      => $resultado['total'],
            'pages'      => $resultado['pages'],
            'page'       => $page,
        ]);
    }
}
