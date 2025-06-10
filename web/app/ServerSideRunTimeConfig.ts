import fs from 'fs';

export enum ConfigOptions {
    DEV_MODE,
    BASE_URL,
    API_URL,
    APP_API_URL,
    REDIS_HOST,
    MEMBER_EMAIL_ADDRESS,
    MEMBER_PASSWORD,
    ENCRYPTION_KEY,
    NEXTAUTH_SECRET,
    NEXTAUTH_WELL_KNOWN_URL,
    NEXTAUTH_CLIENT_ID,
    NEXTAUTH_CLIENT_SECRET,
}

function getConfigValue (
    from: ConfigOptions,
    defaultVal?: string | boolean | number,
): string | boolean | number {
    const fromKey = ConfigOptions[from];

    const fromEnv = process.env[fromKey];

    if (fromEnv !== undefined) {
        return fromEnv;
    }

    const secretPath = `/run/secrets/${fromKey}`;

    if (fs.existsSync(secretPath)) {
        return fs.readFileSync(secretPath).toString().trim();
    }

    if (defaultVal !== undefined) {
        return defaultVal;
    }

    throw new Error([
        fromKey,
        'could not be found in secrets or environment variables',
        'and no default value was provided',
    ].join(' '));
}

export function getConfigString (
    from: ConfigOptions,
    defaultVal?: string,
): string {
    return getConfigValue(from, defaultVal).toString();
}

export function getConfigBoolean (
    from: ConfigOptions,
    defaultVal?: boolean,
): boolean {
    const val = getConfigValue(from, defaultVal);

    if (typeof val === 'string') {
        return val === '1';
    }

    if (typeof val === 'number') {
        return val !== 0;
    }

    return val;
}

export function getConfigNumber (
    from: ConfigOptions,
    defaultVal?: number,
) {
    const val = getConfigValue(from, defaultVal);

    if (typeof val === 'string') {
        return parseInt(val, 10);
    }

    if (typeof val === 'boolean') {
        return val ? 1 : 0;
    }

    return val;
}
