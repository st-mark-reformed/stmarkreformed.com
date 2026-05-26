import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import SeriesPage from './SeriesPage';
import { createPageTitle } from '../../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'Series',
        'Messages',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <SeriesPage />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
