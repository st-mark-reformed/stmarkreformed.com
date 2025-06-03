import React from 'react';
import { Metadata } from 'next';
import Layout from '../../layout/Layout';
import { createPageTitle } from '../../createPageTitle';
import SidebarInnerLayout from '../../layout/SidebarInnerLayout';
import NavItems from '../NavItems';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle([
        'Membership',
        'About',
    ]),
};

export default function MembershipPage () {
    return (
        <Layout hero={{ heroHeading: 'Membership' }}>
            <SidebarInnerLayout navSections={NavItems('/about/membership')}>
                <p>Membership in our local expression of Christ’s Church is constituted by a profession of faith, sealed in baptism. Baptism is the rite of initiation into the covenant of grace and the catholic Church; the one baptized maintains good standing in the Church by walking in faith, by the grace of&nbsp;God.</p>
                <p>Membership vows&nbsp;include:</p>
                <ol>
                    <li>Do you acknowledge yourself to be a sinner in the sight of God, justly deserving His wrath, and without hope apart from His sovereign&nbsp;mercy?</li>
                    <li>Do you believe in the Lord Jesus Christ as the Son of God, and Savior of sinners, and do you trust in Him alone for salvation as He is offered in the Gospel, as priest, king, and&nbsp;prophet?</li>
                    <li>Do you now promise, in humble reliance upon the grace of the Holy Spirit, that you will strive to live a life of repentance and obedience, in a manner worthy of the followers of&nbsp;Christ?</li>
                    <li>Do you promise to support the Church in its worship and work to the best of your&nbsp;ability?</li>
                    <li>Do you submit yourself to the government and discipline of the Church, and promise to pursue its purity and&nbsp;peace?</li>
                </ol>
            </SidebarInnerLayout>
        </Layout>
    );
}
