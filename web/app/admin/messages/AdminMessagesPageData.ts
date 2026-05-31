import { Message } from './Message';

export interface AdminMessagesPageData {
    currentPage: number;
    totalPages: number;
    totalResults: number;
    entries: Message[];
}
