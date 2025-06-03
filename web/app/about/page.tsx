import React from 'react';
import { Metadata } from 'next';
import Layout from '../layout/Layout';
import { createPageTitle } from '../createPageTitle';
import SidebarInnerLayout from '../layout/SidebarInnerLayout';
import NavItems from './NavItems';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle('About'),
};

export default function AboutPage () {
    return (
        <Layout hero={{ heroHeading: 'About St. Mark' }}>
            <SidebarInnerLayout navSections={NavItems('/about')}>
                <p>We at St Mark are committed to following Jesus as a church community in greater Nashville. Some of us live inside the city, while others travel from various communities in Middle Tennessee. All of us our bound together by God’s&nbsp;love.</p>
                <p>Such ties do not happen purely by accident. Along with nurturing wholly informal love and friendship, we also seek to grow as a community by engaging in numerous ministries. We worship together, eat together, pray together, and play&nbsp;together.</p>
                <p>St Mark’s liturgy, preaching and teaching are founded upon the good news of Jesus Christ. It is informed by the Reformed tradition, but also reflects appreciation for the gifts of the Spirit given to the whole Christian Church and not just one stream. Our worship is patterned after the historic Christian liturgy, and our teaching is grounded squarely upon the belief that the Bible is God’s own&nbsp;Word.</p>
            </SidebarInnerLayout>
        </Layout>
    );
}
