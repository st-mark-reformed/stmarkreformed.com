import { ReactNode } from 'react';
import { SidebarNavLink } from './SidebarNavLink';

export interface SidebarNavSection {
    id: string;
    heading?: string | ReactNode;
    nav: Array<SidebarNavLink>;
}
