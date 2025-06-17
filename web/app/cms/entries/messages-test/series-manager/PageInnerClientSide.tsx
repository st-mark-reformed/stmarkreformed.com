'use client';

import React from 'react';
import { MinusCircleIcon } from '@heroicons/react/24/outline';
import { PlusIcon, TrashIcon } from '@heroicons/react/16/solid';
import PageHeader from '../../../layout/PageHeader';
import EmptyState from '../../../layout/EmptyState';

export default function PageInnerClientSide () {
    const newHref = '/cms/entries/messages-test/series-manager/new';

    return (
        <>
            {/* TODO: Overlay */}
            <div className="mb-4 ">
                <PageHeader
                    title="Message Series"
                    buttons={(() => {
                        // if (hasSelected) {
                        //     return [
                        //         {
                        //             id: 'deselectAll',
                        //             type: 'secondary',
                        //             content: (
                        //                 <>
                        //                     <MinusCircleIcon className="h-5 w-5 mr-1" />
                        //                     Deselect All
                        //                 </>
                        //             ),
                        //             onClick: () => setSelectedIds([]),
                        //         },
                        //         {
                        //             id: 'deleteSelected',
                        //             type: 'primary',
                        //             content: (
                        //                 <>
                        //                     <TrashIcon className="h-5 w-5 mr-1" />
                        //                     Delete Selected
                        //                 </>
                        //             ),
                        //             onClick: () => setOverlay('confirmDelete'),
                        //         },
                        //     ];
                        // }

                        console.log('TODO');

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
            {/* TODO: Message display */}
            <EmptyState
                itemNameSingular="Series"
                itemNamePlural="Series"
                buttonHref={newHref}
            />
        </>
    );
}
