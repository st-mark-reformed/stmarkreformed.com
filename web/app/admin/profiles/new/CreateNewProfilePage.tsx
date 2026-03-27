'use client';

import React from 'react';
import CreateEditProfilePage from '../CreateEditProfilePage';

export default function CreateNewProfilePage () {
    return (
        <CreateEditProfilePage
            pageTitle="Create New Profile"
            submitFormAction="new"
        />
    );
}
