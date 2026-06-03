import { PastorsPageItem } from '../../PastorsPageItem';
import RequestFactory from '../../../../api/request/RequestFactory';

export default async function GetEditPastorsPage (
    pastorsPageId: string,
): Promise<PastorsPageItem | null> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/pastors-page/edit/${pastorsPageId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as PastorsPageItem;
}
