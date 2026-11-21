<?php declare(strict_types=1);

namespace App\Library\Api\Requests;

use App\Library\Api\Parameters\BooleanParameter;
use App\Library\Api\Parameters\StringParameter;
use App\Library\Database\Board;

final class CreateBoardRequest extends RequestAbstract {
    private Board $board;

    public function getBoard(): Board {
        return $this->board;
    }

    public function validate(): void {
        $this->requireAuthUser();
    }

    public function execute(): void {
        $newBoard                = new Board();
        $newBoard->owner_user_id = $this->user->user_id;
        $newBoard->title         = $this->validatedData['title'];
        $newBoard->is_public     = $this->validatedData['is_public'];
        $newBoard->save();

        $this->board = $newBoard;
    }

    protected function getParameters(): array {
        return [
            'title'     => (new StringParameter())->setMinLength(1)->setMaxLength(255),
            'is_public' => (new BooleanParameter())->setDefault(false),
        ];
    }
}
