import React from 'react';
import { Metadata } from 'next';
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
                <PageHeader title="Messages" />
            </div>
            <EmptyState
                itemNameSingular="Entry"
                itemNamePlural="Entries"
            />
        </CmsLayout>
    );
}
