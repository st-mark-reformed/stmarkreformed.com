import { MailingList } from '../../MailingList';
import RequestFactory from '../../../../api/request/RequestFactory';

export default async function GetEditMailingList (
    mailingListId: string,
): Promise<MailingList | null> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/mailing-lists/edit/${mailingListId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as MailingList;
}
