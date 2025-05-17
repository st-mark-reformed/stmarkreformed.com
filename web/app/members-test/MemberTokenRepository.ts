import crypto from 'crypto';
import { cookies } from 'next/headers';
import { RequestCookie } from 'next/dist/compiled/@edge-runtime/cookies';
import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';

export async function TokenCookieIsValid (
    memberToken: RequestCookie | undefined,
): Promise<boolean> {
    if (memberToken === undefined) {
        return false;
    }

    const hmac = crypto.createHmac(
        'sha256',
        getConfigString(ConfigOptions.ENCRYPTION_KEY),
    );

    const tokenParts = memberToken.value.split('.');

    const signature = tokenParts.pop();

    if (typeof signature !== 'string') {
        return false;
    }

    const data = tokenParts.join('.');

    const expectedSignature = hmac.update(data).digest('hex');

    try {
        return crypto.timingSafeEqual(
            Buffer.from(signature, 'hex'),
            Buffer.from(expectedSignature, 'hex'),
        );
    } catch (error) {
        return false;
    }
}

export async function HasValidTokenFromCookies (): Promise<boolean> {
    const cookieStore = await cookies();

    return TokenCookieIsValid(cookieStore.get('member'));
}
