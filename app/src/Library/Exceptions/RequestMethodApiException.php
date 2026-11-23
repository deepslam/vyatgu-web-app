<?php declare(strict_types=1);

namespace App\Library\Exceptions;

class RequestMethodApiException extends ApiException {
    public bool $exposeMessage = true;
}
