import { HymnOfTheMonthItem } from '../../HymnOfTheMonthItem';
import RequestFactory from '../../../../api/request/RequestFactory';

export default async function GetEditHymnOfTheMonth (
    hymnOfTheMonthId: string,
): Promise<HymnOfTheMonthItem | null> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/hymns-of-the-month/edit/${hymnOfTheMonthId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as HymnOfTheMonthItem;
}
