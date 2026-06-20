import NavItem from './Layout/NavItem';

export type AdminAreaKey =
    | 'messages'
    | 'internalMessages'
    | 'profiles'
    | 'news'
    | 'menOfTheMark'
    | 'pastorsPage'
    | 'hymnsOfTheMonth'
    | 'resources'
    | 'mailingLists'
    | 'schedule'
    | 'queue';

// The known set of admin edit roles. Centralized here so the role strings are
// not scattered as magic strings across the sidebar and the dashboard.
export type AdminRole =
    | 'EDIT_MESSAGES'
    | 'EDIT_PROFILES'
    | 'EDIT_NEWS'
    | 'EDIT_MEN_OF_THE_MARK'
    | 'EDIT_PASTORS_PAGE'
    | 'EDIT_HYMNS_OF_THE_MONTH'
    | 'EDIT_RESOURCES'
    | 'EDIT_MAILING_LISTS';

export interface AdminArea {
    key: AdminAreaKey;
    name: string;
    href: string;
    icon: NavItem['icon'];
    // null means the area is always visible (e.g. Schedule, Queue).
    requiredRole: AdminRole | null;
    // null means the area has no "create new" concept.
    createHref: string | null;
}

// Order here is the order areas appear in the sidebar and on the dashboard.
export const adminAreas: AdminArea[] = [
    {
        key: 'messages',
        name: 'Messages',
        href: '/admin/messages',
        icon: 'Microphone',
        requiredRole: 'EDIT_MESSAGES',
        createHref: '/admin/messages/new',
    },
    {
        key: 'internalMessages',
        name: 'Internal Messages',
        href: '/admin/internal-messages',
        icon: 'LockClosed',
        requiredRole: 'EDIT_MESSAGES',
        createHref: '/admin/internal-messages/new',
    },
    {
        key: 'profiles',
        name: 'Profiles',
        href: '/admin/profiles',
        icon: 'Users',
        requiredRole: 'EDIT_PROFILES',
        createHref: '/admin/profiles/new',
    },
    {
        key: 'news',
        name: 'News',
        href: '/admin/news',
        icon: 'DocumentDuplicate',
        requiredRole: 'EDIT_NEWS',
        createHref: '/admin/news/new',
    },
    {
        key: 'menOfTheMark',
        name: 'Men of the Mark',
        href: '/admin/men-of-the-mark',
        icon: 'Newspaper',
        requiredRole: 'EDIT_MEN_OF_THE_MARK',
        createHref: '/admin/men-of-the-mark/new',
    },
    {
        key: 'pastorsPage',
        name: "Pastor's Page",
        href: '/admin/pastors-page',
        icon: 'BookOpen',
        requiredRole: 'EDIT_PASTORS_PAGE',
        createHref: '/admin/pastors-page/new',
    },
    {
        key: 'hymnsOfTheMonth',
        name: 'Hymns of the Month',
        href: '/admin/hymns-of-the-month',
        icon: 'MusicalNote',
        requiredRole: 'EDIT_HYMNS_OF_THE_MONTH',
        createHref: '/admin/hymns-of-the-month/new',
    },
    {
        key: 'resources',
        name: 'Resources',
        href: '/admin/resources',
        icon: 'DocumentArrowDown',
        requiredRole: 'EDIT_RESOURCES',
        createHref: '/admin/resources/new',
    },
    {
        key: 'mailingLists',
        name: 'Mailing Lists',
        href: '/admin/mailing-lists',
        icon: 'Envelope',
        requiredRole: 'EDIT_MAILING_LISTS',
        createHref: '/admin/mailing-lists/new',
    },
    {
        key: 'schedule',
        name: 'Schedule',
        href: '/admin/schedule',
        icon: 'Calendar',
        requiredRole: null,
        createHref: null,
    },
    {
        key: 'queue',
        name: 'Queue',
        href: '/admin/queue',
        icon: 'QueueList',
        requiredRole: null,
        createHref: null,
    },
];

export function accessibleAreas (roles: string[]): AdminArea[] {
    return adminAreas.filter(
        (area) => area.requiredRole === null || roles.includes(area.requiredRole),
    );
}
