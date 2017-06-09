<?php

/**
 * Class FactoryX_CouponValidation_Block_Validation
 */
class FactoryX_CouponValidation_Block_Validation extends Mage_Core_Block_Template
{
    /**
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('coupon/validation/validate');
    }

    /**
     * @return string
     */
    public function getRedeemUrl()
    {
        return $this->getUrl('coupon/validation/redeemPost');
    }

}