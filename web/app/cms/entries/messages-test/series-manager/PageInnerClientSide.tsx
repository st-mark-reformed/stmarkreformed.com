'use client';

import React, { useState } from 'react';
import { MinusCircleIcon } from '@heroicons/react/24/outline';
import { PlusIcon, TrashIcon } from '@heroicons/react/16/solid';
import { createPortal } from 'react-dom';
import PageHeader from '../../../layout/PageHeader';
import EmptyState from '../../../layout/EmptyState';
import { MessageSeries } from './MessageSeries';
import RenderOnMount from '../../../../RenderOnMount';
import Message from '../../../messaging/Message';
import ConfirmDeleteOverlay from '../../../ConfirmDeleteOverlay';
import MessageSeriesListingItem from './MessageSeriesListingItem';

export default function PageInnerClientSide (
    {
        messageSeries,
    }: {
        messageSeries: Array<MessageSeries>;
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

    const newHref = '/cms/entries/messages-test/series-manager/new';

    return (
        <>
            {/* TODO: Overlay */}
            <RenderOnMount>
                {(() => {
                    if (overlay === 'confirmDelete') {
                        return createPortal(
                            <ConfirmDeleteOverlay
                                closeOverlay={closeOverlay}
                                isDeleting={isDeleting}
                                heading="Delete Selected Series?"
                                body="This is a non-recoverable action, and has the potential to break relationships with sermon series. Do you wish to proceed?"
                                proceed={() => {
                                    setIsDeleting(true);

                                    // TODO: Implement DeleteMessageSeries
                                    // DeleteMessageSeries(selectedIds).then((response) => {
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
                    title="Message Series"
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

                        return [{
                            id: 'newProfile',
                            type: 'primary',
                            content: (
                                <>
                                    <PlusIcon className="h-5 w-5 mr-1" />
                                    New Series
                                </>
                            ),
                            href: newHref,
                        }];
                    })()}
                />
            </div>
            <Message
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
                if (messageSeries.length > 0) {
                    return null;
                }

                return (
                    <EmptyState
                        itemNameSingular="Series"
                        itemNamePlural="Series"
                        buttonHref={newHref}
                    />
                );
            })()}
            <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                {messageSeries.map((series) => (
                    <MessageSeriesListingItem
                        key={series.id}
                        series={series}
                        selectedIds={selectedIds}
                        setSelectedIds={setSelectedIds}
                    />
                ))}
            </ul>
        </>
    );
}
