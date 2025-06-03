import React from 'react';
import { Metadata } from 'next';
import Link from 'next/link';
import { createPageTitle } from '../createPageTitle';
import BasicPageLayout from '../layout/BasicPageLayout';

export const dynamic = 'force-static';

export const metadata: Metadata = {
    title: createPageTitle(
        'CREC Statement on Mandatory Medical Procedures',
    ),
};

export default function Page () {
    return (
        <BasicPageLayout hero={{ heroHeading: 'CREC Statement on Mandatory Medical Procedures' }}>
            <p><em>The following statement on mandatory medical procedures has been adopted by the Communion of Reformed Evangelical Churches (CREC), the denomination with which St. Mark Reformed Church is affiliated. You can download a PDF of the statement&nbsp;below.</em></p>
            <p>Standing in the ancient Christian tradition, committed to the doctrinal standards of our local and denominational constitutions and the supremacy of the Holy Scriptures, the Communion of Reformed Evangelical Churches (CREC) affirms our religion’s principles of liberty of conscience, honoring and preserving human life from conception to natural death, as well as the sovereignty of individuals and families in medical and healthcare&nbsp;decision-making.</p>
            <p>Therefore, we state our unequivocal support for the right of refusal of mandatory medical procedures, whether ordered by a branch of civil government, an employer, or any other institution to which an individual is subject or dependent – in the event that an individual sincerely believes his or her life, health, wellbeing, or morality is potentially threatened by such procedures or products, or in the event that a parent has the same concern for his or her&nbsp;child.</p>
            <p>We affirm that our Christian religion protects the liberty of individuals and families to refuse any medical procedure or product on the basis of sincerely held concerns for known or unknown side effects, experimental or emergency uses, potential involvement in fetal cell lines whether in development or testing, or medical and/or political corruption or&nbsp;coercion.</p>
            <p>Therefore, in the name of the Lord Jesus Christ, we defend the rights and responsibilities of our members to research these issues in consultation with their medical providers in order to make responsible medical decisions for themselves, including refusing vaccination or gene therapies on religious grounds. And we hereby call upon all governments, schools, employers, and other institutions to respect these deeply held religious convictions by upholding this religious liberty and/or providing religious exemptions as&nbsp;requested.</p>
            <p>On Behalf of the Communion of Reformed Evangelical&nbsp;Churches</p>
            <p>
                Rev. Virgil Hurt, Presiding Minister of Council<br />
                Rev. Dave Hatcher, Presiding Minister of Anselm Presbytery<br />
                Rev. Rob Hadding, Presiding Minister of Athanasius Presbytery<br />
                Rev. Gregg Strawbridge, Presiding Minister of Augustine Presbytery<br />
                Rev. Gene Helsel, Presiding Minister of Knox Presbytery<br />
                Rev. Bogumil Jarmulak, Presiding Minister of Hus Presbytery<br />
                Rev. Bill Smith, Presiding Minister of Tyndale Presbytery<br />
                Rev. Steve Wilkins, Presiding Minister of Wycliffe Presbytery<br />
            </p>
            <div className="mt-8 text-left">
                <div>
                    <div className="inline-flex rounded-md shadow">
                        <Link
                            href="/files/PMOC-CREC-Religious-Exemption-Statement.pdf"
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
