<?php
/**                               ______________________________________________
*                          o O   |                                              |
*                 (((((  o      <  Generated with Cook           (100% Vitamin) |
*                ( o o )         |______________________________________________|
* --------oOOO-----(_)-----OOOo---------------------------------- www.j-cook.pro --- +
* @version		1.6
* @package		ZefaniaBible
* @subpackage	Zefaniabible
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

defined('_JEXEC') or die('Restricted access'); ?>
<?php 
$cls_bible_reading_plan = new BibleReadingPlan($this->bibles, $this->reading, $this->arr_reading_plans, $this->plan,$this->arr_commentary);

class BibleReadingPlan
{
		/*
			a = plan
			b = bible
			c = day
		*/
	public $str_bibleVersion;
	private $str_primary_bible;
	private $int_reading_day;
	private $str_start_reading_date;
	private $biblePath;
	private $str_nativeLocation;
	private $arr_book_info;
	private $arr_bookXMLFile;
	public $str_page_output;
	public $str_bible_layout;
	public $flg_show_credit;
	private $flg_show_pagination_type;
	public $flg_show_page_top;
	private $arr_reading;
	private $str_primary_reading;
	public $arr_total_bibles_loaded;
	public $str_reading_plan;
	private $str_prmary_commentary;
	private $str_Commentary_XML_Path;
	public $flg_show_commentary;
	public $int_day_number;
	private $str_chapter_headings;
	private $str_curr_read_plan;
	public $str_first_verse;
	private $arr_commentary_File;
	private $int_commentary_height;
	private $int_commentary_width;
	private $str_view;
	public $flg_email_button;
	public $flg_reading_rss_button;
	public $flg_show_audio_player;
	public $flg_use_bible_selection;
	private $str_commentary;
		
	public function __construct($arr_bibles, $arr_reading, $arr_reading_plans, $arr_plan, $arr_commentary)
	{
		
		$this->arr_reading = $arr_reading;
		$this->params = JComponentHelper::getParams( 'com_zefaniabible' );		
		$this->doc_page = JFactory::getDocument();	

		$this->str_view = JRequest::getCmd('view');
		$this->str_primary_reading = 	$this->params->get('primaryReading', 'ttb');
		$this->str_primary_bible = 		$this->params->get('primaryBible', 'kjv');	
		$this->flg_show_audio_player = 	$this->params->get('show_audioPlayer', '0');
		$this->flg_show_commentary = $this->params->get('show_commentary', '0');

		$this->str_reading_plan = 	JRequest::getCmd('a', $this->str_primary_reading);	
		$this->str_bibleVersion = 	JRequest::getCmd('b', $this->str_primary_bible);		

		$str_primary_commentary = $this->params->get('primaryCommentary');
		$this->str_commentary = JRequest::getCmd('d',$str_primary_commentary);
								
		$this->flg_show_credit 			= $this->params->get('show_credit','0');
		//$this->flg_show_credit = 1;
		$this->flg_show_pagination_type = $this->params->get('show_pagination_type','0');
		$this->flg_show_page_top 		= $this->params->get('show_pagination_top', '1');
		$this->flg_show_page_bot 		= $this->params->get('show_pagination_bot', '1');
		$this->flg_email_button 		= $this->params->get('flg_email_button', '1');	
		$this->flg_reading_rss_button 	= $this->params->get('flg_plan_rssfeed_button', '1');
		$this->flg_use_bible_selection 	= $this->params->get('flg_use_bible_selection', '1');
		 
		foreach($arr_reading as $obj_reading)
		{
			$this->int_day_number = $obj_reading->day_number;
		}
		foreach ($arr_reading_plans as $plan)
		{
			if($this->str_reading_plan == $plan->alias)
			{
				$this->str_curr_read_plan = $plan->name;
			}
		}
		$this->getMetaData($arr_plan);
		
	}
	public function fnc_output_chapter($arr_plan, $arr_commentary)
	{
			$book = 0;
			$chap = 0;
			$x = 1;
			$y = 1;		

		if($this->flg_show_audio_player)
		{
			require_once(JPATH_COMPONENT_SITE.'/helpers/audioplayer.php');	
			$mdl_audio = new ZefaniaAudioPlayer;
		}			
			foreach($arr_plan as $reading)
			{
				$cnt_verse_count = count($reading);
				$z = 1;
				foreach($reading as $plan)
				{
					if($plan->verse_id == 1)
					{
						$this->str_first_verse = $plan->verse;
					}		
					if (($plan->book_id > $book)or($plan->chapter_id > $chap))
					{
						$book = $plan->book_id;
						$chap = $plan->chapter_id;
						if($y > 1)
						{
							echo '</div>';
						}
						echo '<div class="zef_bible_Header_Label_Plan"><h1 class="zef_bible_Header_Label_h1"><a name="'.$y.'" id="'.$y.'"></a>'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$plan->book_id)." ";
						echo mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8')." ".$plan->chapter_id.'</h1></div>';
						echo '<div class="zef_bible_Chapter">';
						$arr_single_commentary  = $arr_commentary[($y-1)];
						if($this->flg_show_audio_player)
						{
							$obj_player = $mdl_audio->fnc_audio_player($this->str_primary_bible,$plan->book_id,$plan->chapter_id, $y);
							echo '<div class="zef_player-'.$y.'">';
							echo $obj_player;
        					echo "</div>";
							echo '<div style="clear:both"></div>';
						}
						$x = 1;
						$y++;			
					}

					if ($x % 2)
					{
						echo '<div class="odd">';
					}
					else
					{
						echo '<div class="even">'; 
					}
					echo "<div class='zef_verse_number'>".$plan->verse_id."</div><div class='zef_verse'>".$plan->verse."</div>";
					if($this->flg_show_commentary)
					{
						$int_commentary_width = $this->params->get('commentaryWidth','800');
						$int_commentary_heigh = $this->params->get('commentaryHeight','500');
						
						foreach($arr_single_commentary as $int_verse_commentary)
						{
							if($plan->verse_id == $int_verse_commentary->verse_id)
							{
								$str_commentary_url = JRoute::_("index.php?option=com_zefaniabible&view=commentary&a=".$this->str_commentary."&b=".$plan->book_id."&c=".$plan->chapter_id."&d=".$plan->verse_id."&tmpl=component");
								echo '<div class="zef_commentary_hash"><a href="'.$str_commentary_url.'" class="modal" rel="{handler: \'iframe\', size: {x:'.$int_commentary_width.',y:'.$int_commentary_heigh.'}}">'.JText::_('ZEFANIABIBLE_BIBLE_COMMENTARY')."</a></div>";
							}
						}
					}	
					echo '<div style="clear:both"></div></div>';		
					$x++;
					$z++;
				}
			}
	}
	private function getMetaData($arr_plan)
	{
		//RSS RSS 2.0 Feed
		$href = 'index.php?option=com_zefaniabible&view=readingrss&format=raw&a='.$this->str_reading_plan.'&b='.$this->str_bibleVersion.'&c='.$this->int_day_number; 
		$attribs_rss = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0'); 
		$this->doc_page->addHeadLink( $href, 'alternate', 'rel', $attribs_rss );		
			
		$app_site = JFactory::getApplication();
		// add breadcrumbs
		$pathway = $app_site->getPathway();
		$pathway->addItem(($this->str_curr_read_plan." - ". mb_strtoupper($this->str_bibleVersion)." - ".JText::_('ZEFANIABIBLE_READING_PLAN_DAY')." ".$this->int_day_number), JFactory::getURI()->toString());		
		
		foreach ($arr_plan as $obj_plan)
		{
			foreach($obj_plan as $obj_chapter)
			{
				if($obj_chapter->verse_id == 1)
				{
					$this->str_first_verse = $this->str_first_verse. $obj_chapter->verse.' ';
				}
			}	
		}
		$int_len_first_verse = strlen($this->str_first_verse);
		if($int_len_first_verse > 150)
		{
			$this->str_first_verse = mb_substr(strip_tags($this->str_first_verse),0, 147).'...';
		}
		
		$this->doc_page->setTitle($this->str_curr_read_plan." | ". mb_strtoupper($this->str_bibleVersion)." | ".JText::_('ZEFANIABIBLE_READING_PLAN_DAY')." ".$this->int_day_number);		
		$this->doc_page->setMetaData( 'keywords', $this->str_chapter_headings." ".$this->arr_book_info['str_nativeAlias'].", ".$this->str_curr_read_plan .", ".$this->str_bibleVersion.", ".JText::_('ZEFANIABIBLE_READING_PLAN_DAY')." ".$this->int_day_number );
		$this->doc_page->setMetaData( 'description', $this->str_first_verse);
		$this->doc_page->setMetaData( 'og:url', JFactory::getURI()->toString());		
		$this->doc_page->setMetaData( 'og:type', "article" );	
		$this->doc_page->setMetaData( 'og:image', JURI::root()."components/com_zefaniabible/images/bible_100.jpg" );	
		$this->doc_page->setMetaData( 'og:description', $this->str_first_verse );
		$this->doc_page->setMetaData( 'og:site_name', $app_site->getCfg('sitename') );			
	}	
	
	public function paginationButtons($int_day_number)
	{
		$urlPrepend = "document.location.href=('";
		$urlPostpend = "')";		
		if($this->arr_reading[0]->day_number > 1)
		{
			$url[2] = "index.php?option=com_zefaniabible&a=".$this->str_reading_plan."&b=".$this->str_bibleVersion."&view=".$this->str_view."&c=".($this->arr_reading[0]->day_number-1);
			if($this->flg_show_commentary)
			{
				$url[2] = $url[2]."&d=".$this->str_commentary;
			}
			$url[2] = JRoute::_($url[2]);			
			if($this->flg_show_pagination_type == 0)
			{
				echo '<input title="'.JText::_('ZEFANIABIBLE_BIBLE_LAST_DAY_READING').'" type="button" id="zef_Buttons" class="zef_last_day" name="lastday" onclick="'.$urlPrepend.$url[2].$urlPostpend.'"  value="'.JText::_('ZEFANIABIBLE_READING_PLAN_DAY').' '.($this->arr_reading[0]->day_number-1).'" />';
			}
			else
			{
				echo "<a title='".JText::_('ZEFANIABIBLE_BIBLE_LAST_DAY_READING')."' id='zef_links' href='".$url[2]."'>".JText::_('ZEFANIABIBLE_READING_PLAN_DAY')." ".($this->arr_reading[0]->day_number-1)."</a> ";
			}
		}
		if($this->flg_show_pagination_type == 0)
		{
			echo '<input title="'.JText::_('').'" type="button" id="zef_Buttons" disabled="disabled" class="zef_toda" name="today" onclick="'.$urlPrepend.$url[2].$urlPostpend.'"  value="'.JText::_('ZEFANIABIBLE_READING_PLAN_DAY').' '.($this->arr_reading[0]->day_number).'" />';		
		}
		else
		{
			echo JText::_('ZEFANIABIBLE_READING_PLAN_DAY')." ".($this->arr_reading[0]->day_number);			
		}
		$url[3] = "index.php?option=com_zefaniabible&a=".$this->str_reading_plan."&b=".$this->str_bibleVersion."&view=".$this->str_view."&c=".($this->arr_reading[0]->day_number+1);
		if($this->flg_show_commentary)
		{
			$url[3] = $url[3]."&d=".$this->str_commentary;
		}
		$url[3] = JRoute::_($url[3]);	
			
		if($this->flg_show_pagination_type == 0)
		{
			echo '<input title="'.JText::_('ZEFANIABIBLE_BIBLE_NEXT_DAY_READING').'" type="button" id="zef_Buttons" class="zef_next_day" name="nextday" onclick="'.$urlPrepend.$url[3].$urlPostpend.'"  value="'.JText::_('ZEFANIABIBLE_READING_PLAN_DAY').' '.($this->arr_reading[0]->day_number+1).'" />';
		}
		else
		{
			echo "<a title='".JText::_('ZEFANIABIBLE_BIBLE_NEXT_DAY_READING')."' id='zef_links' href='".$url[3]."'>".JText::_('ZEFANIABIBLE_READING_PLAN_DAY')." ".($this->arr_reading[0]->day_number+1)."</a> ";
		}
	}
	public function createBibleDropDown($items)
	{		
		$tempVersion = $this->str_bibleVersion;
		$tmp = ""; 
		foreach($items as $item)
		{						
			if($tempVersion == $item->alias)
			{
				$tmp = $tmp.'<option value="'.$item->alias.'" selected>'.$item->bible_name.'</option>';
			}
			else
			{
				$tmp = $tmp.'<option value="'.$item->alias.'" >'.$item->bible_name.'</option>';
			}
		}
		return $tmp;
	}

	public function createReadingDropDown($readingplans)
	{		
		$tempVersion = $this->str_reading_plan;
		$tmp = ""; 
		foreach($readingplans as $readingplan)
		{
			if($readingplan->publish == 1)
			{								
				if($tempVersion == $readingplan->alias)
				{
					$tmp = $tmp.'<option value="'.$readingplan->alias.'" selected>'.$readingplan->name.'</option>';
				}
				else
				{
					$tmp = $tmp.'<option value="'.$readingplan->alias.'" >'.$readingplan->name.'</option>';
				}
			}
		}
		return $tmp;
	}	
}
?>


