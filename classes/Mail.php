<?php

namespace Classes;

require_once __DIR__ . '/../vendor/autoload.php';

use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Mail
{
    private $transport;
    private $from;

    public function __construct()
    {
        $config = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);

        $this->from = array($config['mail']['from']);
        $this->transport = (new Swift_SmtpTransport($config['mail']['host'], config['mail']['port']))
                            ->setUsername($config['mail']['username'])
                            ->setUsername($config['mail']['password']);
    }

    /**
     * Send Email
     *
     * @param $subject
     * @param $body
     * @param $from
     */
    public function send($to, $subject, $body, $from = array())
    {
        $mailer = new Swift_Mailer($this->transport);
        $message = (new Swift_Message($subject))
                    ->setFrom($from ?? $this->from)->setTo($to)
                    ->setBody($body);

        try {
            $mailer->send($message);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}
