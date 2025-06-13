import React from 'react';
import { PlusIcon } from '@heroicons/react/16/solid';
import { RequestFactory } from '../../api/request/RequestFactory';
import ApiResponseGate from '../ApiResponseGate';
import PageHeader from '../layout/PageHeader';
import EmptyState from '../layout/EmptyState';

export default async function PageInner () {
    // For now, just make sure we're logged in
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/has-cms-access',
        cacheSeconds: 0,
    });

    const newHref = '/cms/profiles/new';

    return (
        <ApiResponseGate apiResponse={apiResponse}>
            <div className="mb-4 ">
                <PageHeader
                    title="Profiles"
                    buttons={[
                        {
                            id: 'newProfile',
                            type: 'primary',
                            content: (
                                <>
                                    <PlusIcon className="h-5 w-5 mr-1" />
                                    New Profile
                                </>
                            ),
                            href: newHref,
                        },
                    ]}
                />
            </div>
            <EmptyState
                itemNameSingular="Profile"
                itemNamePlural="Profiles"
                buttonHref={newHref}
            />
        </ApiResponseGate>
    );
}
