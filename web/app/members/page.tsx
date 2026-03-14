import React from 'react';
import { redirect } from 'next/navigation';
import { Metadata } from 'next';
import { HasValidTokenFromCookies } from './MemberTokenRepository';
import LoginPage from './LoginPage';
import { createPageTitle } from '../createPageTitle';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle('Members Area'),
};

export default async function MembersPage () {
    // eslint-disable-next-line no-console
    console.log('Members Page rendering started');

    const hasValidToken = await HasValidTokenFromCookies();

    if (!hasValidToken) {
        // eslint-disable-next-line no-console
        console.log('No valid token found, displaying login page');

        return <LoginPage />;
    }

    // eslint-disable-next-line no-console
    console.log('Valid token found, redirecting to internal media');

    redirect('/members/internal-media');

    return <></>;
}
