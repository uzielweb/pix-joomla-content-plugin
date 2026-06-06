<?php
namespace PontoMega\Plugin\Content\Pix\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Language\Text;
use Joomla\Event\SubscriberInterface;

class Pix extends CMSPlugin implements SubscriberInterface {
    protected $autoloadLanguage = true;
    
    public static function getSubscribedEvents(): array {
        return [
            'onContentPrepare' => 'onContentPrepare',
        ];
    }

    public function onContentPrepare(ContentPrepareEvent $event) {
        $context = $event->getContext();
        $row     = $event->getItem();

        // Check if $row is an object and has a text property
        if (!\is_object($row) || !property_exists($row, 'text') || \is_null($row->text)) {
            return;
        }

        $regex = '/\{pix:(?:([^|]+)\|)?([^|]+)\|([^}]+)\}/i';

        if (preg_match_all($regex, $row->text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $fullTag = $match[0];
                $currency = !empty($match[1]) ? $match[1] : 'BRL';
                $amount = (float)$match[2];
                $message = $match[3];

                $html = $this->renderPix($amount, $message, $currency);
                $row->text = str_replace($fullTag, $html, $row->text);
            }
        }
        return true;
    }

    private function renderPix($amount, $message, $currency = 'BRL') {
        $wa = \Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->registerAndUseStyle('plg_content_pix', 'media/plg_content_pix/css/pix.css');

        $pixKey = $this->params->get('pix_key', '');
        $name   = $this->params->get('merchant_name', '');
        $city   = $this->params->get('merchant_city', '');

        if (empty($pixKey)) {
            return '<!-- PIX Plugin: ' . Text::_('PLG_CONTENT_PIX_ERROR_NOT_CONFIGURED') . ' -->';
        }

        $payload = $this->generatePayload($pixKey, $name, $city, $amount, $message);
        $formattedAmount = number_format($amount, 2, ',', '.');
        $uniqueId = 'pix_' . substr(md5($payload . uniqid()), 0, 10);
        
        $scriptUrl = \Joomla\CMS\Uri\Uri::root(true) . '/media/plg_content_pix/js/qrcode.min.js';

        return '
        <div class="pontomega-pix-container">
            <div class="pontomega-pix-title">' . Text::sprintf('PLG_CONTENT_PIX_TEXT_PIX_AMOUNT', $currency . ' ' . $formattedAmount) . '</div>
            <div class="pontomega-pix-description">' . htmlspecialchars($message) . '</div>
            
            <div id="' . $uniqueId . '" class="pontomega-pix-qrcode"></div>
            
            <div class="pontomega-pix-button">
                <button onclick="copyPixPayload(\'' . $payload . '\', this)" class="btn btn-primary">' . Text::_('PLG_CONTENT_PIX_TEXT_COPY_CODE') . '</button>
            </div>
            
            <script>
                (function() {
                    const render = () => {
                        const el = document.getElementById("' . $uniqueId . '");
                        if (el) {
                            el.innerHTML = "";
                            new QRCode(el, { text: "' . $payload . '", width: 250, height: 250 });
                        }
                    };
                    
                    if (typeof QRCode === "undefined") {
                        if (!document.getElementById("qrcode_js")) {
                            var script = document.createElement("script");
                            script.id = "qrcode_js";
                            script.src = "' . $scriptUrl . '";
                            script.onload = () => {
                                document.dispatchEvent(new Event("qrcode_loaded"));
                            };
                            document.head.appendChild(script);
                        }
                        document.addEventListener("qrcode_loaded", render);
                    } else {
                        render();
                    }

                    if(typeof window.copyPixPayload === "undefined") {
                        window.copyPixPayload = function(p, b) {
                            navigator.clipboard.writeText(p).then(() => {
                                const t = b.innerText; b.innerText = "' . Text::_('PLG_CONTENT_PIX_TEXT_COPIED') . '"; b.style.background = "#008c32";
                                setTimeout(() => { b.innerText = t; b.style.background = "#00b140"; }, 2000);
                            });
                        };
                    }
                })();
            </script>
        </div>';
    }

    private function generatePayload($key, $name, $city, $amount, $message) {
        // Sanitize txtId for Field 62-05 (alphanumeric only, max 25 chars)
        $txtId = preg_replace('/[^A-Za-z0-9]/', '', $message);
        if (empty($txtId)) $txtId = '***';
        $txtId = substr($txtId, 0, 25);

        // PIX Message (Field 26 subfield 02) - Can contain spaces
        $description = substr($message, 0, 40); // Max 40 chars recommended

        $payload = "000201"; 
        
        $gui = "0014br.gov.bcb.pix";
        $keyFormatted = "01" . str_pad(strlen($key), 2, '0', STR_PAD_LEFT) . $key;
        $descFormatted = "02" . str_pad(strlen($description), 2, '0', STR_PAD_LEFT) . $description;
        
        $merchantAccount = "26" . str_pad(strlen($gui . $keyFormatted . $descFormatted), 2, '0', STR_PAD_LEFT) . $gui . $keyFormatted . $descFormatted;
        $payload .= $merchantAccount;

        $payload .= "52040000"; 
        $payload .= "5303986";  
        
        if ($amount > 0) {
            $amountStr = number_format($amount, 2, '.', '');
            $payload .= "54" . str_pad(strlen($amountStr), 2, '0', STR_PAD_LEFT) . $amountStr;
        }

        $payload .= "5802BR"; 
        $payload .= "59" . str_pad(strlen($name), 2, '0', STR_PAD_LEFT) . $name;
        $payload .= "60" . str_pad(strlen($city), 2, '0', STR_PAD_LEFT) . $city;
        
        $txtIdFormatted = "05" . str_pad(strlen($txtId), 2, '0', STR_PAD_LEFT) . $txtId;
        $payload .= "62" . str_pad(strlen($txtIdFormatted), 2, '0', STR_PAD_LEFT) . $txtIdFormatted;

        $payload .= "6304"; 
        $payload .= $this->crc16($payload);
        
        return $payload;
    }

    private function crc16($data) {
        $polynomial = 0x1021;
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
