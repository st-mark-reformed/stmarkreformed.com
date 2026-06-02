'use client';

import React from 'react';
import CreateEditInternalMessagePage from '../CreateEditInternalMessagePage';

export default function CreateNewInternalMessagePage () {
    return (
        <CreateEditInternalMessagePage
            pageTitle="Create New Internal Message"
            submitFormAction="new"
        />
    );
}
