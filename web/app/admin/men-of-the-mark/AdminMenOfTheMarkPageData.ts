import { MenOfTheMarkItem } from './MenOfTheMarkItem';

export interface AdminMenOfTheMarkPageData {
    currentPage: number;
    totalPages: number;
    totalResults: number;
    entries: MenOfTheMarkItem[];
}
