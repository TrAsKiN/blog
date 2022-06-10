<?php

namespace Blog\Core;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use TypeError;

class Mail
{
    public function __construct(
        private readonly Mailer $mailer,
        private readonly Environment $twig
    ) {
    }

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
        } catch (TransportExceptionInterface|TypeError|LoaderError|RuntimeError|SyntaxError $exception) {
            $exception->getMessage();
        }
    }
}
