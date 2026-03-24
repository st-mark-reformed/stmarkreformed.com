import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponse {
    hasRole: boolean;
}

export default async function GetHasEditProfilesRole () {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/profiles/has-edit-profiles-role',
    });

    const json = response.json as unknown as ApiResponse;

    return json.hasRole;
}
