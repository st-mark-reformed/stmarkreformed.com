import React, { ReactNode } from 'react';
import { HasValidTokenFromCookies } from './MemberTokenRepository';
import LoginPage from './LoginPage';

export default async function LoginGuard (
    {
        children,
    }: {
        children: ReactNode;
    },
) {
    const hasValidToken = await HasValidTokenFromCookies();

    if (!hasValidToken) {
        return <LoginPage />;
    }

    return <>{children}</>;
}
