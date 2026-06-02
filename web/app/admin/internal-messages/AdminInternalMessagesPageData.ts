import { InternalMessage } from './InternalMessage';

export interface AdminInternalMessagesPageData {
    currentPage: number;
    totalPages: number;
    totalResults: number;
    entries: InternalMessage[];
}
