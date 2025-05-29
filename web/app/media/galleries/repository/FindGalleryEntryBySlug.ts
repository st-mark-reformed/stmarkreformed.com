import FindAllGalleryEntries from './FindAllGalleryEntries';
import { GalleryEntryFull } from '../GalleryEntry';

export default function FindGalleryEntryBySlug (
    slug: string,
): GalleryEntryFull | null {
    const allGalleries = FindAllGalleryEntries();

    return allGalleries.filter((entry) => entry.slug === slug)[0] ?? null;
}
