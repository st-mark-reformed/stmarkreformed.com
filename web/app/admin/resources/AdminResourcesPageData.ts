import { ResourceItem } from './ResourceItem';

export interface AdminResourcesPageData {
    currentPage: number;
    totalPages: number;
    totalResults: number;
    entries: ResourceItem[];
}
