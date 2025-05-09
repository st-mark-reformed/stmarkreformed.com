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
};
