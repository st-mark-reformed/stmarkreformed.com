import { AdminPastorsPagePageData } from './AdminPastorsPagePageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetPastorsPage (
    pageNum: number,
    keyword: string,
): Promise<AdminPastorsPagePageData> {
    const query = new URLSearchParams({ page: String(pageNum) });

    if (keyword !== '') {
        query.set('keyword', keyword);
    }

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/pastors-page?${query.toString()}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminPastorsPagePageData;
}
