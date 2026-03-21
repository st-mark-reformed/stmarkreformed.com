import React from 'react';
import PageTitle from '../PageTitle';
import RequestFactory from '../../api/request/RequestFactory';
import Breadcrumbs from '../Breadcrumbs';

export default async function ProfilesPage () {
    const tmp = await RequestFactory().makeWithSignInRedirect({
        uri: '/healthcheck/659f105793f58',
    });

    return (
        <>
            <Breadcrumbs />
            <PageTitle
                buttons={[
                    {
                        type: 'primary',
                        content: 'New Profile',
                        glyph: 'plus',
                        href: '/admin/profiles/new',
                    },
                ]}
            >
                Profiles
            </PageTitle>
            TODO Profiles
        </>
    );
}
