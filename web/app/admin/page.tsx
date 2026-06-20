import React from 'react';
import { Metadata } from 'next';
import AdminLayout from './Layout/AdminLayout';
import DashboardPage from './Dashboard/DashboardPage';
import { createPageTitle } from '../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Dashboard',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="dashboard">
            <DashboardPage />
        </AdminLayout>
    );
}
