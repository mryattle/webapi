/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

module.exports = {
    title: 'Web APIs',
    tagline: 'Analysis and comparison of design paradigms for Web APIs',
    themes: ['@docusaurus/theme-classic', '@docusaurus/theme-live-codeblock'],
    url: 'https://your-docusaurus-test-site.com',
    baseUrl: '/',
    favicon: 'img/favicon.ico',
    organizationName: 'mr_yattle', // Usually your GitHub org/user name.
    projectName: 'webapi', // Usually your repo name.
    scripts: [
      '/socket.io/socket.io.js',
      '/js/rx.lite.compat.js',
      '/js/md5.min.js',
      '/js/sha.js',
      '/js/http-client.js',
    ],    
    themeConfig: {
      navbar: {
        title: 'Homepage',
        logo: {
          alt: 'API Paradigm Logo',
          src: 'img/logo.svg',
        },
        links: [
          {to: 'docs/what-is-an-api', label: 'What is an API ?', position: 'left'},
          {to: 'docs/picking-the-right-paradigm', label: 'Choosing the right API', position: 'left'},
          {
            href: 'https://github.com/mryattle/webapi',
            label: 'GitHub',
            position: 'right',
          },
        ],
      },
      footer: {
        style: 'dark',
        links: [
          {
            title: 'Credits',
            items: [
              {
                label: 'Logo made by Vitaly Gorbachev',
                href: 'https://www.flaticon.com/authors/vitaly-gorbachev',
              },
              {
                label: 'API image made by Eucalyp',
                href: 'https://www.flaticon.com/authors/eucalyp',
              },
              {
                label: 'Security image made by Vectors Market',
                href: 'https://www.flaticon.com/authors/vectors-market',
              },
              {
                label: 'Sourcecode image made by Pixelmeetup',
                href: 'https://www.flaticon.com/authors/pixelmeetup',
              },
            ],
          },
          {
            title: 'Browse',
            items: [
              {
                label: 'What is a web API',
                to: 'docs/what-is-an-api',
              },
              {
                label: 'Picking the right paradigm',
                to: 'docs/picking-the-right-paradigm',
              },
            ],
          },
          {
            title: 'Visit',
            items: [
              {
                label: 'Docusaurus',
                href: 'https://v2.docusaurus.io/',
              },
              {
                label: 'GitHub',
                href: 'https://github.com/mryattle/webapi',
              },              
            ],
          },
        ],
        logo: {
          alt: 'Responsive website logo',
          src: '/img/responsive.png',
        },
        copyright: `built with Docusaurus.`,
      },
    },
    presets: [
      [
        '@docusaurus/preset-classic',
        {
          docs: {
            sidebarPath: require.resolve('./sidebars.js'),
          },
          theme: {
            customCss: require.resolve('./src/css/custom.css'),
          },
        },
      ],
    ],
  };