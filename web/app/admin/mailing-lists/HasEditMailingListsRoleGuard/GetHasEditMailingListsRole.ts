import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponse {
    hasRole: boolean;
}

export default async function GetHasEditMailingListsRole () {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/mailing-lists/has-edit-mailing-lists-role',
    });

    const json = response.json as unknown as ApiResponse;

    return json.hasRole;
}
