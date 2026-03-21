import React from 'react';
import RequestFactory from '../../api/request/RequestFactory';
import PageTitle from '../PageTitle';

export default async function MessagesPage () {
    const tmp = await RequestFactory().makeWithSignInRedirect({
        uri: '/healthcheck/659f105793f58',
    });

    return (
        <>
            <PageTitle>
                Messages
            </PageTitle>
            TODO Messages
        </>
    );
}
