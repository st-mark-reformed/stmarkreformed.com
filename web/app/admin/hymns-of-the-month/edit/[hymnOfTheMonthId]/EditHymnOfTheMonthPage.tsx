import React from 'react';
import GetEditHymnOfTheMonth from './GetEditHymnOfTheMonth';
import EditHymnOfTheMonthPageClientSide from './EditHymnOfTheMonthPageClientSide';

export default async function EditHymnOfTheMonthPage (
    {
        hymnOfTheMonthId,
    }: {
        hymnOfTheMonthId: string;
    },
) {
    const hymnOfTheMonthItem = await GetEditHymnOfTheMonth(hymnOfTheMonthId);

    if (!hymnOfTheMonthItem) {
        return null;
    }

    return (
        <EditHymnOfTheMonthPageClientSide
            hymnOfTheMonthItem={hymnOfTheMonthItem}
        />
    );
}
