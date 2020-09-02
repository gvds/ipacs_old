// const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  purge: {
    content: [
      './app/**/*.php',
      './resources/**/*.html',
      './resources/**/*.js',
      './resources/**/*.jsx',
      './resources/**/*.ts',
      './resources/**/*.tsx',
      './resources/**/*.php',
    ],
    options: {
      // defaultExtractor: (content) => content.match(/[\w-/.:]+(?<!:)/g) || [],
      // whitelistPatterns: [/-active$/, /-enter$/, /-leave-to$/, /show$/],
    },
  },
  future: {
    removeDeprecatedGapUtilities: true,
  },
  theme: {
    extend: {
      colors: {
        gray: {
          '50' : '#f9fafb',
          '100': '#f4f5f7',
          '200': '#e5e7eb',
          '300': '#d2d6dc',
          '400': '#9fa6b2',
          '500': '#6b7280',
          '600': '#4b5563',
          '700': '#374151',
          '800': '#252f3f',
          '900': '#161e2e',
        },
      },
      // fontFamily: {
      //   sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      // },
    },
  },
  variants: {
    backgroundColor: ['odd', 'even'],
  },
  plugins: [
    // require('@tailwindcss/custom-forms'),
    // require('@tailwindcss/ui'),
  ],
};
