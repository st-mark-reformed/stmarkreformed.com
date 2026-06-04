import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditHymnsOfTheMonthRoleGuard from '../HasEditHymnsOfTheMonthRoleGuard/HasEditHymnsOfTheMonthRoleGuard';
import CreateNewHymnOfTheMonthPage from './CreateNewHymnOfTheMonthPage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'Hymns of the Month',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="hymnsOfTheMonth">
            <HasEditHymnsOfTheMonthRoleGuard>
                <CreateNewHymnOfTheMonthPage />
            </HasEditHymnsOfTheMonthRoleGuard>
        </AdminLayout>
    );
}
