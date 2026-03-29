import { Message } from '../../Message';
import RequestFactory from '../../../../api/request/RequestFactory';

export default async function GetEditMessage (
    messageId: string,
): Promise<Message | null> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/messages/edit/${messageId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as Message;
}
