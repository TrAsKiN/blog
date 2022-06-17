<?php

namespace Blog\Core;

use Exception;

trait TokenTrait
{
    /**
     * @throws Exception
     */
    public function createToken(int $length = 40): string
    {
        $length = (int) floor($length / 2);
        return bin2hex(random_bytes($length));
    }
}
