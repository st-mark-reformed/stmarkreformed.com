import { MouseEvent, ReactElement } from 'react';

export type PrimaryOrSecondaryLink = {
    content: string | ReactElement;
    href?: string;
    onClick?: (e: MouseEvent<HTMLButtonElement>) => void;
};
