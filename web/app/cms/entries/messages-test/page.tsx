import React, { Suspense } from 'react';
import { Metadata } from 'next';
import { DocumentIcon } from '@heroicons/react/24/outline';
import { PlusIcon } from '@heroicons/react/16/solid';
import CmsLayout from '../../layout/CmsLayout';
import { createPageTitle } from '../../../createPageTitle';
import PageInner from './PageInner';
import PageHeader from '../../layout/PageHeader';
import PartialPageLoading from '../../../PartialPageLoading';

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
                            id: 'newEntry',
                            type: 'primary',
                            content: (
                                <>
                                    <PlusIcon className="h-5 w-5 mr-1" />
                                    New Entry
                                </>
                            ),
                            href: '/cms/entries/messages-test/new-entry',
                        },
                    ]}
                />
            </div>
            <Suspense fallback={<PartialPageLoading />}>
                <PageInner />
            </Suspense>
        </CmsLayout>
    );
}
