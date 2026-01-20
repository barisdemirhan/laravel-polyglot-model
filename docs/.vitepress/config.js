import { defineConfig } from 'vitepress'

export default defineConfig({
    title: 'Laravel Polyglot Model',
    description: 'Elegant model translations for Laravel with polymorphic storage',

    base: '/laravel-polyglot-model/',

    head: [
        ['link', { rel: 'icon', type: 'image/svg+xml', href: '/logo.svg' }],
    ],

    themeConfig: {
        logo: '/logo.svg',
        socialLinks: [
            { icon: 'github', link: 'https://github.com/barisdemirhan/laravel-polyglot-model' }
        ],
        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2026 Barış Demirhan'
        },
        search: {
            provider: 'local'
        },
    },

    locales: {
        root: {
            label: 'English',
            lang: 'en',
            themeConfig: {
                nav: [
                    { text: 'Home', link: '/' },
                    { text: 'Guide', link: '/installation' },
                    { text: 'API', link: '/api-reference' },
                    {
                        text: 'v1.0.0',
                        items: [
                            { text: 'Changelog', link: 'https://github.com/barisdemirhan/laravel-polyglot-model/blob/main/CHANGELOG.md' },
                        ]
                    }
                ],
                sidebar: [
                    {
                        text: 'Introduction',
                        items: [
                            { text: 'What is this?', link: '/' },
                            { text: 'Installation', link: '/installation' },
                            { text: 'Quick Start', link: '/quick-start' },
                        ]
                    },
                    {
                        text: 'Guide',
                        items: [
                            { text: 'Basic Usage', link: '/usage' },
                            { text: 'Query Scopes', link: '/query-scopes' },
                            { text: 'Events', link: '/events' },
                            { text: 'Caching', link: '/caching' },
                            { text: 'Commands', link: '/commands' },
                        ]
                    },
                    {
                        text: 'Reference',
                        items: [
                            { text: 'API Reference', link: '/api-reference' },
                            { text: 'Configuration', link: '/configuration' },
                        ]
                    }
                ],
                editLink: {
                    pattern: 'https://github.com/barisdemirhan/laravel-polyglot-model/edit/main/docs/:path',
                    text: 'Edit this page on GitHub'
                }
            }
        },
        tr: {
            label: 'Türkçe',
            lang: 'tr',
            link: '/tr/',
            themeConfig: {
                nav: [
                    { text: 'Anasayfa', link: '/tr/' },
                    { text: 'Rehber', link: '/tr/installation' },
                    { text: 'API', link: '/tr/api-reference' },
                    {
                        text: 'v1.0.0',
                        items: [
                            { text: 'Değişiklik Günlüğü', link: 'https://github.com/barisdemirhan/laravel-polyglot-model/blob/main/CHANGELOG.md' },
                        ]
                    }
                ],
                sidebar: [
                    {
                        text: 'Giriş',
                        items: [
                            { text: 'Nedir?', link: '/tr/' },
                            { text: 'Kurulum', link: '/tr/installation' },
                            { text: 'Hızlı Başlangıç', link: '/tr/quick-start' },
                        ]
                    },
                    {
                        text: 'Rehber',
                        items: [
                            { text: 'Temel Kullanım', link: '/tr/usage' },
                            { text: 'Sorgu Kapsamları', link: '/tr/query-scopes' },
                            { text: 'Olaylar', link: '/tr/events' },
                            { text: 'Ön Bellek', link: '/tr/caching' },
                            { text: 'Komutlar', link: '/tr/commands' },
                        ]
                    },
                    {
                        text: 'Referans',
                        items: [
                            { text: 'API Referansı', link: '/tr/api-reference' },
                            { text: 'Konfigürasyon', link: '/tr/configuration' },
                        ]
                    }
                ],
                editLink: {
                    pattern: 'https://github.com/barisdemirhan/laravel-polyglot-model/edit/main/docs/:path',
                    text: 'Bu sayfayı GitHub üzerinde düzenle'
                }
            }
        }
    }
})
