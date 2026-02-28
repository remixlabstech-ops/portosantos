<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Entrada
 *
 * @package App\Models
 */
class Entrada extends BaseModel
{
    protected $table = 'entradas';

    protected $fillable = [
        'cliente_id',
        'categoria',
        'tipo_honorario',
        'valor_causa',
        'percentual',
        'valor',
        'data_vencimento',
        'status',
        'comprovante',
        'observacoes'
    ];

    public function create(array $data)
    {
        // Handle creation logic here
        if (in_array($data['tipo_honorario'], ['Sucumbência', 'Êxito'])) {
            $data['valor'] = ($data['valor_causa'] * $data['percentual']) / 100;
        }
        return parent::create($data);
    }

    public function calculate_sucumbencia()
    {
        // Implementation for calculating sucumbência
    }

    public static function getByStatus($status)
    {
        return self::where('status', $status)->get();
    }

    public static function getPending()
    {
        return self::where('status', 'Pending')->get();
    }

    public static function getOverdue()
    {
        return self::where('data_vencimento', '<', now())->get();
    }

    public static function getTotalByMonth($month, $year)
    {
        return self::whereMonth('data_vencimento', $month)
            ->whereYear('data_vencimento', $year)
            ->sum('valor');
    }

    public static function getByCategoryJuridica($categoria)
    {
        return self::where('categoria', $categoria)->get();
    }
}