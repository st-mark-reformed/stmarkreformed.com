import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponse {
    hasRole: boolean;
}

export default async function GetHasEditMenOfTheMarkRole () {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/men-of-the-mark/has-edit-role',
    });

    const json = response.json as unknown as ApiResponse;

    return json.hasRole;
}
