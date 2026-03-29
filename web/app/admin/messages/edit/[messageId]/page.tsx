import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import GetEditMessage from './GetEditMessage';
import { createPageTitle } from '../../../../createPageTitle';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditMessagesRoleGuard from '../../HasEditMessagesRoleGuard/HasEditMessagesRoleGuard';
import EditMessagePage from './EditMessagePage';

type Props = {
    params: Promise<{ messageId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { messageId } = await params;

    const message = await GetEditMessage(messageId);

    if (!message) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Message: ${message?.title}`,
            'Messages',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { messageId } = await params;

    return (
        <AdminLayout activeNav="messages">
            <HasEditMessagesRoleGuard>
                <EditMessagePage messageId={messageId} />
            </HasEditMessagesRoleGuard>
        </AdminLayout>
    );
}
