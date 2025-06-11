import React from 'react';
import { Metadata } from 'next';
import CmsLayout from '../../../layout/CmsLayout';
import { createPageTitle } from '../../../../createPageTitle';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'New Entry',
            'Messages',
            'CMS',
        ]),
    };
}

export default async function Page () {
    return (
        <CmsLayout>
            TODO
        </CmsLayout>
    );
}
