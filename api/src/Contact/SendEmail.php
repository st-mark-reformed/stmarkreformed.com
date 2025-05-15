<?php

declare(strict_types=1);

namespace App\Contact;

use BuzzingPixel\Templating\TemplateEngineFactory;
use Config\ContactFormRecipients;
use Config\SystemFromAddress;
use Psr\Log\LoggerInterface;
use Soundasleep\Html2Text;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Throwable;

use function count;
use function implode;

readonly class SendEmail
{
    public function __construct(
        private LoggerInterface $logger,
        private MailerInterface $mailer,
        private SystemFromAddress $fromAddress,
        private ContactFormRecipients $recipients,
        private TemplateEngineFactory $templateEngineFactory,
    ) {
    }

    public function send(ContactFormData $data): Result
    {
        if ($data->hasErrors()) {
            return new Result(
                success: false,
                message: implode(' ', [
                    'We ran into the following',
                    count($data->errors) > 1 ? 'errors' : 'error',
                    'trying to send your email:',
                ]),
                errors: $data->errors,
            );
        }

        $message = new Email();

        $message->from($this->fromAddress->address);

        $message->to(...$this->recipients->recipients);

        $message->subject('St. Mark Website Contact Form');

        $message->replyTo(new Address(
            address: $data->emailAddress,
            name: $data->name,
        ));

        $html = $this->templateEngineFactory->create()
            ->templatePath(__DIR__ . '/Email.phtml')
            ->addVar('data', $data)
            ->render();

        $message->html($html);

        $message->text(Html2Text::convert($html));

        try {
            $this->mailer->send($message);

            return new Result(
                success: true,
                message: '',
                errors: [],
            );
        } catch (Throwable $e) {
            $this->logger->error(
                $e->getMessage(),
                [
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ],
            );

            return new Result(
                success: false,
                message: 'An unknown error occurred.',
                errors: [],
            );
        }
    }
}
