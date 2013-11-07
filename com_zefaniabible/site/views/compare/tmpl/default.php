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
$cls_bibleBook = new BibleCompare($this->arr_Chapter, $this->arr_Bibles, $this->str_Bible_Version, $this->int_Bible_Book_ID, $this->str_Bible_Version2, $this->arr_Chapter2, $this->int_Bible_Chapter,$this->arr_commentary, $this->obj_references); 

class BibleCompare {
	public $obj_Bible_Dropdown;
	public $obj_Bible_Dropdown2;	
	public $obj_Book_Dropdown;
	public $int_Bibles_loaded;
	public $str_Chapter_Output;
	public $flg_show_credit;
	public $flg_show_page_top;
	public $flg_show_page_bot;
	public $flg_show_pagination_type;
	private $doc_page;
	public $flg_email_button;
	public $flg_show_audio_player;
	public $int_player_popup_height;
	public $int_player_popup_width;
	public $flg_show_second_player;
	public $flg_use_bible_selection;
	public $flg_show_commentary;
	private $str_commentary;
	public $flg_show_references;
		
	public function __construct($arr_Chapter, $arr_Bibles, $str_Bible_Version, $int_Bible_Book_ID, $str_Bible_Version2, $arr_Chapter2, $int_Bible_Chapter,$arr_commentary, $arr_references)
	{
		$this->params = JComponentHelper::getParams( 'com_zefaniabible' );
		$this->doc_page = JFactory::getDocument();	
		$this->flg_show_page_top 	= $this->params->get('show_pagination_top', '1');
		$this->flg_show_page_bot 	= $this->params->get('show_pagination_bot', '1');	
		$this->flg_show_credit 		= $this->params->get('show_credit','0');
		//$this->flg_show_credit 		= 1;
		$this->flg_show_pagination_type = $this->params->get('show_pagination_type','0');
		$this->flg_email_button 	= $this->params->get('flg_email_button', '1');
		$this->flg_show_audio_player = $this->params->get('show_audioPlayer', '0');
		$this->flg_show_second_player = $this->params->get('show_second_player','1');
		$this->int_player_popup_height = $this->params->get('player_popup_height','300');
		$this->int_player_popup_width = $this->params->get('player_popup_width','300');
		$this->flg_use_bible_selection 	= $this->params->get('flg_use_bible_selection', '1');
		$this->flg_show_commentary = $this->params->get('show_commentary', '0');
		$this->flg_show_references = $this->params->get('show_references', '0');
		$str_primary_commentary = $this->params->get('primaryCommentary');
		$this->str_commentary = JRequest::getCmd('e',$str_primary_commentary);
		$int_commentary_width = $this->params->get('commentaryWidth','800');
		$int_commentary_height = $this->params->get('commentaryHeight','500');
								
		$obj_Bible_Dropdown = '';
		$str_Chapter_Output = '';
		$obj_Book_Dropdown = '';
		$int_Bibles_loaded = 0;
		$a=1;
		$b=1;
		$str_descr = '';
		$str_alias = '';
		$str_alias2= '';
		
		foreach($arr_Bibles as $str_Bible)
		{
			if($str_Bible_Version == $str_Bible->alias)
			{
				$this->obj_Bible_Dropdown = $this->obj_Bible_Dropdown.'<option value="'.$str_Bible->alias.'" selected>'.$str_Bible->bible_name.'</option>';
				$str_alias = $str_Bible->alias;
			}
			else
			{
				$this->obj_Bible_Dropdown = $this->obj_Bible_Dropdown.'<option value="'.$str_Bible->alias.'" >'.$str_Bible->bible_name.'</option>';
			}
			if($str_Bible_Version2 == $str_Bible->alias)
			{
				$this->obj_Bible_Dropdown2 = $this->obj_Bible_Dropdown2.'<option value="'.$str_Bible->alias.'" selected>'.$str_Bible->bible_name.'</option>';
				$str_alias2 = $str_Bible->alias;
			}
			else
			{
				$this->obj_Bible_Dropdown2 = $this->obj_Bible_Dropdown2.'<option value="'.$str_Bible->alias.'" >'.$str_Bible->bible_name.'</option>';
			}			
			$this->int_Bibles_loaded++;
		}
		
		
		for($x = 1; $x <=66; $x++)
		{
			if($int_Bible_Book_ID == $x)
			{
				$this->obj_Book_Dropdown = $this->obj_Book_Dropdown. '<option value="'.$x."-".mb_strtolower(str_replace(" ","-",JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$x)),'UTF-8').'" selected>'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$x).'</option>';						
			}
			else
			{
				$this->obj_Book_Dropdown = $this->obj_Book_Dropdown. '<option value="'.$x."-".mb_strtolower(str_replace(" ","-",JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$x)),'UTF-8').'" >'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$x).'</option>';				
			}
		}

		foreach($arr_Chapter as $arr_verse)
		{
			if($arr_verse->verse_id == 1)
			{
				$str_descr	= $str_descr.$arr_verse->verse;
			}
			$temp[$a] = '<div class="zef_compare_bibles">'.'<div class="zef_verse_number">'.$arr_verse->verse_id.'</div><div class="zef_verse">'.$arr_verse->verse.'</div></div>';		
			$a++;
		}
		foreach($arr_Chapter2 as $arr_verse2)
		{
			if($arr_verse2->verse_id == 1)
			{
				$str_descr	= $str_descr.$arr_verse2->verse;
			}			
			$temp2[$b] = '<div class="zef_compare_bibles">'.'<div class="zef_verse_number">'.$arr_verse2->verse_id.'</div><div class="zef_verse">'.$arr_verse2->verse.'</div></div>';
			$b++;
								
		}
		
		for($x=1; $x< $a; $x++)
		{
			if($arr_verse->verse_id <= 2)
			{
				$str_descr = $str_descr." ".trim($arr_verse->verse);	
			}
			if ($x % 2)
			{
				$this->str_Chapter_Output  = $this->str_Chapter_Output.'<div class="odd">';
			}
			else
			{
				$this->str_Chapter_Output  = $this->str_Chapter_Output.'<div class="even">'; 
			}			
			for($y = 1; $y <= 2; $y++)
			{
				if($y %2 )
				{
					$this->str_Chapter_Output  = $this->str_Chapter_Output. '<div class="zef_bible_1">'.$temp[$x].'</div>';
				}
				else
				{
					$this->str_Chapter_Output  = $this->str_Chapter_Output. '<div class="zef_bible_2">'.$temp2[$x].'</div>';
					if($this->flg_show_references)
					{
						foreach($arr_references as $obj_references)
						{
							if($obj_references->verse_id == $x)
							{
								$temp_link = 'a='.$str_Bible_Version.'&b='.$int_Bible_Book_ID.'&c='.$int_Bible_Chapter.'&d='.$x;
								$str_pre_link = '<a title="'. JText::_('COM_ZEFANIA_BIBLE_SCRIPTURE_BIBLE_LINK')." ".'" target="blank" href="index.php?view=references&option=com_zefaniabible&tmpl=component&'.$temp_link.'" class="modal" rel="{handler: \'iframe\', size: {x:'.$int_commentary_width.',y:'.$int_commentary_height.'}}">';
								$this->str_Chapter_Output  = $this->str_Chapter_Output.'<div class="zef_reference_hash">'.$str_pre_link.JText::_('ZEFANIABIBLE_BIBLE_REFERENCE_LINK').'</a></div>';	
								break;
							}
						}
					}				
					if($this->flg_show_commentary)
					{
						foreach($arr_commentary as $int_verse_commentary)
						{
							if($x == $int_verse_commentary->verse_id)
							{
								$str_commentary_url = JRoute::_("index.php?option=com_zefaniabible&view=commentary&a=".$this->str_commentary."&b=".$int_Bible_Book_ID."&c=".$int_Bible_Chapter."&d=".$x."&tmpl=component");
								$this->str_Chapter_Output  = $this->str_Chapter_Output.'<div class="zef_commentary_hash"><a href="'.$str_commentary_url.'" class="modal" rel="{handler: \'iframe\', size: {x:'.$int_commentary_width.',y:'.$int_commentary_height.'}}">'.JText::_('ZEFANIABIBLE_BIBLE_COMMENTARY_LINK')."</a></div>";
							}
						}
					}						
				}
				
			}
			$this->str_Chapter_Output  = $this->str_Chapter_Output.'<div style="clear:both"></div></div>';			
		}
		$this->fnc_meta_data($int_Bible_Book_ID, $int_Bible_Chapter,$str_descr,$str_alias,$str_alias2);
	}
	private function fnc_meta_data($int_Bible_Book_ID, $int_Bible_Chapter,$str_descr,$str_alias,$str_alias2)
	{
		// add breadcrumbs
		$app_site = JFactory::getApplication();
		$pathway = $app_site->getPathway();
		$pathway->addItem(JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$int_Bible_Book_ID)." ".mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8')." ".$int_Bible_Chapter." - ".$str_alias." - ".$str_alias2, JFactory::getURI()->toString());		
		
		$str_descr = trim(mb_substr($str_descr,0,146))." ...";
		$str_title = JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$int_Bible_Book_ID)." ".mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8')." ".$int_Bible_Chapter.' - '.$str_alias.' - '.$str_alias2;
		$this->doc_page->setMetaData( 'description', strip_tags($str_descr));
		$this->doc_page->setMetaData( 'keywords', $str_title.",".$str_alias.",".$str_alias2 );
		$this->doc_page->setTitle($str_title);
					
		// Facebook Open Graph
		$this->doc_page->setMetaData( 'og:title', $str_title);
		$this->doc_page->setMetaData( 'og:url', JFactory::getURI()->toString());		
		$this->doc_page->setMetaData( 'og:type', "article" );	
		$this->doc_page->setMetaData( 'og:image', JURI::root()."components/com_zefaniabible/images/bible_100.jpg" );	
		$this->doc_page->setMetaData( 'og:description', strip_tags($str_descr) );
		$this->doc_page->setMetaData( 'og:site_name', $app_site->getCfg('sitename') );			
	}
	public function fnc_Pagination_Buttons($str_Bible_Version, $int_Bible_Book_ID, $int_Bible_Chapter, $int_max_chapter, $str_Bible_Version2)
	{	
		$urlPrepend = "document.location.href=('";
		$urlPostpend = "')";
	
		if($int_Bible_Book_ID > 1)
		{
			$url[3] = "index.php?option=com_zefaniabible&a=".$str_Bible_Version."&b=".$str_Bible_Version2."&view=".JRequest::getCmd('view')."&c=".
			($int_Bible_Book_ID-1)."-".str_replace(" ","-",mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.
			($int_Bible_Book_ID-1),'UTF-8')))."&d=1-".mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8');
			if($this->flg_show_commentary)
			{
				$url[3] = $url[3]."&e=".$this->str_commentary;
			}
			$url[3] = JRoute::_($url[3]);
			if($this->flg_show_pagination_type == 0)
			{
				echo '<input title="'.JText::_('ZEFANIABIBLE_BIBLE_LAST_BOOK').'" type="button" id="zef_Buttons" class="zef_lastBook" name="lastBook" onclick="'.$urlPrepend.$url[3].$urlPostpend.'"  value="'. JText::_("ZEFANIABIBLE_BIBLE_BOOK_NAME_".($int_Bible_Book_ID-1)).' 1"'.' />';
			}
			else
			{
				echo "<a title='".JText::_('ZEFANIABIBLE_BIBLE_LAST_BOOK')."' id='zef_links' href='".$url[3]."'>".JText::_("ZEFANIABIBLE_BIBLE_BOOK_NAME_".($int_Bible_Book_ID-1)).' 1'."</a> ";
			}
		}
		if($int_Bible_Chapter > 1)
		{
			$url[1] = "index.php?option=com_zefaniabible&a=".$str_Bible_Version."&b=".$str_Bible_Version2."&view=".JRequest::getCmd('view')."&c=".
			$int_Bible_Book_ID."-".str_replace(" ","-",mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$int_Bible_Book_ID,'UTF-8')))."&d=".($int_Bible_Chapter-1).
			"-".mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8');	
			if($this->flg_show_commentary)
			{
				$url[1] = $url[1]."&e=".$this->str_commentary;
			}
			$url[1] = JRoute::_($url[1]);
				
			if($this->flg_show_pagination_type == 0)
			{
				echo '<input title="'.JText::_('ZEFANIABIBLE_BIBLE_LAST_CHAPTER').'" type="button" id="zef_Buttons" class="zef_lastChapter" name="lastChapter" onclick="'.$urlPrepend.$url[1].$urlPostpend.'"  value="'. JText::_("ZEFANIABIBLE_BIBLE_BOOK_NAME_".$int_Bible_Book_ID)." ".($int_Bible_Chapter-1).'" />';
			}
			else
			{
				echo "<a title='".JText::_('ZEFANIABIBLE_BIBLE_LAST_CHAPTER')."' id='zef_links' href='".$url[1]."'>".JText::_("ZEFANIABIBLE_BIBLE_BOOK_NAME_".$int_Bible_Book_ID).' '.($int_Bible_Chapter-1)."</a> ";
			}
		}
		if($int_Bible_Chapter < $int_max_chapter)
		{
			$url[0] = "index.php?option=com_zefaniabible&a=".$str_Bible_Version."&b=".$str_Bible_Version2."&view=".
			JRequest::getCmd('view')."&c=".$int_Bible_Book_ID."-".str_replace(" ","-",mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$int_Bible_Book_ID),'UTF-8'))."&d=".
			($int_Bible_Chapter+1)."-".mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8');	
			if($this->flg_show_commentary)
			{
				$url[0] = $url[0]."&e=".$this->str_commentary;
			}
			$url[0] = JRoute::_($url[0]);	
						
			if($this->flg_show_pagination_type == 0)
			{			
				echo '<input title="'.JText::_('ZEFANIABIBLE_BIBLE_NEXT_CHAPTER').'" type="button" id="zef_Buttons" class="zef_NextChapter" name="nextChapter" onclick="'.$urlPrepend.$url[0].$urlPostpend.'"  value="'. JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$int_Bible_Book_ID)." ".($int_Bible_Chapter+1).'" />';
			}
			else
			{
				echo "<a title='".JText::_('ZEFANIABIBLE_BIBLE_NEXT_CHAPTER')."' id='zef_links' href='".$url[0]."'>".JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$int_Bible_Book_ID).' '.($int_Bible_Chapter+1)."</a> ";
			}
		}
		if($int_Bible_Book_ID < 66)
		{
			$url[2] = "index.php?option=com_zefaniabible&a=".$str_Bible_Version."&b=".$str_Bible_Version2."&view=".JRequest::getCmd('view')."&c=".
			($int_Bible_Book_ID+1)."-".str_replace(" ","-",mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.
			($int_Bible_Book_ID+1),'UTF-8')))."&d=1-".mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8');
			if($this->flg_show_commentary)
			{
				$url[2] = $url[2]."&e=".$this->str_commentary;
			}
			$url[2] = JRoute::_($url[2]);
			
			if($this->flg_show_pagination_type == 0)
			{			
				echo '<input title="'.JText::_('ZEFANIABIBLE_BIBLE_NEXT_BOOK').'" type="button" id="zef_Buttons" class="zef_NextBook" name="nextBook" onclick="'.$urlPrepend.$url[2].$urlPostpend.'"  value="'. JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.($int_Bible_Book_ID+1)).' 1"'.' />';
			}
			else
			{
				echo "<a title='".JText::_('ZEFANIABIBLE_BIBLE_NEXT_BOOK')."' id='zef_links' href='".$url[2]."'>". JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.($int_Bible_Book_ID+1)).' 1'."</a>";
			} 
		}
	}	
}
?>

