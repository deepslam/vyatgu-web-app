<?php declare(strict_types=1);

namespace App\Library\Database;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int            $column_id          Первичный ключ
 * @property int            $board_id           К какой доске принадлежит колонка
 * @property int            $ordinal_position   Положение колонки на доске
 * @property string         $title              Название колонки
 * @property string|null    $color              6 символов (hex-представление цвета)
 */
class BoardColumn extends Model {
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'column_id';
}
