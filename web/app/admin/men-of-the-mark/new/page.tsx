import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditMenOfTheMarkRoleGuard from '../HasEditMenOfTheMarkRoleGuard/HasEditMenOfTheMarkRoleGuard';
import CreateNewMenOfTheMarkPage from './CreateNewMenOfTheMarkPage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'Men of the Mark',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="menOfTheMark">
            <HasEditMenOfTheMarkRoleGuard>
                <CreateNewMenOfTheMarkPage />
            </HasEditMenOfTheMarkRoleGuard>
        </AdminLayout>
    );
}
