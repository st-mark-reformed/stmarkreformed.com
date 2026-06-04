import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponse {
    hasRole: boolean;
}

export default async function GetHasEditHymnsOfTheMonthRole () {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/hymns-of-the-month/has-edit-hymns-of-the-month-role',
    });

    const json = response.json as unknown as ApiResponse;

    return json.hasRole;
}
