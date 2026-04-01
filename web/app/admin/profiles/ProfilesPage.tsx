import React from 'react';

import GetProfiles from './GetProfiles';
import ProfilesPageClientSide from './ProfilesPageClientSide';

export default async function ProfilesPage () {
    const profiles = await GetProfiles();

    return <ProfilesPageClientSide profiles={profiles} />;
}
