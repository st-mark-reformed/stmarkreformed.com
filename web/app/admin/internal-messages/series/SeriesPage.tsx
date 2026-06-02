import React from 'react';
import GetSeries from './GetSeries';
import SeriesPageClientSide from './SeriesPageClientSide';

export default async function SeriesPage () {
    const series = await GetSeries();

    return <SeriesPageClientSide series={series} />;
}
