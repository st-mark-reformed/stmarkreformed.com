import React from 'react';
import RequestFactory from '../../api/request/RequestFactory';
import AdminLayout from '../Layout/AdminLayout';

export default async function MessagesPage () {
    const tmp = await RequestFactory().makeWithSignInRedirect({
        uri: '/healthcheck/659f105793f58',
    });

    console.log(tmp);

    return (
        <AdminLayout activeNav="messages">
            TODO
        </AdminLayout>
    );
}
