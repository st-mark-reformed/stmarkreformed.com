import { ResourceItem } from '../../ResourceItem';
import RequestFactory from '../../../../api/request/RequestFactory';

export default async function GetEditResource (
    resourceId: string,
): Promise<ResourceItem | null> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/resources/edit/${resourceId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as ResourceItem;
}
