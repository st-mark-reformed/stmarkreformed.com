import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../createPageTitle';
import ResourcesIndexPage from './ResourcesIndexPage';

export const dynamic = 'force-dynamic';

export const metadata: Metadata = {
    title: createPageTitle('Resources'),
};

export default async function Page () {
    return <ResourcesIndexPage pageNum={1} />;
}
