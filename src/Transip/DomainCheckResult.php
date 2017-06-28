<?php

namespace Transip;

/**
 * @author Perfacilis <support@perfacilis.com>
 * @version 0.1
 */
class DomainCheckResult
{

    const STATUS_INYOURACCOUNT = 'inyouraccount';
    const STATUS_UNAVAILABLE = 'unavailable';
    const STATUS_NOTFREE = 'notfree';
    const STATUS_FREE = 'free';
    const STATUS_INTERNALPULL = 'internalpull';
    const STATUS_INTERNALPUSH = 'internalpush';
    const ACTION_REGISTER = 'register';
    const ACTION_TRANSFER = 'transfer';
    const ACTION_INTERNALPULL = 'internalpull';
    const ACTION_INTERNALPUSH = 'internalpush';

    /**
     * The name of the Domain for which we have a status in this object
     *
     * @var string;
     */
    public $domainName;

    /**
     * The status for this domain, one of the DomainCheckResult::STATUS_* constants.
     *
     * @var string
     */
    public $status;

    /**
     * List of available actions to perform on this domain
     *
     * @var string[]
     */
    public $actions;

    public function __construct($domainName, $status, array $actions)
    {
        $this->domainName = $domainName;
        $this->status = $status;
        $this->actions = (array) $actions;
    }

}
