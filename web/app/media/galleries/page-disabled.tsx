// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React from 'react';
import { Metadata } from 'next';
import GalleryIndexPage from './GalleryIndexPage';
import { createPageTitle } from '../../createPageTitle';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle('Photo Galleries'),
};

export default function Page () {
    return <GalleryIndexPage pageNum={1} />;
}
