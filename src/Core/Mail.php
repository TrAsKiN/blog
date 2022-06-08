<?php

namespace Blog\Core;

use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class Mail
{
    public function __construct(
        private readonly Mailer $mailer
    ) {
    }

    /**
     * @throws Exception
     * @throws TransportExceptionInterface
     */
    public function send(string $from, string $to, string $subject, array $content): void
    {
        if (empty($content['text']) || empty($content['html'])) {
            throw new Exception("The text or html content must not be empty!");
        }
        if (empty($from) || empty($to) || empty($subject)) {
            throw new Exception("The receiver, sender or subject must not be empty!");
        }
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->text($content['text'])
            ->html($content['html']);
        $this->mailer->send($email);
    }
}
