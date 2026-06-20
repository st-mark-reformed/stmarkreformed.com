import { AdminAreaKey } from '../adminAreas';
import { DashboardRecentEntry } from './DashboardRecentEntry';
import GetMessages from '../messages/GetMessages';
import GetInternalMessages from '../internal-messages/GetInternalMessages';
import GetProfiles from '../profiles/GetProfiles';
import GetNews from '../news/GetNews';
import GetMenOfTheMark from '../men-of-the-mark/GetMenOfTheMark';
import GetPastorsPage from '../pastors-page/GetPastorsPage';
import GetHymnsOfTheMonth from '../hymns-of-the-month/GetHymnsOfTheMonth';
import GetResources from '../resources/GetResources';
import GetMailingLists from '../mailing-lists/GetMailingLists';

const RECENT_LIMIT = 4;

// The dashboard's read view of each content area: wrap the area's existing
// fetcher, take the most recent few, and normalize to DashboardRecentEntry.
// Areas without a list concept (schedule, queue) are intentionally absent.
const providers: Partial<Record<AdminAreaKey, () => Promise<DashboardRecentEntry[]>>> = {
    messages: async () => (await GetMessages(1, '')).entries
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.title,
            meta: entry.date,
            editHref: `/admin/messages/edit/${entry.id}`,
        })),
    internalMessages: async () => (await GetInternalMessages(1)).entries
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.title,
            meta: entry.date,
            editHref: `/admin/internal-messages/edit/${entry.id}`,
        })),
    profiles: async () => (await GetProfiles())
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.fullNameWithHonorific,
            meta: entry.leadershipPositionHumanReadable,
            editHref: `/admin/profiles/edit/${entry.id}`,
        })),
    news: async () => (await GetNews(1, '')).entries
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.title,
            meta: entry.date,
            editHref: `/admin/news/edit/${entry.id}`,
        })),
    menOfTheMark: async () => (await GetMenOfTheMark(1, '')).entries
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.title,
            meta: entry.date,
            editHref: `/admin/men-of-the-mark/edit/${entry.id}`,
        })),
    pastorsPage: async () => (await GetPastorsPage(1, '')).entries
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.title,
            meta: entry.date,
            editHref: `/admin/pastors-page/edit/${entry.id}`,
        })),
    hymnsOfTheMonth: async () => (await GetHymnsOfTheMonth(1, '')).entries
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.title,
            meta: entry.date,
            editHref: `/admin/hymns-of-the-month/edit/${entry.id}`,
        })),
    resources: async () => (await GetResources(1, '')).entries
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.title,
            meta: entry.date,
            editHref: `/admin/resources/edit/${entry.id}`,
        })),
    mailingLists: async () => (await GetMailingLists(1, '')).entries
        .slice(0, RECENT_LIMIT)
        .map((entry) => ({
            id: entry.id,
            title: entry.listName,
            meta: entry.listAddress,
            editHref: `/admin/mailing-lists/edit/${entry.id}`,
        })),
};

// Whether an area lists entries on the dashboard. False for monitoring-only
// areas (schedule, queue) that have no entries to show.
export function areaHasRecentList (key: AdminAreaKey): boolean {
    return key in providers;
}

// Fetch an area's recent entries, degrading to an empty list on failure so one
// area's error cannot take down the whole dashboard.
export async function getRecentEntries (key: AdminAreaKey): Promise<DashboardRecentEntry[]> {
    const provider = providers[key];

    if (!provider) {
        return [];
    }

    try {
        return await provider();
    } catch {
        return [];
    }
}
