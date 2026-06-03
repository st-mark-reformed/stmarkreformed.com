export default interface NavItem {
    name: string;
    href: string;
    icon: 'Home' | 'Users' | 'Folder' | 'Calendar' | 'DocumentDuplicate' | 'ChartPie' | 'Microphone' | 'QueueList' | 'LockClosed' | 'Newspaper' | 'BookOpen';
    current: boolean;
}
