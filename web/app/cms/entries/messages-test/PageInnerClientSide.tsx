'use client';

import React, { useState } from 'react';
import { createPortal } from 'react-dom';
import { DocumentIcon, MinusCircleIcon, TagIcon } from '@heroicons/react/24/outline';
import { PlusIcon, TrashIcon } from '@heroicons/react/16/solid';
import { Message } from './Message';
import ConfirmDeleteOverlay from '../../ConfirmDeleteOverlay';
import RenderOnMount from '../../../RenderOnMount';
import PageHeader from '../../layout/PageHeader';
import EmptyState from '../../layout/EmptyState';
import MessageDisplay from '../../messaging/Message';
import MessageListingItem from './MessageListingItem';

export default function PageInnerClientSide (
    {
        unpublishedMessages,
        publishedMessages,
    }: {
        unpublishedMessages: Array<Message>;
        publishedMessages: Array<Message>;
    },
) {
    const [overlay, setOverlay] = useState<
        ''
        | 'confirmDelete'
    >('');

    const closeOverlay = () => {
        setOverlay('');
    };

    const [isDeleting, setIsDeleting] = useState(false);

    const [selectedIds, setSelectedIds] = useState<Array<string>>([]);

    const [errorMessages, setErrorMessages] = useState<Array<string>>([]);

    const hasSelected = selectedIds.length > 0;

    const newHref = '/cms/entries/messages-test/new';

    return (
        <>
            <RenderOnMount>
                {(() => {
                    if (overlay === 'confirmDelete') {
                        return createPortal(
                            <ConfirmDeleteOverlay
                                closeOverlay={closeOverlay}
                                isDeleting={isDeleting}
                                heading="Delete Selected Messages?"
                                body="This is a non-recoverable action. Do you wish to proceed?"
                                proceed={() => {
                                    setIsDeleting(true);

                                    // TODO
                                    // DeleteMessage(selectedIds).then((response) => {
                                    //     closeOverlay();
                                    //
                                    //     setIsDeleting(false);
                                    //
                                    //     if (response.success) {
                                    //         setSelectedIds([]);
                                    //
                                    //         return;
                                    //     }
                                    //
                                    //     setErrorMessages(response.messages || ['An unknown error occurred']);
                                    // });
                                }}
                            />,
                            document.body,
                        );
                    }

                    return null;
                })()}
            </RenderOnMount>
            <div className="mb-4 ">
                <PageHeader
                    title="Messages"
                    buttons={(() => {
                        if (hasSelected) {
                            return [
                                {
                                    id: 'deselectAll',
                                    type: 'secondary',
                                    content: (
                                        <>
                                            <MinusCircleIcon className="h-5 w-5 mr-1" />
                                            Deselect All
                                        </>
                                    ),
                                    onClick: () => setSelectedIds([]),
                                },
                                {
                                    id: 'deleteSelected',
                                    type: 'primary',
                                    content: (
                                        <>
                                            <TrashIcon className="h-5 w-5 mr-1" />
                                            Delete Selected
                                        </>
                                    ),
                                    onClick: () => setOverlay('confirmDelete'),
                                },
                            ];
                        }

                        return [
                            {
                                id: 'fileManager',
                                type: 'secondary',
                                content: (
                                    <>
                                        <DocumentIcon className="h-5 w-5 mr-1" />
                                        File Manager
                                    </>
                                ),
                                href: '/cms/entries/messages-test/file-manager',
                            },
                            {
                                id: 'seriesManager',
                                type: 'secondary',
                                content: (
                                    <>
                                        <TagIcon className="h-5 w-5 mr-1" />
                                        Series Manager
                                    </>
                                ),
                                href: '/cms/entries/messages-test/series-manager',
                            },
                            {
                                id: 'newEntry',
                                type: 'primary',
                                content: (
                                    <>
                                        <PlusIcon className="h-5 w-5 mr-1" />
                                        New Entry
                                    </>
                                ),
                                href: newHref,
                            },
                        ];
                    })()}
                />
            </div>
            <MessageDisplay
                isVisible={errorMessages.length > 0}
                setIsVisible={(state) => {
                    if (state !== false) {
                        return;
                    }

                    setErrorMessages([]);
                }}
                type="error"
                heading="There was an error"
                body={errorMessages}
                padBottom
            />
            {(() => {
                if (unpublishedMessages.length > 0 || publishedMessages.length > 0) {
                    return null;
                }

                return (
                    <EmptyState
                        itemNameSingular="Entry"
                        itemNamePlural="Entries"
                        buttonHref={newHref}
                    />
                );
            })()}
            {(() => {
                if (unpublishedMessages.length < 1) {
                    return null;
                }

                return (
                    <div className="mb-12 bg-gray-100 p-4 rounded-lg">
                        <h3 className="font-bold text-lg text-gray-600 ml-1 mb-2">
                            Unpublished drafts
                        </h3>
                        <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                            {unpublishedMessages.map((message) => (
                                <MessageListingItem
                                    key={message.id}
                                    message={message}
                                    selectedIds={selectedIds}
                                    setSelectedIds={setSelectedIds}
                                />
                            ))}
                        </ul>
                    </div>
                );
            })()}
            {(() => {
                if (publishedMessages.length < 1) {
                    return null;
                }

                return (
                    <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                        {publishedMessages.map((message) => (
                            <MessageListingItem
                                key={message.id}
                                message={message}
                                selectedIds={selectedIds}
                                setSelectedIds={setSelectedIds}
                            />
                        ))}
                    </ul>
                );
            })()}
        </>
    );
}
