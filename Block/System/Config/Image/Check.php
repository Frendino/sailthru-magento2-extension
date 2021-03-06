<?php

namespace Sailthru\MageSail\Block\System\Config\Image;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Sailthru\MageSail\Helper\ClientManager;

class Check extends \Magento\Config\Block\System\Config\Form\Field
{

    const CHECK_TEMPLATE = 'system/loimage.phtml';

    const LO_SUCCESS_MESSAGE = '<strong style="color:#3fb220;">Great!</strong> With Sailthru Onsite and the Purchase API configured, you are all set on the Magento end. You must activate a Sailthru Lifecycle Optimizer flow with the Cart Abandonment entry to engage with your non-converting users. To learn more about creating a cart abandonment flow, visit our <a href="https://getstarted.sailthru.com/email/lo/automate-abandoned-cart-reminders/" target="_blank">docs</a>.';

    const LO_ACTION_NEEDED_MESSAGE = '<strong style="color:#f49e42;">Action needed.</strong> You need to have the Sailthru JavaScript set up to use Lifecycle Optimizer for Abandoned Carts. Please go back to the setup tab and enter your Customer ID. Afterwards, you can return here to verify everything is working.';

    const LO_IMG_URL = 'https://getstarted.sailthru.com/wp-content/uploads/2017/08/Screen-Shot-2017-08-14-at-11.43.57-AM-1024x293.png';

    /** @var ClientManager  */
    private $clientManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ClientManager $clientManager,
        array $data = []
    ) {
        $this->clientManager = $clientManager;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::CHECK_TEMPLATE);
        }
        return $this;
    }

    /**
     * Render image
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the image url and label
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $usingJs = $this->clientManager->isJsEnabled();
        $data = array();
        if ($usingJs) {
            $data['markup'] = self::LO_SUCCESS_MESSAGE;
            $data['imgurl'] = self::LO_IMG_URL;
        }
        else {
            $data['markup'] = self::LO_ACTION_NEEDED_MESSAGE;
        }
        $this->addData($data);
        return $this->_toHtml();
    }
}
