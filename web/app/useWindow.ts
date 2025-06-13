import useIsMounted from './useIsMounted';

export default function useWindow () {
    const isMounted = useIsMounted();

    if (!isMounted) {
        return null;
    }

    return window;
}
