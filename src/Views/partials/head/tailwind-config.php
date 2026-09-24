<?php
/** Tailwind CDN bridge: semantic colors → public/css/index.css. Legacy `admin`/`success` hex: migrate views to `primary` / semantic status. */
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;500;600;700&family=Varela+Round&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/index.css">
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    background: 'var(--background)',
                    foreground: 'var(--foreground)',
                    card: {
                        DEFAULT: 'var(--card)',
                        foreground: 'var(--card-foreground)',
                    },
                    primary: {
                        DEFAULT: 'var(--primary)',
                        foreground: 'var(--primary-foreground)',
                        hover: 'var(--primary)',
                        soft: 'var(--accent)',
                        muted: 'var(--muted)',
                    },
                    secondary: {
                        DEFAULT: 'var(--secondary)',
                        foreground: 'var(--secondary-foreground)',
                    },
                    muted: {
                        DEFAULT: 'var(--muted)',
                        foreground: 'var(--muted-foreground)',
                    },
                    accent: {
                        DEFAULT: 'var(--accent)',
                        foreground: 'var(--accent-foreground)',
                    },
                    destructive: {
                        DEFAULT: 'var(--destructive)',
                        foreground: 'var(--background)',
                    },
                    border: 'var(--border)',
                    input: 'var(--input)',
                    ring: 'var(--ring)',
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
                        primary: 'var(--foreground)',
                        secondary: 'var(--muted-foreground)',
                    },
                    page: 'var(--background)',
                },
                fontFamily: {
                    heading: ['"Varela Round"', 'sans-serif'],
                    body: ['"Nunito Sans"', 'sans-serif'],
                },
                borderRadius: {
                    DEFAULT: 'var(--radius)',
                    lg: 'var(--radius)',
                    md: 'calc(var(--radius) - 2px)',
                    sm: 'calc(var(--radius) - 4px)',
                    xl: 'calc(var(--radius) + 4px)',
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
