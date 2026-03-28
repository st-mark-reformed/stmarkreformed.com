import React from 'react';
import { notFound } from 'next/navigation';
import EditSeriesPageClientSide from './EditSeriesPageClientSide';
import GetEditSeries from './GetEditSeries';

export default async function EditSeriesPage (
    {
        seriesId,
    }: {
        seriesId: string;
    },
) {
    const series = await GetEditSeries(seriesId);

    if (!series) {
        notFound();
    }

    return <EditSeriesPageClientSide series={series} />;
}
