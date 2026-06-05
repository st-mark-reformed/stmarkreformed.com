<?php

declare(strict_types=1);

namespace App\MailingLists\Admin\NewMailingList;

use App\MailingLists\Admin\SubscriberResolver;
use App\MailingLists\ConnectionType;
use App\MailingLists\NewMailingList;
use RxAnte\AppBootstrap\Request\ServerRequest;

readonly class NewMailingListFactory
{
    public function __construct(private SubscriberResolver $subscriberResolver)
    {
    }

    public function createFromRequest(ServerRequest $request): NewMailingList
    {
        return new NewMailingList(
            listName: $request->parsedBody->getString(name: 'listName'),
            listAddress: $request->parsedBody->getString(name: 'listAddress'),
            imapServer: $request->parsedBody->getString(name: 'imapServer'),
            imapPort: $request->parsedBody->getInt(name: 'imapPort'),
            connectionType: ConnectionType::fromString(
                $request->parsedBody->getString(name: 'connectionType'),
            ),
            username: $request->parsedBody->getString(name: 'username'),
            password: $request->parsedBody->getString(name: 'password'),
            subscribers: $this->subscriberResolver->resolve(request: $request),
        );
    }
}
