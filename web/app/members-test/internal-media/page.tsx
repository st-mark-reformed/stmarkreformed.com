import React from 'react';
import { Metadata } from 'next';
import MembersInternalMediaPage from './MembersInternalMediaPage';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Internal Media',
        'Members Area',
    ]),
};

export default async function Page () {
    return <MembersInternalMediaPage pageNum={1} />;
}
