import { HymnOfTheMonthItem } from './HymnOfTheMonthItem';

export interface AdminHymnsOfTheMonthPageData {
    currentPage: number;
    totalPages: number;
    totalResults: number;
    entries: HymnOfTheMonthItem[];
}
