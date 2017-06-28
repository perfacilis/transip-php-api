<?php

namespace Transip;

/**
 * @author Perfacilis <support@perfacilis.com>
 * @version 0.1
 */
class DomainService extends Rest
{

    // URI's used to build Endpoint URL's
    const URI_BATCHCHECKAVAILABILITY = 'domain-availability';
    const URI_CHECKAVAILABILITY = 'domain-availability/%1$s';
    const URI_WHOIS = 'domains/%1$s/whois';
    const URI_DOMAINNAMES = 'domains';
    const URI_INFO = 'domains/%1$s';
    const URI_REGISTER = 'domains/%1$s';

    public function __construct(\TransIP_AccessToken $accesstoken)
    {
        parent::__construct($accesstoken);
    }

    /**
     * Checks the availability of multiple domains.
     * 
     * @param array $domain_names
     * @return DomainCheckResult[]
     */
    public function batchCheckAvailability(array $domain_names)
    {
        $response = $this->get(self::URI_BATCHCHECKAVAILABILITY, [
            'domain_names' => implode(',', $domain_names),
        ]);

        if (!isset($response['availability'])) {
            return [];
        }

        $availability = [];
        foreach ($response['availability'] as $domain) {
            $availability[] = new DomainCheckResult($domain['domainName'], $domain['status'], $domain['actions']);
        }

        return $availability;
    }

    /**
     * Checks the availability of a domain.
     * 
     * @param string $domain_name
     * @return DomainCheckResult[]
     */
    public function checkAvailability(string $domain_name)
    {
        $uri = sprintf(self::URI_CHECKAVAILABILITY, $domain_name);
        $response = $this->get($uri);

        $domain = $response['availability'];
        return new DomainCheckResult($domain['domainName'], $domain['status'], $domain['actions']);
    }

    /**
     * Gets the whois of a domain name
     * 
     * @param string $domain_name
     * @return string
     */
    public function getWhois(string $domain_name)
    {
        $uri = sprintf(self::URI_WHOIS, $domain_name);
        $response = $this->get($uri);

        return isset($response['whois']) ? $response['whois'] : '';
    }

    /**
     * Gets the names of all domains in your account.
     *
     * @return Domain[]
     */
    public function getDomainNames()
    {
        $response = $this->get(self::URI_DOMAINNAMES);
        if (!isset($response['domains'])) {
            return [];
        }

        $domains = [];
        foreach ($response['domains'] as $result) {
            $domain = new Domain($result['name']);
            $domain->authCode = $result['authCode'];
            $domain->isLocked = $result['isLocked'];
            $domain->registrationDate = $result['registrationDate'];
            $domain->renewalDate = $result['renewalDate'];

            $domains[] = $domain;
        }

        return $domains;
    }

    /**
     * Get information about a domainName.
     *
     * @param string $domain_name
     */
    public function getInfo(string $domain_name)
    {
        $uri = sprintf(self::URI_INFO, $domain_name);
        $response = $this->get($uri);

        $result = $response['domain'];
        $domain = new Domain($result['name']);
        $domain->authCode = $result['authCode'];
        $domain->isLocked = $result['isLocked'];
        $domain->registrationDate = $result['registrationDate'];
        $domain->renewalDate = $result['renewalDate'];

        return $domain;
    }

    /**
     * Get information about a list of Domain names.
     * 
     * @param array $domain_names
     * @return Domain[]
     */
    public function batchGetInfo(array $domain_names)
    {
        $domains = [];
        foreach ($domain_names as $domain_name) {
            $domains[] = $this->getInfo($domain_name);
        }

        return $domains;
    }

    /**
     * Gets the Auth code for a domainName
     * 
     * @param string $domain_name
     * @return string Auth code, empty when fails
     */
    public function getAuthCode(string $domain_name)
    {
        $domain = $this->getInfo($domain_name);
        return $domain ? $domain->authCode : '';
    }

    /**
     * Gets the lock status for a domainName
     * 
     * @param string $domain_name
     */
    public function getIsLocked(string $domain_name)
    {
        $domain = $this->getInfo($domain_name);
        return $domain ? (bool) $domain->isLocked : false;
    }

