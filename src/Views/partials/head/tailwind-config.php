<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700&family=Varela+Round&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        DEFAULT: '#E07A5F',
                        hover: '#C96A52',
                        soft: '#FDF0EC',
                        muted: '#F4E4DE',
                    },
                    admin: {
                        DEFAULT: '#3D405B',
                        hover: '#2D3142',
                        soft: '#EEF0F4',
                    },
                    success: {
                        DEFAULT: '#2A9D8F',
                        bg: '#E6F5F3',
                    },
                    warning: {
                        DEFAULT: '#E9C46A',
                        bg: '#FEF9E7',
                    },
                    danger: {
                        DEFAULT: '#E76F51',
                    },
                    content: {
                        primary: '#264653',
                        secondary: '#6B7280',
                    },
                    border: {
                        DEFAULT: '#E8E4DF',
                    },
                    page: '#FAFAF8',
                    card: '#FFFFFF',
                },
                fontFamily: {
                    heading: ['"Varela Round"', 'sans-serif'],
                    body: ['"Nunito Sans"', 'sans-serif'],
                },
                borderRadius: {
                    DEFAULT: '12px',
                    btn: '10px',
                },
                boxShadow: {
                    dropdown: '0 10px 40px -8px rgba(38, 70, 83, 0.15), 0 4px 12px -4px rgba(38, 70, 83, 0.08)',
                    soft: '0 2px 4px rgba(38, 70, 83, 0.04), 0 8px 24px rgba(38, 70, 83, 0.08)',
                    'soft-lg': '0 4px 8px rgba(38, 70, 83, 0.05), 0 16px 40px rgba(38, 70, 83, 0.1)',
                    'soft-inset': 'inset 0 1px 2px rgba(38, 70, 83, 0.06)',
                },
                transitionDuration: {
                    soft: '220ms',
                },
            },
        },
    };
</script>
<style>
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
