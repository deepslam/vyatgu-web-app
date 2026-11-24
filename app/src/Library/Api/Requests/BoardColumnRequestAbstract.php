<?php declare(strict_types=1);

namespace App\Library\Api\Requests;

use App\Library\Database\Board;
use App\Library\Database\BoardColumn;
use App\Library\Exceptions\NotFoundApiException;
use App\Library\Exceptions\RequestApiException;

abstract class BoardColumnRequestAbstract extends RequestAbstract {
    protected function requireBoardExistsAndOwnedByUser(int $boardId): void {
        if (!$this->user) {
            return;
        }

        $boardsCount = Board::where([
            'board_id'      => $boardId,
            'owner_user_id' => $this->user->user_id,
        ])->count();
        if ($boardsCount != 1) {
            throw new NotFoundApiException('Board not found');
        }
    }

    protected function requireFreeOrdinalPosition(int $boardId, int $newOrdinalPosition): void {
        $columnsCount = BoardColumn::where([
            'board_id'         => $boardId,
            'ordinal_position' => $newOrdinalPosition,
        ])->count();
        if ($columnsCount) {
            throw new RequestApiException("Column ordinal position {$newOrdinalPosition} is already taken");
        }
    }
}
