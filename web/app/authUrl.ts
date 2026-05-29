import { ConfigOptions, getConfigString } from './ServerSideRunTimeConfig';

export function authUrl (path: string = ''): string {
    const base = getConfigString(ConfigOptions.AUTH_URL).replace(/\/+$/, '');

    if (path === '') {
        return base;
    }

    return `${base}/${path.replace(/^\/+/, '')}`;
}
