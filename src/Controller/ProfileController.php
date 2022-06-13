<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Service\FlashService;
use Blog\Core\Session;
use Blog\Form\ProfileForm;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Server;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProfileController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/profile', name: 'profile', restricted: true)]
    public function dashboard(): ResponseInterface
    {
        return $this->render('profile/dashboard.html.twig');
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/profile/update', name: 'profile_update', restricted: true)]
    public function update(
        ProfileForm $profileForm,
        FlashService $messages
    ): ResponseInterface {
        if ($profileForm->form->isPost() && $profileForm->form->isValid()) {
            if ($profileForm->getResult()) {
                $messages->addFlash("Votre profil a été mis à jour !", 'success');
            }
        }
        return $this->redirect('profile');
    }
}
