import useWindow from './useWindow';

export default function useApiUrl () {
    const hookWindow = useWindow();

    if (!hookWindow) {
        return null;
    }

    if (hookWindow.location.host === 'stmark.localtest.me') {
        return 'https://api.stmark.localtest.me';
    }

    return 'https://api.stmarkreformed.com';
}
