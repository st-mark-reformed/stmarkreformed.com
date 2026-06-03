'use client';

import React from 'react';
import CreateEditNewsPage from '../CreateEditNewsPage';

export default function CreateNewNewsPage () {
    return (
        <CreateEditNewsPage
            pageTitle="Create New News Entry"
            submitFormAction="new"
        />
    );
}
