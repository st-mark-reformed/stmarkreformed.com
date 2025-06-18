// @ts-check

const date = new Date();
const buildDate = date.toUTCString();

/**
 * @type {import('next').NextConfig}
 */
module.exports = {
    env: {
        BUILD_YEAR: date.getFullYear().toString(),
        BUILD_DATE: buildDate,
    },
    publicRuntimeConfig: {
    },
    serverRuntimeConfig: {
    },
    eslint: {
        ignoreDuringBuilds: true,
    },
    poweredByHeader: false,
    reactStrictMode: true,
    experimental: {
        serverActions: {
            bodySizeLimit: '500mb',
        },
    },
    async redirects () {
        return [
            {
                source: '/talent-show',
                destination: 'https://docs.google.com/spreadsheets/d/14vULuTl1ikyzzz4mtK6Hb9j3flG7X_hsCHKil-CAIMg/edit?usp=sharing',
                permanent: true,
            },
            {
                source: '/sms',
                destination: '/media/messages/series/a-theological-interpretation-of-american-history',
                permanent: true,
            },
            {
                source: '/pyles',
                destination: 'https://www.gofundme.com/f/help-for-the-pyle-family-house-fire-recovery',
                permanent: true,
            },
            {
                source: '/prayer-list',
                destination: 'https://docs.google.com/document/d/1dGsAepCpPRJXqvwL0LrUpX8GfWbeiQwuUmN18SgV5MA/edit',
                permanent: true,
            },
            {
                source: '/pancakes',
                destination: 'https://docs.google.com/document/d/1yTjgLJdZOl6JYwmMd_nEChJCTx442uKvpH1pXCRT2zs/edit?usp=sharing',
                permanent: true,
            },
            {
                source: '/media/galleries/twelfth-night-january-2020',
                destination: '/media/galleries/twelfth-night-january-2021',
                permanent: true,
            },
            {
                source: '/blake-purcell',
                destination: 'https://www.facebook.com/tjdraper/posts/10161630527683682',
                permanent: true,
            },
        ];
    },
};
