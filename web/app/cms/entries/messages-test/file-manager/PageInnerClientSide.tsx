'use client';

import React, { useState } from 'react';
import { MinusCircleIcon } from '@heroicons/react/24/outline';
import { TrashIcon } from '@heroicons/react/16/solid';
import { createPortal } from 'react-dom';
import PageHeader from '../../../layout/PageHeader';
import Message from '../../../messaging/Message';
import { File } from './File';
import FileListingItem from './FileListingItem';
import ConfirmDeleteOverlay from '../../../ConfirmDeleteOverlay';
import RenderOnMount from '../../../../RenderOnMount';
import DeleteFiles from './DeleteFiles';

export default function PageInnerClientSide (
    {
        files,
    }: {
        files: Array<File>;
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

    const [selectedNames, setSelectedNames] = useState<Array<string>>([]);

    const [errorMessages, setErrorMessages] = useState<Array<string>>([]);

    const hasSelected = selectedNames.length > 0;

    return (
        <>
            <RenderOnMount>
                {(() => {
                    if (overlay === 'confirmDelete') {
                        return createPortal(
                            <ConfirmDeleteOverlay
                                closeOverlay={closeOverlay}
                                isDeleting={isDeleting}
                                heading="Delete Selected Files?"
                                body="This is a non-recoverable action, and has the potential to break relationships with sermons. Do you wish to proceed?"
                                proceed={() => {
                                    setIsDeleting(true);

                                    DeleteFiles(selectedNames).then((response) => {
                                        closeOverlay();

                                        setIsDeleting(false);

                                        if (response.success) {
                                            setSelectedNames([]);

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
                    title="Message File Manager"
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
                                    onClick: () => setSelectedNames([]),
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

                        return [];
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
            <ul className="divide-y divide-gray-100 overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                {files.map((file) => (
                    <FileListingItem
                        key={file.filename}
                        file={file}
                        selectedNames={selectedNames}
                        setSelectedNames={setSelectedNames}
                    />
                ))}
            </ul>
        </>
    );
}
