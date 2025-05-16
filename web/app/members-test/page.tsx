import React from 'react';
import Layout from '../layout/Layout';
import HasValidTokenFromCookies from './MemberTokenRepository';
import LoginPage from './LoginPage';

export default async function MembersPage () {
    const hasValidToken = await HasValidTokenFromCookies();

    if (!hasValidToken) {
        return <LoginPage />;
    }

    return (
        <Layout>
            TODO
        </Layout>
    );
}
