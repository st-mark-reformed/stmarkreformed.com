'use server';

import { RequestFactory } from '../../../../../api/request/RequestFactory';
import { MessageSeries } from '../MessageSeries';

export interface Option {
    label: string;
    value: string;
}

export type Options = Array<Option>;

export default async function GetSeriesSelectOptions (): Promise<Options | null> {
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/cms/entries/messages/series',
        cacheSeconds: 0,
    });

    const series = apiResponse.json as unknown as Array<MessageSeries & {
        id: string;
    }>;

    if (!Array.isArray(series)) {
        return null;
    }

    return series.map((seriesItem) => ({
        label: seriesItem.title,
        value: seriesItem.id,
    }));
}
