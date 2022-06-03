<?php

namespace Blog\Core;

class Security
{
    private function getOptimalCost(): int
    {
        $timeTarget = 0.05;
        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash('test', PASSWORD_BCRYPT, ['cost' => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);
        return $cost;
    }
}
