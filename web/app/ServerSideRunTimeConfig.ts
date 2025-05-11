import fs from 'fs';

/**
 * Due to legacy reasons, some variables are lower_snake_case. All new variables
 * should match our standards as UPPER_SNAKE_CASE
 */
export enum ConfigOptions {
    BASE_URL,
    REDIS_HOST,
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
