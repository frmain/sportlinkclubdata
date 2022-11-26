<?php
namespace SportlinkClubData;



/**
 * Sportlink ClubsManager top-level class
 *
 */
class ClubsManager
{

	/** @var array $keys	clientIDs sportlink  */
	protected $keys;
	
	/** @var ClubManager[] $clubmanagers  */
	protected $clubmanagers;
	
	/** @var Option[] */
	protected $options_sortorder = [];
	
	/** @var Option[] */
	protected $options_leagueperiod = [];
	
	/** @var Option[] */
	protected $options_leaguetype = [];
	
	/** @var Option[] */
	protected $options_daytype = [];
	
	/** @var Option[] */
	protected $options_invoicestatus = [];
	
	/** @var Option[] */
	protected $options_sex = [];
	
	/** @var Option[] */
	protected $options_agecategory = [];
	
	/** @var Option[] */
	protected $options_matchtype = [];
	
	/** @var Option[] */
	protected $options_teamrole = [];
	
	/** @var Option[] */
	protected $options_teamtype = [];
	
	
	/**
	 * @param array $keys   client id's for sportlink api
	 */
	public function __construct($keys = [])
	{
		$this->keys = $keys;
		$index = 0;
		foreach($this->keys as $key) {
			$datamanager = new DataManager($this, $key);
			$clubmanager = new ClubManager($datamanager);
			$clubmanager->index = $index;
			$this->clubmanagers[] = $clubmanager;
			$index++;
		}
	}
	
	
	/**
	 * Get the ClubManagers contained in the ClubsManager
	 *
	 * @return ClubManager[]
	 */
	public function getClubManagers()
	{
		return $this->clubmanagers;
	}
	
	/**
	 * Find the index of clientid in keys array (in order to hide the clientid)
	 *
	 * @return integer
	 */
	public function getClubIndex($key)
	{
		
		return array_search($key, $this->keys);
	}
	
	/**
	 * Find the ClubManager belonging to the clubindex
	 *
	 * @return ClubManager
	 */
	public function getClubManager($clubindex)
	{
		if (array_key_exists($clubindex, $this->clubmanagers)) {
			return $this->clubmanagers[$clubindex];
		} else {
			return null;
		}
	}
	
	/**
	 * Get the clubs 
	 *
	 * @return Club[]
	 */
	public function getClubs()
	{
		foreach ($this->clubmanagers as $clubmanager) {
			$club = $clubmanager->getClub();
			$club->clubindex = $clubmanager->index;
			$clubs[] = $club;
		}
			
		return $clubs;
	}
	
	/**
	 * Get main club
	 *
	 * @return Club
	 */
	public function getMainClub()
	{
		return $this->getClubs()[0];
	}
	
	
	/**
	 * Get the clubcodes (of all clubs belonging to main club)
	 *
	 * @return array string clubcodes
	 */
	public function getClubcodes() 
	{
		$clubcodes = array();
		foreach ($this->getClubs() as $club) {
			$clubcodes[] = $club->clubcode;
		}
		return $clubcodes;
	}
	
	/**
	 * Get a list of leagues in which teams of the managed clubs are participating
	 * 
	 * @param boolean $regularonly only teams in a *regular* competition are returned
	 * @param boolean $allperiods all periods are returned, else only current period is returned
	 * @return League[][] array of teams, keyed by *teamcode*, holding array of leagues, keyed by *poulecode*
	 */
	public function getLeagues($regularonly=false, $allperiods=true)
	{
		$leagues = [];
		foreach ($this->clubmanagers as $clubmanager) {
			$leagues = $leagues + $clubmanager->getLeagues($regularonly, $allperiods);
		}
		
		return $leagues;
	}

