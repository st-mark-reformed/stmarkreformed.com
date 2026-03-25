import RequestFactory from '../../../../api/request/RequestFactory';
import { Profile } from '../../Profile';

type ProfileResponse =
    | Profile
    | null;

export default async function GetEditProfile (
    profileId: string,
): Promise<ProfileResponse> {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: `/admin/profiles/edit/${profileId}`,
        cacheSeconds: 0,
    });

    if (response.status !== 200) {
        return null;
    }

    return response.json as unknown as ProfileResponse;
}
