import NextAuth from 'next-auth';
import { NextAuthAuth0ProviderFactory, NextAuthOptionsConfigFactory } from 'rxante-oauth';
import { ConfigOptions, getConfigBoolean, getConfigString } from '../../../ServerSideRunTimeConfig';
import { TokenRepositoryFactory } from '../TokenRepositoryFactory';

const handler = NextAuth(NextAuthOptionsConfigFactory({
    debug: getConfigBoolean(ConfigOptions.DEV_MODE),
    providers: [NextAuthAuth0ProviderFactory({
        wellKnownUrl: getConfigString(ConfigOptions.NEXTAUTH_WELL_KNOWN_URL),
        clientId: getConfigString(ConfigOptions.NEXTAUTH_CLIENT_ID),
        clientSecret: getConfigString(ConfigOptions.NEXTAUTH_CLIENT_SECRET),
        audience: 'smrc_website',
    })],
    secret: getConfigString(ConfigOptions.NEXTAUTH_SECRET),
    tokenRepository: TokenRepositoryFactory(),
}));

export { handler as GET, handler as POST };
