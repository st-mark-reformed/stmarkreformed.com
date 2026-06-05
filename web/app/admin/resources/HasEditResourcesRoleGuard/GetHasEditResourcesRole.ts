import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponse {
    hasRole: boolean;
}

export default async function GetHasEditResourcesRole () {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/resources/has-edit-resources-role',
    });

    const json = response.json as unknown as ApiResponse;

    return json.hasRole;
}
