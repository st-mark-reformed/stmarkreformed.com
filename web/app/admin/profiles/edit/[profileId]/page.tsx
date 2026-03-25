import React from 'react';
import type { Metadata } from 'next';
import { notFound } from 'next/navigation';
import AdminLayout from '../../../Layout/AdminLayout';
import HasEditProfilesRoleGuard from '../../HasEditProfilesRoleGuard/HasEditProfilesRoleGuard';
import EditProfilePage from './EditProfilePage';
import GetEditProfile from './GetEditProfile';
import { createPageTitle } from '../../../../createPageTitle';

type Props = {
    params: Promise<{ profileId: string }>;
};

export async function generateMetadata ({ params }: Props): Promise<Metadata> {
    const { profileId } = await params;

    const profile = await GetEditProfile(profileId);

    if (!profile) {
        notFound();
    }

    return {
        title: createPageTitle([
            `Edit Profile: ${profile.fullNameWithHonorific}`,
            'Profiles',
            'Admin',
        ]),
    };
}

export default async function Page ({ params }: Props) {
    const { profileId } = await params;

    return (
        <AdminLayout activeNav="profiles">
            <HasEditProfilesRoleGuard>
                <EditProfilePage profileId={profileId} />
            </HasEditProfilesRoleGuard>
        </AdminLayout>
    );
}
