import React from 'react';
import { Metadata } from 'next';
import Layout from '../../layout/Layout';
import { createPageTitle } from '../../createPageTitle';
import SidebarInnerLayout from '../../layout/SidebarInnerLayout';
import NavItems from '../NavItems';
import SectionWithH2Heading from '../../typography/SectionWithH2Heading';
import Links from './Links';

export const metadata: Metadata = {
    title: createPageTitle([
        'Connections and Associations',
        'About',
    ]),
};

export default function ConnectionsPage () {
    return (
        <Layout hero={{ heroHeading: 'Connections and Associations' }}>
            <SidebarInnerLayout nav={NavItems('/about/connections')}>
                <SectionWithH2Heading heading="Ministries and Affiliations">
                    <p>The following is a list of links to ministries we know and&nbsp;love:</p>
                    <Links links={[
                        {
                            content: 'Communion of Reformed Evangelical Churches (CREC)',
                            href: 'https://crechurches.org/',
                        },
                        {
                            content: 'Christ Church, Moscow, ID',
                            href: 'https://www.christkirk.com/',
                        },
                        {
                            content: 'Fight, Laugh, Feast',
                            href: 'https://flfnetwork.com/',
                        },
                        {
                            content: 'Presbyterian Church in America (PCA)',
                            href: 'https://pcanet.org/',
                        },
                        {
                            content: 'Blog and Mablog by Doug Wilson',
                            href: 'https://dougwils.com/',
                        },
                        {
                            content: 'Theopolis, President Peter J. Leithart',
                            href: 'https://theopolisinstitute.com/',
                        },
                        {
                            content: 'Athanasius Press',
                            href: 'https://athanasiuspress.org/',
                        },
                        {
                            content: 'Peru Mission',
                            href: 'https://www.perumission.org/',
                        },
                        {
                            content: 'JEEP (Joint Eastern European Project)',
                            href: 'https://www.jeeproject.net/',
                        },
                        {
                            content: 'Canon Press, Moscow, ID',
                            href: 'https://canonpress.com/',
                        },
                        {
                            content: 'Uri Brito, Presiding Minister, Athanasius Presbytery, CREC',
                            href: 'https://uribrito.com/',
                        },
                        {
                            content: 'Kuyperian Commentary',
                            href: 'https://kuyperian.com/',
                        },
                        {
                            content: 'James B. Jordan, Biblical Horizons',
                            href: 'https://biblicalhorizons.com/about/about-james-jordan/',
                        },
                        {
                            content: 'Paedocommunion.com',
                            href: 'https://paedocommunion.com/',
                        },
                        {
                            content: 'Trinitas Classical Academy',
                            href: 'https://tcafranklin.org/',
                        },
                        {
                            content: 'Hope Russia',
                            href: 'https://hoperussia.org/',
                        },
                        {
                            content: 'Huguenot Heritage',
                            href: 'https://huguenotheritage.com/',
                        },
                        {
                            content: 'Pregnancy Centers of Middle Tennessee',
                            href: 'https://www.pcofmt.com/',
                        },
                        {
                            content: 'Pilgrim Hill Reformed Fellowship',
                            href: 'https://www.pilgrimhill.church/',
                        },
                    ]}
                    />
                </SectionWithH2Heading>
            </SidebarInnerLayout>
        </Layout>
    );
}
