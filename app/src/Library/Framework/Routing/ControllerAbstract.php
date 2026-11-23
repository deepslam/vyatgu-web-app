<?php declare(strict_types=1);

namespace App\Library\Framework\Routing;

use App\Library\Api\View\JsonView;
use App\Library\Exceptions\ApiException;
use App\Library\Exceptions\ControllerApiException;
use App\Library\Exceptions\NotFoundApiException;
use App\Library\Exceptions\ParametersValidationApiException;
use App\Library\Exceptions\RoutingApiException;
use App\Library\Framework\Routing\Generated\FeatureControllerMap;
use App\Library\Framework\Utils;
use App\Library\Super;
use Throwable;

class ControllerAbstract {
    public static function processRequest(): void {
        $request = HttpRequest::createFromGlobals();

        $currentControllerClassName = self::getCurrentControllerClassName($request);
        $actionName                 = $request->action;

        try {
            if (!$currentControllerClassName || !class_exists($currentControllerClassName) || !method_exists($currentControllerClassName, $actionName)) {
                throw new RoutingApiException("This combination of class '{$currentControllerClassName}' and method '{$request->action}' does not exist");
            }

            $actionResult = (new $currentControllerClassName())->$actionName($request);
            if (!$actionResult instanceof ViewAbstract) {
                throw new ControllerApiException("Controller method '{$currentControllerClassName}::{$actionName}()' did not return a ViewAbstract object");
            }
        } catch (Throwable $t) {
            Super::log()->warning(trim(Utils::getClassSimpleName($t::class) . ' ' . $t->getMessage()), $t->getTrace());
            $actionResult = static::getExceptionActionResult($request, $t);
        } finally {
            $actionResult->sendHeadersAndRender();
        }
    }

    protected static function getExceptionActionResult(HttpRequest $request, Throwable $t): ViewAbstract {
        $exceptionResult = new JsonView(['error' => 'Server error :(']);
        $exceptionResult->setResponseCode(500);

        if ($t instanceof ApiException) {
            $errors = [
                'error' => !empty($t->exposeMessage) ? $t->getMessage() : 'Unprocessable API request'
            ];

            if ($t instanceof ParametersValidationApiException) {
                $errors['parameter_errors'] = $t->getErrors();
            }

            $httpCode = $t instanceof NotFoundApiException ? 404: 400;

            $exceptionResult = new JsonView($errors);
            $exceptionResult->setResponseCode($httpCode);
        }

        return $exceptionResult;
    }

    private static function getCurrentControllerClassName(HttpRequest $request): ?string {
        if (!$request->feature || !$request->controller) {
            Super::log()->warning('Empty URI parts');
            return null;
        }

        if (!AvailableFeaturesEnum::isValid($request->feature)) {
            Super::log()->warning("Invalid feature '{$request->feature}'");
            return null;
        }

        if (empty(FeatureControllerMap::getMap()[$request->feature])) {
            Super::log()->warning("Feature '{$request->feature}' not in controller map");
            return null;
        }

        foreach (FeatureControllerMap::getMap()[$request->feature] as $fullControllerClassName) {
            if (Utils::getClassSimpleName($fullControllerClassName) === $request->controller) {
                return $fullControllerClassName;
            }
        }

        return null;
    }
}
