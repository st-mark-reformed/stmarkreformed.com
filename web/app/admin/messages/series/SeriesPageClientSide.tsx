'use client';

import React, { useRef, useState } from 'react';
import { Series } from './Series';
import Breadcrumbs from '../../Breadcrumbs';
import PageTitle, { Button } from '../../PageTitle';
import CardList, { CardListHandle } from '../../CardList/CardList';

export default function SeriesPageClientSide (
    {
        series,
    }: {
        series: Series[];
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
                type: 'primary',
                content: 'New Series',
                glyph: 'plus',
                href: '/admin/messages/series/new',
            },
        ];
    })();

    return (
        <>
            <Breadcrumbs
                crumbs={[
                    {
                        content: 'Messages',
                        href: '/admin/messages',
                    },
                ]}
            />
            <PageTitle buttons={buttons}>
                Series
            </PageTitle>
            <CardList
                ref={formRef}
                noItemsFoundMessage="No series found."
                items={series.map((seriesItem) => ({
                    id: seriesItem.id,
                    columns: [
                        {
                            id: 'title',
                            line1: seriesItem.title,
                            line2: seriesItem.slug,
                        },
                        [
                            {
                                content: 'Edit',
                                href: `/admin/messages/series/edit/${seriesItem.id}`,
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
