import {
    ForwardRefExoticComponent,
    PropsWithoutRef,
    SVGProps,
    RefAttributes,
} from 'react';
import {
    MicrophoneIcon,
    UserIcon,
} from '@heroicons/react/24/outline';
import { usePathname } from 'next/navigation';

type NavigationItem = {
    name: string;
    href?: string;
    // eslint-disable-next-line max-len
    icon: ForwardRefExoticComponent<PropsWithoutRef<SVGProps<SVGSVGElement>> & { title?: string; titleId?: string } & RefAttributes<SVGSVGElement>>;
    current: boolean;
};

function createItem (
    {
        name,
        href,
        icon,
        currentPathname,
    }: {
        name: string;
        href?: string;
        // eslint-disable-next-line max-len
        icon: ForwardRefExoticComponent<PropsWithoutRef<SVGProps<SVGSVGElement>> & { title?: string; titleId?: string } & RefAttributes<SVGSVGElement>>;
        currentPathname: string;
    },
): NavigationItem {
    const trimmedPath = currentPathname.split('/').filter(Boolean).join('/');

    const firstThreeSegments = trimmedPath.split('/').slice(0, 3).join('/');

    return {
        name,
        href,
        icon,
        current: `/${firstThreeSegments}` === href,
    };
}

export default function Navigation (): Array<NavigationItem> {
    const currentPathname = usePathname();

    return [
        createItem({
            name: 'Messages',
            href: '/cms/entries/messages-test',
            icon: MicrophoneIcon,
            currentPathname,
        }),
        createItem({
            name: 'Profiles',
            href: '/cms/profiles',
            icon: UserIcon,
            currentPathname,
        }),
    ];
}
