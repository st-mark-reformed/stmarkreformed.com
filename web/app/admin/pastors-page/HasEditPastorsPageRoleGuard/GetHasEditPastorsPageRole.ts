import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponse {
    hasRole: boolean;
}

export default async function GetHasEditPastorsPageRole () {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/pastors-page/has-edit-pastors-page-role',
    });

    const json = response.json as unknown as ApiResponse;

    return json.hasRole;
}
