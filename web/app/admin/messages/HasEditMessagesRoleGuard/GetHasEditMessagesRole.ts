import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponse {
    hasRole: boolean;
}

export default async function GetHasEditMessagesRole () {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/messages/has-edit-messages-role',
    });

    const json = response.json as unknown as ApiResponse;

    return json.hasRole;
}
