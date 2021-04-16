module.exports = {
    purge: {
        content: [
            './app/Views/twig/*.twig',
        ],
    },
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {},
    },
    variants: {
        extend: {
            fontWeight: ['odd'],
            textAlign: ['odd'],
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
