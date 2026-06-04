import { AdminHymnsOfTheMonthPageData } from './AdminHymnsOfTheMonthPageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetHymnsOfTheMonth (
    pageNum: number,
    keyword: string,
): Promise<AdminHymnsOfTheMonthPageData> {
    const query = new URLSearchParams({ page: String(pageNum) });

    if (keyword !== '') {
        query.set('keyword', keyword);
    }

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/hymns-of-the-month?${query.toString()}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminHymnsOfTheMonthPageData;
}