<form action="<?php echo JFactory::getURI()->toString(); ?>" method="post" id="adminForm" name="adminForm">
	<div id="zef_Bible_Main">
    	<div class="zef_legend">
        	<?php if($cls_bibleBook->flg_email_button){?>
            <div class="zef_email_button"><a rel="nofollow" title="<?php echo JText::_('ZEFANIABIBLE_EMAIL_BUTTON_TITLE'); ?>" target="blank" href="index.php?view=subscribe&option=com_zefaniabible&tmpl=component" class="modal" rel="{handler: 'iframe', size: {x:500,y:400}}" ><img class="zef_email_img" src="<?php echo JURI::root()."components/com_zefaniabible/images/e_mail.png"; ?>" /></a></div>
            <?php } ?>
            <div class="zef_bible_Header_Label"><h1 class="zef_bible_Header_Label_h1"><?php echo JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$this->int_Bible_Book_ID)." ".mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8')." ".$this->int_Bible_Chapter; ?></h1></div>
            <?php if($cls_bibleBook->flg_use_bible_selection){?>            
                <div class="zef_bible_label"><?php echo JText::_('ZEFANIABIBLE_BIBLE_VERSION_FIRST');?></div>
                <div class="zef_bible">
                    <select name="a" id="bible" class="inputbox" onchange="this.form.submit()">
                        <?php echo $cls_bibleBook->obj_Bible_Dropdown; ?>
                    </select>
                </div>
				<div style="clear:both;"></div>                
               <div class="zef_bible_label"><?php echo JText::_('ZEFANIABIBLE_BIBLE_VERSION_SECOND');?></div>
                <div class="zef_bible">
                    <select name="b" id="bible" class="inputbox" onchange="this.form.submit()">
                        <?php echo $cls_bibleBook->obj_Bible_Dropdown2; ?>
                    </select>
                </div>                
            <?php } else {
				echo '<input type="hidden" name="a" value="'.$this->str_Bible_Version.'" />';
				echo '<input type="hidden" name="b" value="'.$this->str_Bible_Version2.'" />';
             }?> 			
            <div class="zef_book_label"><?php echo JText::_('ZEFANIABIBLE_BIBLE_BOOK');?></div>
            <div class="zef_book">
                <select name="c" id="book" class="inputbox" onchange="this.form.submit()">
					<?php 
						echo $cls_bibleBook->obj_Book_Dropdown;
					?>
                </select>
            </div>

            <div class="zef_chapter_label"><?php echo JText::_('ZEFANIABIBLE_BIBLE_CHAPTER');?></div>
            <div class="zef_Chapter">
                <select name="d" id="chapter" class="inputbox" onchange="this.form.submit()">
					<?php 
						for( $x = 1; $x <= $this->int_max_chapter; $x++)
						{
							if($x == $this->int_Bible_Chapter)
							{
								echo '<option value="'.$x.'-'.mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8').'" selected="selected">'.$x.'</option>';
							}
							else
							{
								echo '<option value="'.$x.'-'.mb_strtolower(JText::_('ZEFANIABIBLE_BIBLE_CHAPTER'),'UTF-8').'">'.$x.'</option>';
							}
						}
					?>               
                </select>
            </div>
			<?php if($cls_bibleBook->flg_show_commentary){ ?>
            <div style="clear:both;"></div>
            <div>
                <div class="zef_commentary_label"><?php echo JText::_('COM_ZEFANIABIBLE_COMMENTARY_LABEL');?></div>
                <div class="zef_commentary">
                    <select name="e" id="commentary" class="inputbox" onchange="this.form.submit()">
                        <?php echo $this->obj_commentary_dropdown;?>
                     </select>
                </div>
            </div>
            <?php } ?>            
             <div style="clear:both;"></div>
            <div class="zef_top_pagination">
         		<?php if($cls_bibleBook->flg_show_page_top){ $cls_bibleBook->fnc_Pagination_Buttons($this->str_Bible_Version,$this->int_Bible_Book_ID, $this->int_Bible_Chapter, $this->int_max_chapter,$this->str_Bible_Version2);} ?>
            </div>              
        </div>   
             <div class="zef_player">
			<?php if(($cls_bibleBook->flg_show_audio_player)and($this->obj_player_one)){ ?>              
             	<div class="zef_player-1"> 
                	<?php echo $this->obj_player_one; 
							echo '<div style="clear:both;"></div>';
				            echo  '<a href="#" onclick="return popitup(\''.JURI::root().'index.php?option=com_zefaniabible&a='.$this->str_Bible_Version.'&view=player&tmpl=component&b='.$this->int_Bible_Book_ID.'\')" target="_blank" >'.JText::_('ZEFANIABIBLE_PLAYER_WHOLE_BOOK')."</a>";
					?>
                </div>
            <?php }?>                   
                <?php if(($cls_bibleBook->flg_show_audio_player)and($cls_bibleBook->flg_show_second_player)and($this->obj_player_two)){?>
                <div class="zef_player-2"> 
               		<?php echo $this->obj_player_two; 
							echo '<div style="clear:both;"></div>';
				            echo  '<a href="#" onclick="return popitup(\''.JURI::root().'index.php?option=com_zefaniabible&a='.$this->str_Bible_Version2.'&view=player&tmpl=component&b='.$this->int_Bible_Book_ID.'\')" target="_blank" >'.JText::_('ZEFANIABIBLE_PLAYER_WHOLE_BOOK')."</a>";					
					?>
                </div>
                <?php }?>
            </div>

        <div style="clear:both;"></div>
        <div class="zef_bible_Chapter"><article><?php echo $cls_bibleBook->str_Chapter_Output; ?></article></div>     
        <div class="zef_footer">
            <div class="zef_bot_pagination">
            	<?php if($cls_bibleBook->flg_show_page_bot){ $cls_bibleBook->fnc_Pagination_Buttons($this->str_Bible_Version,$this->int_Bible_Book_ID, $this->int_Bible_Chapter, $this->int_max_chapter, $this->str_Bible_Version2);} ?>        
            	<div style="clear:both;"></div>
	            <?php if($cls_bibleBook->flg_show_credit){ echo JText::_('ZEFANIABIBLE_DEVELOPED_BY')." <a href='http://www.zefaniabible.com/?utm_campaign=".JRequest::getCmd('view')."&utm_medium=referral&utm_source=".substr(JURI::base(),7,-1)."' target='_blank'>Zefania Bible</a>"; } ?>
            </div>             
			
        </div>
    </div>
	<input type="hidden" name="option" value="<?php echo JRequest::getCmd('option');?>" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd('view');?>" />
    <input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid'); ?>"/>
</form>
<script language="javascript" type="text/javascript">
function popitup(url) {
	newwindow=window.open(url,'name','height=<?php echo ($cls_bibleBook->int_player_popup_height);?>,width=<?php echo ($cls_bibleBook->int_player_popup_width);?>','scrollbars=no','resizable=no');
	if (window.focus) {newwindow.focus()}
	return false;
}
</script>
