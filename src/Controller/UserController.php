<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Authentication\PasswordEncoder;
use Blog\Core\Authentication\UserProvider;
use Blog\Core\Controller;
use Blog\Core\Form;
use Blog\Core\Mail;
use Blog\Core\Service\FlashService;
use Blog\Core\Session;
use Blog\Entity\User;
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
     */
    #[Route('/login', name: 'login')]
    public function login(
        ServerRequestInterface $request,
        UserProvider $provider,
        FlashService $messages,
        Form $form
    ): ResponseInterface {
        $session = $request->getAttribute(Session::class);
        $requirements = [
            'required' => ['email', 'password'],
            'notEmpty' => ['email', 'password'],
        ];
        if ($form->isPost() && $form->isValid($requirements)) {
            try {
                $user = $provider->login($form->getData('email'), $form->getData('password'));
                $session->set('username', $user->getUsername());
                $session->set('token', $user->getToken());
                $messages->addFlash("Vous êtes connecté !", 'success');
                return $this->redirect('home');
            } catch (Exception $exception) {
                $messages->addFlash($exception->getMessage(), 'danger');
            }
        }
        return $this->render('user/login.html.twig', [
            'form' => $form->getData(),
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws PDOException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws TypeError
     * @throws Exception
     */
    #[Route('/forgotten-password', name: 'forgotten')]
    public function forgotten(
        UserRepository $repository,
        FlashService $messages,
        Form $form,
        PasswordEncoder $encoder,
        Mail $mail
    ): ResponseInterface {
        $requirements = [
            'required' => ['email'],
            'notEmpty' => ['email'],
        ];
        if ($form->isPost() && $form->isValid($requirements)) {
            $user = $repository->findByEmail($form->getData('email'));
            if ($user instanceof User) {
                $user->setToken($encoder->createToken());
                $repository->updateUser($user);
                $mail->send(
                    'moi@traskin.net',
                    $user->getEmail(),
                    'Mot de passe oublié',
                    [
                        'html' => $this->twig->render('mail/forgotten.html.twig', [
                            'user' => $user,
                        ]),
                        'text' => $this->twig->render('mail/forgotten.txt.twig', [
                            'user' => $user,
                        ]),
                    ]
                );
            }
            $messages->addFlash(
                "Si cette adresse est associé à un compte, vous allez recevoir un mail pour changer votre mot de passe",
                'success'
            );
        }
        return $this->render('user/forgotten.html.twig', [
            'form' => $form->getData(),
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws PDOException
     * @throws Exception
     */
    #[Route('/forgotten-password/{token}', name: 'password')]
    public function password(
        string $token,
        Form $form,
        UserRepository $repository,
        FlashService $messages,
        PasswordEncoder $encoder
    ): ResponseInterface {
        $requirements = [
            'required' => ['password', 'passwordConfirm'],
            'notEmpty' => ['password', 'passwordConfirm'],
            'length' => ['password', null, 50],
            'isEquals' => ['password', 'passwordConfirm'],
        ];
        if ($form->isPost() && $form->isValid($requirements)) {
            $user = $repository->findByToken($token);
            if (!$user instanceof User) {
                $messages->addFlash("Aucune correspondance avec un utilisateur enregistré !", 'danger');
                return $this->redirect('forgotten');
            }
            $user->setPassword($encoder->encodePassword($form->getData('password')));
            $user->setToken(null);
            if (!$repository->updateUser($user)) {
                $messages->addFlash("Impossible de mettre à jour les informations !", 'danger');
                return $this->redirect('forgotten');
            }
            $messages->addFlash("Votre nouveau mot de passe a bien été enregistré !", 'success');
            return $this->redirect('login');
        }
        return $this->render('user/password.html.twig', [
            'token' => $token,
            'form' => $form->getData()
        ]);
    }

    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    #[Route('/register', name: 'register')]
    public function register(
        FlashService $messages,
        PasswordEncoder $encoder,
        UserRepository $repository,
        Mail $mail,
        Form $form
    ): ResponseInterface {
        $requirements = [
            'required' => ['email', 'username', 'password', 'passwordConfirm'],
            'notEmpty' => ['email', 'username', 'password', 'passwordConfirm'],
            'length' => ['password', null, 50],
            'isEquals' => ['password', 'passwordConfirm'],
        ];
        if ($form->isPost() && $form->isValid($requirements)) {
            try {
                $user = new User();
                $user->setUsername($form->getData('username'));
                $user->setEmail($form->getData('email'));
                $user->setPassword($encoder->encodePassword($form->getData('password')));
                $user->setActive(false);
                $user->setToken($encoder->createToken());
                if (!$repository->addUser($user)) {
                    throw new Exception("Unable to register user");
                }
                try {
                    $mail->send(
                        'moi@traskin.net',
                        $user->getEmail(),
                        'Création de compte',
                        [
                            'html' => $this->twig->render('mail/register.html.twig', [
                                'user' => $user,
                            ]),
                            'text' => $this->twig->render('mail/register.txt.twig', [
                                'user' => $user,
                            ]),
                        ]
                    );
                } catch (TransportExceptionInterface|TypeError $exception) {
                    $messages->addFlash($exception->getMessage(), 'danger');
                }
                $messages->addFlash("Votre compte a bien été enregistré !", 'success');
                return $this->redirect('login');
            } catch (Exception $exception) {
                $messages->addFlash($exception->getMessage(), 'danger');
            }
        }
        return $this->render('user/register.html.twig', [
            'form' => $form->getData(),
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
        $session->unset('username');
        $session->unset('token');
        return $this->redirect('home');
    }
}
