<?php declare(strict_types=1);

namespace App\Library\Database;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int            $card_id
 * @property int            $board_id
 * @property int            $column_id
 * @property int            $sort_index
 * @property string         $title
 * @property string|null    $content
 * @property string|null    $color
 * @property bool           $is_archived
 * @property string         $created_datetime
 * @property string|null    $updated_datetime
 */
class Card extends Model {
    /**
     * @var string
     */
    public const CREATED_AT = 'created_datetime';

    /**
     * @var string|null
     */
    public const UPDATED_AT = 'updated_datetime';

    /**
     * @var string
     */
    protected $primaryKey = 'card_id';
}
