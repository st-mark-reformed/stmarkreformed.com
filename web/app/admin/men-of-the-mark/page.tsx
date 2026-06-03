import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import MenOfTheMarkPage from './MenOfTheMarkPage';
import HasEditMenOfTheMarkRoleGuard from './HasEditMenOfTheMarkRoleGuard/HasEditMenOfTheMarkRoleGuard';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Men of the Mark',
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
        <AdminLayout activeNav="menOfTheMark">
            <HasEditMenOfTheMarkRoleGuard>
                <MenOfTheMarkPage pageNum={1} keyword={keyword ?? ''} />
            </HasEditMenOfTheMarkRoleGuard>
        </AdminLayout>
    );
}
