import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../../HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import CreateNewSeriesPage from './CreateNewSeriesPage';
import { createPageTitle } from '../../../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'New Series',
        'Messages',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <CreateNewSeriesPage />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
