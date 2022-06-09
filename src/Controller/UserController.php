<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Authentication\PasswordEncoder;
use Blog\Core\Authentication\UserProvider;
use Blog\Core\Controller;
use Blog\Core\FlashMessages;
use Blog\Core\Form;
use Blog\Core\Session;
use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws NotFoundExceptionInterface
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route('/login', name: 'login')]
    public function login(
        ServerRequestInterface $request,
        UserProvider $provider,
        FlashMessages $messages
    ): ResponseInterface {
        $session = $request->getAttribute(Session::class);
        $form = $this->get(Form::class);
        $requirements = [
            'required' => ['email', 'password'],
            'notEmpty' => ['email', 'password'],
        ];
        if ($form->isPost() && $form->isValid($requirements)) {
            try {
                $user = $provider->login($form->getForm()['email'], $form->getForm()['password']);
                $session->set('username', $user->getUsername());
                $session->set('token', $user->getToken());
                $messages->addFlash("Vous êtes connecté !", 'success');
                return $this->redirect('home');
            } catch (Exception $exception) {
                $messages->addFlash($exception->getMessage(), 'danger');
            }
        }
        return $this->render('user/login.html.twig', [
            'form' => $form->getForm(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/register', name: 'register')]
    public function register(
        ServerRequestInterface $request,
        FlashMessages $messages,
        PasswordEncoder $encoder,
        UserRepository $repository
    ): ResponseInterface {
        $form = $this->get(Form::class);
        $requirements = [
            'required' => ['email', 'username', 'password', 'passwordConfirm'],
            'notEmpty' => ['email', 'username', 'password', 'passwordConfirm'],
            'length' => ['password', null, 50],
            'isEquals' => ['password', 'passwordConfirm'],
        ];
        if ($form->isPost() && $form->isValid($requirements)) {
            try {
                $user = new User();
                $user->setUsername($form->getForm()['username']);
                $user->setEmail($form->getForm()['email']);
                $user->setPassword($encoder->encodePassword($form->getForm()['password']));
                $user->setToken($encoder->createToken());
                $user->setActive(true);
                if (!$repository->addUser($user)) {
                    throw new Exception("Unable to register user");
                }
                $messages->addFlash("Votre compte a bien été enregistré !", 'success');
                return $this->redirect('login');
            } catch (Exception $exception) {
                $messages->addFlash($exception->getMessage(), 'danger');
            }
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->getForm(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/logout', name: 'logout', restricted: true)]
    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(Session::class);
        $session->erase();
        return $this->redirect('home');
    }
}
