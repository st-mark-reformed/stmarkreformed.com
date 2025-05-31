import FindNewsItemsByPage from './FindNewsItemsByPage';
import { NewsItem } from './NewsItem';

export default async function FindAllNewsItems (): Promise<Array<NewsItem>> {
    const firstPageData = await FindNewsItemsByPage(1);

    if (!firstPageData) {
        return [];
    }

    const { totalPages } = firstPageData;
    let allItems = [...firstPageData.entries];

    // Fetch all other pages and combine the items
    // eslint-disable-next-line no-plusplus
    for (let pageNum = 2; pageNum <= totalPages; pageNum++) {
        // eslint-disable-next-line no-await-in-loop
        const pageData = await FindNewsItemsByPage(pageNum);

        if (pageData && pageData.entries.length > 0) {
            allItems = [...allItems, ...pageData.entries];
        }
    }

    return allItems;
}
