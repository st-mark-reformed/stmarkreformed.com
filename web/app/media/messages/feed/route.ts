// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable indent */
import { ConfigOptions, getConfigString } from '../../../ServerSideRunTimeConfig';
import FindAllMessages from '../repository/FindAllMessages';

export const dynamic = 'force-dynamic';

const escapeHTML = (str: string) => str.replace(
    /[&<>'"]/g,
    // @ts-expect-error TS2769
    (tag) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;',
    }[tag]),
);

export async function GET () {
    const baseUrl = getConfigString(ConfigOptions.BASE_URL);

    const messages = await FindAllMessages(100);

    const date = new Date(messages[0].postDate);

    const lastPubDate = date.toUTCString().replace('GMT', '-0600');

    return new Response(
        `<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="https://purl.org/dc/terms" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:admin="http://webns.net/mvcb/" xmlns:atom="http://www.w3.org/2005/Atom/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:spotify="http://www.spotify.com/ns/rss" xmlns:psc="https://podlove.org/simple-chapters/" version="2.0">
    <channel>
        <atom:link href="${baseUrl}/media/messages/feed" type="application/rss+xml" rel="self"/>
        <title>Messages From St. Mark Reformed Church</title>
        <link>${baseUrl}/media/messages/feed</link>
        <pubDate>${lastPubDate}</pubDate>
        <description>Serving Christ and the world through liturgy, mission, and community.</description>
        <itunes:summary>Serving Christ and the world through liturgy, mission, and community.</itunes:summary>
        <language>en-US</language>
        <itunes:author>St. Mark Reformed Church</itunes:author>
        <image>
            <url>${baseUrl}/images/podcast/st-mark-podcast-art.jpg</url>
        </image>
        <itunes:image href="${baseUrl}/images/podcast/st-mark-podcast-art.jpg"/>
        <itunes:owner>
            <itunes:name>St. Mark Reformed Church</itunes:name>
            <itunes:email>info@stmarkreformed.com</itunes:email>
        </itunes:owner>
        <itunes:category text="Religion &amp; Spirituality"/>
        <itunes:explicit>no</itunes:explicit>
        <copyright>Copyright ${date.getFullYear()} St. Mark Reformed Church</copyright>
        ${messages.map((message) => {
            const messageDate = new Date(message.postDate);

            const pubDate = messageDate.toUTCString().replace('GMT', '-0600');

            let authorTag = '';
            if (message.by?.title) {
                authorTag = `<author>${message.by.title}</author>
            <itunes:author>${message.by.title}</itunes:author>`;
            }

            let enclosureTag = '';
            if (message.audioFileName) {
                enclosureTag = `<enclosure url="${baseUrl}/uploads/audio/${message.audioFileName}" length="${message.audioFileSize}" type="audio/mpeg"/>`;
            }

            return (
                `<item>
            <title><![CDATA[${escapeHTML(message.title)}]]></title>
            <link>${baseUrl}/media/messages/${message.slug}</link>
            <guid isPermaLink="false">${message.uid}</guid>
            <pubDate>${pubDate}</pubDate>
            ${authorTag}
            ${enclosureTag}
            <content:encoded>
                ${escapeHTML(`<ul>
                    <li><strong>Title:</strong> ${message.title}</li>
                    <li><strong>Date:</strong> ${message.postDateDisplay}</li>
                    ${message.by?.title ? `<li><strong>By:</strong> <a href="${baseUrl}/media/messages/by/${message.by.slug}">${message.by.title}</a></li>` : ''}
                    ${message.series?.title ? `<li><strong>Series:</strong> <a href="${baseUrl}/media/messages/series/${message.series.slug}">${message.series.title}</a></li>` : ''}
                    ${message.text ? `<li><strong>Text:</strong> ${message.text}</li>` : ''}
                </ul>
            `)}</content:encoded>
            <image>
                <url>${baseUrl}/images/podcast/st-mark-podcast-art.jpg</url>
            </image>
            <itunes:image href="${baseUrl}/images/podcast/st-mark-podcast-art.jpg" />
        </item>`
            );
        }).join(`
        `)}
    </channel>
</rss>
`,
        {
            headers: {
                'Content-Type': 'text/xml',
            },
        },
    );
}
