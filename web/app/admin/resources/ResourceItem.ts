export interface ResourceItem {
    id: string;
    isEnabled: boolean;
    date: string;
    title: string;
    slug: string;
    body: string;
    downloads: Array<{
        filename: string;
    }>;
}
