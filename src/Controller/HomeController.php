<?php

namespace Blog\Controller;

use Blog\Form\ContactForm;
use Blog\Repository\PostRepository;
use Exception;
use Framework\Attribute\Route;
use Framework\Controller;
use Framework\Csrf;
use Framework\Mail;
use Framework\Service\FlashService;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use TypeError;

class HomeController extends Controller
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    #[Route('/', name: 'home')]
    public function home(
        PostRepository $postRepository,
        Csrf $csrf
    ): ResponseInterface {
        $posts = $postRepository->getPaginatedList(1, 1);
        $token = $csrf->new();
        return $this->render('home/home.html.twig', compact('posts', 'token'));
    }

    /**
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     * @throws TypeError
     * @throws Exception
     */
    #[Route('/contact', name: 'contact')]
    public function contact(
        Mail $mail,
        ContactForm $contactForm,
        FlashService $messages
    ): ResponseInterface {
        if ($contactForm->form->isPost() && $contactForm->form->isValid()) {
            if ($formValues = $contactForm->getResult()) {
                $mail->send('marchal.simon@gmail.com', "Nouveau message !", 'contact', ['form' => $formValues]);
                $messages->addFlash("Votre message a été envoyé !", 'success');
            }
        }
        return $this->redirect('home');
    }
}
