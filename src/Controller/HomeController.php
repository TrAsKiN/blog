<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Mail;
use Blog\Core\Service\FlashService;
use Blog\Form\ContactForm;
use Blog\Repository\PostRepository;
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
     */
    #[Route('/', name: 'home')]
    public function home(PostRepository $postRepository): ResponseInterface
    {
        $posts = $postRepository->getPaginatedList(1, 1);
        return $this->render('home/home.html.twig', compact('posts'));
    }

    /**
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     * @throws TypeError
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
