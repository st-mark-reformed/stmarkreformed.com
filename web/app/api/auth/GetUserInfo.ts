import GetWellKnown from './GetWellKnown';
import RequestFactory from '../request/RequestFactory';

interface UserInfo {
    id: string;
    sub: string;
    email: string;
    name: string;
    roles: string[];
}

export default async function GetUserInfo (): Promise<UserInfo> {
    const wellKnown = await GetWellKnown();

    const userInfoUrl = new URL(wellKnown.userinfoEndpoint);

    const userInfo = await RequestFactory('auth').makeWithSignInRedirect({
        uri: userInfoUrl.pathname,
    });

    return userInfo.json as unknown as UserInfo;
}
