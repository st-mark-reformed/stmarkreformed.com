export function createPageTitle (title: string | Array<string>) {
    const suffix = 'St. Mark Reformed Church';

    if (!title) {
        return suffix;
    }

    if (typeof title === 'string') {
        title = [title];
    }

    title.push(suffix);

    return title.join(' | ');
}
