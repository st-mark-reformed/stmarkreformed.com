export interface GalleryEntry {
    title: string;
    slug: string;
    videos?: Array<string>; // youtube ID
    pictures?: Array<string>; // filename
    posterFilename?: string;
}

export interface GalleryEntryFull {
    title: string;
    slug: string;
    href: string;
    videos: Array<string>; // youtube ID
    pictures: Array<string>; // href
    posterUrl: string;
}
