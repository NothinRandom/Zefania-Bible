<?php
/**                               ______________________________________________
*                          o O   |                                              |
*                 (((((  o      <  Generated with Cook           (100% Vitamin) |
*                ( o o )         |______________________________________________|
* --------oOOO-----(_)-----OOOo---------------------------------- www.j-cook.pro --- +
* @version		1.6
* @package		ZefaniaBible
* @subpackage	Zefaniabiblebooknames
* @copyright	Missionary Church of Grace
* @author		Andrei Chernyshev - www.missionarychurchofgrace.org - andrei.chernyshev1@gmail.com
* @license		GNU/GPL
*
* /!\  Joomla! is free software.
* This version may have been modified pursuant to the GNU General Public License,
* and as distributed it includes or is derivative of works licensed under the
* GNU General Public License or other free or open source software licenses.
*
*             .oooO  Oooo.     See COPYRIGHT.php for copyright notices and details.
*             (   )  (   )
* -------------\ (----) /----------------------------------------------------------- +
*               \_)  (_/
*/
defined('_JEXEC') or die('Restricted access');
JHTML::stylesheet('verse.css', 'modules/mod_verseoftheday/css/'); 
$cls_verse_of_day = new ZefVerseOfTheDay($params);
class ZefVerseOfTheDay
{
	private $str_bible_alias;
	private $str_start_date;
	private $arr_db_call_info;
	private $biblePath;
	private $str_today;
	private $int_day_diff;
	private $int_max_verses;
	private $int_verse_remainder;
	private $arr_verse_info;
	private $str_xml_book_file;
	private $int_link_type;
	private $int_display_order;
	public $str_menuItem;
	public function __construct($params)
	{
		/*
			a = bible
			b = book
			c = chapter
			d = verse
		*/		
		$this->str_bible_alias = $params->get('bibleAlias', $this->fnc_first_bible_record());
		$this->int_link_type = $params->get('link_type', 0);
		$this->int_display_order = $params->get('display_order', 0);
		$this->str_menuItem = $params->get('vd_mo_menuitem', 0);
		
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
		JFactory::getLanguage()->load('com_zefaniabible', 'components/com_zefaniabible', null, true);
		
		$jlang = JFactory::getLanguage();
		$jlang->load('mod_verseoftheday', JPATH_COMPONENT, 'en-GB', true);
		$jlang->load('mod_verseoftheday', JPATH_COMPONENT, null, true);
				
		$this->str_start_date = new DateTime($params->get('start_date'));	
		// time zone offset.
		$config = JFactory::getConfig();
		date_default_timezone_set($config->get('offset'));	

		$this->str_today = new DateTime(date('Y-m-d'));
		$this->int_day_diff = round(abs($this->str_today->format('U') - $this->str_start_date->format('U')) / (60*60*24));	
		
		$this->fnc_Get_Verse_Of_The_Day_Info();
		if($this->int_display_order == 0)
		{
			$this->int_verse_remainder = $this->int_day_diff % $this->int_max_verses;
		}
		else
		{
			$this->int_verse_remainder = mt_rand(1, $this->int_max_verses);
		}
		if($this->int_verse_remainder == 0)
		{
			$this->int_verse_remainder = $this->int_max_verses;
		}
		$this->params = JComponentHelper::getParams( 'com_zefaniabible' );
		$this->biblePath = $this->params->get('xmlBiblesPath', 'media/com_zefaniabible/bibles/');
		$this->arr_db_call_info = $this->fnc_Get_Bible_Book_Info();
		$this->fnc_Get_Bible_Book_XML_File();	
	}
	protected function fnc_Get_Bible_Book_XML_File()
	{
		try
		{
			$db = JFactory::getDBO();
			$query = "SELECT a.book_id, a.chapter_id, a.verse_id, a.verse FROM `#__zefaniabible_bible_text` AS a".
					" INNER JOIN `#__zefaniabible_bible_names` AS b ON a.bible_id = b.id".
					" WHERE a.bible_id=".$this->arr_db_call_info['int_id']." AND a.book_id=".
					$this->arr_verse_info['book_name'][$this->int_verse_remainder] ." AND a.chapter_id=".
					$this->arr_verse_info['chapter_number'][$this->int_verse_remainder];
					if($this->arr_verse_info['end_verse'][$this->int_verse_remainder] != 0)
					{
						$query = $query ." AND a.verse_id>=".$this->arr_verse_info['begin_verse'][$this->int_verse_remainder]." AND a.verse_id<=".$this->arr_verse_info['end_verse'][$this->int_verse_remainder];
					}
					else
					{
						$query = $query ." AND a.verse_id=".$this->arr_verse_info['begin_verse'][$this->int_verse_remainder];
					}
					$query = $query ." ORDER BY a.book_id, a.chapter_id, a.verse_id";
			$db->setQuery($query);
			$data = $db->loadObjectList(); 
			$str_verse_output = '';
			
			foreach($data as $datum)
			{		
				if($this->arr_verse_info['end_verse'][$this->int_verse_remainder] == 0)
				{
					$str_temp = '<div class="zef_verse_of_day_header">'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$this->arr_verse_info['book_name'][$this->int_verse_remainder])." ".$this->arr_verse_info['chapter_number'][$this->int_verse_remainder].":"
					.$this->arr_verse_info['begin_verse'][$this->int_verse_remainder]."</div>";
					if($this->int_link_type == 1)
					{
						$str_verse_output = $str_verse_output. "<a rel='nofollow' title='".JText::_('MOD_ZEFANIABIBLE_VERSE_OF_THE_DAY_BIBLE_LINK_DESC')."' id='zef_links' href='".JRoute::_("index.php?option=com_zefaniabible&view=standard&Itemid=".$this->str_menuItem."&a=".$this->str_bible_alias."&b=".$this->arr_verse_info['book_name'][$this->int_verse_remainder]."-".str_replace(" ","-",mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$this->arr_verse_info['book_name'][$this->int_verse_remainder],'UTF-8')))."&c=".$this->arr_verse_info['chapter_number'][$this->int_verse_remainder].'-'.mb_strtolower(JText::_('MOD_ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8'))."'>"
						.$str_temp."</a>";
					}
					else
					{
						$str_verse_output = $str_verse_output. $str_temp;
					}
					$str_verse_output = $str_verse_output. '<div class="zef_verse_of_day_verse">'.$datum->verse.'</div>';		
				}
				else
				{
					$str_temp = '';
					if($datum->verse_id == $this->arr_verse_info['begin_verse'][$this->int_verse_remainder])
					{
						$str_temp = '<div class="zef_verse_of_day_header">'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$this->arr_verse_info['book_name'][$this->int_verse_remainder]).
									" ".$this->arr_verse_info['chapter_number'][$this->int_verse_remainder].":".$this->arr_verse_info['begin_verse'][$this->int_verse_remainder].
									"-".$this->arr_verse_info['end_verse'][$this->int_verse_remainder]."</div>";
					}
					if($this->int_link_type == 1)
					{
						$str_verse_output = $str_verse_output. "<a rel='nofollow'  title='".JText::_('MOD_ZEFANIABIBLE_VERSE_OF_THE_DAY_BIBLE_LINK_DESC')."' id='zef_links' href='".JRoute::_("index.php?option=com_zefaniabible&view=standard&Itemid=".$this->str_menuItem."&a=".$this->str_bible_alias."&b=".$this->arr_verse_info['book_name'][$this->int_verse_remainder]."-".str_replace(" ","-",mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$this->arr_verse_info['book_name'][$this->int_verse_remainder],'UTF-8')))."&c=".$this->arr_verse_info['chapter_number'][$this->int_verse_remainder].'-'.mb_strtolower(JText::_('MOD_ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8'))."'>"
						.$str_temp
						."</a>";
					}
					else
					{
						$str_verse_output = $str_verse_output. $str_temp;	
					}
					$str_verse_output = $str_verse_output. $datum->verse." ";					
				}
			}
			if($this->int_link_type == 2)
			{
				$str_verse_output = $str_verse_output. "<a rel='nofollow' title='".JText::_('MOD_ZEFANIABIBLE_VERSE_OF_THE_DAY_BIBLE_LINK_DESC')."' id='zef_links' href='".JRoute::_("index.php?option=com_zefaniabible&view=standard&Itemid=".$this->str_menuItem."&a=".$this->str_bible_alias."&b=".$this->arr_verse_info['book_name'][$this->int_verse_remainder]."-".str_replace(" ","-",mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$this->arr_verse_info['book_name'][$this->int_verse_remainder],'UTF-8')))."&c=".$this->arr_verse_info['chapter_number'][$this->int_verse_remainder].'-'.mb_strtolower(JText::_('MOD_ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8'))."'>"
				.JText::_('MOD_ZEFANIABIBLE_VERSE_OF_THE_DAY_BIBLE_LINK')."</a>";
			}			
			$str_verse_output = $str_verse_output. '<div style="clear:both"></div>';
			echo $str_verse_output ;
		}
		catch (JException $e)
		{
			$this->setError($e);
		}
	}
	protected function fnc_Get_Verse_Of_The_Day_Info()
	{
		try
		{
			$db = JFactory::getDBO();
			$query 	= "SELECT * FROM #__zefaniabible_zefaniaverseofday WHERE publish=1";
			$db->setQuery($query);
			$arr_rows = $db->loadObjectList();	
			$x = 0;
			foreach($arr_rows as $arr_row)
			{
				$this->arr_verse_info['book_name'][$x] = $arr_row->book_name;
				$this->arr_verse_info['chapter_number'][$x] = $arr_row->chapter_number;
				$this->arr_verse_info['begin_verse'][$x] = $arr_row->begin_verse;
				$this->arr_verse_info['end_verse'][$x] = $arr_row->end_verse;
				$x++;
			}
			$this->int_max_verses = count($this->arr_verse_info['book_name']);	
		}
		catch (JException $e)
		{
			$this->setError($e);
		}
	}	

	protected function fnc_Get_Bible_Book_Info()
	{
		try 
		{
			$db = JFactory::getDBO();
			$query 	= "SELECT * FROM #__zefaniabible_bible_names WHERE alias='".$this->str_bible_alias."'";
			$db->setQuery($query);
			$arr_rows = $db->loadObjectList();
			foreach($arr_rows as $arr_row)
			{
				$arr_temp['int_id'] = $arr_row->id;
				$arr_temp['str_nativeTitle'] = $arr_row->bible_name;
				$arr_temp['str_nativeAlias'] = $arr_row->alias;
			}	
		}
		catch (JException $e)
		{
			$this->setError($e);
		}
		return $arr_temp;
	}
	function fnc_first_bible_record()
	{
		try 
		{
			$db = JFactory::getDBO();
			$query  = $db->getQuery(true);
			$query->select('alias');
			$query->from('`#__zefaniabible_bible_names`');	
			$query->where("publish = 1");
			$query->order('id');		
			$db->setQuery($query,0, 1);
			$data = $db->loadResult();
		}
		catch (JException $e)
		{
			$this->setError($e);
		}
		return $data;			
	}	
}
?>

