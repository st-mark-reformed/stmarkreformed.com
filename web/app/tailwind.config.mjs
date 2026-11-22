/** @type {import('tailwindcss').Config} */
export default {
    theme: {
        extend: {
            fontFamily: {
                sans: ['Open Sans', 'sans-serif'],
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        // Muted dark-mode (prose-invert) colors
                        '--tw-prose-invert-body': theme('colors.stone.300'),
                        '--tw-prose-invert-headings': theme('colors.stone.200'),
                        '--tw-prose-invert-lead': theme('colors.stone.300'),
                        '--tw-prose-invert-bold': theme('colors.stone.200'),
                        '--tw-prose-invert-counters': theme('colors.stone.400'),
                        '--tw-prose-invert-bullets': theme('colors.stone.500'),
                        '--tw-prose-invert-hr': theme('colors.stone.600'),
                        '--tw-prose-invert-quotes': theme('colors.stone.200'),
                        '--tw-prose-invert-quote-borders': theme('colors.stone.600'),
                        '--tw-prose-invert-captions': theme('colors.stone.400'),
                        '--tw-prose-invert-th-borders': theme('colors.stone.600'),
                        '--tw-prose-invert-td-borders': theme('colors.stone.700'),
                        'ul>li>:first-child': {
                            marginTop: 0,
                            marginBottom: 0,
                        },
                        'ul>li>:last-child': {
                            marginTop: 0,
                            marginBottom: 0,
                        },
                        a: {
                            color: theme('colors.crimson'),
                            'text-decoration': 'underline',
                            '&:hover': {
                                color: theme('colors.crimson-dark'),
                            },
                        },
                    },
                },
                'over-dark': {
                    css: {
                        a: {
                            color: '#fff',
                            'text-decoration': 'underline',
                            '&:hover': {
                                color: theme('colors.gray.300'),
                            },
                        },
                    },
                },
            }),
        },
    },
};
