<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Csrf;
use Blog\Core\Mail;
use Blog\Core\Service\FlashService;
use Blog\Core\Session;
use Blog\Entity\User;
use Blog\Form\ForgottenForm;
use Blog\Form\LoginForm;
use Blog\Form\RegisterForm;
use Blog\Form\ResetPasswordForm;
use Blog\Repository\UserRepository;
use Exception;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use TypeError;

class UserController extends Controller
{
    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    #[Route('/login', name: 'login')]
    public function login(
        ServerRequestInterface $request,
        FlashService $messages,
        LoginForm $loginForm,
        Csrf $csrf
    ): ResponseInterface {
        $session = $request->getAttribute(Session::class);
        if ($loginForm->form->isPost() && $loginForm->form->isValid()) {
            if ($user = $loginForm->getResult()) {
                $session->set('token', $user->getToken());
                $messages->addFlash("Vous êtes connecté !", 'success');
                return $this->redirect('home');
            }
        }
        $token = $csrf->new();
        return $this->render('user/login.html.twig', [
            'form' => $loginForm->form->getData(),
            'token' => $token,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws TypeError
     * @throws Exception
     */
    #[Route('/forgotten-password', name: 'forgotten')]
    public function forgottenPassword(
        FlashService $messages,
        ForgottenForm $forgottenForm,
        Mail $mail,
        Csrf $csrf
    ): ResponseInterface {
        if ($forgottenForm->form->isPost() && $forgottenForm->form->isValid()) {
            if ($user = $forgottenForm->getResult()) {
                $mail->send($user->getEmail(), 'Mot de passe oublié', 'forgotten', ['user' => $user]);
            }
            $messages->addFlash(
                "Si cette adresse est associé à un compte, vous allez recevoir un mail pour changer votre mot de passe",
                'success'
            );
        }
        $token = $csrf->new();
        return $this->render('user/forgotten.html.twig', [
            'form' => $forgottenForm->form->getData(),
            'token' => $token,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    #[Route('/reset-password/{token}', name: 'resetPassword')]
    public function resetPassword(
        string $token,
        ResetPasswordForm $resetPasswordForm,
        FlashService $messages,
        Csrf $csrf
    ): ResponseInterface {
        if ($resetPasswordForm->form->isPost() && $resetPasswordForm->form->isValid()) {
            if (!$resetPasswordForm->getResult($token)) {
                $messages->addFlash("Impossible de changer le mot de passe !", 'danger');
                return $this->redirect('forgotten');
            }
            $messages->addFlash("Votre nouveau mot de passe a bien été enregistré !", 'success');
            return $this->redirect('login');
        }
        $tokenCsrf = $csrf->new();
        return $this->render('user/reset.html.twig', [
            'token' => $token,
            'form' => $resetPasswordForm->form->getData(),
            'csrf' => $tokenCsrf,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws TypeError
     * @throws Exception
     */
    #[Route('/register', name: 'register')]
    public function register(
        FlashService $messages,
        Mail $mail,
        RegisterForm $registerForm,
        Csrf $csrf
    ): ResponseInterface {
        if ($registerForm->form->isPost() && $registerForm->form->isValid()) {
            if ($user = $registerForm->getResult()) {
                $mail->send($user->getEmail(), 'Création de compte', 'register', ['user' => $user]);
                $messages->addFlash("Votre compte a bien été enregistré !", 'success');
                return $this->redirect('login');
            }
        }
        $token = $csrf->new();
        return $this->render('user/register.html.twig', [
            'form' => $registerForm->form->getData(),
            'token' => $token,
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    #[Route('/activate/{token}', name: 'activation')]
    public function activation(
        string $token,
        UserRepository $repository,
        FlashService $messages
    ): ResponseInterface {
        $user = $repository->findByToken($token);
        if (!$user instanceof User) {
            $messages->addFlash("Impossible d'activer votre compte !", 'danger');
            return $this->redirect('login');
        }
        $user->setActive(true);
        $user->setToken(null);
        if (!$repository->updateUser($user)) {
            $messages->addFlash("Unable to update user", 'danger');
        }
        $messages->addFlash("Votre compte est désormais actif !", 'success');
        return $this->redirect('login');
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/logout', name: 'logout', restricted: true)]
    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(Session::class);
        $session->unset('token');
        return $this->redirect('home');
    }
}
