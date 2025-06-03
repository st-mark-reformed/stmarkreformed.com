import { ReactNode } from 'react';

export interface SidebarNavLink {
    content: string | ReactNode;
    href: string;
    isActive?: boolean;
}
