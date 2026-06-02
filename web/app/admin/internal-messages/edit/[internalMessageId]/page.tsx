import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import GetEditInternalMessage from './GetEditInternalMessage';
import { createPageTitle } from '../../../../createPageTitle';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../../../messages/HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import EditInternalMessagePage from './EditInternalMessagePage';

type Props = {
    params: Promise<{ internalMessageId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { internalMessageId } = await params;

    const message = await GetEditInternalMessage(internalMessageId);

    if (!message) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Internal Message: ${message?.title}`,
            'Internal Messages',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { internalMessageId } = await params;

    return (
        <AdminLayout activeNav="internalMessages">
            <HasEditMessagesRoleGuard>
                <EditInternalMessagePage internalMessageId={internalMessageId} />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
