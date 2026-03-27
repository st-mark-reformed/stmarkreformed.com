import React from 'react';
import Breadcrumbs from '../../Breadcrumbs';
import PageTitle from '../../PageTitle';

export default async function SeriesPage () {
    return (
        <>
            <Breadcrumbs
                crumbs={[
                    {
                        content: 'Messages',
                        href: '/admin/messages',
                    },
                ]}
            />

            <PageTitle
                buttons={[
                    {
                        type: 'primary',
                        content: 'New Series',
                        glyph: 'plus',
                        href: '/admin/messages/series/new',
                    },
                ]}
            >
                Series
            </PageTitle>
            {/* TODO Series */}
            <div className="text-center">
                <p className="text-sm/6 text-gray-500 dark:text-gray-400">
                    No series found.
                </p>
            </div>
        </>
    );
}
