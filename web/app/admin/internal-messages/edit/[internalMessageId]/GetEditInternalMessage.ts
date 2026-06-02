import { InternalMessage } from '../../InternalMessage';
import RequestFactory from '../../../../api/request/RequestFactory';

export default async function GetEditInternalMessage (
    internalMessageId: string,
): Promise<InternalMessage | null> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/internal-messages/edit/${internalMessageId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as InternalMessage;
}
