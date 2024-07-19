<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\SendEmailController;
use Symfony\Component\Validator\Constraints as Assert;



#[ApiResource(
    operations: [
        new Post(
            controller: SendEmailController::class,
            normalizationContext: ['groups' => ['email_send']],
            denormalizationContext: ['groups' => ['email_send']],
            validate: false,
        )

    ]
)]

class EmailMessage
{
    #[Assert\NotBlank]
    #[Groups(['email_send'])]
    public ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['email_send'])]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Groups(['email_send'])]
    public ?string $message = null;


}

