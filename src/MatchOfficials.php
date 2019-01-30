<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class MatchOfficials extends ClubDataItem
{

	/**
	 * @var integer
	 */
	private $wedstrijdcode;
	
	/**
	 * @var string|null
	 */
	public $verenigingsscheidsrechtercode;
	
	/**
	 * @var string|null
	 */
	public $overigeofficialcode;
	
	/**
	 * @var string|null
	 */
	public $vereningsscheidsrechter;
	
	
	/**
	 * @var string|null
	 */
	public $overigeofficial;

	protected $officials;

	/**
	 * @param ClubData $api
	 * @param integer $wedstrijdcode
	 */
	public function __construct(ClubData $api, $wedstrijdcode)
	{
		parent::__construct($api);
		$this->wedstrijdcode=$wedstrijdcode;
	}
	
	/**
	 * Get the list of officials
	 *
	 * @return MatchOfficial[]
	 */
	public function getOfficials()
	{
		// for efficiency, no need to request officials when already available
		if ($this->officials) {
			return $this->officials;
		}
	
		$params['wedstrijdcode'] = $this->wedstrijdcode;
		$response = $this->api->request('wedstrijd-officials', $params);
	
		$this->officials = array();
		foreach($response as $item){
			/** @var MatchOfficial $official */
			$official = $this->api->map($item, new MatchOfficial($this->api));
			$this->officials[] = $official;
		}
	
		return  $this->officials;
	}
	
}