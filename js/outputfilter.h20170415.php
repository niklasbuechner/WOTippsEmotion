<?php

/**
 * Smarty plugin
 *
 * @package    Webseiten Optimierungs Tipps - Optimierer
 * @subpackage PluginsFilter
 */

/**
 * Smarty insert emotion worlds plugin
 *
 * @param string $source input string
 * @return string filtered output
 */
function smarty_outputfilter_h20170415($input)
{
    $debug = [];
    $groups = null;
    $html = '';
    $path = realpath(__DIR__ . '/../../../../../../../var/cache/');
    $host = null;
    //$path = realpath(__DIR__ . '/var/cache/');

    preg_match_all(
            '/\/(?:[a-zA-Z0-9\/\.-]*)widgets\/emotion\/[a-zA-Z0-9-]+\/emotionId\/[0-9]+\/controllerName\/[a-zA-Z0-9-]+\/?/',
            $input, $groups
    );

    if ($groups == null || count($groups) < 1)
    {
        $debug['groups'] = 'None';
        return getOutput($input, $debug);
    }

    if (!is_dir($path . '/WOTippsEmotion/'))
    {
        try
        {
            mkdir($path . '/WOTippsEmotion/');
        }
        catch (Exception $ex)
        {
            $debug['Exception'] = true;
            return getOutput($input, $debug);
        }

        if (!is_dir($path . '/WOTippsEmotion/'))
        {
            $debug['dir'] = 'Does not exist';
            return getOutput($input, $debug);
        }
    }

    $debug['groups'] = count($groups[0]);

    foreach ($groups[0] as $extract)
    {
        $debugArr = [];
        $emotion = null;
        $url = $extract;

        if ( !(strpos($extract, '?') === false) )
        {
            continue;
        }

        if (substr($url, -1) == '/')
        {
            $url = substr($url, 0, strlen($url) -1);
        }

        $url = preg_replace('/\//', '-', $url);
        $debugArr['url'] = $url;

        if (file_exists($path . '/WOTippsEmotion/' . $url . '.wotipps.emotion'))
        {
            $debugArr['src'] = 'file';
            $emotion = file_get_contents($path . '/WOTippsEmotion/' . $url . '.wotipps.emotion');
        }
        else
        {
            if (substr($extract, 0, 4) != 'http')
            {
                if ($host === null) {
                    $hostMatches = null;
                    preg_match('/<link rel="canonical" href="(http[s]*:\/\/[a-zA-Z0-9\.\-]+)\//',
                        $input, $hostMatches);

                    if (isset($hostMatches[1])) {
                        $host = $hostMatches[1];
                        $debug['url'] = $host;
                    } else {
                        $host = '';
                        $debug['url'] = 'Host can not be determined';
                    }
                }
                $cUrl = $host . $extract;
            }
            else
            {
                $cUrl = $extract;
            }
            $debugArr['src'] = 'load';
            $debugArr['ajaxUrl'] = $cUrl;

            $ch = curl_init($cUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);

            if (curl_errno($ch) == 0)
            {
                file_put_contents($path . '/WOTippsEmotion/' . $url . '.wotipps.emotion', $result);
                $emotion = $result;
                $debugArr['msg'] = 'Saved to disk';
            }
            else
            {
                $debugArr['msg'] = curl_error($ch);
            }

            curl_close($ch);
        }

        if ($emotion != null)
        {
            $emotion = preg_replace('/product-slider--content/', 'product-slider--content wotipps-product-slider-hidden', $emotion);

            $template  = '<script type="text/template" class="wotipps-' . $url . ' wotipps-hidden">';
            $template .= $emotion;
            $template .= '</script>';

            $pattern = '/<div(?:\s)+class="emotion--wrapper"(?:\s)+data-controllerUrl="' .
                    preg_replace('/\//', '\/', $extract) .
                    '"(?:\s)+data-availableDevices="([0-9,]+)">/';

            $replacement = $template . '<div class="emotion--wrapper" data-controllerUrl="' .$extract .
                    '" data-availableDevices="$1" id="wotipps-emotion-container-' . $url . '">';
            $replacement .= <<<SCRIPT
<script type="text/javascript">
(function() {
const availableDevices = "$1";
const width = document.documentElement.clientWidth;
function show(i) {
    if (document.getElementById('wotipps-{$url}')) {
        document.getElementById('wotipps-emotion-container-{$url}').innerHTML = document.getElementById('wotipps-{$url}').innerHTML;
    }
}
if (width < 480 && availableDevices.indexOf('4') != -1) {show(1);}
if (width < 768 && availableDevices.indexOf('3') != -1 && width > 479) {show(2);}
if (width < 1024 && availableDevices.indexOf('2') != -1 && width > 767) {show(3);}
if (width < 1260 && availableDevices.indexOf('1') != -1 && width > 1023) {show(4);}
if (availableDevices.indexOf('0') != -1 && width > 1259) {show(5);}
}())
</script>
SCRIPT;


            $input = preg_replace($pattern, $replacement, $input);

            $output['pattern'] = $pattern;
            $output['replacement'] = $replacement;

            $debugArr['success'] = 'Emotion replaced';
            $debugArr['pattern'] = $pattern;
            //$debugArr['replacement'] = substr($replacement, 0, 200);
        }
        else
        {
            $debugArr['success'] = 'Emotion not found';
        }

        $debug['emotion'][] = $debugArr;
    }

    //$input = preg_replace('/<\/body>/', $html . '</body>', $input);

    return getOutput($input, $debug);
}

//echo smarty_outputfilter_h20170415('/shopware/51/widgets/emotion/index/emotionId/4/controllerName/index </body>');

function getOutput($input, $json)
{
    $string = json_encode($json);
    return str_replace('</body>', '<style>.wotipps-hidden{visibility:hidden;display:none;}</style><script>function wotippsDeb(){var a = ' . $string . ';}</script>', $input);
}
