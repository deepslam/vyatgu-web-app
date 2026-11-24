<?php declare(strict_types=1);

namespace App\Library\Api\Requests\BoardColumn;

use App\Library\Api\Parameters\IdParameter;
use App\Library\Database\BoardColumn;
use App\Library\Database\Database;
use App\Library\Exceptions\RequestApiException;
use App\Library\Framework\Routing\HttpMethodsEnum;
use App\Library\Framework\Routing\HttpRequest;
use Illuminate\Database\Eloquent\Collection;
use LogicException;

final class MoveBoardColumnRequest extends BoardColumnRequestAbstract {
    public const int LEFT = -1;
    public const int RIGHT = 1;

    /** @var BoardColumn[] */
    private Collection $boardColumns;

    private int $moveDirection;

    public function __construct(HttpRequest $httpRequest, HttpMethodsEnum $requestMethod, int $moveDirection) {
        parent::__construct($httpRequest, $requestMethod);

        if ($moveDirection !== self::LEFT && $moveDirection !== self::RIGHT) {
            throw new LogicException('Invalid move direction');
        }

        $this->moveDirection = $moveDirection;
    }


    public function validate(): void {
        $this->requireAuthUser();
        $this->requireBoardExistsAndOwnedByUser($this->validatedData['board_id']);

        $this->boardColumns = BoardColumn::where('board_id', $this->validatedData['board_id'])
            ->orderBy('ordinal_position')
            ->get();

        if ($this->boardColumns->count() < 2) {
            throw new RequestApiException('Too few columns on the board to make a move');
        }

        if (
            ($this->moveDirection === self::LEFT && $this->boardColumns->first()->column_id === $this->validatedData['column_id'])
            || ($this->moveDirection === self::RIGHT && $this->boardColumns->last()->column_id === $this->validatedData['column_id'])
        ) {
            throw new RequestApiException('Moving column is already at the edge');
        }
    }

    public function execute(): void {
        $indexedColumns = $this->boardColumns->values();

        $currentBoardColumn = null;
        foreach ($indexedColumns as $index => $boardColumn) {
            if ($boardColumn->column_id === $this->validatedData['column_id']) {
                $currentBoardColumn = $boardColumn;
                $targetBoardColumn  = $indexedColumns->get($index + $this->moveDirection);
                break;
            }
        }

        if ($currentBoardColumn === null || $targetBoardColumn === null) {
            throw new RequestApiException('Unexpected column logic error');
        }

        /** @var BoardColumn $currentBoardColumn */
        /** @var BoardColumn $targetBoardColumn */
        Database::getInstance()::transaction(function () use ($currentBoardColumn, $targetBoardColumn) {
            $oldTargetOrdinalPosition             = $targetBoardColumn->ordinal_position;
            $targetBoardColumn->ordinal_position  = $currentBoardColumn->ordinal_position;
            $currentBoardColumn->ordinal_position = $oldTargetOrdinalPosition;

            $targetBoardColumn->save();
            $currentBoardColumn->save();
        });
    }

    protected function getParameters(): array {
        return [
            'column_id' => new IdParameter(),
            'board_id'  => new IdParameter(),
        ];
    }
}
