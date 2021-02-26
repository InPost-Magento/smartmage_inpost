<?php

namespace Smartmage\Inpost\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\View\Helper\SecureHtmlRenderer;


/**
 * Class DefaultSendingPoint
 * @package Smartmage\Inpost\Block\Adminhtml\Config\Form\Field
 */
class DefaultSendingPoint  extends Field
{
    protected $code = '';

    public function __construct(
        Context $context,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null,
        $code = null
    )
    {
        parent::__construct($context, $data, $secureRenderer);
        $this->code = $code;
    }

    /**
     * @inheritDoc
     */
    public function render(AbstractElement $element)
    {

       $html = parent::render($element);
        return $html . '
<script type="text/javascript">
window.easyPackAsyncInit = function () {
                    easyPack.init({
              instance: "pl",
              mapType: "osm",
              searchType: "osm",
              points: {
                types: ["parcel_locker", "pop"],
              },
              map: {
                useGeolocation: true,
                initialTypes: ["parcel_locker", "pop"]
              }
            })
                };

            window.onload = function() {
              easyPack.dropdownWidget("row_carriers_inpostlocker_' . $this->code .'_default_sending_point", function(point) {
                  jQuery("#carriers_inpostlocker_' . $this->code . '_default_sending_point").val(point.name);
              });
            }
          </script>
';
    }


}
