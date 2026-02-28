<?php
/**
 * Model: Log de operações
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class Log extends BaseModel
{
    protected string $table = 'logs';

    /**
     * Registra operação no log de auditoria.
     *
     * @param string $acao INSERT|UPDATE|DELETE
     * @param string $tabela Nome da tabela afetada
     * @param int $registroId ID do registro
     * @param mixed $valoresAntes Estado anterior (array ou null)
     * @param mixed $valoresDepois Estado posterior (array ou null)
     */
    public function registrar(
        string $acao,
        string $tabela,
        int $registroId,
        mixed $valoresAntes = null,
        mixed $valoresDepois = null
    ): void {
        $stmt = $this->db->prepare(
            "INSERT INTO logs (usuario_id, acao, tabela, registro_id, valores_antes, valores_depois, ip, empresa, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            null, // usuario_id – preparado para autenticação futura
            strtoupper($acao),
            $tabela,
            $registroId,
            $valoresAntes !== null ? json_encode($valoresAntes, JSON_UNESCAPED_UNICODE) : null,
            $valoresDepois !== null ? json_encode($valoresDepois, JSON_UNESCAPED_UNICODE) : null,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            EMPRESA_PADRAO,
        ]);
    }
}
