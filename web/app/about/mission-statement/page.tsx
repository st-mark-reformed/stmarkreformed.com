import React from 'react';
import { Metadata } from 'next';
import Layout from '../../layout/Layout';
import { createPageTitle } from '../../createPageTitle';
import SidebarInnerLayout from '../../layout/SidebarInnerLayout';
import NavItems from '../NavItems';
import SectionWithH2Heading from '../../typography/SectionWithH2Heading';
import InlineButtonLinks from '../../typography/InlineButtonLinks';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle([
        'Mission Statement',
        'About',
    ]),
};

export default function MissionStatementPage () {
    return (
        <Layout hero={{ heroHeading: 'Mission Statement' }}>
            <SidebarInnerLayout navSections={NavItems('/about/mission-statement')}>
                <SectionWithH2Heading heading="St. Mark is committed to:">
                    <ul>
                        <li>Worship that is robust, reverent, and joyful through the preaching of the Word, right
                            celebration of the sacraments and a high view of congregational singing of Psalms and the
                            historical&nbsp;hymns
                        </li>
                        <li>The verbal, plenary inspiration, inerrancy and infallibility of the Holy&nbsp;Scriptures</li>
                        <li>The “solas” of the Reformation: Sola Scriptura, Sola Gratia, Sola Fide, Solo Christo, and Soli
                            Deo&nbsp;Gloria
                        </li>
                        <li>The Westminster Standards and church&nbsp;creeds</li>
                        <li>Commitment to true biblical shepherding through the balance of exhortation and right exercise of
                            church&nbsp;discipline
                        </li>
                    </ul>
                    <p>We encourage families to worship together on the Lord’s Day. As members of the covenant people, small
                        children and newborns are most welcome in our&nbsp;services.
                    </p>
                    <p><strong>The Lord’s Supper </strong>is observed every Lord’s Day as the climax of covenant renewal
                        worship. Our congregation practices covenant communion, inviting all those who are baptized
                        disciples of Jesus Christ, under the authority of Christ and his body, the Church, without
                        distinction due to age or mental ability, to His Table. By eating the bread and wine with us as a
                        visitor, you are acknowledging to the elders of this local church that you are in covenant with God
                        as an active member of a congregation in which the Gospel is faithfully confessed, taught, and
                        believed. You also acknowledge that you are a sinner, without hope except in the sovereign mercy of
                        God, and that you are trusting in Jesus Christ alone for salvation. If you have any doubt about your
                        participation, please speak to one of the Elders or the Pastor before or after the&nbsp;service.
                    </p>
                    <p>Perhaps one of the best ways to learn about us is to see our liturgy. Reverent and vibrant worship
                        are an important part of who we are and seeing the order of our service will hopefully
                        be&nbsp;helpful.
                    </p>
                    <InlineButtonLinks buttons={[
                        {
                            content: 'Sample Liturgy',
                            href: '/uploads/general/sample-liturgy.pdf',
                        },
                    ]}
                    />
                    <p>If you are interested in learning more about this work, <a className="font-bold" href="mailto:pastor@stmarkreformed.com">email our pastor</a> or call <a className="font-bold" href="tel:+16154383109">(615)&nbsp;438-3109</a>.</p>
                </SectionWithH2Heading>
            </SidebarInnerLayout>
        </Layout>
    );
}
