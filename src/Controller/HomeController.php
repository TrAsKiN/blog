<?php

namespace Blog\Controller;

use Blog\Controller;
use Blog\Route;

class HomeController extends Controller
{
    #[Route('/', name: 'home')]
    public function home(): string
    {
        return $this->twig->render('home/home.html.twig', []);
    }
}
