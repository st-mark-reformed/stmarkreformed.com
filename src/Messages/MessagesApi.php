<?php

declare(strict_types=1);

namespace App\Messages;

use App\Messages\RetrieveMessages\MessageRetrieval;
use App\Messages\RetrieveMessages\MessageRetrievalParams;
use App\Messages\RetrieveMessages\MessagesResult;

class MessagesApi
{
    public function __construct(private MessageRetrieval $messageRetrieval)
    {
    }

    public function retrieveMessages(
        ?MessageRetrievalParams $params = null
    ): MessagesResult {
        return $this->messageRetrieval->fromParams(params: $params);
    }
}
