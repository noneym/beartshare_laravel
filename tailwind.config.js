/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        // Blade dışında sınıf adı taşıyan PHP (ör. modellerdeki durum rozeti renkleri)
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#D4A017',
                'primary-dark': '#B8860B',
                artpuan: '#88bd6a',
                // Açık zeminde küçük yazı / buton için koyu ton (beyaz yazıyla AA kontrast)
                'artpuan-ink': '#47732f',
                'artpuan-soft': '#eef5e8',
                'brand-black100': '#14171c',
                'brand-grey250': '#e5e5e5',
                'brand-grey300': '#d1d5db',
            },
            fontFamily: {
                sans: ['Prompt', 'Helvetica', 'Arial', 'ui-sans-serif', 'system-ui', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
            },
            fontSize: {
                xsm: '0.65rem',
            },
        },
    },
    plugins: [],
};
