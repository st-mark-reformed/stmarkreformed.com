import React from 'react';

export default function FullPageLoading () {
    return (
        <div
            className="fixed top-0 left-0 right-0 bottom-0 w-full h-screen z-50 overflow-hidden bg-white opacity-75 flex flex-col items-center justify-center"
        >
            <div className="loader border-t-gray-500 ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 inline-block align-middle mr-1" />
        </div>
    );
}
