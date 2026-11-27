<?php declare(strict_types=1);

namespace App\Library\Api;

use App\Library\Api\Requests\Board\CreateBoardRequest;
use App\Library\Api\Requests\Board\DeleteBoardRequest;
use App\Library\Api\Requests\Board\EditBoardRequest;
use App\Library\Api\Requests\BoardColumn\BoardColumnsRequest;
use App\Library\Api\Requests\BoardColumn\CreateBoardColumnRequest;
use App\Library\Api\Requests\BoardColumn\DeleteBoardColumnRequest;
use App\Library\Api\Requests\BoardColumn\EditBoardColumnRequest;
use App\Library\Api\Requests\BoardColumn\MoveBoardColumnRequest;
use App\Library\Api\Requests\User\AuthRequest;
use App\Library\Api\Requests\User\RegistrationRequest;
use App\Library\Api\Responses\BoardColumnResponseModel;
use App\Library\Api\Responses\BoardResponseModel;
use App\Library\Api\Responses\UserResponseModel;
use App\Library\Api\View\JsonView;
use App\Library\Auth\UserAuth;
use App\Library\Database\Board;
use App\Library\Framework\Routing\ControllerAbstract;
use App\Library\Framework\Routing\HttpMethodsEnum;
use App\Library\Framework\Routing\HttpRequest;
use App\Library\Framework\Routing\ViewAbstract;

final class FrontendController extends ControllerAbstract {
    /* User */

    public function registerAction(HttpRequest $request): ViewAbstract {
        $registrationRequest = new RegistrationRequest($request, HttpMethodsEnum::POST);
        $registrationRequest->validate();
        $registrationRequest->execute();

        return new JsonView(UserResponseModel::getInfo($registrationRequest->getUser()));
    }

    public function loginAction(HttpRequest $request): ViewAbstract {
        $authRequest = new AuthRequest($request, HttpMethodsEnum::POST);
        $authRequest->validate();
        $authRequest->execute();

        return new JsonView(UserResponseModel::getInfo($authRequest->getUser()));
    }

    public function userAction(HttpRequest $request): ViewAbstract {
        $request->requireHttpMethod(HttpMethodsEnum::GET);
        $user = UserAuth::requireAuthUser();

        return new JsonView(UserResponseModel::getInfo($user));
    }

    public function logoutAction(HttpRequest $request): ViewAbstract {
        $request->requireHttpMethod(HttpMethodsEnum::GET);
        UserAuth::requireAuthUser();
        UserAuth::logout();

        return new JsonView([]);
    }

    /* Board */

    public function boardsAction(HttpRequest $request): ViewAbstract {
        $request->requireHttpMethod(HttpMethodsEnum::GET);
        $user = UserAuth::requireAuthUser();

        $boards = Board::where('owner_user_id', $user->user_id)
            ->orderByDesc('updated_datetime')
            ->get();

        return new JsonView(['boards' => BoardResponseModel::getList($boards)]);
    }

    public function createBoardAction(HttpRequest $request): ViewAbstract {
        $createBoardRequest = new CreateBoardRequest($request, HttpMethodsEnum::POST);
        $createBoardRequest->validate();
        $createBoardRequest->execute();

        return new JsonView(BoardResponseModel::getInfo($createBoardRequest->getBoard()));
    }

    public function editBoardAction(HttpRequest $request): ViewAbstract {
        $editBoardRequest = new EditBoardRequest($request, HttpMethodsEnum::POST);
        $editBoardRequest->validate();
        $editBoardRequest->execute();

        return new JsonView(BoardResponseModel::getInfo($editBoardRequest->getBoard()));
    }

    public function deleteBoardAction(HttpRequest $request): ViewAbstract { // удалять колонки!
        $deleteBoardRequest = new DeleteBoardRequest($request, HttpMethodsEnum::POST);
        $deleteBoardRequest->validate();
        $deleteBoardRequest->execute();

        return new JsonView([]);
    }

    /* BoardColumn */

    public function boardColumnsAction(HttpRequest $request): ViewAbstract {
        $boardColumnsRequest = new BoardColumnsRequest($request, HttpMethodsEnum::GET);
        $boardColumnsRequest->validate();
        $boardColumnsRequest->execute();

        return new JsonView(['board_columns' => BoardColumnResponseModel::getList($boardColumnsRequest->boardColumns)]);
    }

    public function createBoardColumnAction(HttpRequest $request): ViewAbstract {
        $createBoardColumnRequest = new CreateBoardColumnRequest($request, HttpMethodsEnum::POST);
        $createBoardColumnRequest->validate();
        $createBoardColumnRequest->execute();

        return new JsonView(BoardColumnResponseModel::getInfo($createBoardColumnRequest->boardColumn));
    }

    public function editBoardColumnAction(HttpRequest $request): ViewAbstract {
        $editBoardColumnRequest = new EditBoardColumnRequest($request, HttpMethodsEnum::POST);
        $editBoardColumnRequest->validate();
        $editBoardColumnRequest->execute();

        return new JsonView(BoardColumnResponseModel::getInfo($editBoardColumnRequest->boardColumn));
    }

    public function deleteBoardColumnAction(HttpRequest $request): ViewAbstract {
        $deleteBoardColumnRequest = new DeleteBoardColumnRequest($request, HttpMethodsEnum::POST);
        $deleteBoardColumnRequest->validate();
        $deleteBoardColumnRequest->execute();

        return new JsonView([]);
    }

    public function moveBoardColumnLeftAction(HttpRequest $request):ViewAbstract {
        $moveBoardColumnRequest = new MoveBoardColumnRequest($request, HttpMethodsEnum::POST, MoveBoardColumnRequest::LEFT);
        $moveBoardColumnRequest->validate();
        $moveBoardColumnRequest->execute();

        return new JsonView([]);
    }

    public function moveBoardColumnRightAction(HttpRequest $request):ViewAbstract {
        $moveBoardColumnRequest = new MoveBoardColumnRequest($request, HttpMethodsEnum::POST, MoveBoardColumnRequest::RIGHT);
        $moveBoardColumnRequest->validate();
        $moveBoardColumnRequest->execute();

        return new JsonView([]);
    }

    // карточный CRUD
    // новый порядок карточек в колонке
    // перенос карточек по колонкам
}
