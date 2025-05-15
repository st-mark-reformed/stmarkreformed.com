import React from 'react';

export default function Alert ({
    headline = '',
    content = '',
    contentList = [],
    type = 'info',
}: {
    headline?: string;
    content?: string;
    contentList?: string[];
    type?: 'info' | 'success' | 'caution' | 'error';
}) {
    let wrapperBackgroundClass;
    let headlineClass;
    let contentClass;

    // Switch case for different alert types
    switch (type) {
        case 'success':
            wrapperBackgroundClass = 'bg-green-50';
            headlineClass = 'text-green-800';
            contentClass = 'text-green-700';
            break;
        case 'caution':
            wrapperBackgroundClass = 'bg-yellow-50';
            headlineClass = 'text-yellow-800';
            contentClass = 'text-yellow-700';
            break;
        case 'error':
            wrapperBackgroundClass = 'bg-lightest-red';
            headlineClass = 'text-dark-red';
            contentClass = 'text-red';
            break;
        default:
            wrapperBackgroundClass = 'bg-blue-50';
            headlineClass = 'text-blue-800';
            contentClass = 'text-blue-700';
            break;
    }

    // Define the icon for each alert type
    const renderIcon = () => {
        switch (type) {
            case 'success':
                return <span className="h-5 w-5 text-green-400">✔️</span>;
            case 'caution':
                return <span className="h-5 w-5 text-yellow-400">⚠️</span>;
            case 'error':
                return <span className="h-5 w-5 text-red">❌</span>;
            default:
                return <span className="h-5 w-5 text-blue-400">ℹ️</span>;
        }
    };

    return (
        <div className={`${wrapperBackgroundClass} p-4 shadow sm:rounded-lg`}>
            <div className="flex">
                <div className="flex-shrink-0">{renderIcon()}</div>
                <div className="ml-3">
                    {(() => {
                        if (!headline) {
                            return null;
                        }

                        return <h3 className={`text-sm font-medium ${headlineClass}`}>{headline}</h3>;
                    })()}
                    {(() => {
                        if (!(content || contentList.length > 0)) {
                            return null;
                        }

                        return (
                            <div className={`${headline ? 'mt-2 ' : ''}text-sm ${contentClass}`}>
                                {(() => {
                                    if (!content) {
                                        return null;
                                    }

                                    return <p>{content}</p>;
                                })()}
                                {(() => {
                                    if (contentList.length === 0) {
                                        return null;
                                    }

                                    return (
                                        <ul className={`${content ? 'mt-2 ' : ''}list-disc list-inside`}>
                                            {contentList.map((item, index) => (
                                                <li key={item}>{item}</li>
                                            ))}
                                        </ul>
                                    );
                                })()}
                            </div>
                        );
                    })()}
                </div>
            </div>
        </div>
    );
}
