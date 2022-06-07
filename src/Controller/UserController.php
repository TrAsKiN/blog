<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Authentication\PasswordEncoder;
use Blog\Core\Controller;
use Blog\Core\FlashMessages;
use Blog\Core\Session;
use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends Controller
{
    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    #[Route('/login', name: 'login')]
    public function login(
        ServerRequestInterface $request,
        UserRepository $repository,
        PasswordEncoder $encoder,
        FlashMessages $messages
    ): ResponseInterface {
        $session = $request->getAttribute(Session::class);
        if ($request->getMethod() === 'POST') {
            $form = $request->getParsedBody();
            try {
                $user = $repository->findByEmail($form['email']);
                if (!$user instanceof User) {
                    throw new Exception("User does not exist!");
                }
                if (!$encoder->isPasswordValid($user->getPassword(), $form['password'])) {
                    throw new Exception("Password is not correct!");
                }
                $token = $encoder->createToken();
                if (!$repository->setToken($user->getId(), $token)) {
                    throw new Exception("Unable to modify user's token");
                }
                $user->setToken($token);
                $session->set('username', $user->getUsername());
                $session->set('token', $user->getToken());
                return $this->redirect('home');
            } catch (Exception $exception) {
                $messages->addFlash($exception->getMessage(), 'danger');
            }
        }
        return $this->render('user/login.html.twig', [
            'form' => $form ?? null,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/register', name: 'register')]
    public function register(
        ServerRequestInterface $request,
        FlashMessages $messages,
        PasswordEncoder $encoder,
        UserRepository $repository
    ): ResponseInterface {
        if ($request->getMethod() === 'POST') {
            $form = $request->getParsedBody();
            try {
                if (empty($form)
                    || empty($form['email'])
                    || empty($form['username'])
                    || empty($form['password'])
                    || empty($form['passwordConfirm'])
                ) {
                    throw new Exception("All fields in the form are required!");
                }
                if ($form['password'] !== $form['passwordConfirm']) {
                    throw new Exception("The password and its confirmation are not identical");
                }
                $user = new User();
                $user->setUsername($form['username']);
                $user->setEmail($form['email']);
                $user->setPassword($encoder->encodePassword($form['password']));
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
            'form' => $form ?? null,
        ]);
    }

    #[Route('/logout', name: 'logout', restricted: true)]
    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $session = $request->getAttribute(Session::class);
        $session->erase();
        return $this->redirect('home');
    }
}
