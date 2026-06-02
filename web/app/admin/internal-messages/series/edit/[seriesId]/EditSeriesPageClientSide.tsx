'use client';

import React from 'react';
import { Series } from '../../Series';
import CreateEditSeriesPage from '../../CreateEditSeriesPage';

export default function EditSeriesPageClientSide (
    {
        series,
    }: {
        series: Series;
    },
) {
    return (
        <CreateEditSeriesPage
            pageTitle={`Edit Series: ${series.title}`}
            submitFormAction="edit"
            initialValues={series}
            seriesId={series.id}
        />
    );
}
