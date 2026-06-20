import React from 'react';
import GetUserInfo from '../../api/auth/GetUserInfo';
import Breadcrumbs from '../Breadcrumbs';
import PageTitle from '../PageTitle';
import Alert from '../../Alert';
import { accessibleAreas } from '../adminAreas';
import { getRecentEntries } from './recentEntryProviders';
import DashboardCard from './DashboardCard';

export default async function DashboardPage () {
    const userinfo = await GetUserInfo();

    const areas = accessibleAreas(userinfo.roles);

    const cards = await Promise.all(areas.map(async (area) => ({
        area,
        recent: await getRecentEntries(area.key),
    })));

    return (
        <>
            <Breadcrumbs />
            <PageTitle>Dashboard</PageTitle>
            {(() => {
                if (cards.length > 0) {
                    return null;
                }

                return (
                    <Alert
                        headline="No admin areas available"
                        content="Your account does not currently have access to any admin areas. Contact an administrator if you believe this is a mistake."
                        type="info"
                    />
                );
            })()}
            <div className="grid gap-4 min-[820px]:grid-cols-2 2xl:grid-cols-3">
                {cards.map(({ area, recent }) => (
                    <DashboardCard key={area.key} area={area} recent={recent} />
                ))}
            </div>
        </>
    );
}
