<?php

declare(strict_types=1);

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MailjetService
{
    protected string $public_key;
    protected string $secret_key;

    public function __construct(ContainerBagInterface $container)
    {
        $this->public_key = $container->get('mailjet_public_key');
        $this->secret_key = $container->get('mailjet_secret_key');
    }

    public function send(string $to_mail, string $to_name, string $subject, string $content): bool
    {
        $mj = new Client($this->public_key, $this->secret_key, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "compte-essais@nubox.fr",
                        'Name' => "La boutique française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_mail,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2592985,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    }
}
