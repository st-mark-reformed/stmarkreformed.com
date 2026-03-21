import React from 'react';
import AdminLayout from '../Layout/AdminLayout';
import ProfilesPage from './ProfilesPage';

export default async function Page () {
    return (
        <AdminLayout activeNav="profiles">
            <ProfilesPage />
        </AdminLayout>
    );
}
