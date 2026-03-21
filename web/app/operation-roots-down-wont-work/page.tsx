import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../createPageTitle';
import BasicPageLayout from '../layout/BasicPageLayout';
import Layout from '../layout/Layout';

const pageTitle = 'Operation Roots Down';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle(pageTitle),
};

export default function OperationRootsDown () {
    return (
        <BasicPageLayout
            hero={{
                heroImage1x: '/images/operation-roots-down/operation-roots-down-header.jpg',
            }}
        >
            todo
        </BasicPageLayout>
    );
}
