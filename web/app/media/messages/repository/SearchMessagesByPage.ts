import { cache } from 'react';
import { MessagesPageData } from '../../../audio/MessagesPageData';
import { MessagesSearchParamsParent } from '../search/MessagesSearchParams';
import { ConfigOptions, getConfigString } from '../../../ServerSideRunTimeConfig';

const SearchMessagesByPage = cache(async (
    pageNum: number,
    messagesSearchParams: MessagesSearchParamsParent,
): Promise<null | MessagesPageData> => {
    const headers = new Headers({
        RequestType: 'api',
        Accept: 'application/json',
    });

    const options = { headers } as RequestInit;

    const url = new URL(
        `${getConfigString(ConfigOptions.APP_API_URL)}/api/media/messages/search`,
    );

    messagesSearchParams.params.by.forEach((byItem) => {
        url.searchParams.append('by[]', byItem);
    });

    messagesSearchParams.params.series.forEach((seriesItem) => {
        url.searchParams.append('series[]', seriesItem);
    });

    if (messagesSearchParams.params.scripture_reference) {
        url.searchParams.append(
            'scripture_reference',
            messagesSearchParams.params.scripture_reference,
        );
    }

    if (messagesSearchParams.params.title) {
        url.searchParams.append(
            'title',
            messagesSearchParams.params.title,
        );
    }

    if (messagesSearchParams.params.date_range_start) {
        url.searchParams.append(
            'date_range_start',
            messagesSearchParams.params.date_range_start,
        );
    }

    if (messagesSearchParams.params.date_range_end) {
        url.searchParams.append(
            'date_range_end',
            messagesSearchParams.params.date_range_end,
        );
    }

    url.searchParams.append('page', pageNum.toString());

    const response = await fetch(url, options);

    if (response.status !== 200) {
        return null;
    }

    try {
        return await response.json();
    } catch (error) {
        return null;
    }
});

export default SearchMessagesByPage;
