<?php declare(strict_types=1);

namespace App\Library\Api\Requests\BoardColumn;

use App\Library\Api\Parameters\IdParameter;
use App\Library\Database\BoardColumn;
use Illuminate\Database\Eloquent\Collection;

final class BoardColumnsRequest extends BoardColumnRequestAbstract {
    /** @var BoardColumn[] */
    public private(set) Collection $boardColumns;

    public function validate(): void {
        $this->requireAuthUser();
        $this->requireBoardExistsAndOwnedByUser($this->validatedData['board_id']);
    }

    public function execute(): void {
        $this->boardColumns = BoardColumn::where('board_id', $this->validatedData['board_id'])
            ->orderBy('ordinal_position')
            ->get();
    }

    protected function getParameters(): array {
        return [
            'board_id' => new IdParameter(),
        ];
    }
}
