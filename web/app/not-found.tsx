import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from './createPageTitle';
import FullPageError from './FullPageError';
import Layout from './layout/Layout';

export const metadata: Metadata = {
    title: createPageTitle('Page Not Found'),
};

export default function NotFoundPage () {
    return (
        <Layout>
            <FullPageError
                statusCode={404}
                heading="Page Not Found"
                errorMessage="We weren’t able to find that page 🫠"
            />
        </Layout>
    );
}
