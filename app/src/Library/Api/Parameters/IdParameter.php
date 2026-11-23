<?php declare(strict_types=1);

namespace App\Library\Api\Parameters;

class IdParameter extends IntegerParameter {
    public function __construct() {
        $this->setMinValue(1);
    }
}
