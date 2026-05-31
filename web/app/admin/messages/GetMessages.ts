import { AdminMessagesPageData } from './AdminMessagesPageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetMessages (
    pageNum: number,
    keyword: string,
): Promise<AdminMessagesPageData> {
    const query = new URLSearchParams({ page: String(pageNum) });

    if (keyword !== '') {
        query.set('keyword', keyword);
    }

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/messages?${query.toString()}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminMessagesPageData;
}
