'use client';

import React from 'react';
import { Profile } from '../../Profile';
import CreateEditProfilePage from '../../CreateEditProfilePage';

export default function EditProfilePageClientSide (
    {
        profile,
    }: {
        profile: Profile;
    },
) {
    return (
        <CreateEditProfilePage
            pageTitle={`Edit Profile: ${profile.fullNameWithHonorific}`}
            submitFormAction="edit"
            initialValues={profile}
            profileId={profile.id}
        />
    );
}
