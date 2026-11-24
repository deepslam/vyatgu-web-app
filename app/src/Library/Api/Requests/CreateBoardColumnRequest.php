<?php declare(strict_types=1);

namespace App\Library\Api\Requests;

use App\Library\Api\Parameters\ColorParameter;
use App\Library\Api\Parameters\IdParameter;
use App\Library\Api\Parameters\IntegerParameter;
use App\Library\Api\Parameters\StringParameter;
use App\Library\Database\BoardColumn;

final class CreateBoardColumnRequest extends BoardColumnRequestAbstract {
    public private(set) BoardColumn $boardColumn;

    public function validate(): void {
        $this->requireAuthUser();
        $this->requireBoardExistsAndOwnedByUser($this->validatedData['board_id']);
        $this->requireFreeOrdinalPosition($this->validatedData['board_id'], $this->validatedData['ordinal_position']);
    }

    public function execute(): void {
        $boardColumn                   = new BoardColumn();
        $boardColumn->board_id         = $this->validatedData['board_id'];
        $boardColumn->ordinal_position = $this->validatedData['ordinal_position'];
        $boardColumn->title            = $this->validatedData['title'];
        $boardColumn->color            = $this->validatedData['color'] ?? null;
        $boardColumn->save();

        $this->boardColumn = $boardColumn;
    }

    protected function getParameters(): array {
        return [
            'board_id'         => new IdParameter(),
            'ordinal_position' => (new IntegerParameter())->setMinValue(0)->setMaxValue(32),
            'title'            => (new StringParameter())->setMinLength(1)->setMaxLength(255),
            'color'            => (new ColorParameter())->setRequired(false)->setNullable(),
        ];
    }
}
