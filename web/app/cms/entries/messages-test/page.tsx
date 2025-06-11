import React from 'react';
import { Metadata } from 'next';
import { PlusIcon } from '@heroicons/react/16/solid';
import CmsLayout from '../../layout/CmsLayout';
import { createPageTitle } from '../../../createPageTitle';
import PageHeader from '../../layout/PageHeader';
import EmptyState from '../../layout/EmptyState';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Messages',
            'CMS',
        ]),
    };
}

export default async function Page () {
    return (
        <CmsLayout>
            <div className="mb-4 ">
                <PageHeader
                    title="Messages"
                    buttons={[
                        {
                            id: 'newEntry',
                            type: 'primary',
                            content: (
                                <>
                                    <PlusIcon className="h-5 w-5 mr-1" />
                                    New Entry
                                </>
                            ),
                            href: '#newEntry',
                        },
                    ]}
                />
            </div>
            <EmptyState
                itemNameSingular="Entry"
                itemNamePlural="Entries"
                buttonHref="#newEntry"
            />
        </CmsLayout>
    );
}
