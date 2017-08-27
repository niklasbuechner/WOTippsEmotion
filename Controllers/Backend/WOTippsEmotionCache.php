<?php

/*
 * WOTippsEmotion
 * By Niklas Buechner
 * http://www.wotipps.de
 * This code may not be distributed without written permission.
 */


/**
 * Controller responsible for clearing the separate emotion cache.
 *
 * @author Niklas
 */
class Shopware_Controllers_Backend_WOTippsEmotionCache extends Shopware_Controllers_Backend_Application
{
    protected $model = '\Shopware\Models\Media\Media';
    
    public function clearAction()
    {
        $path = realpath(__DIR__ . '/../../../../../../../../var/cache/');
        $message = [];
        
        if (is_dir($path . '/WOTippsEmotion/'))
        {
            if ($dh = opendir($path . '/WOTippsEmotion/'))
            {
                while (($file = readdir($dh)) !== false)
                {
                    $message[] = array(
                        "file" => $path . '/WOTippsEmotion/' . $file,
                        'type' => filetype($path . '/WOTippsEmotion/' . $file)
                    );
                    
                    if (filetype($path . '/WOTippsEmotion/' . $file) == 'file')
                    {
                        unlink($path . '/WOTippsEmotion/' . $file);
                    }
                }
                
                closedir($dh);
            }
        }
        
        $this->View()->assign(array(
            "success" => true,
            'message' => $message
        ));
    }
}
