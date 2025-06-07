import FindAllMessagesByPage from './FindAllMessagesByPage';
import { Entry } from '../../../audio/Entry';

export default async function FindAllMessages (
    limit?: number,
): Promise<Array<Entry>> {
    const firstPageData = await FindAllMessagesByPage(1);

    if (!firstPageData) {
        return [];
    }

    const { totalPages } = firstPageData;
    let allItems = [...firstPageData.entries];

    // Fetch all other pages and combine the items
    // eslint-disable-next-line no-plusplus
    for (let pageNum = 2; pageNum <= totalPages; pageNum++) {
        // eslint-disable-next-line no-await-in-loop
        const pageData = await FindAllMessagesByPage(pageNum);

        if (pageData && pageData.entries.length > 0) {
            allItems = [...allItems, ...pageData.entries];
        }
    }

    if (limit !== undefined && limit > 0) {
        return allItems.slice(0, limit);
    }

    return allItems;
}
