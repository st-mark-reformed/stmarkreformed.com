import RSS from 'rss';
import MenOfTheMarkMetaData from '../MenOfTheMarkMetaData';
import { ConfigOptions, getConfigString } from '../../../ServerSideRunTimeConfig';
import FindAllMenOfTheMarkEntries from '../repository/FindAllMenOfTheMarkEntries';

export const dynamic = 'force-dynamic';

export async function GET () {
    const { entries } = await FindAllMenOfTheMarkEntries();

    const siteUrl = getConfigString(ConfigOptions.BASE_URL);

    const feed = new RSS({
        title: MenOfTheMarkMetaData.title,
        description: MenOfTheMarkMetaData.description,
        generator: 'stmarkreformed.com',
        feed_url: `${siteUrl}/publications/men-of-the-mark-test/rss`,
        site_url: `${siteUrl}/publications/men-of-the-mark-test`,
        copyright: new Date().getFullYear().toString(),
        language: 'en',
    });

    entries.forEach((entry) => {
        feed.item({
            title: entry.title,
            guid: entry.uid,
            description: entry.bodyHtml,
            url: `${siteUrl}/publications/men-of-the-mark-test/${entry.slug}`,
            date: new Date(entry.publicationDate).toUTCString().replace(
                'GMT',
                '-0600',
            ),
        });
    });

    return new Response(
        feed.xml({ indent: true }),
        {
            headers: {
                'Content-Type': 'text/xml',
            },
        },
    );
}
