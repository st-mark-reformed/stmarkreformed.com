import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import NewsPage from './NewsPage';
import HasEditNewsRoleGuard from './HasEditNewsRoleGuard/HasEditNewsRoleGuard';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'News',
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
        <AdminLayout activeNav="news">
            <HasEditNewsRoleGuard>
                <NewsPage pageNum={1} keyword={keyword ?? ''} />
            </HasEditNewsRoleGuard>
        </AdminLayout>
    );
}
