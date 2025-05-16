import React from 'react';
import { Metadata } from 'next';
import Layout from '../../layout/Layout';
import { createPageTitle } from '../../createPageTitle';
import SidebarInnerLayout from '../../layout/SidebarInnerLayout';
import NavItems from '../NavItems';
import SectionWithH2Heading from '../../typography/SectionWithH2Heading';

export const metadata: Metadata = {
    title: createPageTitle([
        'Liturgy and Sacraments',
        'About',
    ]),
};

export default function LiturgyAndSacramentsPage () {
    return (
        <Layout hero={{ heroHeading: 'Liturgy and Sacraments' }}>
            <SidebarInnerLayout nav={NavItems('/about/liturgy-and-sacraments')}>
                <SectionWithH2Heading heading="The Covenantal Form of Worship">
                    <p>The Lord’s Day (Sunday) worship service at SMRC follows the Bible’s covenant renewal pattern of
                        Calling, Confession/Cleansing, Consecration, Communion, and Commissioning. Every worship service
                        should include a call to worship; singing to God; confession of sin and declaration of forgiveness;
                        calling upon God’s name in prayer; reading and exposition of the Word; the collection of tithes and
                        offerings; confession of our common faith; celebration of the Eucharistic feast with bread and wine;
                        and a&nbsp;benediction.
                    </p>
                </SectionWithH2Heading>
                <SectionWithH2Heading heading="The Nature of Baptism">
                    <p>Baptism is a blessed sacrament of the New Covenant instituted by our Lord as a sign and seal of
                        salvation and initiation into his new humanity. The sacramental washing with water in the name of
                        the Triune God, Father, Son, and Holy Spirit, officially admits a person into the kingdom, temple,
                        and covenant family of God. By the promise of the Word and the work of the Holy Spirit, baptism
                        becomes an effectual means of salvation to believers. As a means of grace, baptism testifies of
                        their identification with the Triune God of Scripture, union with Christ, regeneration, forgiveness
                        of sin, consecration to walk in newness of life, and fellowship in the Body of Christ (Mt. 28:19-20;
                        1 Cor. 12:13; Col. 2:11-12; Gal. 3:27; Rom. 6:3-5; Tit. 3:5; Mark 1:4). The one baptism of the New
                        Covenant is the fulfillment of the many baptismal events and rituals of the Old Covenant, as well as
                        circumcision, and anointing into office; hence, baptism is not to be&nbsp;repeated.
                    </p>
                    <p>Baptism, as a public instrument of union with Christ and His people, is ordinarily to be performed in
                        the context of a Lord’s Day covenant renewal service, at the beginning of the liturgy. However,
                        baptism’s validity is in no way tied to its enactment at a certain time or in a certain place or by
                        a certain person. Especially in cases of extremity or emergency, baptism may be performed outside of
                        a regular worship service. In cases of emergency baptisms, the session and congregation should be
                        notified of the action as soon as possible. While symbolic actions surrounding the rite of baptism
                        and drawing attention to its meaning are entirely proper, the baptismal liturgy should be kept
                        relatively simple. Washing with water in the name of the Father, Son, and Holy Spirit should not be
                        encrusted with additional, extra-biblical rituals that would obscure or subvert the meaning of
                        baptism&nbsp;itself.
                    </p>
                </SectionWithH2Heading>
                <SectionWithH2Heading heading="Recipients of Baptism">
                    <p>Baptism, as has been nearly universally held in the Church, is appropriately administered to the
                        children of Christians in infancy, since to them, no less than to adults are the promises of the
                        kingdom. Every covenantal administration in Scripture makes provision for the next generation; the
                        ritual washings and baptismal types of the Old Covenant included children; Jesus declared that even
                        the infants of His people participate in his covenant and kingdom, and are believers; and the
                        Apostles continued the practice of including children by baptizing households and regarding the
                        children of Christians to be “in the Lord.” Thus, Christ and His redemptive benefits belong to
                        disciples of all ages (Acts 2:39; Mt. 18:15-17; Eph. 6:4). That which is signified and conferred in
                        baptism is applicable to infants promised to be in covenant, as well as to adults who profess faith
                        in the God who raised Jesus Christ from the&nbsp;dead.
                    </p>
                </SectionWithH2Heading>
                <SectionWithH2Heading heading="Lord’s Supper">
                    <p>The Lord’s Supper is a blessed Sacrament of the New Covenant instituted by our Lord as a sign and
                        seal of His redemptive work, and as a means of offering and giving Himself and His benefits to His
                        people through the elements of bread and wine. By eating the bread and drinking the cup in a worthy
                        manner, as one family, believers Spiritually feed upon Christ, renew their union and communion with
                        Him, memorialize His death before the Father, and Spiritually commune with other covenant members
                        (Luke 22:20; Mat. 26:26-28; 1 Cor. 11:23-26; 1 Cor. 10:14-21). The Eucharistic feast is the supreme
                        climax of covenant renewal worship, as we celebrate peace with God and one another. The Lord’s
                        Supper fulfills all the sacramental meals of the Old&nbsp;Covenant.
                    </p>
                </SectionWithH2Heading>
                <SectionWithH2Heading heading="Recipients of the Lord’s Supper">
                    <p>St. Mark practices “open communion.” That is, we encourage all baptized Christians (in good standing, not excommunicated) to celebrate the feast of the Eucharist and so commune with Christ in His body. Visiting Christians should abide by the policies of their home Church with regard to participation. In principle, we confess that the Eucharistic table belongs to all of God’s people, and invite all other Christians to join with us in feasting and celebrating the Lord’s presence and gifts in and through bread and&nbsp;wine.</p>
                    <p>Under the headship of Christ, the responsibility for administering the Sacraments remains with the session. Baptized children are welcome to partake as soon as they are physically&nbsp;able.</p>
                </SectionWithH2Heading>
            </SidebarInnerLayout>
        </Layout>
    );
}
