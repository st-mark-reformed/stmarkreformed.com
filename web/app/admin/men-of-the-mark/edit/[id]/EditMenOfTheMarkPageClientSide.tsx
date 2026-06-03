'use client';

import React from 'react';
import { MenOfTheMarkItem } from '../../MenOfTheMarkItem';
import CreateEditMenOfTheMarkPage from '../../CreateEditMenOfTheMarkPage';

export default function EditMenOfTheMarkPageClientSide (
    {
        item,
    }: {
        item: MenOfTheMarkItem;
    },
) {
    return (
        <CreateEditMenOfTheMarkPage
            pageTitle={`Edit Men of the Mark: ${item.title}`}
            submitFormAction="edit"
            initialValues={{
                ...item,
                // datetime-local expects YYYY-MM-DDTHH:mm
                date: item.date.replace(' ', 'T').slice(0, 16),
            }}
            itemId={item.id}
        />
    );
}
