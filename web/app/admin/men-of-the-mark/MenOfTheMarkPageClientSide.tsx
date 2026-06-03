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
import { MenOfTheMarkItem } from './MenOfTheMarkItem';
import SubmitDeleteMenOfTheMarkFormAction from './SubmitDeleteMenOfTheMarkFormAction';
import Alert from '../../Alert';
import Pagination from '../../Pagination/Pagination';
import MenOfTheMarkSearchForm from './MenOfTheMarkSearchForm';

export default function MenOfTheMarkPageClientSide (
    {
        items,
        currentPage,
        totalPages,
        keyword,
    }: {
        items: MenOfTheMarkItem[];
        currentPage: number;
        totalPages: number;
        keyword: string;
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
        SubmitDeleteMenOfTheMarkFormAction,
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
                type: 'primary',
                content: 'New Entry',
                glyph: 'plus',
                href: '/admin/men-of-the-mark/new',
            },
        ];
    })();

    const pagination = (
        <Pagination
            baseUrl="/admin/men-of-the-mark"
            currentPage={currentPage}
            totalPages={totalPages}
            queryString={keyword === '' ? '' : `keyword=${encodeURIComponent(keyword)}`}
        />
    );

    return (
        <>
            <Breadcrumbs />
            <PageTitle buttons={buttons}>Men of the Mark</PageTitle>
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
            <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>{pagination}</div>
                <div className="pb-3">
                    <MenOfTheMarkSearchForm defaultKeyword={keyword} />
                </div>
            </div>
            <CardList
                ref={formRef}
                formAction={formAction}
                noItemsFoundMessage="No Men of the Mark entries found."
                items={items.map((item) => ({
                    id: item.id,
                    columns: [
                        {
                            id: `title-${item.id}`,
                            line1: <>{item.title} <span className="text-xs font-light ml-2">(slug: {item.slug})</span></>,
                            line2: item.isEnabled ? 'Enabled' : 'Disabled',
                        },
                        {
                            id: `metadata-${item.id}`,
                            line1: item.date,
                        },
                        [
                            {
                                content: 'View Page',
                                href: `/publications/men-of-the-mark/${item.slug}`,
                                type: 'secondary',
                                rightGlyph: 'chevron-double-left',
                            },
                            {
                                content: 'Edit',
                                href: `/admin/men-of-the-mark/edit/${item.id}`,
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
