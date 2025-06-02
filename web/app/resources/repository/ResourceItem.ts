export interface ResourceItem {
    title: string;
    slug: string;
    body: string;
    resourceDownloads: Array<{
        filename: string;
    }>;
}
