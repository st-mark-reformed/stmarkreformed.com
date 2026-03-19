import { AuthCodeGrantApiFactory } from '../AuthCodeGrantApiFactory';

export async function GET (request: Request) {
    return (await AuthCodeGrantApiFactory()).respondToAuthCodeCallback(request);
}
