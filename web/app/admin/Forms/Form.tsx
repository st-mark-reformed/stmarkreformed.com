import React from 'react';

export default function Form (
    {
        children,
    }: {
        children: React.ReactNode;
    },
) {
    return (
        <form>
            <div className="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                {children}
            </div>
        </form>
    );
}
