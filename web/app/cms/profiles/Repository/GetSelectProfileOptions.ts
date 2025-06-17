'use server';

import { Profile } from '../Profile';
import { RequestFactory } from '../../../api/request/RequestFactory';

export interface Option {
    label: string;
    value: string;
}

export type Options = Array<Option>;

export default async function GetSelectProfileOptions (): Promise<Options | null> {
    const apiResponse = await RequestFactory().makeWithToken({
        uri: '/cms/profiles',
        cacheSeconds: 0,
    });

    const profiles = apiResponse.json as unknown as Array<Profile>;

    if (!Array.isArray(profiles)) {
        return null;
    }

    return profiles.map((profile) => ({
        label: profile.fullNameWithHonorific,
        value: profile.id,
    }));
}
