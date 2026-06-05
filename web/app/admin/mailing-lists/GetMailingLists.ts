import { AdminMailingListsPageData } from './AdminMailingListsPageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetMailingLists (
    pageNum: number,
    keyword: string,
): Promise<AdminMailingListsPageData> {
    const query = new URLSearchParams({ page: String(pageNum) });

    if (keyword !== '') {
        query.set('keyword', keyword);
    }

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/mailing-lists?${query.toString()}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminMailingListsPageData;
}
