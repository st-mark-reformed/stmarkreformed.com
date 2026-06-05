'use client';

import React from 'react';
import CreateEditResourcePage from '../CreateEditResourcePage';

export default function CreateNewResourcePage () {
    return (
        <CreateEditResourcePage
            pageTitle="Create New Resource"
            submitFormAction="new"
        />
    );
}
