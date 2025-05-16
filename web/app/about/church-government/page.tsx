import React from 'react';
import { Metadata } from 'next';
import Layout from '../../layout/Layout';
import { createPageTitle } from '../../createPageTitle';
import SidebarInnerLayout from '../../layout/SidebarInnerLayout';
import NavItems from '../NavItems';
import SectionWithH2Heading from '../../typography/SectionWithH2Heading';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle([
        'Church Government',
        'About',
    ]),
};

export default function ChurchGovernmentPage () {
    return (
        <Layout hero={{ heroHeading: 'Church Government' }}>
            <SidebarInnerLayout nav={NavItems('/about/church-government')}>
                <SectionWithH2Heading heading="Pastor">
                    <p>The pastor (Minister of the Word) is an ordinary and perpetual officer in the Church (Eph. 4: 11; 1 Tim. 3:1-7; Tit. 1:5- 9). The pastorate is especially the new covenant counterpart to the old covenant priesthood, even as each local congregation is a miniature fulfillment of the typology of the old covenant temple. The pastor is the primary servant-priest among and towards the royal priesthood of the whole congregation, with the goal of presenting the people in Christ as an acceptable offering to the Father, sanctified by the Holy Spirit (Rom. 15:16). It especially belongs to the pastoral&nbsp;office:</p>
                    <ul>
                        <li>To pray for and with his flock, as the mouth of the people unto God; to pray publicly for the people, especially in gathered worship; to pray privately for and with the people, especially for the sick; and to pray for the&nbsp;lost</li>
                        <li>To oversee the planning and leading of the Lord’s Day liturgy, as the priests of the Old Covenant led the people in worship at the tabernacle and&nbsp;temple</li>
                        <li>To read, preach, and teach the Scriptures publicly, as the mouth of God to people, even as the priests in the Jewish Church were trusted with the public reading and exposition of the&nbsp;Word</li>
                        <li>To study the Scriptures diligently, in order to feed the flock divine truth, as he preaches, teaches, convinces, reproves, exhorts, and comforts from the&nbsp;Word</li>
                        <li>To train the people to live as a royal priesthood, offering Spiritual sacrifices in all of life, and especially in gathered&nbsp;worship</li>
                        <li>To administer the Sacraments publicly and privately in emergency situations, as the priests under the Law administered the&nbsp;sacrifices</li>
                        <li>To declare absolution to the Lord’s repentant people, both publicly in gathered worship, and privately, after they have confessed their&nbsp;sins</li>
                        <li>To bless the people from God, declaring a benediction, as the priests did under the Old&nbsp;Covenant</li>
                        <li>To encourage husbands and fathers to be faithful in loving their wives as Christ loves the Church and in raising their children in the fear and admonition of the Lord; to encourage wives and mothers to be diligent and faithful by caring for their families with joy and contentment; to encourage singles to pursue purity and service in accord with their vocations; and to encourage children to grow towards maturity in the grace and knowledge of the Lord Jesus&nbsp;Christ</li>
                        <li>To take care of the poor, in conjunction with the other&nbsp;officers</li>
                        <li>To represent the Chief Shepherd, Jesus Christ, in lovingly caring for and disciplining the flock in conjunction with the other&nbsp;elders</li>
                        <li>To lead the session as moderator and overseer; To represent the local congregation as a permanent delegate to all higher assemblies of the&nbsp;Church</li>
                    </ul>
                </SectionWithH2Heading>
                <SectionWithH2Heading heading="Ruling Elder">
                    <p>Ruling elders differ from other elders (the Ministers of Word and Sacrament) in that they are not subject to examinations from presbytery with regard to their call to office; they typically have daily vocations outside the Church; and they usually do not receive remuneration from the Church for their services. However, on the session, they rule jointly with the other elders and have the same formal authority. They may serve as representatives of the Church in presbytery and council meetings. It especially belongs to the office of ruling&nbsp;elder:</p>
                    <ul>
                        <li>To serve on the session, and thus rule the&nbsp;people</li>
                        <li>To advise Ministers of Word and Sacraments in their special work and represent the congregation on the&nbsp;session</li>
                        <li>To oversee the doctrine and practice of the&nbsp;flock</li>
                        <li>To set an example of godliness in all&nbsp;things</li>
                        <li>To act as peacekeepers and judges in cases of&nbsp;dispute</li>
                        <li>To pray with and for the people, especially in time of illness; and to anoint the sick with oil when requested, along with the Ministers of Word and&nbsp;Sacrament</li>
                        <li>To counsel and nurture the members of the congregation towards godliness, encouraging and correcting them as&nbsp;needed</li>
                        <li>To assist the pastor in leading the liturgy when needed or&nbsp;appropriate</li>
                        <li>To assist in the distribution of the Lord’s Supper and the collection of tithes and&nbsp;offerings;</li>
                        <li>To execute Church discipline when and as situations require&nbsp;it</li>
                        <li>To join with the deacons in caring for the poor and&nbsp;needy</li>
                    </ul>
                    <p>Ruling elders labor beside Ministers of the Word and Sacraments (pastors, teachers, and evangelists) in lovingly shepherding and disciplining the&nbsp;people.</p>
                </SectionWithH2Heading>
                <SectionWithH2Heading heading="Deacon">
                    <p>The Scripture holds out deacons as distinct officers in the Church. Deacons are called to be assistants to the Ministers of Word and Sacrament (Acts 6:1-6) just as the Levites were assistants to the priests; and also to act as assistants to the ruling elders, operating under their oversight and authority. Deacons can be gifted and used in a wide variety of ways in the life of the Church. Deacons serve in ways authorized by the elders, freeing the elders to focus on their more specialized tasks. Primarily, it belongs to the office of&nbsp;deacon:</p>
                    <ul>
                        <li>To take special care in mercy ministries and in meeting the needs of the poor, the immigrant, the prisoner, the fatherless, and the widow, first within the household of God, and second, in the&nbsp;world</li>
                        <li>To befriend the friendless, and care for those in distress, in times of illness, bereavement, or other adversity, after the example of the Lord Jesus&nbsp;Christ</li>
                        <li>To disburse funds from the Church treasury on behalf of the&nbsp;session</li>
                        <li>To encourage the rest of the Church’s membership to excel in hospitality and benevolence, ministering to one another and to those outside the Church in deed as well as&nbsp;word</li>
                        <li>To assist in the distribution of the Lord’s Supper and the collection of tithes and&nbsp;offerings</li>
                        <li>To assist in the Church’s liturgical feasts and fellowship&nbsp;meals</li>
                        <li>To make recommendations to the session about budget and property, as stewards of the Church’s resources and assistants to the&nbsp;elders</li>
                        <li>To care for and maintain the property of the&nbsp;Church</li>
                    </ul>
                </SectionWithH2Heading>
            </SidebarInnerLayout>
        </Layout>
    );
}
