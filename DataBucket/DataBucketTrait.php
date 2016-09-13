<?php

namespace SAPF\DataBucket;

trait DataBucketTrait
{

    /**
     * @var \SAPF\DataBucket\DataBucket 
     */
    protected $_dataBucket = false;

    /**
     * Element's DataBucket (for eg. to store some params for helpers)
     * @return \SAPF\DataBucket\DataBucket
     */
    public function bucket()
    {
        if ($this->_dataBucket == false) {
            $this->_dataBucket = new \SAPF\DataBucket\DataBucket();
        }
        return $this->_dataBucket;
    }

}
