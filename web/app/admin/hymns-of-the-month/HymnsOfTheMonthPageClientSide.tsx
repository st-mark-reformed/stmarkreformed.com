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
import { HymnOfTheMonthItem } from './HymnOfTheMonthItem';
import SubmitDeleteHymnsOfTheMonthFormAction from './SubmitDeleteHymnsOfTheMonthFormAction';
import Alert from '../../Alert';
import Pagination from '../../Pagination/Pagination';
import HymnsOfTheMonthSearchForm from './HymnsOfTheMonthSearchForm';

export default function HymnsOfTheMonthPageClientSide (
    {
        hymnOfTheMonthItems,
        currentPage,
        totalPages,
        keyword,
    }: {
        hymnOfTheMonthItems: HymnOfTheMonthItem[];
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
        SubmitDeleteHymnsOfTheMonthFormAction,
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
                href: '/admin/hymns-of-the-month/new',
            },
        ];
    })();

    const pagination = (
        <Pagination
            baseUrl="/admin/hymns-of-the-month"
            currentPage={currentPage}
            totalPages={totalPages}
            queryString={keyword === '' ? '' : `keyword=${encodeURIComponent(keyword)}`}
        />
    );

    return (
        <>
            <Breadcrumbs />
            <PageTitle buttons={buttons}>Hymns of the Month</PageTitle>
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
                    <HymnsOfTheMonthSearchForm defaultKeyword={keyword} />
                </div>
            </div>
            <CardList
                ref={formRef}
                formAction={formAction}
                noItemsFoundMessage="No Hymns of the Month entries found."
                items={hymnOfTheMonthItems.map((hymnOfTheMonthItem) => ({
                    id: hymnOfTheMonthItem.id,
                    columns: [
                        {
                            id: `title-${hymnOfTheMonthItem.id}`,
                            line1: <>{hymnOfTheMonthItem.title} <span className="text-xs font-light ml-2">(slug: {hymnOfTheMonthItem.slug})</span></>,
                            line2: hymnOfTheMonthItem.isEnabled ? 'Enabled' : 'Disabled',
                        },
                        {
                            id: `metadata-${hymnOfTheMonthItem.id}`,
                            line1: hymnOfTheMonthItem.hymnPsalmName,
                            line2: `${hymnOfTheMonthItem.practiceTracks.length} practice track${hymnOfTheMonthItem.practiceTracks.length === 1 ? '' : 's'}`,
                        },
                        [
                            {
                                content: 'View Page',
                                href: `/members/hymns-of-the-month/${hymnOfTheMonthItem.slug}`,
                                type: 'secondary',
                                rightGlyph: 'chevron-double-left',
                            },
                            {
                                content: 'Edit',
                                href: `/admin/hymns-of-the-month/edit/${hymnOfTheMonthItem.id}`,
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
