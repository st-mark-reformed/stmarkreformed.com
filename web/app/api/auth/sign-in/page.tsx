import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import ClientSidePage from './ClientSidePage';

export async function generateMetadata (): Promise<Metadata> {
    return {
        title: createPageTitle([
            'Sign In',
            'CMS',
        ]),
    };
}

export default function Page () {
    return <ClientSidePage />;
}
