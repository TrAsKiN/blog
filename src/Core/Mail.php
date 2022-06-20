<?php

namespace Blog\Core;

use Blog\Core\Service\FlashService;
use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use TypeError;

class Mail
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly Environment $twig,
        private readonly FlashService $messages
    ) {
    }

    /**
     * @throws TypeError
     * @throws TransportExceptionInterface
     */
    public function send(string $to, string $subject, string $template, array $params = []): void
    {
        try {
            $email = (new Email())
                ->from('moi@traskin.net')
                ->to($to)
                ->subject($subject)
                ->text($this->twig->render(sprintf('mail/%s.txt.twig', $template), $params))
                ->html($this->twig->render(sprintf('mail/%s.html.twig', $template), $params))
            ;
            $this->mailer->send($email);
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'warning');
        }
    }
}
