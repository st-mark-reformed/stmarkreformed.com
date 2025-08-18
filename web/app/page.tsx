import React from 'react';
import Link from 'next/link';
import Layout from './layout/Layout';
import ImageContentCta from './PageComponents/ImageContentCta';
import UpcomingEvents from './PageComponents/UpcomingEvents/UpcomingEvents';
import LatestGalleries from './PageComponents/LatestGalleries';
import LatestSermon from './PageComponents/LatestSermon';
import LatestNews from './PageComponents/LatestNews';

export const dynamic = 'force-dynamic';

export default async function Page () {
    return (
        <Layout
            hero={
                {
                    heroUpperCta: {
                        linkText: 'Learn more about us',
                        linkData: '/about',
                    },
                    heroHeading: 'St. Mark',
                    heroSubheading: 'Reformed Church',
                    heroParagraph: 'Committed to robust, liturgical, covenant renewal worship, celebrating the sacraments each week, psalm singing, and the solas of the Reformation.',
                }
            }
        >
            <ImageContentCta
                image="/images/home/bfp-map-image.png"
                preHeading="Join us for Covenant Renewal Worship"
                heading="Sundays at 11:00 am"
                content={(
                    <>
                        <p>
                            Brentwood First Presbyterian&nbsp;Church<br />
                            1301&nbsp;Franklin&nbsp;Rd.<br />
                            Brentwood,&nbsp;TN&nbsp;37027<br />
                        </p>
                        <p>We also normally have Sunday School at 10:00 AM. See our <Link href="/calendar">calendar</Link> for an up-to-date&nbsp;schedule.</p>
                        <p>You can also call for more info at (615)&nbsp;438-3109</p>
                        <p>Please note if you need to send something to us, our mailing address is different from our meeting address. For mailing purposes only, please use the&nbsp;following:</p>
                        <p>
                            General Correspondence and financial donations may be sent&nbsp;to:<br />
                            PO Box&nbsp;1543<br />
                            Franklin, TN&nbsp;37065
                        </p>
                    </>
                )}
                cta={{
                    linkText: 'Get Directions on Google Maps',
                    linkData: 'https://maps.app.goo.gl/y44VamLxKE1EgLyn6',
                    newWindow: true,
                }}
            />
            <UpcomingEvents />
            <LatestGalleries
                heading="A picture is worth a thousand words"
                subHeading="Take a look at the life of St. Mark through a few of our smiling faces and latest events"
            />
            <LatestSermon />
            <LatestNews />
        </Layout>
    );
}