	/**
	 * Get a combined list of teams of the clubs
	 * 
	 * @param boolean $full Should Team objects be populated with all data? Otherwise only main fields are filled
	 *
	 * @return Team[] array of teams
	 */
	public function getTeams($full=false)
	{
		$teams = [];
		foreach ($this->clubmanagers as $clubmanager) {
			$teams = $teams + $clubmanager->getTeams($full);
		}
		
		return $teams;
	}
	
	
	
	/**
	 * Get scheduled matches for the clubs, sorted on date and time
	 *
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset (default: 30)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, 1 = next week, 2 = ...)  (default: 0) 
	 * @param integer $teamcode return only matches from team
	 * @param integer $localteamcode return only matches from localteam
	 * @param boolean $onlyownteam return only matches from own League
	 * @param string $sortorder (use options_sortorder)
	 * @param integer $rowcount max number of rows to return (default: 100) 
	 * @param boolean $home return home matches
	 * @param boolean $away return away matches
	 * @param string $matchtype (use options_matchtype)
	 * @param string $leaguetype (use options_leaguetype)
	 * @param string $daytype (use options_daytype)
	 * @param string $agecategory (use options_agecategory)
	 * @return ClubMatch[]
	 */
	public function getSchedule($daysahead=null, $weekoffset=null, $teamcode=null, $localteamcode=null, $onlyownteam=false, $rowcount=null, $home=true, $away=true, $matchtype=null, 
			$leaguetype=null, $daytype=null, $agecategory=null)
	{

		$matches = [];
		foreach ($this->clubmanagers as $clubmanager) {
			$matches = $matches + $clubmanager->getSchedule($daysahead, $weekoffset, $teamcode, $onlyownteam, null, $rowcount, $home, $away, $matchtype,
				$leaguetype, $daytype, $agecategory);
		}
		
		array_multisort(array_column($matches, 'wedstrijddatum'), SORT_ASC,	$matches);
		
		return $matches;
	}

	/**
	 * Get cancelled matches for the clubs
	 *
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset (default: 30)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, 1 = next week, 2 = ...)  (default: 0)
	 * @param string $sortorder (use options_sortorder, default: datum)
	 * @param integer $rowcount max number of rows to return (default: 20)
	 * @return ClubMatchCancellation[]
	 */
	public function getCancellations($daysahead=null, $weekoffset=null, $rowcount=null)
	{
		$matches = [];
		foreach ($this->clubmanagers as $clubmanager) {
			$matches = $matches + $clubmanager->getCancellations($daysahead, $weekoffset, null, $rowcount);
		}
		
		array_multisort(array_column($matches, 'wedstrijddatum'), SORT_ASC, $matches);

		return $matches;
	}
	
	/**
	 * Get results of played matches of the clubs, sorted on date and time
	 *
	 * @param integer $daysahead return only matches maximum x days ahead relative to $weekoffset (default: 7)
	 * @param integer $weekoffset return matches from week y relative to current week (0 = this week, 1 = next week, 2 = ...)  (default: -1)
	 * @param integer $teamcode return only matches from team
	 * @param integer $localteamcode return only matches from team
	 * @param boolean $onlyownteam return only matches from own League
	 * @param string $sortorder (use options_sortorder)
	 * @param integer $rowcount max number of rows to return (default: 100)
	 * @param string $matchtype (use options_matchtype)
	 * @return ClubMatchResult[]
	 */
	public function getResults($daysahead=null, $weekoffset=null, $teamcode=null, $localteamcode=null, $onlyownteam=false, $rowcount=null, 
			$matchtype=null, $agecategory=null)
	{
		$matches = [];
		foreach ($this->clubmanagers as $clubmanager) {
			$matches = $matches + $clubmanager->getResults($daysahead, $weekoffset, $teamcode, $localteamcode, $onlyownteam, null, $rowcount,
				$matchtype, $agecategory);
		}
		
		array_multisort(array_column($matches, 'wedstrijddatum'), SORT_DESC, 
						$matches);

		return $matches;
	}
	


