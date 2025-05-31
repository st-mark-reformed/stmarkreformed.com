import RSS from 'rss';
import { ConfigOptions, getConfigString } from '../../ServerSideRunTimeConfig';
import FindAllNewsItems from '../repository/FindAllNewsItems';

export async function GET () {
    const entries = await FindAllNewsItems();

    const siteUrl = getConfigString(ConfigOptions.BASE_URL);

    const feed = new RSS({
        title: 'SMRC News',
        generator: 'stmarkreformed.com',
        feed_url: `${siteUrl}/news/rss`,
        site_url: `${siteUrl}/news`,
        copyright: new Date().getFullYear().toString(),
        language: 'en',
    });

    entries.forEach((entry) => {
        feed.item({
            title: entry.title,
            guid: entry.uid,
            description: entry.content,
            url: `${siteUrl}/news/${entry.slug}`,
            date: new Date(entry.postDate).toUTCString(),
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
