import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../../Layout/AdminLayout';
import { createPageTitle } from '../../../createPageTitle';
import CreateNewProfilePage from './CreateNewProfilePage';
import HasEditProfilesRoleGuard from '../HasEditProfilesRoleGuard/HasEditProfilesRoleGuard';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'Profiles',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="profiles">
            <HasEditProfilesRoleGuard>
                <CreateNewProfilePage />
            </HasEditProfilesRoleGuard>
        </AdminLayout>
    );
}
