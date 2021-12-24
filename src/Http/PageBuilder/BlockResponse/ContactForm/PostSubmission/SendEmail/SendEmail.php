<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\SendEmail;

use App\Email\EmailApi;
use App\Email\Entities\Email;
use App\Email\Entities\EmailRecipient;
use App\Http\PageBuilder\BlockResponse\ContactForm\PostSubmission\FormValues;
use App\Http\Shared\Exceptions\InvalidEmailAddress;
use craft\errors\InvalidFieldException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SendEmail implements SendEmailContract
{
    public function __construct(
        private EmailApi $emailApi,
        private TwigEnvironment $twig,
        private GetEmailRecipients $getEmailRecipients,
    ) {
    }

    /**
     * @throws InvalidEmailAddress
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidFieldException
     */
    public function send(FormValues $formValues): SendEmailResult
    {
        $twigContext = [
            'name' => $formValues->name(),
            'emailAddress' => $formValues->email(),
            'message' => $formValues->message(),
        ];

        $email = new Email(
            subject: 'St. Mark Website Contact Form',
            recipients: $this->getEmailRecipients->get(),
            from: new EmailRecipient(
                emailAddress: $formValues->email()->toString(),
                name: $formValues->name()->toString(),
            ),
            plaintext: $this->twig->render(
                '@app/Http/PageBuilder/BlockResponse/ContactForm/PostSubmission/SendEmail/EmailTemplatePlainText.twig',
                $twigContext,
            ),
            html: $this->twig->render(
                '@app/Http/PageBuilder/BlockResponse/ContactForm/PostSubmission/SendEmail/EmailTemplateHtml.twig',
                $twigContext,
            ),
        );

        if ($email->isNotValid()) {
            foreach ($email->errorMessages() as $key => $message) {
                $formValues = $formValues->withErrorMessage(
                    key: $key,
                    message: $message,
                );
            }

            return new SendEmailResult(
                sentSuccessfully: false,
                formValues: $formValues,
            );
        }

        $this->emailApi->enqueue(email: $email);

        return new SendEmailResult(
            sentSuccessfully: true,
            formValues: $formValues,
        );
    }
}
