<?php

namespace Blog\Controller;

use Blog\Form\ProfileForm;
use Exception;
use Framework\Attribute\Route;
use Framework\Controller;
use Framework\Csrf;
use Framework\Service\FlashService;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
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
     * @throws Exception
     */
    #[Route('/profile', name: 'profile', restricted: true)]
    public function dashboard(
        Csrf $csrf
    ): ResponseInterface {
        $token = $csrf->new();
        return $this->render('profile/dashboard.html.twig', compact('token'));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
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
