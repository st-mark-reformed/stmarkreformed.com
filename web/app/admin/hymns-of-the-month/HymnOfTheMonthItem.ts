export interface HymnOfTheMonthPracticeTrack {
    title: string;
    path: string;
}

export interface HymnOfTheMonthItem {
    id: string;
    isEnabled: boolean;
    date: string;
    title: string;
    slug: string;
    hymnPsalmName: string;
    musicSheetPath: string;
    practiceTracks: HymnOfTheMonthPracticeTrack[];
}
