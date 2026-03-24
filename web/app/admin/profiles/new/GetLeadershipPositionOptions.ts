import RequestFactory from '../../../api/request/RequestFactory';

interface LeadershipPositionOption {
    name: string;
    label: string;
    defaultChecked?: boolean;
}

export default async function GetLeadershipPositionOptions (): Promise<LeadershipPositionOption[]> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/profiles/leadership-positions',
        cacheSeconds: 0,
    });

    return response.json as unknown as LeadershipPositionOption[];
}
