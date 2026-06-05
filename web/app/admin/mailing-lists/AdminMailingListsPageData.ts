import { MailingList } from './MailingList';

export interface AdminMailingListsPageData {
    currentPage: number;
    totalPages: number;
    totalResults: number;
    entries: MailingList[];
}
