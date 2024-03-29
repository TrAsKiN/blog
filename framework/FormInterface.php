<?php

namespace Framework;

interface FormInterface
{
    /**
     * This method must process the information sent from the form and return the result
     * or null if an exception is thrown.
     *
     * @param mixed|null $params
     * @return array|object|null
     */
    public function getResult(mixed $params = null): object|array|null;
}
