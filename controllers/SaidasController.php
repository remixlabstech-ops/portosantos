<?php
/**
 * Saidas Controller
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Saida.php';
require_once __DIR__ . '/../models/Fornecedor.php';
require_once __DIR__ . '/../models/CentroCusto.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Log.php';

class SaidasController extends BaseController
{
    private Saida $saida;
    private Fornecedor $fornecedor;
    private CentroCusto $centroCusto;
    private Categoria $categoria;
    private Log $log;

    public function __construct()
    {
        $this->saida       = new Saida();
        $this->fornecedor  = new Fornecedor();
        $this->centroCusto = new CentroCusto();
        $this->categoria   = new Categoria();
        $this->log         = new Log();
    }

    /**
     * Lista saídas com filtros e paginação.
     */
    public function index(): void
    {
        $page   = max(1, (int) $this->get('page', 1));
        $filtros = [
            'fornecedor_id' => $this->get('fornecedor_id'),
            'status'        => $this->get('status'),
            'data_inicio'   => $this->get('data_inicio'),
            'data_fim'      => $this->get('data_fim'),
        ];

        $resultado = $this->saida->listar(array_filter($filtros), $page);

        $this->render('saidas', [
            'saidas'         => $resultado['data'],
            'total'          => $resultado['total'],
            'pages'          => $resultado['pages'],
            'page'           => $page,
            'filtros'        => $filtros,
            'categorias'     => $this->categoria->listarPorTipo('saida'),
            'centrosCusto'   => $this->centroCusto->listarAtivos(),
        ]);
    }
}
