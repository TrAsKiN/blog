<?php

namespace Blog\Controller;

use Blog\Controller;
use Blog\Route;
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
    public function home(): string
    {
        return $this->render('home/home.html.twig');
    }
}
