<?php
// src/Controller/SendEmailController.php

namespace App\Controller;

use App\Entity\EmailMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SendEmailController extends AbstractController
{
    private MailerInterface $mailer;
    private ValidatorInterface $validator;
    private ParameterBagInterface $params;

    public function __construct(MailerInterface $mailer, ValidatorInterface $validator,ParameterBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->validator = $validator;
        $this->params = $params;
    }

    public function __invoke(EmailMessage $data): Response
    {


        // Validation
        $errors = $this->validator->validate($data);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }


        $fromEmail = $this->params->get('email_from');
        $toEmail = $this->params->get('email_to');

        try {
        // Envoi
        $email = (new Email())
            ->from($fromEmail)
            ->replyTo($data->email)
            ->to( $toEmail) // Remplacez par l'adresse email de destination
            ->subject('Message du formulaire de contact ' . $data->name)
            ->text($data->message);

        $this->mailer->send($email);
            return $this->json(['success' => true], Response::HTTP_OK);

        } catch (\Exception $e) {

            return $this->json(['success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

