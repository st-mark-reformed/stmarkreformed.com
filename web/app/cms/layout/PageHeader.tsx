import React from 'react';
import { PageHeaderButton, PageHeaderButtonConfig } from './PageHeaderButton';

export default function PageHeader (
    {
        title,
        subTitle,
        buttons = [],
    }: {
        title: string;
        subTitle?: string;
        buttons?: Array<PageHeaderButtonConfig>;
    },
) {
    return (
        <div className="bg-amber-950 px-4 py-5 sm:px-6 rounded-xl">
            <div className="md:flex md:items-center md:justify-between">
                <div className="min-w-0 flex-1">
                    <h2 className="text-2xl font-bold leading-7 text-white sm:truncate sm:text-3xl sm:tracking-tight pb-1.5">
                        {title}
                    </h2>
                    {(() => {
                        if (!subTitle) {
                            return null;
                        }

                        return (
                            <h3 className="leading-7 text-white sm:truncate sm:tracking-tight">
                                {subTitle}
                            </h3>
                        );
                    })()}
                </div>
                {(() => {
                    if (buttons.length < 1) {
                        return null;
                    }

                    return (
                        <div className="mt-4 flex md:ml-4 md:mt-0">
                            {buttons.map((buttonConfig, index) => (
                                <PageHeaderButton
                                    key={buttonConfig.id}
                                    buttonConfig={buttonConfig}
                                    useMarginLeft={index !== 0}
                                />
                            ))}
                        </div>
                    );
                })()}
            </div>
        </div>
    );
}
