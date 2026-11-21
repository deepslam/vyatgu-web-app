<?php declare(strict_types=1);

namespace App\Library\Api\Requests;

use App\Library\Api\Parameters\IdParameter;
use App\Library\Database\BoardColumn;
use App\Library\Exceptions\NotFoundApiException;
use App\Library\Super;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class DeleteBoardColumnRequest extends BoardColumnRequestAbstract {
    private BoardColumn $boardColumn;

    public function validate(): void {
        $this->requireAuthUser();
        $this->requireBoardExistsAndOwnedByUser($this->validatedData['board_id']);

        try {
            $this->boardColumn = BoardColumn::where([
                'column_id' => $this->validatedData['column_id'],
                'board_id'  => $this->validatedData['board_id'],
            ])->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Super::log()->warning($e->getMessage());
            throw new NotFoundApiException('Board column not found');
        }
    }

    public function execute(): void {
        $this->boardColumn->delete();
    }

    protected function getParameters(): array {
        return [
            'column_id'        => new IdParameter(),
            'board_id'         => new IdParameter(),
        ];
    }
}
