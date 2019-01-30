<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class Club extends ClubDataItem
{

	/**
	 * @var string
	 */
	public $thuisshirtkleur;

	/**
	 * @var string
	 */
	public $thuisbroekkleur;

	/**
	 * @var string
	 */
	public $thuissokkenkleur;

	/**
	 * @var string
	 */
	public $uitshirtkleur;
	
	/**
	 * @var string
	 */
	public $uitbroekkleur;
	
	/**
	 * @var string
	 */
	public $uitsokkenkleur;
	
	/**
	 * code of the club (like a KNVB verenigingscode)
	 * @var string	
	 */
	public $clubcode;
	
	/**
	 * full name of the club
	 * @var string
	 */
	public $clubnaam;

	/**
	 * additional information
	 * @var string
	 */
	public $informatie;
	
	/**
	 * @var \DateTime
	 */
	public $oprichtingsdatum;

	/**
	 * @var \DateTime
	 */
	public $oprichtingsdatetime;
	
	/**
	 * @var string
	 */
	public $telefoonnummer;
	
	/**
	 * @var string
	 */
	public $fax;
	
	/**
	 * @var string
	 */
	public $email;
	
	/**
	 * @var string
	 */
	public $website;
	
	/**
	 * @var string
	 */
	public $straatnaam;
	
	/**
	 * @var string
	 */
	public $huisnummer;
	
	/**
	 * @var string
	 */
	public $nummertoevoeging;
	
	/**
	 * @var string
	 */
	public $postcode;
	
	/**
	 * @var string
	 */
	public $plaats;
	
	/**
	 * @var string
	 */
	public $banknummer;
	
	/**
	 * @var string
	 */
	public $tennamevan;
	
	/**
	 * @var string
	 */
	public $tennamevanplaats;
	
	/**
	 * @var string
	 */
	public $naamsecretaris;
	
	/**
	 * @var string
	 */
	public $kvknummer;
	
	/**
	 * url of the club logo (normal format)
	 * @var string|null	 
	 */
	public $logo;
	
	/**
	 * url of the club logo (small format)
	 * @var string|null
	 */
	public $kleinlogo;
	
	/**
	 * @var ClubAddress 
	 */
	private $visitingAddress;
	
	/**
	 * @param ClubData $api
	 */
	public function __construct(ClubData $api)
	{
		parent::__construct($api);
		$this->populate();
	}
	
	/**
	 * @return void
	 */
	protected function populate()
	{
		$response = $this->api->request('clubgegevens');
	
		if (isset($response) && isset($response["gegevens"])) {
			$this->api->map($response['gegevens'], $this);
		}
		if (isset($response) && isset($response["bezoekadres"])) {
			$this->visitingAddress = $this->api->map($response['bezoekadres'], new ClubAddress($this->api));
		}
	}
	
	/**
	 * @return ClubAddress
	 */
	public function getVisitingAddress() 
	{
		return $this->visitingAddress;
	}

}
