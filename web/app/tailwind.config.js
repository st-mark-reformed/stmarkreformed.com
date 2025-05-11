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
