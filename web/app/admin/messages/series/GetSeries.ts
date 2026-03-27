import { Series } from './Series';
import RequestFactory from '../../../api/request/RequestFactory';

export default async function GetSeries (): Promise<Series[]> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/messages/series',
        cacheSeconds: 0,
    });

    return response.json as unknown as Series[];
}
