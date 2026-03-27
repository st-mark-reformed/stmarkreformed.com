'use client';

import React from 'react';
import CreateEditSeriesPage from '../CreateEditSeriesPage';

export default function CreateNewSeriesPage () {
    return (
        <CreateEditSeriesPage
            pageTitle="Create New Series"
            submitFormAction="new"
        />
    );
}
