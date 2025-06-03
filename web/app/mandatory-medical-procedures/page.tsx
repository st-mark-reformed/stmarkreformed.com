import React from 'react';
import { Metadata } from 'next';
import Link from 'next/link';
import { createPageTitle } from '../createPageTitle';
import BasicPageLayout from '../layout/BasicPageLayout';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle('Statement on Mandatory Medical Procedures'),
};

export default function Page () {
    return (
        <BasicPageLayout hero={{ heroHeading: 'Statement on Mandatory Medical Procedures' }}>
            <p>Whereas Holy Scripture, along with the confessional documents of St. Mark Reformed Church, affirm the
                preservation of life<sup>1</sup>; promote the principle of a godly self love<sup>2</sup>; and the
                freedom for an individual’s conscience not to be bound<sup>3</sup>;
            </p>
            <p>Be it resolved that the officers of St. Mark Reformed Church support the refusal of mandatory medical
                procedures – even if ordered by any branch of the civil government, an employer, or any other
                institution to which an individual is subject or dependent – in the event that an individual believes
                his or her life, health, and/or wellbeing is potentially threatened by such procedures, or in the event
                that a parent has the same concern for his or her&nbsp;child.
            </p>
            <hr />
            <p>
                <sup>1</sup> Exodus 20:13; Deuteronomy 5:17; <em>Westminster Larger Catechism Q&amp;A 136</em>.<br />
                <sup>2</sup> Ephesians 5:28-29; Matthew 22:36-40.<br />
                <sup>3</sup> Romans 14:8, 10, 12, 14, 23; <em>Westminster Confession of Faith</em> XX.II.<br />
                <br />
            </p>
            <p className="text-right text-xs text-gray-500">Revised 06/27/21</p>
            <div className="mt-8 text-left">
                <div>
                    <div className="inline-flex rounded-md shadow">
                        <Link
                            href="/files/Statement-on-Mandatory-Medical-Procedures-Adopted-2021-06-27.pdf"
                            className="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-gray-100 bg-crimson hover:bg-crimson-dark not-prose"
                        >
                            Download PDF of Statement
                        </Link>
                    </div>
                </div>
            </div>
        </BasicPageLayout>
    );
}
