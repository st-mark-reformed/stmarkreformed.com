import React from 'react';
import { Metadata } from 'next';
import { createPageTitle } from '../../../createPageTitle';
import AdminLayout from '../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../../messages/HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import CreateNewInternalMessagePage from './CreateNewInternalMessagePage';

export const metadata: Metadata = {
    title: createPageTitle([
        'Create New',
        'Internal Messages',
        'Admin',
    ]),
};

export default async function Page () {
    return (
        <AdminLayout activeNav="internalMessages">
            <HasEditMessagesRoleGuard>
                <CreateNewInternalMessagePage />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
