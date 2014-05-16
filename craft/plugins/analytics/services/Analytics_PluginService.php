<?php

/**
 * Craft Analytics by Dukt
 *
 * @package   Craft Analytics
 * @author    Benjamin David
 * @copyright Copyright (c) 2014, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 * @link      https://dukt.net/craft/analytics/
 */

namespace Craft;

require_once(CRAFT_PLUGINS_PATH.'analytics/vendor/autoload.php');

use VIPSoft\Unzip\Unzip;
use Symfony\Component\Filesystem\Filesystem;

class Analytics_PluginService extends BaseApplicationComponent
{
    // --------------------------------------------------------------------

    public function download($pluginHandle)
    {
        Craft::log(__METHOD__, LogLevel::Info, true);

        // -------------------------------
        // Get ready to download & unzip
        // -------------------------------

        $return = array('success' => false);

        $filesystem = new Filesystem();
        $unzipper  = new Unzip();

        $pluginComponent = craft()->plugins->getPlugin($pluginHandle, false);


        // plugin path

        $pluginZipDir = CRAFT_PLUGINS_PATH."_".$pluginHandle."/";
        $pluginZipPath = CRAFT_PLUGINS_PATH."_".$pluginHandle.".zip";


        // remote plugin zip url

        $remotePlugin = $this->_getRemotePlugin($pluginHandle);

        if(!$remotePlugin) {
            $return['msg'] = "Couldn't get plugin last version";

            Craft::log(__METHOD__.' : Could not get last version' , LogLevel::Info, true);

            return $return;
        }

        $remotePluginZipUrl = $remotePlugin['xml']->enclosure['url'];

        // -------------------------------
        // Download & Install
        // -------------------------------

        try {

            // download remotePluginZipUrl to pluginZipPath

            $zipContents = file_get_contents($remotePluginZipUrl);

            file_put_contents($pluginZipPath, $zipContents);


            // unzip pluginZipPath into pluginZipDir

            $contents = $unzipper->extract($pluginZipPath, $pluginZipDir);


            // move files we want to keep from their current location to unzipped location
            // keep : .git

            if(file_exists(CRAFT_PLUGINS_PATH.$pluginHandle.'/.git') && !$pluginZipDir.$contents[0].'/.git') {
                $filesystem->rename(CRAFT_PLUGINS_PATH.$pluginHandle.'/.git',
                    $pluginZipDir.$contents[0].'/.git');
            }


            // remove current files
            // make a backup of existing plugin (to storage ?) ?

            $filesystem->remove(CRAFT_PLUGINS_PATH.$pluginHandle);


            // move new files to final destination

            $filesystem->rename($pluginZipDir.$contents[0].'/'.$pluginHandle.'/', CRAFT_PLUGINS_PATH.$pluginHandle);

        } catch (\Exception $e) {

            $return['msg'] = $e->getMessage();

            Craft::log(__METHOD__.' : Crashed : '.$e->getMessage() , LogLevel::Info, true);

            return $return;
        }


        // remove download files

        try {
            $filesystem->remove($pluginZipDir);
            $filesystem->remove($pluginZipPath);
        } catch(\Exception $e) {

            $return['msg'] = $e->getMessage();

            Craft::log(__METHOD__.' : Crashed : '.$e->getMessage() , LogLevel::Info, true);

            return $return;
        }

        Craft::log(__METHOD__.' : Success : ' , LogLevel::Info, true);

        $return['success'] = true;

        return $return;
    }

    // --------------------------------------------------------------------

    public function enable($pluginHandle)
    {
        Craft::log(__METHOD__, LogLevel::Info, true);

        $pluginComponent = craft()->plugins->getPlugin($pluginHandle, false);

        try {

            if(!$pluginComponent->isEnabled) {
                if (craft()->plugins->enablePlugin($pluginHandle)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }

        } catch(\Exception $e) {

            Craft::log(__METHOD__.' : Crashed : '.$e->getMessage(), LogLevel::Info, true);

            return false;
        }
    }

    // --------------------------------------------------------------------

    public function install($pluginHandle)
    {
        Craft::log(__METHOD__, LogLevel::Info, true);

        craft()->plugins->loadPlugins();

        $pluginComponent = craft()->plugins->getPlugin($pluginHandle, false);

        try {
            if(!$pluginComponent)
            {
                Craft::log(__METHOD__.' : '.$pluginHandle.' component not found', LogLevel::Info, true);

                return false;
            }

            if(!$pluginComponent->isInstalled) {
                if (craft()->plugins->installPlugin($pluginHandle)) {
                    return true;
                } else {

                    Craft::log(__METHOD__.' : '.$pluginHandle.' component not installed', LogLevel::Info, true);

                    return false;
                }
            } else {
                return true;
            }
        } catch(\Exception $e) {

            Craft::log(__METHOD__.' : Crashed : '.$e->getMessage(), LogLevel::Info, true);

            return false;
        }
    }

    // --------------------------------------------------------------------

    private function _getRemotePlugin($pluginHandle)
    {
        Craft::log(__METHOD__, LogLevel::Info, true);

        $url = 'http://dukt.net/craft/'.$pluginHandle.'/releases.xml';



        // devMode

        $pluginHashes = craft()->config->get('pluginHashes');

        if(isset($pluginHashes[$pluginHandle])) {

            $url = 'http://dukt.net/actions/tracks/updates/'.$pluginHashes[$pluginHandle].'/develop/xml';
        }


        // or refresh cache and get new updates if cache expired or forced update

        $xml = simplexml_load_file($url);


        // XML from here on

        $namespaces = $xml->getNameSpaces(true);

        $versions = array();
        $zips = array();
        $xml_version = array();

        if (!empty($xml->channel->item)) {
            foreach ($xml->channel->item as $version) {
                $ee_addon       = $version->children($namespaces['ee_addon']);
                $version_number = (string) $ee_addon->version;
                $versions[$version_number] = array('xml' => $version, 'addon' => $ee_addon);
                return $versions[$version_number];
            }
        } else {
            Craft::log(__METHOD__.' : Could not get channel items', LogLevel::Info, true);
        }
    }

    // --------------------------------------------------------------------
}

