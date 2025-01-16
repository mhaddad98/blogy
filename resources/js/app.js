import { computed, createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import "../css/app.css";
import Layout from "../js/Shared/Layout.vue";

createInertiaApp({
    resolve: async (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", {
            eager: true,
        });

        const importPages = pages[`./Pages/${name}.vue`];

        if (!importPages) throw new Error(`Page not found: ${name}`);

        const pageModule = await importPages;
        const page = pageModule.default;

        if (page.layout === undefined) {
            page.layout = Layout;
        }
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
