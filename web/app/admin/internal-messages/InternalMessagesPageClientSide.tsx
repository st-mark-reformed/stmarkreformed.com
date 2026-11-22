'use client';

import React, {
    useActionState,
    useEffect,
    useRef,
    useState,
} from 'react';
import { useRouter } from 'next/navigation';
import PageTitle, { Button } from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';
import CardList, { CardListHandle } from '../CardList/CardList';
import { InternalMessage } from './InternalMessage';
import SubmitDeleteInternalMessagesFormAction from './SubmitDeleteInternalMessagesFormAction';
import Alert from '../../Alert';
import Pagination from '../../Pagination/Pagination';

export default function InternalMessagesPageClientSide (
    {
        messages,
        currentPage,
        totalPages,
    }: {
        messages: InternalMessage[];
        currentPage: number;
        totalPages: number;
    },
) {
    const router = useRouter();

    const formRef = useRef<CardListHandle>(null);

    const [hasChecked, setHasChecked] = useState(false);

    const [state, formAction, isPending] = useActionState(
        /**
         * I can't figure out why the types aren't matching. As far as I can
         * tell, they're exactly right.
         */
        // @ts-expect-error TS2769
        SubmitDeleteInternalMessagesFormAction,
        {
            status: 'unsubmitted',
            iteration: 0,
            message: '',
        },
    );

    useEffect(() => {
        formRef.current?.clearCheckedItems();
    }, [state]);

    useEffect(() => {
        if (state.status !== 'success') {
            return;
        }

        router.refresh();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [state]);

    const buttons: Button[] = (() => {
        if (isPending) {
            return [
                {
                    type: 'pending',
                    content: 'Deleting…',
                    glyph: 'trash',
                    href: 'delete-button',
                    onClick: () => {},
                },
            ];
        }

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
                href: '/admin/internal-messages/series',
            },
            {
                type: 'secondary',
                content: 'New Series',
                glyph: 'plus',
                href: '/admin/internal-messages/series/new',
            },
            {
                type: 'primary',
                content: 'New Internal Message',
                glyph: 'plus',
                href: '/admin/internal-messages/new',
            },
        ];
    })();

    const pagination = (
        <Pagination
            baseUrl="/admin/internal-messages"
            currentPage={currentPage}
            totalPages={totalPages}
        />
    );

    return (
        <>
            <Breadcrumbs />
            <PageTitle buttons={buttons}>Internal Messages</PageTitle>
            {(() => {
                if (state.status !== 'failure' || isPending) {
                    return null;
                }

                return (
                    <div className="mb-4">
                        <Alert
                            headline={state.message}
                            type="error"
                        />
                    </div>
                );
            })()}
            <div className="pb-3">{pagination}</div>
            <CardList
                ref={formRef}
                formAction={formAction}
                noItemsFoundMessage="No internal messages found."
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
                                href: `/members/internal-media/${message.slug}`,
                                type: 'secondary',
                                rightGlyph: 'chevron-double-left',
                            },
                            {
                                content: 'Edit',
                                href: `/admin/internal-messages/edit/${message.id}`,
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
            <div className="pt-6">{pagination}</div>
        </>
    );
}
