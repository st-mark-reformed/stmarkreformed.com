import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import PastorsPagePage from './PastorsPagePage';
import HasEditPastorsPageRoleGuard from './HasEditPastorsPageRoleGuard/HasEditPastorsPageRoleGuard';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        "Pastor's Page",
        'Admin',
    ]),
};

export default async function Page (
    {
        searchParams,
    }: {
        searchParams: Promise<{
            keyword?: string;
        }>;
    },
) {
    const { keyword } = await searchParams;

    return (
        <AdminLayout activeNav="pastorsPage">
            <HasEditPastorsPageRoleGuard>
                <PastorsPagePage pageNum={1} keyword={keyword ?? ''} />
            </HasEditPastorsPageRoleGuard>
        </AdminLayout>
    );
}
