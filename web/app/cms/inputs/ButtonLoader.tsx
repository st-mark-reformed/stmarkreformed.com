import React from 'react';

export default function ButtonLoader (
    {
        orientation = 'left',
    }: {
        orientation?: 'left' | 'right';
    },
) {
    const margin = orientation === 'left' ? 'mr-1' : 'ml-1';

    return (
        <div className={`inline-block align-middle ${margin}`}>
            <div
                className="overflow-hidden opacity-75 flex flex-col items-center justify-center"
            >
                <div className="loader ease-linear rounded-full border-2 border-t-2 border-white h-3 w-3" />
            </div>
        </div>
    );
}
