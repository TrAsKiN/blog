<?php

namespace Blog\Controller;

use Framework\Attribute\Route;
use Framework\Controller;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class LegalsController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/mentions-legales', name: 'legal')]
    public function legalInformation(): ResponseInterface
    {
        return $this->render('legals/legal.html.twig');
    }

    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/donnees-personnelles', name: 'personal')]
    public function personalData(): ResponseInterface
    {
        return $this->render('legals/personal.html.twig');
    }
}
