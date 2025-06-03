import RSS from 'rss';
import { ConfigOptions, getConfigString } from '../../ServerSideRunTimeConfig';
import FindAllNewsItems from '../../news/repository/FindAllNewsItems';

export async function GET () {
    const entries = await FindAllNewsItems('pastorsPage');

    const siteUrl = getConfigString(ConfigOptions.BASE_URL);

    const feed = new RSS({
        title: "Pastor's Page",
        generator: 'stmarkreformed.com',
        feed_url: `${siteUrl}/pastors-page-test/rss`,
        site_url: `${siteUrl}/pastors-page-test`,
        copyright: new Date().getFullYear().toString(),
        language: 'en',
    });

    entries.forEach((entry) => {
        feed.item({
            title: entry.title,
            guid: entry.uid,
            description: entry.content,
            url: `${siteUrl}/pastors-page-test/${entry.slug}`,
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
