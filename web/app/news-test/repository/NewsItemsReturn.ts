import { NewsItem } from './NewsItem';

export interface NewsItemsReturn {
    currentPage: number;
    perPage: number;
    totalResults: number;
    totalPages: number;
    pagesArray: Array<{
        isActive: boolean;
        label: string | number;
        target: string;
    }>;
    prevPageLink: string | null;
    nextPageLink: string | null;
    firstPageLink: string | null;
    lastPageLink: string | null;
    entries: Array<NewsItem>;
}
