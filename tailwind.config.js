/** @type {import('tailwindcss').Config} */
module.exports = {
    safelist: ["dark"],
    prefix: "",

    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.{ts,tsx,vue}",
        "./node_modules/flowbite/**/*.js",
    ],

    theme: {},
    plugins: [require("flowbite/plugin")],
};
