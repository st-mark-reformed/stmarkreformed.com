'use server';

import crypto from 'crypto';
import { cookies } from 'next/headers';
import { FormValues } from './FormValues';
import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';

export default async function PostLoginForm (formValues: FormValues): Promise<{
    isValid: boolean;
}> {
    // eslint-disable-next-line no-console
    console.log('PostLoginForm start');

    if (
        formValues.email !== getConfigString(ConfigOptions.MEMBER_EMAIL_ADDRESS)
        || formValues.password !== getConfigString(ConfigOptions.MEMBER_PASSWORD)
    ) {
        // eslint-disable-next-line no-console
        console.log('PostLoginForm invalid credentials');

        return { isValid: false };
    }

    // eslint-disable-next-line no-console
    console.log('PostLoginForm credentials valid');

    const hmac = crypto.createHmac(
        'sha256',
        getConfigString(ConfigOptions.ENCRYPTION_KEY),
    );

    const signature = hmac.update(formValues.email).digest('hex');

    const token = `${formValues.email}.${signature}`;

    // eslint-disable-next-line no-console
    console.log('PostLoginForm token created');

    const cookieStore = await cookies();

    cookieStore.set({
        name: 'member',
        value: token,
        secure: true,
        path: '/',
        maxAge: 60 * 60 * 24 * 7, // 1 week in seconds
    });

    // eslint-disable-next-line no-console
    console.log('PostLoginForm cookie set');

    return { isValid: true };
}
