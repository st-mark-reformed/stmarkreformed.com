import React from 'react';
import { DocumentIcon, TagIcon } from '@heroicons/react/24/outline';
import { PlusIcon } from '@heroicons/react/16/solid';
import { RequestFactory } from '../../../api/request/RequestFactory';
import EmptyState from '../../layout/EmptyState';
import ApiResponseGate from '../../ApiResponseGate';
import PageHeader from '../../layout/PageHeader';

export default async function PageInner () {
    // For now, just make sure we're logged in
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/has-cms-access',
        cacheSeconds: 0,
    });

    const newHref = '/cms/entries/messages-test/new';

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <div className="mb-4 ">
                <PageHeader
                    title="Messages"
                    buttons={[
                        {
                            id: 'fileManager',
                            type: 'secondary',
                            content: (
                                <>
                                    <DocumentIcon className="h-5 w-5 mr-1" />
                                    File Manager
                                </>
                            ),
                            href: '/cms/entries/messages-test/file-manager',
                        },
                        {
                            id: 'seriesManager',
                            type: 'secondary',
                            content: (
                                <>
                                    <TagIcon className="h-5 w-5 mr-1" />
                                    Series Manager
                                </>
                            ),
                            href: '/cms/entries/messages-test/series-manager',
                        },
                        {
                            id: 'newEntry',
                            type: 'primary',
                            content: (
                                <>
                                    <PlusIcon className="h-5 w-5 mr-1" />
                                    New Entry
                                </>
                            ),
                            href: newHref,
                        },
                    ]}
                />
            </div>
            <EmptyState
                itemNameSingular="Entry"
                itemNamePlural="Entries"
                buttonHref={newHref}
            />
        </ApiResponseGate>
    );
}
