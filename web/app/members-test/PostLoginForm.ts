'use server';

import bcrypt from 'bcrypt';
import crypto from 'crypto';
import { cookies } from 'next/headers';
import { FormValues } from './FormValues';
import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';

export default async function PostLoginForm (formValues: FormValues): Promise<{
    isValid: boolean;
}> {
    // Create password:
    // const tmp = await bcrypt.hash(
    //     'PASSWORD HERE',
    //     10,
    // );

    if (
        formValues.email !== getConfigString(ConfigOptions.MEMBER_EMAIL_ADDRESS)
        || !await bcrypt.compare(
            formValues.password,
            getConfigString(ConfigOptions.HASHED_MEMBER_PASSWORD),
        )
    ) {
        return { isValid: false };
    }

    const hmac = crypto.createHmac(
        'sha256',
        getConfigString(ConfigOptions.ENCRYPTION_KEY),
    );

    const signature = hmac.update(formValues.email).digest('hex');

    const token = `${formValues.email}.${signature}`;

    const cookieStore = await cookies();

    cookieStore.set({
        name: 'member',
        value: token,
        secure: true,
        path: '/',
        maxAge: 60 * 60 * 24 * 7, // 1 week in seconds
    });

    return { isValid: true };
}
