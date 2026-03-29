import { Message } from './Message';
import RequestFactory from '../../api/request/RequestFactory';

export default async function GetMessages (): Promise<Message[]> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/messages',
        cacheSeconds: 0,
    });

    return response.json as unknown as Message[];
}
