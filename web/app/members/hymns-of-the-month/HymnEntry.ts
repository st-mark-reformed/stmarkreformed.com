export interface HymnEntry {
    title: string;
    slug: string;
    hymnPsalmName: string;
    content: string;
    musicSheetFileName: string | null;
    musicSheetFilePath: string | null;
    practiceTracks: Array<{
        title: string;
        path: string;
    }>;
}