	/**
	 * Get optionlist
	 *
	 * @param string $listtype indicate optionlist
	 * @return Option[]
	 */
	protected function getOptions($listtype)
	{
		$response = $this->request('keuzelijst-' . $listtype);
	
		$options = array();
		foreach($response as $item){
			$options[] = $this->map($item, new Option($this));
		}
	
		return $options;
	}
	
	/**
	 * Get optionlist sortorder
	 *
	 * @return OptionSortorder[]
	 */
	public function getOptionsSortorder()
	{
		if ($this->options_sortorder) {
			return $this->options_sortorder;
		}
	
		$response = $this->request('keuzelijst-sorteervolgordes');
	
		$this->options_sortorder = array();
		foreach($response as $item){
			$this->options_sortorder[] = $this->map($item, new OptionSortorder($this));
		}
	
		return $this->options_sortorder;
	}
	
	/**
	 * Get optionlist leagueperiod
	 *
	 * @return Option[]
	 */
	public function getOptionsLeaguePeriod()
	{
		if ($this->options_leagueperiod) {
			return $this->options_leagueperiod;
		}
	
		$this->options_leagueperiod = $this->getOptions('competitieperiode');
		return $this->options_leagueperiod;
	}
	
	/**
	 * Get optionlist leaguetype
	 *
	 * @return Option[]
	 */
	public function getOptionsLeagueType()
	{
		if ($this->options_leaguetype) {
			return $this->options_leaguetype;
		}
	
		$this->options_leaguetype = $this->getOptions('competitiesoorten');
		return $this->options_leaguetype;
	}
	
	/**
	 * Get optionlist daytype
	 *
	 * @return Option[]
	 */
	public function getOptionsDayType()
	{
		if ($this->options_daytype) {
			return $this->options_daytype;
		}
	
		$this->options_daytype = $this->getOptions('dagsoorten');
		return $this->options_daytype;
	}
	
	/**
	 * Get optionlist invoicestatus
	 *
	 * @return Option[]
	 */
	public function getOptionsInvoiceStatus()
	{
		if ($this->options_invoicestatus) {
			return $this->options_invoicestatus;
		}
	
		$this->options_invoicestatus = $this->getOptions('factuurstatussen');
		return $this->options_invoicestatus;
	}
	
	/**
	 * Get optionlist sex
	 *
	 * @return Option[]
	 */
	public function getOptionsSex()
	{
		if ($this->options_sex) {
			return $this->options_sex;
		}
	
		$this->options_sex = $this->getOptions('geslacht');
		return $this->options_sex;
	}
	
	/**
	 * Get optionlist agecategory
	 *
	 * @return Option[]
	 */
	public function getOptionsAgeCategory()
	{
		if ($this->options_agecategory) {
			return $this->options_agecategory;
		}
	
		$this->options_agecategory = $this->getOptions('leeftijdscategorieen');
		return $this->options_agecategory;
	}
	
	/**
	 * Get optionlist matchtype
	 *
	 * @return Option[]
	 */
	public function getOptionsMatchType()
	{
		if ($this->options_matchtype) {
			return $this->options_matchtype;
		}
	
		$this->options_matchtype = $this->getOptions('spelsoorten');
		return $this->options_matchtype;
	}
	
	/**
	 * Get optionlist teamrole
	 *
	 * @return Option[]
	 */
	public function getOptionsTeamRole()
	{
		if ($this->options_teamrole) {
			return $this->options_teamrole;
		}
	
		$this->options_teamrole = $this->getOptions('teampersoonrollen');
		return $this->options_teamrole;
	}
	
	/**
	 * Get optionlist teamtype
	 *
	 * @return Option[]
	 */
	public function getOptionsTeamType()
	{
		if ($this->options_teamtype) {
			return $this->options_teamtype;
		}
	
		$this->options_teamtype = $this->getOptions('teamsoorten');
		return $this->options_teamtype;
	}

}
