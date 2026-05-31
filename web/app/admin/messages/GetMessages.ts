import { AdminMessagesPageData } from './AdminMessagesPageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetMessages (
    pageNum: number,
): Promise<AdminMessagesPageData> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/messages?page=${pageNum}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminMessagesPageData;
}
