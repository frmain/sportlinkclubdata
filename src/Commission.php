<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class Commission extends ClubDataItem
{
	/**
	 * @var string
	 */
	public $commissiecode;
	
	/**
	 * @var string
	 */
	public $commissienaam;
	
	/**
	 * @var string
	 */
	public $omschrijving;
	
	/**
	 * @var string|null
	 */
	public $foto;
	
	/**
	 * @var string|null
	 */
	public $opmerkingen;
	
	/**
	 * @var string|null
	 */
	public $telefoon;
	
	/**
	 * @var string|null
	 */
	public $mobiel;
	
	/**
	 * @var string|null
	 */
	public $email;
	
	/**
	 * @var CommissionMember[]
	 */
	protected $members;
	
	/**
	 * @return CommissionMember[]
	 */
	public function getMembers($withphoto=false)
	{
	    $params['commissiecode'] = $this->commissiecode;
	    if ($withphoto) $params['toonlidfoto'] = 'JA';
	    
	    $response = $this->api->request('commissie-leden', $params);
	    
	    $this->members = array();
	    foreach($response as $item){
	        /** @var CommissionMember $member */
	        $member = $this->api->map($item, new CommissionMember($this->api));
	        $member->private = (stripos($item['lid'], "AFGESCHERMD")!==false); // there is no better way to do this unfortunately
	        $this->members[] = $member;
	    }
	    
	    return $this->members;
	}
	
	
}