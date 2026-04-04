import React from 'react';
import { Metadata } from 'next';
import Image from 'next/image';
import { createPageTitle } from '../createPageTitle';
import Layout from '../layout/Layout';

const pageTitle = 'Operation Roots Down';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle(pageTitle),
};

export default function OperationRootsDown () {
    return (
        <Layout
            hero={{
                heroHeading: pageTitle,
                heroParagraph: 'like a tree planted by rivers of water',
                heroImage1x: '/images/operation-roots-down/tree-roots.jpg',
                heroUpperCta: {
                    linkText: 'Scroll to: Ways to give',
                    linkData: '#give',
                },
            }}
        >
            <div className="mx-auto max-w-3xl py-8 px-4">
                <Image
                    src="/images/operation-roots-down/operation-roots-down-header.jpg"
                    alt="Operation Roots Down banner"
                    width={1280}
                    height={720}
                />
                <div className="mt-3 text-lg text-gray-600 prose max-w-none">
                    <p className="text-bronze-lightened-2 pb-2 pt-6 text-xl font-semibold">
                        In 2006, a group of Nashville-area families met together to establish a congregation in the CREC.
                    </p>

                    <p>That congregation was particularized a year later, and God has been uncommonly gracious and kind over the years, pouring out blessing upon blessing as we have sought to faithfully &ldquo;serve Christ and the world through liturgy, mission, and community.&rdquo;</p>

                    <p>While the Lord has faithfully provided many different locations for us to meet, He has not yet to blessed us with a place uniquely &ldquo;our own&rdquo; &mdash; a place where we might put down roots and establish a presence of worship and fellowship which we pray will grow and flourish for generations.</p>

                    <p>A Building Fund was established a number of years ago which the Lord has blessed with steady growth. However, its value is still too small to make a &rdquo;respectable offer&ldquo; for acquiring a location. As opportunities arise, we now have an added sense of urgency to secure a location within the coming months as <strong>our current meeting location has been sold</strong>.</p>

                    <p className="text-bronze-lightened-2 pb-2 pt-2 text-xl font-semibold">
                        With these things in mind, the Session of SMRC is launching &ldquo;Operation Roots Down,&rdquo; a focused fundraising effort with the goal of <strong>raising $3 million</strong> within the next few months.
                    </p>

                    <p>As a friend of this congregation, we appeal to you in Christ’s Name that, whether your means be small or great, you <strong>prayerfully consider an offering</strong> which would further us on toward this goal. Additionally, if you know of others within your own circle of acquaintances with the means and the inclination to add to these efforts, we humbly ask you to <strong>spread the word</strong> to them and speak a good word to them on our behalf.</p>

                    <p>In the future we may be able to take payments online (though the cost of processing online payments is something to consider), but for now here are the ways you can give:</p>

                    <ul id="give">
                        <li><strong>Place a check in the offering plate</strong> with &ldquo;Building Fund&rdquo; in the memo line.</li>
                        <li><strong>Give a check directly to a deacon</strong>, TJ Draper or Randy Sadler (again, with &ldquo;Building Fund&rdquo; in the memo line).</li>
                        <li>
                            <p className="mb-4"><strong>Mail a check</strong> to TJ Draper, Deacon and Treasurer (the P.O. box gets checked roughly twice a month and you could mail the check there, but it might sit there a while). Make the check payable to Saint Mark Reformed Church.</p>
                            <p>
                                TJ Draper<br />
                                1530 Halifax Dr.<br />
                                Spring Hill, TN 37174
                            </p>
                        </li>
                        <li>
                            To wire transfer funds, donate stock, or any other ideas for donations, please contact TJ Draper by email (<a href="mailto:tjdraper@stmarkreformed.com">tjdraper@stmarkreformed.com</a>) or phone (<a href="tel:+1-615-351-6284">615-351-6284</a>), or talk to him in-person on Sunday morning.
                        </li>
                    </ul>

                    <p>Thank you in advance for your prayers and generosity on behalf of the Lord’s work in the Greater Nashville area.</p>

                    <p>In Christ&lsquo;s Name,<br />
                        The Session of Saint Mark Reformed Church
                    </p>
                </div>
            </div>
        </Layout>
    );
}
