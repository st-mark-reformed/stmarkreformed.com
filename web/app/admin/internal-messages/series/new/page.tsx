import React from 'react';
import { Metadata } from 'next';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../../../messages/HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import CreateNewSeriesPage from './CreateNewSeriesPage';
import { createPageTitle } from '../../../../createPageTitle';

export const metadata: Metadata = {
    title: createPageTitle([
        'New Series',
        'Internal Messages',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="internalMessages">
            <HasEditMessagesRoleGuard>
                <CreateNewSeriesPage />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
