'use client';

import React, { useState } from 'react';
import { PlusIcon, TrashIcon } from '@heroicons/react/16/solid';
import { createPortal } from 'react-dom';
import { MinusCircleIcon } from '@heroicons/react/24/outline';
import { Profile } from './Profile';
import ProfileListingItem from './ProfileListingItem';
import PageHeader from '../layout/PageHeader';
import EmptyState from '../layout/EmptyState';
import RenderOnMount from '../../RenderOnMount';
import ConfirmDeleteOverlay from '../ConfirmDeleteOverlay';
import DeleteProfiles from './DeleteProfiles';
import Message from '../messaging/Message';

export default function PageInnerClientSide (
    {
        profiles,
    }: {
        profiles: Array<Profile>;
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

    const newHref = '/cms/profiles/new';

    const [errorMessages, setErrorMessages] = useState<Array<string>>([]);

    const hasSelected = selectedIds.length > 0;

    return (
        <>
            <RenderOnMount>
                {(() => {
                    if (overlay === 'confirmDelete') {
                        return createPortal(
                            <ConfirmDeleteOverlay
                                closeOverlay={closeOverlay}
                                isDeleting={isDeleting}
                                heading="Delete Selected Profiles?"
                                body="This is a non-recoverable action, and has the potential to break relationships with sermon speakers. Do you wish to proceed?"
                                proceed={() => {
                                    setIsDeleting(true);

                                    DeleteProfiles(selectedIds).then((response) => {
                                        closeOverlay();

                                        setIsDeleting(false);

                                        if (response.success) {
                                            setSelectedIds([]);

                                            return;
                                        }

                                        setErrorMessages(response.messages || ['An unknown error occurred']);
                                    });
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
                    title="Profiles"
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
                                    New Profile
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
                if (profiles.length > 0) {
                    return null;
                }

                return (
                    <EmptyState
                        itemNameSingular="Profile"
                        itemNamePlural="Profiles"
                        buttonHref={newHref}
                    />
                );
            })()}
            <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                {profiles.map((profile) => (
                    <ProfileListingItem
                        key={profile.id}
                        profile={profile}
                        selectedIds={selectedIds}
                        setSelectedIds={setSelectedIds}
                    />
                ))}
            </ul>
        </>
    );
}
