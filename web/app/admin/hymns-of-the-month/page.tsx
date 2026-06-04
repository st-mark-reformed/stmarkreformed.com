import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import HymnsOfTheMonthPage from './HymnsOfTheMonthPage';
import HasEditHymnsOfTheMonthRoleGuard from './HasEditHymnsOfTheMonthRoleGuard/HasEditHymnsOfTheMonthRoleGuard';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Hymns of the Month',
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
        <AdminLayout activeNav="hymnsOfTheMonth">
            <HasEditHymnsOfTheMonthRoleGuard>
                <HymnsOfTheMonthPage pageNum={1} keyword={keyword ?? ''} />
            </HasEditHymnsOfTheMonthRoleGuard>
        </AdminLayout>
    );
}
