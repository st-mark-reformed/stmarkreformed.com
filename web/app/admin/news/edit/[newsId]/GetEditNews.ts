import { NewsItem } from '../../NewsItem';
import RequestFactory from '../../../../api/request/RequestFactory';

export default async function GetEditNews (
    newsId: string,
): Promise<NewsItem | null> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/news/edit/${newsId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as NewsItem;
}
