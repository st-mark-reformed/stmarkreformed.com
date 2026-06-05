'use client';

import React from 'react';
import { ResourceItem } from '../../ResourceItem';
import CreateEditResourcePage from '../../CreateEditResourcePage';

export default function EditResourcePageClientSide (
    {
        resourceItem,
    }: {
        resourceItem: ResourceItem;
    },
) {
    return (
        <CreateEditResourcePage
            pageTitle={`Edit Resource: ${resourceItem.title}`}
            submitFormAction="edit"
            initialValues={{
                isEnabled: resourceItem.isEnabled,
                // datetime-local expects YYYY-MM-DDTHH:mm
                date: resourceItem.date.replace(' ', 'T').slice(0, 16),
                title: resourceItem.title,
                slug: resourceItem.slug,
                body: resourceItem.body,
                downloads: resourceItem.downloads.map((download) => ({
                    filename: download.filename,
                    file: '',
                })),
            }}
            resourceId={resourceItem.id}
        />
    );
}
