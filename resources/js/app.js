import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Chart = Chart;

Alpine.data('themeManager', () => ({
    isDark: false,
    theme: 'system',

    init() {
        const stored = localStorage.getItem('pm-theme');
        this.theme = stored || 'system';
        this.applyTheme();

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (this.theme === 'system') {
                this.isDark = e.matches;
                this.syncClass();
            }
        });
    },

    applyTheme() {
        if (this.theme === 'dark') {
            this.isDark = true;
        } else if (this.theme === 'light') {
            this.isDark = false;
        } else {
            this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        this.syncClass();
        localStorage.setItem('pm-theme', this.theme);
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: this.isDark } }));
    },

    syncClass() {
        document.documentElement.classList.toggle('dark', this.isDark);
    },

    setTheme(val) {
        this.theme = val;
        this.applyTheme();
    }
}));

window.Alpine = Alpine;
Alpine.start();
