import React from 'react';

export default function PortalOverlay (
    {
        children,
    }: {
        children: React.ReactNode;
    },
) {
    return (
        <div className="relative z-50">
            <div className="relative z-50">
                <div className="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" />
                <div className="fixed inset-0 z-10 overflow-y-auto">
                    <div className="flex min-h-full items-end justify-center p-4 text-center sm:items-center">
                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
