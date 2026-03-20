import { AuthCodeGrantApiFactory } from '../AuthCodeGrantApiFactory';
import { ConfigOptions, getConfigString } from '../../../ServerSideRunTimeConfig';

export async function GET () {
    await (await AuthCodeGrantApiFactory()).deleteSessionAndCookie();

    return Response.redirect(getConfigString(
        ConfigOptions.AUTH_SIGN_OUT_REDIRECT_URL,
    ));
}
