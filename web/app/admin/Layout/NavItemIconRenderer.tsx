import {
    CalendarIcon,
    ChartPieIcon,
    DocumentDuplicateIcon,
    FolderIcon,
    HomeIcon,
    UsersIcon,
    MicrophoneIcon, QueueListIcon,
    LockClosedIcon,
    NewspaperIcon,
    BookOpenIcon,
    MusicalNoteIcon,
    DocumentArrowDownIcon,
} from '@heroicons/react/24/outline';
import React from 'react';
import NavItem from './NavItem';

export default function NavItemIconRenderer (
    {
        item,
    }: {
        item: NavItem;
    },
) {
    const classes = ['size-6 shrink-0'];

    switch (item.icon) {
        case 'Home':
            return <HomeIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'Users':
            return <UsersIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'Folder':
            return <FolderIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'Calendar':
            return <CalendarIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'DocumentDuplicate':
            return <DocumentDuplicateIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'ChartPie':
            return <ChartPieIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'Microphone':
            return <MicrophoneIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'QueueList':
            return <QueueListIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'LockClosed':
            return <LockClosedIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'Newspaper':
            return <NewspaperIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'BookOpen':
            return <BookOpenIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'MusicalNote':
            return <MusicalNoteIcon aria-hidden="true" className={classes.join(' ')} />;
        case 'DocumentArrowDown':
            return <DocumentArrowDownIcon aria-hidden="true" className={classes.join(' ')} />;
        default:
            return <></>;
    }
}