<form action="<?php echo JFactory::getURI()->toString(); ?>" method="post" id="adminForm" name="adminForm">
	<div id="zef_Bible_Main">
    	<div class="zef_legend">
        		<?php if($cls_bible_reading_plan->flg_email_button){?>
        		<div class="zef_email_button"><a title="<?php echo JText::_('ZEFANIABIBLE_EMAIL_BUTTON_TITLE'); ?>" target="blank" href="index.php?view=subscribe&option=com_zefaniabible&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x:500,y:400}}" ><img class="zef_email_img" src="<?php echo JURI::root()."components/com_zefaniabible/images/e_mail.png"; ?>" /></a></div>
                <?php } 
					if($cls_bible_reading_plan->flg_reading_rss_button){
				?>
					<div class="zef_reading_rss">
                    	<a rel="nofollow" title="<?php echo JText::_('ZEFANIABIBLE_RSS_BUTTON_TITLE'); ?>" target="blank" href="index.php?option=com_zefaniabible&view=readingrss&format=raw&a=<?php echo $cls_bible_reading_plan->str_reading_plan; ?>&b=<?php echo $cls_bible_reading_plan->str_bibleVersion; ?>&c=<?php echo $cls_bible_reading_plan->int_day_number;?>" target="_blank" >
                			<img class="zef_email_img" src="<?php echo JURI::root()."components/com_zefaniabible/images/feeds.png"; ?>" />
						</a>
					</div>                
				<?php }?>                
                <div class="zef_reading_label"><?php echo JText::_('ZEFANIABIBLE_READING_PLAN');?></div>
                <div class="zef_reading_plan">
                    <select name="a" id="reading" class="inputbox" onchange="this.form.submit()">
                        <?php echo $cls_bible_reading_plan->createReadingDropDown($this->arr_reading_plans);?>
                    </select>
                </div>
                <div style="clear:both"></div>     
                 <?php if($cls_bible_reading_plan->flg_use_bible_selection){?>                
                <div class="zef_bible_label"><?php echo JText::_('ZEFANIABIBLE_BIBLE_VERSION');?></div>
                <div class="zef_bible">
                    <select name="b" id="bible" class="inputbox" onchange="this.form.submit()">
                        <?php echo $cls_bible_reading_plan->createBibleDropDown($this->bibles);?>
                    </select>
                </div>
				 <?php }else {
					echo '<input type="hidden" name="b" value="'.$cls_bible_reading_plan->str_bibleVersion.'" />';
				} ?>
				<?php if($cls_bible_reading_plan->flg_show_commentary){ ?>
                <div style="clear:both;"></div>
                <div>
                    <div class="zef_commentary_label"><?php echo JText::_('COM_ZEFANIABIBLE_COMMENTARY_LABEL');?></div>
                    <div class="zef_commentary">
                        <select name="d" id="commentary" class="inputbox" onchange="this.form.submit()">
                            <?php echo $this->obj_commentary_dropdown;?>
                         </select>
                    </div>
                </div>
                <?php } ?>                
                 <div style="clear:both"></div>      
            <div class="zef_top_pagination">        
                    <?php 
                        if($cls_bible_reading_plan->flg_show_page_top)
                        {
                            $cls_bible_reading_plan->paginationButtons($this->int_day_number);
                        }
                    ?>              
          </div>
	</div> 
	<article><?php echo $cls_bible_reading_plan->fnc_output_chapter($this->plan, $this->arr_commentary); ?></article>
        <div class="zef_footer">
			<div class="zef_bot_pagination">        
                <?php 
					if($cls_bible_reading_plan->flg_show_page_bot)
					{
						$cls_bible_reading_plan->paginationButtons($this->int_day_number);
					}
				?>   
                <div style="clear:both"></div>
                <?php  
				if($cls_bible_reading_plan->flg_show_credit)
				{
					echo JText::_('ZEFANIABIBLE_DEVELOPED_BY')." <a href='http://www.zefaniabible.com/?utm_campaign=".JRequest::getCmd('view')."&utm_medium=referral&utm_source=".substr(JURI::base(),7,-1)."' target='_blank'>Zefania Bible</a>";
				}
            	?>               
            </div> 
			     
        </div>
    </div>
	<input type="hidden" name="option" value="<?php echo JRequest::getCmd('option');?>" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd('view');?>" />
    <input type="hidden" name="c" value="<?php echo ($this->int_day_number); ?>" />
    <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>"/>
</form>
