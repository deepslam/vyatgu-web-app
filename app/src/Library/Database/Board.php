<?php declare(strict_types=1);

namespace App\Library\Database;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int            $board_id
 * @property string         $title
 * @property int            $owner_user_id
 * @property bool           $is_public
 * @property string         $created_datetime
 * @property string|null    $updated_datetime
 */
class Board extends Model {
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
    protected $primaryKey = 'board_id';
}
