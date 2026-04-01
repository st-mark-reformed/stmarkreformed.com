'use client';

import React, { useRef, useState } from 'react';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import CardList, { CardListHandle } from '../CardList/CardList';
import { Message } from './Message';

export default function MessagesPageClientSide (
    {
        messages,
    }: {
        messages: Message[];
    },
) {
    const formRef = useRef<CardListHandle>(null);

    const [hasChecked, setHasChecked] = useState(false);

    const buttons: Button[] = (() => {
        if (hasChecked) {
            return [
                {
                    type: 'secondary',
                    content: 'Deselect All',
                    glyph: 'x-mark',
                    href: 'deselect-button',
                    onClick: () => {
                        formRef.current?.clearCheckedItems();
                    },
                },
                // TODO: if isPending
                {
                    type: 'primary',
                    content: 'Delete Selected',
                    glyph: 'trash',
                    href: 'delete-button',
                    onClick: () => {
                        formRef.current?.requestSubmit();
                    },
                },
            ];
        }

        return [
            {
                type: 'secondary',
                content: 'View All Series',
                glyph: 'eye',
                href: '/admin/messages/series',
            },
            {
                type: 'secondary',
                content: 'New Series',
                glyph: 'plus',
                href: '/admin/messages/series/new',
            },
            {
                type: 'primary',
                content: 'New Message',
                glyph: 'plus',
                href: '/admin/messages/new',
            },
        ];
    })();

    return (
        <>
            <Breadcrumbs />
            <PageTitle buttons={buttons}>
                Messages
            </PageTitle>
            <CardList
                ref={formRef}
                noItemsFoundMessage="No messages found."
                items={messages.map((message) => ({
                    id: message.id,
                    columns: [
                        {
                            id: `title-${message.id}`,
                            line1: <>{message.title} <span className="text-xs font-light ml-2">(slug: {message.slug})</span></>,
                            line2: message.speaker.fullNameWithHonorific,
                        },
                        {
                            id: `metadata-${message.id}`,
                            line1: message.date,
                            line2: (
                                <>
                                    {/* eslint-disable-next-line react/no-unstable-nested-components */}
                                    {(() => {
                                        if (!message.passage) {
                                            return null;
                                        }

                                        return <><span className="font-bold">Text:</span>&nbsp;{message.passage}</>;
                                    })()}
                                    {/* eslint-disable-next-line react/no-unstable-nested-components */}
                                    {(() => {
                                        if (!message.series.title) {
                                            return null;
                                        }

                                        return <><span className="font-bold ml-2">Series:</span>&nbsp;{message.series.title}</>;
                                    })()}
                                </>
                            ),
                        },
                        [
                            {
                                content: 'View Page',
                                href: `/media/messages/${message.slug}`,
                                type: 'secondary',
                                rightGlyph: 'chevron-double-left',
                            },
                            {
                                content: 'Edit',
                                href: `/admin/messages/edit/${message.id}`,
                                type: 'primary',
                                rightGlyph: 'pencil',
                            },
                        ],
                    ],
                }))}
                onCheckedChange={(hasCheckedItems) => {
                    setHasChecked(hasCheckedItems);
                }}
                showCheckBoxes
            />
        </>
    );
}
