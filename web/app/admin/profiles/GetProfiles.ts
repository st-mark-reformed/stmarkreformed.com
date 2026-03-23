import RequestFactory from '../../api/request/RequestFactory';

interface Profile {
    id: string;
    titleOrHonorific: string;
    firstName: string;
    lastName: string;
    fullName: string;
    fullNameWithHonorific: string;
    email: string;
    leadershipPosition: string;
    leadershipPositionHumanReadable: string;
    bio: string;
    hasMessages: boolean;
}

export default async function GetProfiles (): Promise<Profile[]> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/profiles',
        cacheSeconds: 0,
    });

    return response.json as unknown as Profile[];
}
