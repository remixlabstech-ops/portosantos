<?php
class Dashboard extends BaseModel {

    public function getResumoMes(int $mes, int $ano): array {
        $entradas = (float)($this->fetchOne(
            "SELECT COALESCE(SUM(valor_entrada),0) AS t FROM entradas
             WHERE deleted_at IS NULL AND status != 'Cancelado'
               AND MONTH(data_entrada)=? AND YEAR(data_entrada)=?",
            [$mes, $ano]
        )['t'] ?? 0);

        $saidas = (float)($this->fetchOne(
            "SELECT COALESCE(SUM(valor),0) AS t FROM saidas
             WHERE deleted_at IS NULL AND status != 'Cancelado'
               AND MONTH(data_saida)=? AND YEAR(data_saida)=?",
            [$mes, $ano]
        )['t'] ?? 0);

        return [
            'total_entradas' => $entradas,
            'total_saidas'   => $saidas,
            'lucro_liquido'  => $entradas - $saidas,
        ];
    }

    public function getGraficoMensal(int $ano): array {
        $entradas = $this->fetchAll(
            "SELECT MONTH(data_entrada) AS mes, COALESCE(SUM(valor_entrada),0) AS total
             FROM entradas WHERE deleted_at IS NULL AND status != 'Cancelado'
               AND YEAR(data_entrada)=?
             GROUP BY MONTH(data_entrada)",
            [$ano]
        );

        $saidas = $this->fetchAll(
            "SELECT MONTH(data_saida) AS mes, COALESCE(SUM(valor),0) AS total
             FROM saidas WHERE deleted_at IS NULL AND status != 'Cancelado'
               AND YEAR(data_saida)=?
             GROUP BY MONTH(data_saida)",
            [$ano]
        );

        $e = array_fill(1, 12, 0);
        $s = array_fill(1, 12, 0);
        foreach ($entradas as $r) { $e[(int)$r['mes']] = (float)$r['total']; }
        foreach ($saidas   as $r) { $s[(int)$r['mes']] = (float)$r['total']; }

        return [
            'entradas' => array_values($e),
            'saidas'   => array_values($s),
        ];
    }

    public function getTotalReceber(): float {
        $row = $this->fetchOne(
            "SELECT COALESCE(SUM(valor_entrada),0) AS t FROM entradas
             WHERE deleted_at IS NULL AND status = 'Aberto'"
        );
        return (float)($row['t'] ?? 0);
    }

    public function getTotalPagar(): float {
        $row = $this->fetchOne(
            "SELECT COALESCE(SUM(valor),0) AS t FROM saidas
             WHERE deleted_at IS NULL AND status = 'Aberto'"
        );
        return (float)($row['t'] ?? 0);
    }

    public function getInadimplenciaTotal(): array {
        $row = $this->fetchOne(
            "SELECT COALESCE(SUM(valor_entrada),0) AS total, COUNT(*) AS qtd
             FROM entradas
             WHERE deleted_at IS NULL AND status = 'Aberto'
               AND data_vencimento < CURDATE()"
        );
        return [
            'total' => (float)($row['total'] ?? 0),
            'qtd'   => (int)($row['qtd'] ?? 0),
        ];
    }

    public function getRankingClientes(int $limit = 5): array {
        return $this->fetchAll(
            "SELECT c.nome, COALESCE(SUM(e.valor_entrada),0) AS total
             FROM entradas e
             JOIN clientes c ON e.cliente_id = c.id
             WHERE e.deleted_at IS NULL AND e.status = 'Recebido'
             GROUP BY e.cliente_id, c.nome
             ORDER BY total DESC
             LIMIT ?",
            [$limit]
        );
    }

    public function getRankingCentrosCusto(int $limit = 5): array {
        return $this->fetchAll(
            "SELECT cc.nome, COALESCE(SUM(s.valor),0) AS total
             FROM saidas s
             JOIN centros_custo cc ON s.centro_custo_id = cc.id
             WHERE s.deleted_at IS NULL AND s.status = 'Pago'
             GROUP BY s.centro_custo_id, cc.nome
             ORDER BY total DESC
             LIMIT ?",
            [$limit]
        );
    }

    public function getGraficoPorArea(): array {
        return $this->fetchAll(
            "SELECT cat.tipo AS area, COALESCE(SUM(e.valor_entrada),0) AS total
             FROM entradas e
             JOIN categorias_receita cat ON e.categoria_id = cat.id
             WHERE e.deleted_at IS NULL AND e.status != 'Cancelado'
             GROUP BY cat.tipo
             ORDER BY total DESC"
        );
    }
}
