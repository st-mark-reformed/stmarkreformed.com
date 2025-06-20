'use server';

import { RequestFactory } from '../../../../../api/request/RequestFactory';
import { File } from '../File';

export default async function GetSelectFileNames (): Promise<Array<string> | null> {
    const apiResponse = await RequestFactory().makeWithSignInRedirect({
        uri: '/cms/entries/messages/files',
        cacheSeconds: 0,
    });

    const files = apiResponse.json as unknown as Array<File>;

    if (!Array.isArray(files)) {
        return null;
    }

    return files.map((file) => file.filename);
}
