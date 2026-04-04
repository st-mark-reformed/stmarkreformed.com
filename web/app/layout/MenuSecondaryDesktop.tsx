import Link from 'next/link';
import React from 'react';
import { SecondaryMenu } from './MainMenu';

export default function MenuSecondaryDesktop () {
    return (
        <>
            {SecondaryMenu.map((menuItem) => {
                const classes = ['text-base text-white rounded-md'];

                if (menuItem.isEmphasized) {
                    classes.push('bg-crimson hover:bg-crimson-dark font-bold px-3');
                } else {
                    classes.push('bg-transparent hover:text-goldenrod font-normal');
                }

                return (
                    <Link
                        key={menuItem.link}
                        href={menuItem.link}
                        className={classes.join(' ')}
                    >
                        {menuItem.name}
                    </Link>
                );
            })}
        </>
    );
}
