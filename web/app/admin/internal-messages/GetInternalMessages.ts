import { AdminInternalMessagesPageData } from './AdminInternalMessagesPageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetInternalMessages (
    pageNum: number,
): Promise<AdminInternalMessagesPageData> {
    const query = new URLSearchParams({ page: String(pageNum) });

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/internal-messages?${query.toString()}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminInternalMessagesPageData;
}
