<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class RangeCore extends ObjectModel
{
	public $id_carrier;
	public $delimiter1;
	public $delimiter2;

	protected static $range_table;
	protected static $range_identifier;

	protected $webserviceParameters = array(
			'objectsNodeName' => 'price_ranges',
			'objectNodeName' => 'price_range',
			'fields' => array(
				'id_carrier' => array('xlink_resource' => 'carriers'),
			)
	);

	/**
	* Get all available price ranges
	*
	* @return array Ranges
	*/
	public static function getRanges($id_carrier)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * 
			FROM `'._DB_PREFIX_.static::$range_table.'` 
			WHERE `id_carrier` = '.(int)$id_carrier.'
			ORDER BY `delimiter1` ASC');
	}
	
	public static function rangeExist($id_carrier, $delimiter1, $delimiter2)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT count(*)
			FROM `'._DB_PREFIX_.static::$range_table.'`
			WHERE `id_carrier` = '.(int)$id_carrier.'
			AND `delimiter1` = '.(float)$delimiter1.' AND `delimiter2`='.(float)$delimiter2);
	}
	
	public static function isOverlapping($id_carrier, $delimiter1, $delimiter2, $id_rang = null)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT count(*)
			FROM `'._DB_PREFIX_.static::$range_table.'`
			WHERE `id_carrier` = '.(int)$id_carrier.'
			AND ((`delimiter1` >= '.(float)$delimiter1.' AND `delimiter1` < '.(float)$delimiter2.')
			    OR (`delimiter2` > '.(float)$delimiter1.' AND `delimiter2` < '.(float)$delimiter2.')
			    OR ('.(float)$delimiter1.' > `delimiter1` AND '.(float)$delimiter1.' < `delimiter2`)
			    OR ('.(float)$delimiter2.' < `delimiter1` AND '.(float)$delimiter2.' > `delimiter2`)
			    )
			'.(!is_null($id_rang) ? ' AND `'.pSQL(static::$range_identifier).'` != '.(int)$id_rang : ''));
	}
}