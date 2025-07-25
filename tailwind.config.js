module.exports = {
  content: ["./**/*.{php,js}", "./partials/**/*.{php,js}"],
  theme: {
    screens: {
      xs: "360px",
      sm: "640px",
      md: "768px",
      lg: "1024px",
      xl: "1280px",
      "2xl": "1536px",
    },
    extend: {
      colors: {
        brand: {
          100: "#FB295B",
          200: "#C3D3ED",
          300: "#A5BDE4",
          400: "#87A7DB",
        },
      },
    },
    plugins: [],
  },
};
