import { AdminMenOfTheMarkPageData } from './AdminMenOfTheMarkPageData';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetMenOfTheMark (
    pageNum: number,
    keyword: string,
): Promise<AdminMenOfTheMarkPageData> {
    const query = new URLSearchParams({ page: String(pageNum) });

    if (keyword !== '') {
        query.set('keyword', keyword);
    }

    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/men-of-the-mark?${query.toString()}`,
        cacheSeconds: 0,
    });

    return response.json as unknown as AdminMenOfTheMarkPageData;
}
