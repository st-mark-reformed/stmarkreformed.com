// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable @typescript-eslint/naming-convention */

export interface MessagesSearchParams {
    by: Array<string>;
    series: Array<string>;
    scripture_reference: string;
    title: string;
    date_range_start: string;
    date_range_end: string;
}

export interface RawSearchParams {
    [key: string]: string | string[] | undefined;
}

export interface MessagesSearchParamsParent {
    hasAnyParams: boolean;
    params: MessagesSearchParams;
}

export function createMessagesSearchParamsFromRaw (
    rawParams: RawSearchParams,
): MessagesSearchParamsParent {
    let by = rawParams['by[]'];
    if (!Array.isArray(by)) {
        by = [];
    }

    let series = rawParams['series[]'];
    if (!Array.isArray(series)) {
        series = [];
    }

    let {
        scripture_reference,
        title,
        date_range_start,
        date_range_end,
    } = rawParams;

    if (typeof scripture_reference !== 'string') {
        scripture_reference = '';
    }

    if (typeof title !== 'string') {
        title = '';
    }

    if (typeof date_range_start !== 'string') {
        date_range_start = '';
    }

    if (typeof date_range_end !== 'string') {
        date_range_end = '';
    }

    const params: MessagesSearchParams = {
        by,
        series,
        scripture_reference,
        title,
        date_range_start,
        date_range_end,
    };

    return {
        hasAnyParams: params.by.length > 0
            || params.series.length > 0
            || params.scripture_reference !== ''
            || params.title !== ''
            || params.date_range_start !== ''
            || params.date_range_end !== '',
        params,
    };
}
