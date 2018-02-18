<?php

namespace dev\controllers;

use Craft;
use craft\mail\Message;
use dev\services\StorageService;

/**
 * Class ContactController
 */
class ContactController extends BaseController
{
    /** @var StorageService $storage */
    private $storage;

    /** @var array $inputValues */
    private $inputValues = [
        'site' => '',
        'mailing_address' => '',
        'name' => '',
        'email' => '',
        'message' => '',
    ];

    private $redirectUri = '/';

    /**
     * Init runs on controller construction
     */
    public function init()
    {
        $this->storage = StorageService::getInstance();

        $request = Craft::$app->getRequest();

        $this->redirectUri = $request->post('redirect');

        foreach ($this->inputValues as $key => &$val) {
            $val = $request->post($key);
        }
    }

    /**
     * Deals with contact form submission
     * @throws \Exception
     */
    public function actionFormSubmission()
    {
        $this->requirePostRequest();

        $this->storage->set($this->inputValues, 'ContactInputValues');

        if ($this->inputValues['site'] ||
            $this->inputValues['mailing_address']
        ) {
            $this->storage->set(true, 'ContactHasErrors');
            $this->storage->set(
                'We were unable to validate your form submission',
                'ContactErrorMessage'
            );
            return;
        }

        $inputErrors = [];

        if (! $this->inputValues['name']) {
            $inputErrors['name'] = 'Name is required';
        }

        if (! $this->inputValues['email']) {
            $inputErrors['email'] = 'Email is required';
        } else {
            $validEmail = filter_var(
                $this->inputValues['email'],
                FILTER_VALIDATE_EMAIL
            );

            if (! $validEmail) {
                $inputErrors['email'] = 'A valid email address is required';
            }
        }

        if (! $this->inputValues['message']) {
            $inputErrors['message'] = 'Message is required';
        }

        $this->storage->set($inputErrors, 'ContactInputErrors');

        if ($inputErrors) {
            $this->storage->set(
                'We were unable to validate your form submission',
                'ContactErrorMessage'
            );
            return;
        }

        $message = new Message();

        /** @var array $contactFormRecipients */
        $contactFormRecipients = Craft::$app->getGlobals()
            ->getSetByHandle('general')
            ->contactFormRecipients;

        $to = [];

        foreach ($contactFormRecipients as $recipient) {
            $to[] = $recipient['emailAddress'];
        }

        $message->setFrom('info@stmarkreformed.com');
        $message->setSubject('St. Mark Website Contact Form');
        $message->setTo($to);
        $message->setReplyTo([
            $this->inputValues['email'] => $this->inputValues['name']
        ]);

        $html = "<strong>From Name:</strong> {$this->inputValues['name']}<br>";
        $text = "From Name: {$this->inputValues['name']}\n";

        $html .= "<strong>From Email:</strong> {$this->inputValues['email']}<br>";
        $text .= "From Email: {$this->inputValues['email']}\n";

        $html .= '<strong>Message:</strong> ' .
            nl2br($this->inputValues['message']);
        $text .= "Message: {$this->inputValues['message']}";

        $message->setHtmlBody($html);
        $message->setTextBody($text);

        Craft::$app->getMailer()->send($message);

        $this->redirect($this->redirectUri);
    }
}
