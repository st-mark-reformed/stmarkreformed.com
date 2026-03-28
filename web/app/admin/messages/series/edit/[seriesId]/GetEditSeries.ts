import { Series } from '../../Series';
import RequestFactory from '../../../../../api/request/RequestFactory';

type SeriesResponse =
    | Series
    | null;

export default async function GetEditSeries (
    seriesId: string,
): Promise<SeriesResponse> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/messages/series/edit/${seriesId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as SeriesResponse;
}
