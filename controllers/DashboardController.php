<?php
class DashboardController extends BaseController {

    private Dashboard $model;

    public function __construct() {
        $this->model = new Dashboard();
    }

    public function index(): array {
        $mes = (int)(date('m'));
        $ano = (int)(date('Y'));

        $resumo      = $this->model->getResumoMes($mes, $ano);
        $grafico     = $this->model->getGraficoMensal($ano);
        $receber     = $this->model->getTotalReceber();
        $pagar       = $this->model->getTotalPagar();
        $inadimplencia = $this->model->getInadimplenciaTotal();
        $rankingClientes = $this->model->getRankingClientes(5);
        $rankingCentros  = $this->model->getRankingCentrosCusto(5);
        $graficoPorArea  = $this->model->getGraficoPorArea();

        return $this->jsonResponse([
            'success'          => true,
            'resumo_mes'       => $resumo,
            'grafico_mensal'   => $grafico,
            'total_receber'    => $receber,
            'total_pagar'      => $pagar,
            'inadimplencia'    => $inadimplencia,
            'ranking_clientes' => $rankingClientes,
            'ranking_centros'  => $rankingCentros,
            'grafico_area'     => $graficoPorArea,
        ]);
    }
}
