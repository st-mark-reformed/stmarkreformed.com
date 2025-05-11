// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable react/no-danger */
import React, { ReactNode } from 'react';
import Nav from './Nav';
import Hero, { HeroType } from './HeroType';

export default async function Layout (
    {
        children,
        hero = null,
    }: {
        children: ReactNode;
        hero?: null | HeroType;
    },
) {
    hero = hero || {};
    hero.heroDarkeningOverlayOpacity = hero?.heroDarkeningOverlayOpacity || 0;
    hero.heroImage1x = hero?.heroImage1x || '/images/default-hero.jpg';
    hero.heroImage2x = hero?.heroImage2x || undefined;

    return (
        <>
            {(() => {
                if (!hero.heroImage1x && !hero.heroImage2x) {
                    return null;
                }

                return (
                    <style
                        dangerouslySetInnerHTML={{
                            __html: `
                                .hero-background-image {
                                    background-image: url('${hero.heroImage1x || hero.heroImage2x}');
                                    background-attachment: fixed;
                                }

                                @media (-webkit-min-device-pixel-ratio: 1.3), (min-resolution: 1.3dppx) {
                                    .hero-background-image {
                                        background-image: url('${hero.heroImage2x || hero.heroImage1x}');
                                    }
                                }

                                @media (hover: none) {
                                    .hero-background-image {
                                        background-attachment: initial;
                                    }
                                }
                            `,
                        }}
                    />
                );
            })()}
            <div className="h-full bg-bronze">
                <div className="bg-white">
                    <div className="relative overflow-hidden">
                        <div className="hero-background-image bg-bronze bg-cover bg-no-repeat bg-center relative z-50">
                            {(() => {
                                if (hero.heroDarkeningOverlayOpacity < 1) {
                                    return null;
                                }

                                return (
                                    <div
                                        className="absolute w-full h-full inset-0 z-10 bg-black"
                                        style={{
                                            opacity: hero.heroDarkeningOverlayOpacity / 100,
                                        }}
                                    />
                                );
                            })()}
                            <header className="relative z-50">
                                <Nav />
                            </header>
                            <Hero hero={hero} />
                        </div>
                        <main className="relative z-10">
                            {children}
                        </main>
                        {/* <Footer menu={menu} /> */}
                    </div>
                </div>
            </div>
        </>
    );
}
