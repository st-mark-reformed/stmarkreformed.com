'use client';

import React from 'react';
import CreateEditHymnOfTheMonthPage from '../CreateEditHymnOfTheMonthPage';

export default function CreateNewHymnOfTheMonthPage () {
    return (
        <CreateEditHymnOfTheMonthPage
            pageTitle="Create New Hymn of the Month Entry"
            submitFormAction="new"
        />
    );
}
