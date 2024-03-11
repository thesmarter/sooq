import { createStore } from "vuex";

export default createStore({
    state: {
        isDarkMode: false,
        activeLanguage: "en",
        dir: "ltr",
        sidebarNavOpen: false,
        sidebarFull: true,
        showMenu: false,
        sidebarActive: 'Dashboard',
        supportedLanguages: [
            { name: "English", languageCode: "en" },
            { name: "French", languageCode: "fr" },
            { name: "Arabic", languageCode: "ar" },
        ],
    },
    getters: {
        dir(state) {
            return state.dir;
        },
        isDarkMode(state) {
            return state.isDarkMode;
        },
        activeLanguage(state) {
            return state.activeLanguage;
        },
        sidebarNavOpen(state) {
            return state.sidebarNavOpen;
        },
        sidebarFull(state) {
            return state.sidebarFull;
        },
        sidebarActive(state) {
            return state.sidebarActive;
        },
        showMenu(state) {
            return state.showMenu;
        },
        supportedLanguages(state) {
            return state.supportedLanguages;
        },
    },
    mutations: {
        setSidebarFull(state, sidebarFull) {
            state.sidebarFull = sidebarFull;
        },
        setSidebarNavOpen(state, sidebarNavOpen) {
            state.sidebarNavOpen = sidebarNavOpen;
        },
        setShowMenu(state, showMenu) {
            state.showMenu = showMenu;
        },
        setSidebarActive(state, sidebarActive) {
            state.sidebarActive = sidebarActive;
        },
        setDir(state) {
            if (state.dir == "ltr") {
                state.dir = "rtl";
            } else {
                state.dir = "ltr";
            }
        },
        setDarkMode(state) {
            if (state.isDarkMode) {
                state.isDarkMode = false;
            } else {
                state.isDarkMode = true;
            }
        },
        loadDarkMode(state) {
            if (
                localStorage.isDarkMode != null &&
                localStorage.isDarkMode == "true"
            ) {
                state.isDarkMode = true;
            } else {
                localStorage.isDarkMode = false;
                state.isDarkMode = false;
            }
        },
        setActiveLanguage(state, lang) {
            state.activeLanguage = lang;
        },
        loadActiveLang(state, lang) {
            if (localStorage.activeLanguage != null) {
                state.activeLanguage = localStorage.getItem("activeLanguage");
            } else {
                localStorage.activeLanguage = "en";
                state.activeLanguage = "en";
            }
            state.activeLanguage = lang;
        },
        loadDir(state) {
            if (localStorage.dir != null && localStorage.dir == "rtl") {
                state.dir = "rtl";
            } else {
                localStorage.dir = "ltr";
                state.dir = "ltr";
            }
        },
    },
    actions: {
        async handleSidebarFull({ commit }, sidebarFull) {
            commit("setSidebarFull", sidebarFull);
        },
        async handleSidebarNavOpen({ commit }, sidebarNavOpen) {
            commit("setSidebarNavOpen", sidebarNavOpen);
        },
        async handleShowMenu({ commit }, showMenu) {
            commit("setShowMenu", showMenu);
        },
        async handleSidebarActive({ commit }, handleSidebarActive) {
            commit("setSidebarActive", handleSidebarActive);
        },
        async toggleDir({ commit }) {
            commit("setDir");
        },
        async toggleDarkMode({ commit }) {
            if (
                localStorage.isDarkMode != null &&
                localStorage.isDarkMode == "true"
            ) {
                localStorage.isDarkMode = "false";
            } else {
                localStorage.isDarkMode = "true";
            }
            commit("setDarkMode");
                        
        },
        async loadIsDarkMode({ commit }) {
            commit("loadDarkMode");
        },
        async handleActiveLanguage({ commit }, lang) {
            localStorage.activeLanguage = lang;
            commit("setActiveLanguage", lang);
            // await this.loadActiveLanguage(lang);
            window.location.reload();
        },
        async loadActiveLanguage({ commit }, lang) {
            commit("loadActiveLang", lang);
        },
        async toggleDir({ commit }) {
            if (localStorage.dir != null && localStorage.dir == "ltr") {
                localStorage.dir = "rtl";
            } else {
                localStorage.dir = "ltr";
            }
            commit("setDir");
            this.loadDirectory();
        },
        async loadDirectory({ commit }) {
            commit("loadDir");
        },
    },
});
