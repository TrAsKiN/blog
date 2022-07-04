<?php

namespace Framework;

use PDO;

abstract class Database
{
    public function __construct(
        protected readonly PDO $pdo
    ) {
    }
}
