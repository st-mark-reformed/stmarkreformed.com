import { PastorsPageItem } from './PastorsPageItem';

export interface AdminPastorsPagePageData {
    currentPage: number;
    totalPages: number;
    totalResults: number;
    entries: PastorsPageItem[];
}
