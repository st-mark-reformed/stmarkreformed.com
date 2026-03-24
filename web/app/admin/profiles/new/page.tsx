import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../../Layout/AdminLayout';
import { createPageTitle } from '../../../createPageTitle';
import CreateNewProfilePage from './CreateNewProfilePage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'Profiles',
        'Admin',
    ]),
};

export default function Page () {
    return (
        <AdminLayout activeNav="profiles">
            <CreateNewProfilePage />
        </AdminLayout>
    );
}
