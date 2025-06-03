import { NavSection } from '../layout/SidebarInnerLayout';

export default function NavItems (activeUri: string): Array<NavSection> {
    return [{
        id: 'AboutNav',
        nav: [
            {
                content: 'About',
                href: '/about',
                isActive: activeUri === '/about',
            },
            {
                content: 'Mission Statement',
                href: '/about/mission-statement',
                isActive: activeUri === '/about/mission-statement',
            },
            {
                content: 'Liturgy and Sacraments',
                href: '/about/liturgy-and-sacraments',
                isActive: activeUri === '/about/liturgy-and-sacraments',
            },
            {
                content: 'Leadership',
                href: '/about/leadership',
                isActive: activeUri === '/about/leadership',
            },
            {
                content: 'Church Government',
                href: '/about/church-government',
                isActive: activeUri === '/about/church-government',
            },
            {
                content: 'Membership',
                href: '/about/membership',
                isActive: activeUri === '/about/membership',
            },
            {
                content: 'Connections and Associations',
                href: '/about/connections',
                isActive: activeUri === '/about/connections',
            },
        ],
    }];
}
