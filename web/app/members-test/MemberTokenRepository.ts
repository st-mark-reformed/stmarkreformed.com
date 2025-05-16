import crypto from 'crypto';
import { cookies } from 'next/headers';
import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';

export default async function HasValidTokenFromCookies () {
    const cookieStore = await cookies();

    const memberToken = cookieStore.get('member');

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
