<?php
declare(strict_types=1);

namespace Smartmage\Inpost\Block\Adminhtml\System;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Module\ModuleListInterface;
class ModuleVersion extends AbstractBlock implements CommentInterface
{

    private ResourceInterface $resource;
    protected $_moduleList;
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        array $data = [],
        ResourceInterface $resource,
        ModuleListInterface $moduleList
    ){
        parent::__construct($context, $data);
        $this->resource = $resource;
        $this->_moduleList = $moduleList;
    }

    public function getCommentText($elementValue)
    {
        $moduleCode = 'Smartmage_Inpost';
        $moduleInfo = $this->_moduleList->getOne($moduleCode);
        $moduleXmlVersion = $moduleInfo['setup_version'];
        $moduleDbVersion = $this->resource->getDbVersion($moduleCode);
        $result = __('Module version') . ': ' . $moduleDbVersion;
        if($moduleXmlVersion != $moduleDbVersion) {
            $result .= ' (' . __('setup:upgrade required, module.xml version') .  ': ' . $moduleXmlVersion . ')';
        }
        return $result;
    }
}
