<?php

namespace Blog\Controller;

use Blog\Core\Controller;
use Blog\Core\Route;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends Controller
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/', name: 'home')]
    public function home(): ResponseInterface
    {
        return $this->render('home/home.html.twig');
    }
}
