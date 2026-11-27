<?php declare(strict_types=1);

namespace App\Library\Api\Requests\Board;

use App\Library\Api\Parameters\IdParameter;
use App\Library\Api\Requests\RequestAbstract;
use App\Library\Database\Board;
use App\Library\Database\BoardColumn;
use App\Library\Database\Card;
use App\Library\Database\Database;
use App\Library\Exceptions\NotFoundApiException;
use App\Library\Super;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteBoardRequest extends RequestAbstract {
    private Board $board;

    public function validate(): void {
        $this->requireAuthUser();

        // Убедимся, что такая доска есть
        // и принадлежит нужному пользователю.
        try {
            $this->board = Board::where([
                'board_id'      => $this->validatedData['board_id'],
                'owner_user_id' => $this->user->user_id,
            ])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Super::log()->warning($e->getMessage());
            throw new NotFoundApiException('Board not found');
        }
    }

    public function execute(): void {
        Database::getInstance()::transaction(function () {
            $boardId = $this->board->board_id;

            // Все карточки со всех колонок доски
            Card::where('board_id', $boardId)->delete();

            // Все колонки доски
            BoardColumn::where('board_id', $boardId)->delete();

            // Саму доску
            $this->board->delete();
        });
    }

    protected function getParameters(): array {
        return [
            'board_id'  => new IdParameter(),
        ];
    }
}
