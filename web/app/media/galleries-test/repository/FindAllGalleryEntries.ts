import { GalleryEntryFull } from '../GalleryEntry';
import AllIndex from '../source/AllIndex';

export default function FindAllGalleryEntries (): Array<GalleryEntryFull> {
    return AllIndex.map((entry) => {
        const href = `/media/galleries-test/${entry.slug}`;

        const pictureUrlBase = `/images/galleries/${entry.slug}`;

        let posterFilename = '';

        if (entry.posterFilename) {
            posterFilename = entry.posterFilename;
        } else {
            posterFilename = entry.pictures?.[0] ?? '';
        }

        let posterUrl = '';

        if (posterFilename) {
            posterUrl = `${pictureUrlBase}/${posterFilename}`;
        }

        return ({
            title: entry.title,
            slug: entry.slug,
            href,
            videos: entry.videos || [],
            pictures: (entry.pictures || []).map(
                (filename) => `${pictureUrlBase}/${filename}`,
            ),
            posterUrl,
        });
    });
}
