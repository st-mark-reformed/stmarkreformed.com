import React from 'react';
import { notFound } from 'next/navigation';
import GetEditProfile from './GetEditProfile';
import EditProfilePageClientSide from './EditProfilePageClientSide';

export default async function EditProfilePage (
    {
        profileId,
    }: {
        profileId: string;
    },
) {
    const profile = await GetEditProfile(profileId);

    if (!profile) {
        notFound();
    }

    return <EditProfilePageClientSide profile={profile} />;
}
