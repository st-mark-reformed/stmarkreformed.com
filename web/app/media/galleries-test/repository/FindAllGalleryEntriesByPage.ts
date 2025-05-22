import { GalleryEntryFull } from '../GalleryEntry';
import FindAllGalleryEntries from './FindAllGalleryEntries';

const perPage = 12;

export default function FindAllGalleryEntriesByPage (
    pageNum: number,
): {
        totalResults: number;
        totalPages: number;
        entries: Array<GalleryEntryFull>;
    } {
    const allGalleries = FindAllGalleryEntries();
    const totalResults = allGalleries.length;
    const totalPages = Math.ceil(totalResults / perPage);

    // Ensure the page number is within bounds
    const validPageNum = Math.max(1, Math.min(pageNum, totalPages));

    const startIndex = (validPageNum - 1) * perPage;
    const endIndex = startIndex + perPage;

    const entries = allGalleries.slice(startIndex, endIndex);

    return {
        totalResults,
        totalPages,
        entries,
    };
}
