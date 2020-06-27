<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Controller\Adminhtml\Recipient\Street;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var ResultFactory
     */
    private $resultPageFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param ResultFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ResultFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit Recipient Address.
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        return $this->resultPageFactory->create(ResultFactory::TYPE_PAGE);
    }
}
