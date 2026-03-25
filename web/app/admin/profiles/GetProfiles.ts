import RequestFactory from '../../api/request/RequestFactory';
import { Profile } from './Profile';

export default async function GetProfiles (): Promise<Profile[]> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/profiles',
        cacheSeconds: 0,
    });

    return response.json as unknown as Profile[];
}
