import Link from 'next/link';
import React from 'react';
import { SecondaryMenu } from './MainMenu';

export default function MenuSecondaryMobile () {
    return (
        <>
            <div className="mt-6 px-5">
                <p className="text-center text-base font-medium text-gray-500">
                    {SecondaryMenu.map((menuItem) => {
                        const classes = ['inline-block mt-4 text-gray-50 py-2 px-4 rounded-md'];

                        if (menuItem.isEmphasized) {
                            classes.push('bg-crimson hover:bg-crimson-dark font-bold');
                        } else {
                            classes.push('bg-bronze-lightened-2 hover:bg-bronze');
                        }

                        return (
                            <div key={menuItem.link}>
                                <Link
                                    href={menuItem.link}
                                    className={classes.join(' ')}
                                >
                                    {menuItem.name}
                                </Link>
                            </div>
                        );
                    })}
                </p>
            </div>
        </>
    );
}
