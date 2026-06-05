import React from 'react';
import { notFound } from 'next/navigation';
import type { Metadata } from 'next';
import GetEditResource from './GetEditResource';
import { createPageTitle } from '../../../../createPageTitle';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditResourcesRoleGuard from '../../HasEditResourcesRoleGuard/HasEditResourcesRoleGuard';
import EditResourcePage from './EditResourcePage';

type Props = {
    params: Promise<{ resourceId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { resourceId } = await params;

    const resourceItem = await GetEditResource(resourceId);

    if (!resourceItem) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Resource: ${resourceItem?.title}`,
            'Resources',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { resourceId } = await params;

    return (
        <AdminLayout activeNav="resources">
            <HasEditResourcesRoleGuard>
                <EditResourcePage resourceId={resourceId} />
            </HasEditResourcesRoleGuard>
        </AdminLayout>
    );
}
