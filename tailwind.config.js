/* eslint-disable global-require */

module.exports = {
    purge: {
        enable: false, // We're doing purge as part of the build process
    },
    theme: {
        extend: {
            colors: {
                gold: '#f3c213',
                crimson: '#c31132',
                crimsonDark: '#a41130',
                goldenrod: '#df9c17',
                'saddle-brown-lightened-2': '#9b7e15',
                'saddle-brown-lightened-1': '#8b6e15',
                'saddle-brown': '#7b6014',
                'bronze-lightened-2': '#594b08',
                'bronze-lightened-1': '#493a08',
                bronze: '#392c08',
            },
            fontFamily: {},
            typography: () => ({
                DEFAULT: {
                    css: {
                        'ul>li>:first-child': {
                            marginTop: 0,
                            marginBottom: 0,
                        },
                        'ul>li>:last-child': {
                            marginTop: 0,
                            marginBottom: 0,
                        },
                    },
                },
            }),
        },
    },
    variants: {},
    plugins: [
        require('@tailwindcss/ui'),
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/forms'),
        // Customize: https://github.com/tailwindlabs/tailwindcss-typography#customization
        // https://github.com/tailwindlabs/tailwindcss-typography/blob/master/src/styles.js
        require('@tailwindcss/typography'),
    ],
};
