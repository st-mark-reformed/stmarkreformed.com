'use client';

import React from 'react';
import { HymnOfTheMonthItem } from '../../HymnOfTheMonthItem';
import CreateEditHymnOfTheMonthPage from '../../CreateEditHymnOfTheMonthPage';

export default function EditHymnOfTheMonthPageClientSide (
    {
        hymnOfTheMonthItem,
    }: {
        hymnOfTheMonthItem: HymnOfTheMonthItem;
    },
) {
    return (
        <CreateEditHymnOfTheMonthPage
            pageTitle={`Edit Hymn of the Month: ${hymnOfTheMonthItem.title}`}
            submitFormAction="edit"
            initialValues={{
                isEnabled: hymnOfTheMonthItem.isEnabled,
                // The month picker expects "YYYY-MM".
                month: hymnOfTheMonthItem.date.slice(0, 7),
                hymnPsalmName: hymnOfTheMonthItem.hymnPsalmName,
                musicSheet: hymnOfTheMonthItem.musicSheetPath,
                practiceTracks: hymnOfTheMonthItem.practiceTracks.map((track) => ({
                    title: track.title,
                    file: track.path,
                })),
            }}
            hymnOfTheMonthId={hymnOfTheMonthItem.id}
        />
    );
}
