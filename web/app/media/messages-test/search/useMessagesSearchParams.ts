import { useSearchParams } from 'next/navigation';
import { MessagesSearchParams } from './MessagesSearchParams';

interface ReturnType {
    hasAnyParams: boolean;
    params: MessagesSearchParams;
}

export default function useMessagesSearchParams (): ReturnType {
    const searchParams = useSearchParams();

    const params: MessagesSearchParams = {
        by: searchParams.getAll('by[]'),
        series: searchParams.getAll('series[]'),
        scripture_reference: searchParams.get('scripture_reference') || '',
        title: searchParams.get('title') || '',
        date_range_start: searchParams.get('date_range_start') || '',
        date_range_end: searchParams.get('date_range_end') || '',
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
