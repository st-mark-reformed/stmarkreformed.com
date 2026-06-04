export type CreateEditHymnOfTheMonthPracticeTrackValue = {
    title: string;
    // Either an existing stored relative path or a new base64 data URI.
    file: string;
};

export type CreateEditHymnOfTheMonthValues = {
    isEnabled: boolean;
    // "YYYY-MM" from the month picker.
    month: string;
    hymnPsalmName: string;
    // Either an existing stored relative path or a new base64 data URI.
    musicSheet: string;
    practiceTracks: CreateEditHymnOfTheMonthPracticeTrackValue[];
};
