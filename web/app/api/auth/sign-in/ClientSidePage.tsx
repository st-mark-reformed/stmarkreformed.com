'use client';

import React, { useEffect } from 'react';
import { signIn } from 'next-auth/react';
import FullPageLoading from '../../../FullPageLoading';

export default function ClientSidePage () {
    useEffect(() => {
        const url = new URL(window.location.href);

        signIn('auth0', {
            callbackUrl: url.searchParams.get('authReturn')?.toString(),
        });
    }, []);

    return <FullPageLoading />;
}
