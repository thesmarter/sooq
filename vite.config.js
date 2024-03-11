
import vue from '@vitejs/plugin-vue';
import { loadEnv } from 'vite';
import i18n from 'laravel-vue-i18n/vite';

export default ({ mode }) => {
    require('dotenv').config({path : '.env', debug: true, override: true});
    Object.assign(process.env, loadEnv(mode, process.cwd(), ''));
    let appDomain = process.env.VITE_BASE_DOMAIN;

    let clearSlash =  process.env.VITE_APP_DIR.replaceAll('/' , '');
    let subFolder = clearSlash.replaceAll("\\", "");

    let appUrl = "";
    if(!subFolder){
        appUrl = appDomain;
    } else {
        appUrl = appDomain+subFolder+'/';
    }

    return ({
    base: process.env.APP_ENV === 'local' ? '' : `${appUrl}build/`, //command === 'serve' ? '' : '/build/',
    publicDir: 'fake_dir_so_nothing_gets_copied',
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            input: ['resources/js/app.js'],
            // output: {
            //     entryFileNames : "[name].js",
            //     chunkFileNames : "[name].js",
            //     assetFileNames : "[name].[ext]",
            // }
        },
    },
    resolve: {
        alias: {
          '@': '/resources/js', //path.resolve(__dirname, './resources/js'),//'resources/js',
          '@template1': '/Modules/Template/PSXFETemplate/Resources/Pages', //path.resolve(__dirname, './Modules/Template/PSXFETemplate/Resources/Pages'),//'Modules/Template/PSXFETemplate/Resources/Pages',
          '@templateCore': '/Modules/TemplateCore', //path.resolve(__dirname, './Modules/TemplateCore'),//'Modules/TemplateCore',
          '@public': '/public',
          '@vendorPanel': '/Modules/StoreFront/VendorPanel/Resources/Pages',
        }
      },
    plugins: [
        {
            name: 'blade',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        },
        vue(),
        i18n(),
    ],
    css: {
        postCss: {
            plugins: {
                tailwindcss: {},
                autoprefixer: {},
                },
        },
    },
  });
};
