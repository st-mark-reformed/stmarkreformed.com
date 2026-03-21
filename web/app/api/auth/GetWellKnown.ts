import { GetWellKnown as RxAnteGetWellKnown } from 'rxante-oauth/dist/WellKnown';
import { ConfigOptions, getConfigString } from '../../ServerSideRunTimeConfig';
import getRedisClient from '../../cache/RedisClient';

export default async function GetWellKnown () {
    return RxAnteGetWellKnown(
        getConfigString(ConfigOptions.AUTH_WELL_KNOWN_URL),
        getRedisClient(),
    );
}
