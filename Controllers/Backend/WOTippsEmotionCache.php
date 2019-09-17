<?php

/**
 * Controller responsible for clearing the separate emotion cache.
 */
class Shopware_Controllers_Backend_WOTippsEmotionCache extends Shopware_Controllers_Backend_Application
{
    protected $model = '\Shopware\Models\Media\Media';

    public function clearAction()
    {
        $path = realpath(__DIR__ . '/../../../../../../../../var/cache/');
        $message = [];

        if (is_dir($path . '/WOTippsEmotion/')) {
            // phpcs:ignore
            if ($dh = opendir($path . '/WOTippsEmotion/')) {
                // phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
                while (($file = readdir($dh)) !== false) {
                    $message[] = [
                        "file" => $path . '/WOTippsEmotion/' . $file,
                        'type' => filetype($path . '/WOTippsEmotion/' . $file),
                    ];

                    if (filetype($path . '/WOTippsEmotion/' . $file) == 'file') {
                        unlink($path . '/WOTippsEmotion/' . $file);
                    }
                }

                closedir($dh);
            }
        }

        $this->View()->assign([
            "success" => true,
            'message' => $message,
        ]);
    }
}
