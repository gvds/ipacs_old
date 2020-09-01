const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  purge: {
    // content: [
    //   './app/**/*.php',
    //   './resources/**/*.html',
    //   './resources/**/*.js',
    //   './resources/**/*.jsx',
    //   './resources/**/*.ts',
    //   './resources/**/*.tsx',
    //   './resources/**/*.php',
    //   './resources/**/*.vue',
    //   './resources/**/*.twig',
    // ],
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
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  variants: {
    backgroundColor: ['odd', 'even'],
  },
  plugins: [
    // require('@tailwindcss/custom-forms'),
    require('@tailwindcss/ui'),
  ],
};
