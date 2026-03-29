'use client';

import React from 'react';
import CreateEditMessagePage from '../CreateEditMessagePage';

export default function CreateNewMessagePage () {
    return (
        <CreateEditMessagePage
            pageTitle="Create New Message"
            submitFormAction="new"
        />
    );
}
