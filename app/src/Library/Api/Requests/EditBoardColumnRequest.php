<?php declare(strict_types=1);

namespace App\Library\Api\Requests;

use App\Library\Api\Parameters\ColorParameter;
use App\Library\Api\Parameters\IdParameter;
use App\Library\Api\Parameters\IntegerParameter;
use App\Library\Api\Parameters\StringParameter;
use App\Library\Database\BoardColumn;
use App\Library\Exceptions\NotFoundApiException;
use App\Library\Super;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class EditBoardColumnRequest extends BoardColumnRequestAbstract {
    public private(set) BoardColumn $boardColumn;

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

        if (isset($this->validatedData['ordinal_position'])) {
            $this->requireFreeOrdinalPosition($this->validatedData['board_id'], $this->validatedData['ordinal_position']);
        }
    }

    public function execute(): void {
        $this->boardColumn->ordinal_position = $this->validatedData['ordinal_position'] ?? $this->boardColumn->ordinal_position;
        $this->boardColumn->title            = $this->validatedData['title'] ?? $this->boardColumn->title;
        $this->boardColumn->color            = $this->validatedData['color'] ?? null;
        $this->boardColumn->save();
    }

    protected function getParameters(): array {
        return [
            'column_id'        => new IdParameter(),
            'board_id'         => new IdParameter(),
            'ordinal_position' => (new IntegerParameter())->setMinValue(0)->setMaxValue(32)->setRequired(false),
            'title'            => (new StringParameter())->setMinLength(1)->setMaxLength(255)->setRequired(false),
            'color'            => (new ColorParameter())->setRequired(false)->setNullable(),
        ];
    }
}
