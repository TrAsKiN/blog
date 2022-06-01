<?php

namespace Blog\Core;

use PDO;

abstract class Database
{
    public function __construct(
        protected readonly PDO $pdo
    ) {
    }
}
