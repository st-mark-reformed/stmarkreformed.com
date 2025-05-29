export type MenuItemType = {
    name: string;
    link: string;
    children: MenuItemType[];
};

export type MenuItems = Array<MenuItemType>;

export const MainMenu: MenuItems = [
    {
        name: 'Calendar',
        link: '/calendar',
        children: [],
    },
    {
        name: 'About',
        link: '/about',
        children: [
            {
                name: 'Mission Statement',
                link: '/about/mission-statement',
                children: [],
            },
            {
                name: 'Liturgy and Sacraments',
                link: '/about/liturgy-and-sacraments',
                children: [],
            },
            {
                name: 'Leadership',
                link: '/about/leadership',
                children: [],
            },
            {
                name: 'Church Government',
                link: '/about/church-government',
                children: [],
            },
            {
                name: 'Membership',
                link: '/about/membership',
                children: [],
            },
            {
                name: 'Connections and Associations',
                link: '/about/connections',
                children: [],
            },
        ],
    },
    {
        name: 'Media',
        link: '',
        children: [
            {
                name: 'Messages',
                link: '/media/messages',
                children: [],
            },
            {
                name: 'Galleries',
                link: '/media/galleries',
                children: [],
            },
            {
                name: 'Resources',
                link: '/resources',
                children: [],
            },
            {
                name: 'News',
                link: '/news',
                children: [],
            },
            {
                name: 'Men of the Mark',
                link: '/publications/men-of-the-mark',
                children: [],
            },
        ],
    },
    {
        name: 'Contact',
        link: '/contact',
        children: [],
    },
];

export const SecondaryMenu: MenuItems = [
    {
        name: 'Members',
        link: '/members',
        children: [],
    },
];