    /**
     * Registers a domain name, will automatically create and sign a proposition for it
     * 
     * @param string $domain_name
     * @param Transip\WhoisContact[] $contacts
     * @param Transip\NameServer[] $nameservers
     * @param Transip\DnsEntry[] $dns_entries
     * @return bool True when registration was successfull
     */
    public function register($domain_name, array $contacts, array $nameservers, array $dns_entries)
    {
        $uri = sprintf(self::URI_REGISTER, $domain_name);
        $this->post($uri, [
            'contacts' => $contacts,
            'nameservers' => $nameservers,
            'dns_entries' => $dns_entries,
        ]);

        return $this->getHttpCode() === 200;
    }

    /**
     * Cancels a domain name, will automatically create and sign a cancellation document
     * Please note that domains with webhosting cannot be cancelled through the API
     *
     * @param string $domain_name
     */
    public function cancel(string $domain_name)
    {
        
    }

    /**
     * Transfers a domain with changing the owner, not all TLDs support this (e.g. nl)
     * 
     * @param \Transip\Domain $domain
     * @param string $auth_code
     */
    public function transferWithOwnerChange(Domain $domain, string $auth_code)
    {

    }

    /**
     * Transfers a domain without changing the owner
     * 
     * @param \Transip\Domain $domain
     * @param string $auth_code
     */
    public function transferWithoutOwnerChange(Domain $domain, string $auth_code)
    {

    }

    /**
     * Starts a nameserver change for this domain, will replace all existing nameservers with the new nameservers
     * 
     * @param string $domain_name
     * @param \Transip\Nameserver[] $nameservers
     */
    public function setNameservers(string $domain_name, array $nameservers)
    {
        
    }

    /**
     * Lock this domain in real time
     *
     * @param string $domain_name
     */
    public static function setLock(string $domain_name)
    {

    }

    /**
     * unlocks this domain in real time
     *
     * @param string $domain_name
     */
    public static function unsetLock(string $domain_name)
    {

    }

    /**
     * Sets the DnEntries for this Domain, will replace all existing dns entries with the new entries
     * 
     * @param string $domain_name
     * @param \Transip\DnsEntry[] $dns_entries
     */
    public function setDnsEntries(string $domain_name, array $dns_entries)
    {

    }

    /**
     * Starts an owner change of a Domain, brings additional costs with the following TLDs:
     * .be
     *
     * @param string $domain_name
     * @param \Transip\WhoisContact $registrant_whois_contact
     */
    public function setOwner(string $domain_name, WhoisContact $registrant_whois_contact)
    {
        
    }

    /**
     * Starts a contact change of a domain, this will replace all existing contacts
     * 
     * @param string $domain_name
     * @param \Transip\WhoisContact[] $contacts
     */
    public function setContacts(string $domain_name, array $contacts)
    {
        
    }

    /**
     * Get TransIP supported TLDs
     *
     * @return \Transip\Tld[] Array of Tld objects
     */
    public function getAllTldInfos()
    {
        
    }

    /**
     * Get info about a specific TLD
     * 
     * @param string $tld_name
     * @return \Transip\Tld Tld object with info about this Tld
     */
    public function getTldInfo(string $tld_name)
    {
        
    }

    /**
     * Gets info about the action this domain is currently running
     *
     * @param string $domain_name
     */
    public function getCurrentDomainAction(string $domain_name)
    {
        
    }

    /**
     * Retries a failed domain action with new domain data. The Domain#name field must contain
     * the name of the Domain, the nameserver, contacts, dnsEntries fields contain the new data for this domain.
     * Set a field to null to not change the data.
     *
     * @param \Transip\Domain $domain
     */
    public function retryCurrentDomainActionWithNewData(Domain $domain)
    {
        
    }

    /**
     * Retry a transfer action with a new authcode
     * 
     * @param \Transip\Domain $domain
     * @param string $new_auth_code
     */
    public function retryTransferWithDifferentAuthCode(Domain $domain, string $new_auth_code)
    {
        
    }

    /**
     * Cancels a failed domain action
     * 
     * @param \Transip\Domain $domain
     */
    public function cancelDomainAction(Domain $domain)
    {
        
    }

}
