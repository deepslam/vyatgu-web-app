<?php declare(strict_types=1);

namespace App\Library\Api\Responses;

use App\Library\Database\BoardColumn;
use Illuminate\Database\Eloquent\Collection;

final class BoardColumnResponseModel {
    public static function getInfo(BoardColumn $boardColumn): array {
        return [
            'column_id'        => $boardColumn->column_id,
            'board_id'         => $boardColumn->board_id,
            'ordinal_position' => $boardColumn->ordinal_position,
            'title'            => $boardColumn->title,
            'color'            => $boardColumn->color,
        ];
    }

    /**
     * @param BoardColumn[] $boards
     */
    public static function getList(Collection $boardColumns): array {
        $result = [];
        foreach ($boardColumns as $boardColumn) {
            $result[] = self::getInfo($boardColumn);
        }
        return $result;
    }
}
