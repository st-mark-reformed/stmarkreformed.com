import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';

export async function GET () {
    const baseUrl = getConfigString(ConfigOptions.BASE_URL);

    return Response.redirect(new URL(`${baseUrl}/admin/messages`));
}
