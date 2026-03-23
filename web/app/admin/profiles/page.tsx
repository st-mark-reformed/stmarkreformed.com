import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../Layout/AdminLayout';
import ProfilesPage from './ProfilesPage';
import { createPageTitle } from '../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Profiles',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="profiles">
            <ProfilesPage />
        </AdminLayout>
    );
}
