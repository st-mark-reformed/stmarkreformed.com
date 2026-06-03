import { NewsItem } from './NewsItem';

export interface AdminNewsPageData {
    currentPage: number;
    totalPages: number;
    totalResults: number;
    entries: NewsItem[];
}
