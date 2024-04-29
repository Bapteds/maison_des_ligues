<?php

namespace App\Services;

// ---------------------------------------------------------------------------------------------------

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

// ---------------------------------------------------------------------------------------------------

class Mailer
{
    private $mailer;



    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    public function sendEmail(string $from, string $to, string $subject, string $textBody, string $htmlBody = null)
    {
        $email = (new Email())->from($from)->to($to)->subject($subject)->text($textBody);

        if ($htmlBody) {
            $email->html($htmlBody);
        }

        $this->mailer->send($email);
    }
}
