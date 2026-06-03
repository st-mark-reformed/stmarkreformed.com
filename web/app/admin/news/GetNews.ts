import { AdminNewsPageData } from './AdminNewsPageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetNews (
    pageNum: number,
    keyword: string,
): Promise<AdminNewsPageData> {
    const query = new URLSearchParams({ page: String(pageNum) });

    if (keyword !== '') {
        query.set('keyword', keyword);
    }

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/news?${query.toString()}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminNewsPageData;
}
