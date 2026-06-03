'use client';

import React from 'react';
import { PastorsPageItem } from '../../PastorsPageItem';
import CreateEditPastorsPagePage from '../../CreateEditPastorsPagePage';

export default function EditPastorsPagePageClientSide (
    {
        pastorsPageItem,
    }: {
        pastorsPageItem: PastorsPageItem;
    },
) {
    return (
        <CreateEditPastorsPagePage
            pageTitle={`Edit Pastor's Page: ${pastorsPageItem.title}`}
            submitFormAction="edit"
            initialValues={{
                ...pastorsPageItem,
                // datetime-local expects YYYY-MM-DDTHH:mm
                date: pastorsPageItem.date.replace(' ', 'T').slice(0, 16),
            }}
            pastorsPageId={pastorsPageItem.id}
        />
    );
}
