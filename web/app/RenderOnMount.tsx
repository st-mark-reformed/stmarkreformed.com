'use client';

import React from 'react';
import useIsMounted from './useIsMounted';

export default function RenderOnMount (
    {
        children,
    }: {
        children: React.ReactNode;
    },
) {
    const isMounted = useIsMounted();

    if (!isMounted) {
        return null;
    }

    return <>{children}</>;
}
