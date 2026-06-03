import { MenOfTheMarkItem } from '../../MenOfTheMarkItem';
import RequestFactory from '../../../../api/request/RequestFactory';

export default async function GetEditMenOfTheMark (
    id: string,
): Promise<MenOfTheMarkItem | null> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/men-of-the-mark/edit/${id}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as MenOfTheMarkItem;
}
