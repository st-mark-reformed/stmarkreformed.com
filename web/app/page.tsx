import React from 'react';
import Link from 'next/link';
import Image from 'next/image';
import Layout from './layout/Layout';
import ImageContentCta, {
    ImageContentCtaBackgroundColor,
    ImageContentDisposition,
} from './PageComponents/ImageContentCta';
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
            {/* <div className="bg-crimson-lightened-4 text-center border-b-4 border-t-4 border-crimson-dark">
                <div className="mx-auto max-w-5xl px-6 py-20">
                    <div className="shrink-0 inline-block mx-auto">
                        <InformationCircleIcon aria-hidden="true" className="size-8 text-crimson-dark" />
                    </div>
                    <h2 className="font-semibold tracking-tight text-balance text-crimson-dark text-4xl leading-snug">
                        Services canceled for 1/25/2026 due to inclement weather and dangerous road conditions
                    </h2>
                </div>
            </div> */}
            {/* TODO: componentize this */}
            <div className="relative bg-gray-50 pt-16 pb-20 px-4 sm:px-6 lg:pt-24 lg:pb-28 lg:px-8 border-t-8 border-goldenrod">
                <div className="absolute inset-0">
                    <div className="bg-bronze h-full relative">
                        <Image
                            src="/images/operation-roots-down/tree-roots.jpg"
                            alt="Operation Roots Down"
                            fill
                            className="object-cover opacity-40"
                        />
                    </div>
                </div>
                <div className="relative max-w-3xl mx-auto">
                    <div className="text-center">
                        <h2 className="text-3xl tracking-tight font-extrabold sm:text-4xl block bg-clip-text text-transparent bg-gradient-to-r from-teal-500 to-teal-300 pb-1">Operation Roots Down</h2>
                        <p className="mt-3 max-w-2xl mx-auto text-xl text-gray-100 sm:mt-2">like a tree planted by rivers of water</p>
                    </div>
                    <div className="prose max-w-none mt-8 text-gray-100">
                        <p>The Lord has faithfully provided many different locations for us to meet, but He has not yet to blessed us with a place uniquely &ldquo;our own&rdquo; &mdash; a place where we might put down roots.</p>
                        <p>The Session of SMRC is launching &ldquo;Operation Roots Down,&rdquo; a focused fundraising effort with the goal of <strong className="text-gray-100">raising $3 million</strong> within the next few months.</p>
                    </div>
                    <div className="text-center mt-16">
                        <Link
                            href="/operation-roots-down"
                            className="shadow-lg inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-medium rounded-md text-white bg-goldenrod hover:bg-saddle-brown-lightened-2"
                        >
                            Learn More
                        </Link>
                    </div>
                </div>
            </div>
            {/* End TODO */}
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
