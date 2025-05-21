import React from 'react';
import { redirect } from 'next/navigation';
import { Metadata } from 'next';
import { HasValidTokenFromCookies } from './MemberTokenRepository';
import LoginPage from './LoginPage';
import { ConfigOptions, getConfigString } from '../ServerSideRunTimeConfig';
import { createPageTitle } from '../createPageTitle';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle('Members Area'),
};

export default async function MembersPage () {
    const hasValidToken = await HasValidTokenFromCookies();

    if (!hasValidToken) {
        return <LoginPage />;
    }

    redirect(`${getConfigString(ConfigOptions.BASE_URL)}/members/internal-media`);

    return <></>;
}
