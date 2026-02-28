<?php
/**
 * Entradas Controller
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Entrada.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Log.php';

class EntradasController extends BaseController
{
    private Entrada $entrada;
    private Cliente $cliente;
    private Categoria $categoria;
    private Log $log;

    public function __construct()
    {
        $this->entrada   = new Entrada();
        $this->cliente   = new Cliente();
        $this->categoria = new Categoria();
        $this->log       = new Log();
    }

    /**
     * Lista entradas com filtros e paginação.
     */
    public function index(): void
    {
        $page   = max(1, (int) $this->get('page', 1));
        $filtros = [
            'cliente_id'  => $this->get('cliente_id'),
            'status'      => $this->get('status'),
            'data_inicio' => $this->get('data_inicio'),
            'data_fim'    => $this->get('data_fim'),
            'valor_min'   => $this->get('valor_min'),
            'valor_max'   => $this->get('valor_max'),
        ];

        $resultado = $this->entrada->listar(array_filter($filtros), $page);

        $this->render('entradas', [
            'entradas'   => $resultado['data'],
            'total'      => $resultado['total'],
            'pages'      => $resultado['pages'],
            'page'       => $page,
            'filtros'    => $filtros,
            'categorias' => $this->categoria->listarPorTipo('entrada'),
        ]);
    }
}
