<?php

namespace Modules\Installer\Helpers;

use App\Config\ps_constant;
use Modules\Core\Entities\Installer;
use Modules\Core\Entities\DomainChange;

class InstalledFileManager
{
    /**
     * Create installed file.
     *
     * @return int
     */
    public function create()
    {
        $installedLogFile = storage_path('installed');

        $dateStamp = date('Y/m/d h:i:sa');

        if (!file_exists($installedLogFile)) {
            $message = trans('installer_messages.installed.success_log_message') . $dateStamp . "\n";

            file_put_contents($installedLogFile, $message);
        } else {
            $message = trans('installer_messages.updater.log.success_message') . $dateStamp;

            file_put_contents($installedLogFile, $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        }

        $installer = Installer::first();
        if (!empty($installer)) {
            $installedFile = file_exists(storage_path('installed'));
            if (!empty($installedFile)) {
                $installer->is_installed = 1;
                $installer->update();
            }
        } else {
            $installedFile = file_exists(storage_path('installed'));
            if (!empty($installedFile)) {
                $installerNew = new Installer();
                $installerNew->is_installed = 1;
                $installerNew->save();
            }
        }
        $domain = DomainChange::first();

        // $path_to_file = public_path('build/app.js');
        // $path_to_file2 = public_path('build/PsApiService2.js');
        // $path_to_file3 = public_path('build/DropZone3.js');
        // $path_to_file4 = public_path('build/OpenStreetMap.js');
        // $path_to_file5 = public_path('build/PsFrontendLayout.js');
        // $path_to_file6 = public_path('build/psApiService.js');

        $pathArr = findFindWithHashKey(public_path(ps_constant::appJSFilePath));
        if(count($pathArr) !== 0){
            $path_to_file = $pathArr[0];
        }

        $pathArr2 = findFindWithHashKey(public_path(ps_constant::PsApiServiceJSFilePath));
        if(count($pathArr2) !== 0){
            $path_to_file2 = $pathArr2[0];
        }

        $pathArr6 = findFindWithHashKey(public_path(ps_constant::psApiServiceJSFilePath));
        if(count($pathArr6) !== 0){
            $path_to_file6 = $pathArr6[0];
        }


        // app.js
        if (config('app.dir') === '') {
            // dd(ps_constant::searchDomain.ps_constant::searchSubFolder.'/');
            findAndReplaceForBuildFolder($path_to_file, $domain->domain_name . $domain->sub_folder . '/', config("app.base_domain"));
            findAndReplaceForBuildFolder($path_to_file, $domain->sub_folder, config("app.dir"));
            findAndReplaceForBuildFolder($path_to_file, $domain->domain_name, config("app.base_domain"));
        } else {
            if(empty($domain->sub_folder)){
                findAndReplaceForBuildFolder($path_to_file, $domain->domain_name, config("app.base_domain").config('app.dir').'/');
            }else{
                findAndReplaceForBuildFolder($path_to_file, $domain->domain_name, config("app.base_domain"));
                findAndReplaceForBuildFolder($path_to_file, $domain->sub_folder, config("app.dir"));
            }
        }

        // findAndReplaceForBuildFolder($path_to_file, ps_constant::searchSubFolder, config("app.dir"));


        //psApiService2.js
        if (config('app.dir') === '') {
            findAndReplaceForBuildFolder($path_to_file2, '/' . $domain->sub_folder, config("app.dir"));
        } else {
            if(empty($domain->sub_folder)){

                $findString2 = 'let C="";C=""';
                $replaceString2='let C="";C="/'.config('app.dir').'"';

                findAndReplaceForBuildFolder($path_to_file2, $findString2, $replaceString2);

            }else{
            findAndReplaceForBuildFolder($path_to_file2, $domain->sub_folder, config("app.dir"));
            }
        }


        //psApiService.js
        if (config('app.dir') === '') {
            findAndReplaceForBuildFolder($path_to_file6, $domain->sub_folder, config("app.dir"));
        } else {
            if(empty($domain->sub_folder)){
                $findString = 'let a="",h="";h=""';
                $replaceString='let a="",h="";h="'.config('app.dir').'"';

                findAndReplaceForBuildFolder($path_to_file6, $findString, $replaceString);

            }else{

                findAndReplaceForBuildFolder($path_to_file6, $domain->sub_folder, config("app.dir"));
            }
        }






        $domain->domain_name = config('app.base_domain');
        $domain->sub_folder = config('app.dir');
        $domain->save();

        return $message;
    }

    /**
     * Update installed file.
     *
     * @return int
     */
    public function update()
    {
        return $this->create();
    }
}
