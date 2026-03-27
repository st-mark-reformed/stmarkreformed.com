import React from 'react';
import PageTitle from '../PageTitle';
import Breadcrumbs from '../Breadcrumbs';

export default async function MessagesPage () {
    return (
        <>
            <Breadcrumbs />
            <PageTitle
                buttons={[
                    {
                        type: 'secondary',
                        content: 'View All Series',
                        glyph: 'eye',
                        href: '/admin/messages/series',
                    },
                    {
                        type: 'secondary',
                        content: 'New Series',
                        glyph: 'plus',
                        href: '/admin/messages/series/new',
                    },
                    {
                        type: 'primary',
                        content: 'New Message',
                        glyph: 'plus',
                        href: '/admin/messages/new',
                    },
                ]}
            >
                Messages
            </PageTitle>
            {/* TODO Messages */}
            <div className="text-center">
                <p className="text-sm/6 text-gray-500 dark:text-gray-400">
                    No messages found.
                </p>
            </div>
        </>
    );
}
