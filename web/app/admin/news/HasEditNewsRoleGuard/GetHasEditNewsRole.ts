import RequestFactory from '../../../api/request/RequestFactory';

interface ApiResponse {
    hasRole: boolean;
}

export default async function GetHasEditNewsRole () {
    const response = await RequestFactory().makeWithSignInRedirect({
        uri: '/admin/news/has-edit-news-role',
    });

    const json = response.json as unknown as ApiResponse;

    return json.hasRole;
}
