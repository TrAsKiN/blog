<?php

namespace Blog\Core;

use Blog\Core\Service\FlashService;
use Exception;
use Psr\Http\Message\ServerRequestInterface;

class Form
{
    private readonly Validator $validator;

    public function __construct(
        private readonly ServerRequestInterface $request,
        private readonly FlashService $messages
    ) {
        if ($this->isPost()) {
            $this->validator = new Validator($this->request->getParsedBody());
        }
    }

    public function isPost(): bool
    {
        return $this->request->getMethod() === 'POST';
    }

    public function isValid(array $requirements): bool
    {
        foreach ($requirements as $requirement => $params) {
            try {
                call_user_func_array([$this->validator, $requirement], $params);
            } catch (Exception $exception) {
                $this->messages->addFlash($exception->getMessage(), 'warning');
                return false;
            }
        }
        return true;
    }

    public function getData(string $key = null): mixed
    {
        if (!is_null($key) && array_key_exists($key, $this->request->getParsedBody())) {
            return $this->request->getParsedBody()[$key];
        }
        return $this->request->getParsedBody();
    }
}
