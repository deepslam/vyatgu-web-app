<?php declare(strict_types=1);

namespace App\Library\Api\Responses;

use App\Library\Database\Board;
use Illuminate\Database\Eloquent\Collection;

final class BoardResponseModel {
    public static function getInfo(Board $board): array {
        return [
            'board_id'         => $board->board_id,
            'title'            => $board->title,
            'owner_user_id'    => $board->owner_user_id,
            'is_public'        => $board->is_public,
            'created_datetime' => $board->created_datetime?->format('c'),
            'updated_datetime' => $board->updated_datetime?->format('c'),
        ];
    }

    /**
     * @param Board[] $boards
     */
    public static function getList(Collection $boards): array {
        $result = [];
        foreach ($boards as $board) {
            $result[] = self::getInfo($board);
        }
        return $result;
    }
}
