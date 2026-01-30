import React from 'react';
import { Metadata } from 'next';
import Layout from '../../layout/Layout';
import { createPageTitle } from '../../createPageTitle';
import SidebarInnerLayout from '../../layout/SidebarInnerLayout';
import NavItems from '../NavItems';
import Profile from './Profile';
import ProfileSection from './ProfileSection';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle([
        'Leadership',
        'About',
    ]),
};

export default function LeadershipPage () {
    return (
        <Layout hero={{ heroHeading: 'Church Officers' }}>
            <SidebarInnerLayout navSections={NavItems('/about/leadership')}>
                <div className="mx-auto max-w-3xl">
                    <ProfileSection headline="Pastor">
                        <Profile
                            image={{
                                href1x: '/images/about/leadership/joe-thacker-1x.jpg',
                                href2x: '/images/about/leadership/joe-thacker-2x.jpg',
                            }}
                            name="Rev. Joe Thacker"
                        >
                            <p>Joe was born and raised in Maryland. He graduated with honors from Covenant College with a B.A. in Biblical Studies, and received his Master of Divinity Degree from Reformed Theological Seminary in Jackson, MS. He has been the pastor of St. Mark Reformed Church since October 2007, and was a Humanities teacher for High School and Middle School at Trinitas Classical Academy of Franklin from 2014 to 2023. Joe and his wife, Deborah, have four children. In addition to the activities in which his children are involved, Joe enjoys fitness, literature, movies, and&nbsp;sports.</p>
                        </Profile>
                    </ProfileSection>
                    <ProfileSection headline="Associate Pastor">
                        <Profile
                            image={{
                                href1x: '/images/about/leadership/burke-shade-1x.jpg',
                                href2x: '/images/about/leadership/burke-shade-2x.jpg',
                            }}
                            name="Rev. Burke Shade"
                        >
                            <p>Burke was raised in Dallas, Texas, before graduating from the US Naval Academy in 1979 and spending five years as a Supply Officer in the USMC. After five years of being a Commercial Real Estate agent/broker in Tyler, Texas, he attended and graduated from Westminster Theological Seminary in California in 1992. From there he spent almost six years as a pastor of Evangelical Presbyterian Church (PCA) in Carbondale, IL, and then another nineteen years as Pastor of Cornerstone Reformed Church in Carbondale (CREC). He and his wife Ruth have been married forty-four years, have eight children and twenty-two grandchildren, and both have never known a day when they didn’t know and profess Jesus as Lord and&nbsp;Savior.</p>
                        </Profile>
                    </ProfileSection>
                    <ProfileSection headline="Ruling Elders">
                        <Profile
                            image={{
                                href1x: '/images/about/leadership/abe-goolsby-1x.jpg',
                                href2x: '/images/about/leadership/abe-goolsby-2x.jpg',
                            }}
                            name="Abe Goolsby"
                        >
                            <p>Abe Goolsby has deep roots in Middle Tennessee, as does his wife, Shannon. Theirs was one of the founding families of the congregation. Abe has enjoyed a 25+ year career as an award-winning graphic designer and illustrator, and is currently employed full-time as Director of Digital Content for an independent cable television network. When not otherwise occupied, he takes delight in English and Latin literature, history, theology, music, the visual arts, gardening, and the outdoors. He and his wife Shannon have six children. Abe accepted the call to serve the congregation as a ruling elder in August,&nbsp;2020.</p>
                        </Profile>
                        <Profile
                            image={{
                                href1x: '/images/about/leadership/jeff-kernodle-1x.jpg',
                                href2x: '/images/about/leadership/jeff-kernodle-2x.jpg',
                            }}
                            name="Jeff Kernodle"
                        />
                    </ProfileSection>
                    <ProfileSection headline="Deacons" isLast>
                        <Profile
                            image={{
                                href1x: '/images/about/leadership/tj-draper-1x.jpg',
                                href2x: '/images/about/leadership/tj-draper-2x.jpg',
                            }}
                            name="TJ Draper"
                        >
                            <p>TJ is a family man and a software engineer. He is thankful to be father to four children and has been married to their mother, the love of his life, since&nbsp;2005.</p>
                            <p>TJ grew up in a Christian home where he learned the importance of God’s word and of his need for a savior and as an adult has discovered a love and passion for Christ’s Church, which was the impetus for accepting the nomination and call to be a deacon at St.&nbsp;Mark.</p>
                        </Profile>
                        <Profile
                            image={{
                                href1x: '/images/about/leadership/randy-sadler.jpg',
                                href2x: '/images/about/leadership/randy-sadler.jpg',
                            }}
                            name="Randy Sadler"
                        >
                            <p>Randy and Susan are grateful to have been raised in Christian homes. They are native Nashvillians, high school sweethearts, and have been married for 42 years. They have two married children, two wonderful in-law children, and ten beautiful grandchildren—all baptized and walking with the Lord. Thanks be to God!</p>
                            <p>Randy has a technical degree from NADC and has been in the transportation industry for over 30 years. He currently serves as Fixed Operations Manager at Carpenter Bus Sales. Susan is a loving wife, mother, grandmother, and homemaker.</p>
                            <p>Randy and Susan homeschooled their children and have been active in classical Christian education for over 29 years. Randy served on the Board of Trustees at Artios Academy and The Classical Academy of Franklin.</p>
                            <p>Prior to coming to SMRC, Randy served as Deacon in the PCA, on Budget and Finance, and Benevolence.</p>
                            <p>When Randy isn’t working at Carpenter Bus, he loves spending time with his family, fixing things around the house, smoking meat for Sabbath Eve, reading or listening to a book on the Canon App, and riding his grandchildren around on the four wheeler.</p>
                            <p>Randy is grateful to worship and grow in knowledge, understanding, and wisdom at SMRC and looks forward to the opportunities to serve the Lord as he serves the saints&nbsp;here.</p>
                        </Profile>
                    </ProfileSection>
                </div>
            </SidebarInnerLayout>
        </Layout>
    );
}
