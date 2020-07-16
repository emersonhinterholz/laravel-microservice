<?php

namespace Tests\Helpers;

use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid {

    public static function generate() {

        return RamseyUuid::uuid4();
    }
}
