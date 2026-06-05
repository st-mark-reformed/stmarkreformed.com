'use client';

import React from 'react';
import { MailingList } from '../../MailingList';
import CreateEditMailingListPage from '../../CreateEditMailingListPage';

export default function EditMailingListPageClientSide (
    {
        mailingList,
    }: {
        mailingList: MailingList;
    },
) {
    return (
        <CreateEditMailingListPage
            pageTitle={`Edit Mailing List: ${mailingList.listName}`}
            submitFormAction="edit"
            initialValues={{
                listName: mailingList.listName,
                listAddress: mailingList.listAddress,
                imapServer: mailingList.imapServer,
                imapPort: String(mailingList.imapPort),
                connectionType: mailingList.connectionType,
                username: mailingList.username,
                // Never prefilled; blank submits as "keep current".
                password: '',
                subscribers: mailingList.subscribers.map((subscriber) => ({
                    name: subscriber.name,
                    emailAddress: subscriber.emailAddress,
                })),
            }}
            mailingListId={mailingList.id}
        />
    );
}
