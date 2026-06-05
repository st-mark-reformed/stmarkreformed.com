import { AdminResourcesPageData } from './AdminResourcesPageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetResources (
    pageNum: number,
    keyword: string,
): Promise<AdminResourcesPageData> {
    const query = new URLSearchParams({ page: String(pageNum) });

    if (keyword !== '') {
        query.set('keyword', keyword);
    }

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/resources?${query.toString()}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminResourcesPageData;
}
