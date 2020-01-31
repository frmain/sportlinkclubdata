<?php
namespace SportlinkClubData;


/**
 * @author Foeke
 *
 */
class Player extends ClubDataItem
{

	/**
	 * @var string|null
	 */
	public $relatiecode;
	
	/**
	 * @var string
	 */
	public $naam;
	
	/**
	 * @var string|null
	 */
	public $rol;
	
	/**
	 * @var string|null
	 */
	public $functie;
	
	/**
	 * @var string|null
	 */
	public $einddatum;
	
	/**
	 * @var string|null
	 */
	public $email;
	
	/**
	 * @var string|null
	 */
	public $email2;
	
	/**
	 * @var string|null
	 */
	public $telefoon;
	
	/**
	 * @var string|null
	 */
	public $telefoon2;
	
	/**
	 * @var string|null
	 */
	public $mobiel;
	
	/**
	 * @var string|null
	 */
	public $foto;
	
	/**
	 * @deprecated
	 * boolean $private calculated field when naam=="Afgeschermd"
	 * @var boolean|null
	 */
	public $private;
	
	/**
	 * function to determine if name of player is protected by user
	 * @return boolean
	 */
	public function getPlayerPrivate() {
		return $this->getPrivate($this->naam);
	}
	
}