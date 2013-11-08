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
defined('_JEXEC') or die('Restricted access');
$function	= JFactory::getApplication()->input->get('function', 'jSelectScripture');
$document	= JFactory::getDocument();
$document->addStyleSheet('/plugins/content/zefaniascripturelinks/css/zefaniascripturelinks.css'); 
		
$cls_button_scripture = new cls_button_scripture($this->arr_bible_verse); 

class cls_button_scripture {
	/*
		a = Link Type
		b = Alias
		c = Bible Book
		d = Begin Chap
		e = Begin Verse
		f = End Chap
		g = End Verse
	*/
		public $int_link_type;
		public $str_bible_alias;
		public $int_bible_book_id;
		public $int_begin_chap;
		public $int_begin_verse;
		public $int_end_chap;
		public $int_end_verse;
		public $str_output_preview;
		public $str_output_editor;
		public $str_label;
		public $flg_use_tags;
		public $int_modal_width;
		public $int_modal_height;
		public $str_bible_gateway_version;
		
	public function __construct($arr_bible_verse)	
	{
		$arr_toolTipArray = array('className'=>'zefania-tip', 
			'fixed'=>true,
			'showDelay'=>'500',
			'hideDelay'=>'5000'
			);						
		JHTML::_('behavior.tooltip', '.hasTip-zefania', $arr_toolTipArray);		
		$params = JComponentHelper::getParams( 'com_zefaniabible' );
		$str_primary_bible = $params->get('primaryBible');
		$this->int_modal_width = $params->get('int_modal_width',800);
		$this->int_modal_height = $params->get('int_modal_height', 500);
		$this->str_bible_gateway_version = $params->get('bible_gateway_version', 9); 
		
		$this->int_link_type = JRequest::getInt('a');
		$this->str_bible_alias = JRequest::getCmd('b',$str_primary_bible);
		$this->int_bible_book_id = JRequest::getInt('c');
		$this->int_begin_chap = JRequest::getInt('d');
		$this->int_begin_verse = JRequest::getInt('e');
		$this->int_end_chap = JRequest::getInt('f');
		$this->int_end_verse = JRequest::getInt('g');
		$this->str_label = JRequest::getCmd('h','');
		$this->flg_use_tags = JRequest::getBool('i');
		$flg_add_title = 0;
		$str_link = JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$this->int_bible_book_id)." ".$this->int_begin_chap; 
		if(($this->int_begin_chap)and(!$this->int_end_chap)and($this->int_begin_verse)and(!$this->int_end_verse))
		{
			$str_link = $str_link.":".$this->int_begin_verse;
		}
		else if(($this->int_begin_chap)and(!$this->int_end_chap)and($this->int_begin_verse)and($this->int_end_verse))
		{
			$str_link = $str_link.":".$this->int_begin_verse."-".$this->int_end_verse;
		}
		elseif(($this->int_begin_chap)and($this->int_end_chap)and(!$this->int_begin_verse)and(!$this->int_end_verse))
		{
			$str_link = $str_link."-".$this->int_end_chap;
		}
		else if(($this->int_begin_chap)and($this->int_end_chap)and($this->int_begin_verse)and($this->int_end_verse))
		{
			$str_link = $str_link.":".$this->int_begin_verse."-".$this->int_end_chap.":".$this->int_end_verse;
		}	
		if(($this->str_label == "")and($this->int_link_type == 1)and($this->int_begin_chap != 0))
		{
			$this->str_label = $str_link;
		}
		if($str_primary_bible != $this->str_bible_alias)
		{
			$flg_add_title = 1;
		}
		if(($this->str_bible_alias != '')and($this->int_begin_chap != 0)and($this->int_bible_book_id != 0))
		{
			switch ($this->int_link_type)
			{
				case 1:
					$this->fnc_default_link($str_link,$flg_add_title);
					break;
				case 2:
					$this->fnc_create_text_link($arr_bible_verse, $this->int_bible_book_id, $this->int_begin_chap, $this->int_end_chap, $this->int_begin_verse, $this->int_end_verse, $flg_add_title );
					break;
				case 3:
					$this->fnc_create_text_link($arr_bible_verse, $this->int_bible_book_id, $this->int_begin_chap, $this->int_end_chap, $this->int_begin_verse, $this->int_end_verse, $flg_add_title );
					if($this->flg_use_tags)
					{
						$temp = $this->str_output_editor;
						if($flg_add_title)
						{
							$this->str_output_editor = "{zefaniabible tooltip ".$this->str_bible_alias."}".$str_link."{/zefaniabible}";
						}
						else
						{
							$this->str_output_editor = "{zefaniabible tooltip}".$str_link."{/zefaniabible}";
						}
						$this->str_output_preview = $this->str_output_editor.'<hr><legend>'.JText::_('COM_ZEFANIABIBLE_TEXT_PREVIEW').'</legend>'.JHTML::tooltip($temp,'', '', $str_link, '', false,'hasTip-zefania');
					}
					else
					{
						$this->str_output_editor = JHTML::tooltip($this->str_output_editor,'', '', $str_link, '', false,'hasTip-zefania');
						$this->str_output_preview = $this->str_output_editor;
					}
					break;
				case 4:
				case 5:
					$this->fnc_biblegateway_link($str_link,$flg_add_title);
					break;
				default:
					$this->fnc_default_link($str_link,$flg_add_title);
			}
		}
	}
	private function fnc_biblegateway_link($str_link,$flg_add_title)
	{
			// Bible gateway coding begins here
			$str_output = '';
			$str_link_url = 'http://classic.biblegateway.com/passage/index.php?search='.urlencode($str_link).';&version='.$this->str_bible_gateway_version.';&interface=print';			
			$str_pre_link = '<a title="'. JText::_('COM_ZEFANIABIBLE_SCRIPTURE_BIBLE_LINK')." ".$str_link.'" target="blank" href="'.$str_link_url.'" class="modal" rel="{handler: \'iframe\', size: {x:'.$this->int_modal_width.',y:'.$this->int_modal_height.'}}">';
			if($this->int_link_type == 5)
			{
				$str_link = $this->str_label;
			}
			$str_output = $str_pre_link.$str_link .'</a>';
			// Bible gateway coding ends here
			$this->str_output_editor = $str_output;
			$str_output = $str_output. '<hr><legend>'.JText::_('COM_ZEFANIABIBLE_MODAL_PREVIEW').'</legend><iframe src="'.$str_link_url.'" width="700" border="1"></iframe>';
			$this->str_output_preview = $str_output;		
	}
	private function fnc_default_link($str_link,$flg_add_title)
	{
		$str_output = '';
		$str_url_link = JURI::root().'index.php?view=scripture&option=com_zefaniabible&tmpl=component&a='.$this->str_bible_alias.'&b='.$this->int_bible_book_id.'&c='.$this->int_begin_chap.'&d='.$this->int_begin_verse.'&e='.$this->int_end_chap.'&f='.$this->int_end_verse;
		$str_output = $str_output.'<a title="'. JText::_('COM_ZEFANIABIBLE_SCRIPTURE_BIBLE_LINK')." ".$str_link.'" target="blank" href="'.JRoute::_($str_url_link).'" class="modal" rel="{handler: \'iframe\', size: {x:'.$this->int_modal_width.',y:'.$this->int_modal_height.'}}">';
		if($this->int_link_type == 1)
		{
			$str_output = $str_output. $this->str_label;
		}
		else
		{
			$str_output = $str_output. $str_link;
		}
		$str_output = $str_output. '</a>';
		if($this->flg_use_tags)
		{
			if($flg_add_title)
			{
				$this->str_output_editor = "{zefaniabible ".$this->str_bible_alias."}".$str_link."{/zefaniabible}";
			}
			else
			{
				$this->str_output_editor = "{zefaniabible}".$str_link."{/zefaniabible}";
			}
		}
		else
		{
			$this->str_output_editor = $str_output;
		}
		$str_output = $this->str_output_editor. '<hr><legend>'.JText::_('COM_ZEFANIABIBLE_MODAL_PREVIEW').'</legend><iframe src="'.$str_url_link.'" width="700" border="1"></iframe>';
		$this->str_output_preview = $str_output;
	}
	protected function fnc_create_text_link($arr_verses, $str_Bible_book_id, $str_begin_chap, $str_end_chap, $str_begin_verse, $str_end_verse, $flg_add_title )
	{
		$verse = '';
		$x = 1;
		$int_verse_cnt = count($arr_verses);
		foreach($arr_verses as $obj_verses)
		{	
			// Genesis 1:1
			if(($str_begin_chap)and(!$str_end_chap)and($str_begin_verse)and(!$str_end_verse))
			{
				$verse = 		'<div class="zef_content_scripture">';
				$title = JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$str_Bible_book_id).' '.$str_begin_chap.':'.$str_begin_verse;
				$verse = $verse.	'<div class="zef_content_title">'.$title;
				if($flg_add_title)
				{
					$verse = $verse.' - '.$obj_verses->bible_name;
				}
				$verse = $verse.	'</div><div class="zef_content_verse"><div class="odd">'.$obj_verses->verse.'</div></div>';
				$verse = $verse.'</div>';			
			}
			// Genesis 1:1-3
			else if(($str_begin_chap)and(!$str_end_chap)and($str_begin_verse)and($str_end_verse))
			{
				if($obj_verses->verse_id == $str_begin_verse)
				{
					$verse = $verse.'<div class="zef_content_scripture">';
					$title = JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$str_Bible_book_id).' '.$str_begin_chap.':'.$str_begin_verse.'-'.$str_end_verse;
					$verse = $verse.	'<div class="zef_content_title">'.$title;
					if($flg_add_title)
					{
						$verse = $verse.' - '.$obj_verses->bible_name;
					}
					$verse = $verse.	'</div><div class="zef_content_verse" >';
				}
				if ($x % 2 )
				{
					$verse = $verse.'<div class="odd">';
				}
				else
				{
					$verse = $verse.'<div class="even">';
				}
				$verse = $verse.	'<div class="zef_content_verse_id" >'.$obj_verses->verse_id.'</div>';
				$verse = $verse.	'<div class="zef_content_verse_text">'.$obj_verses->verse.'</div>';
				$verse = $verse.	'<div class="zef_conent_clear"></div>';
				$verse = $verse.'</div>';
				if($x == $int_verse_cnt)
				{
					$verse = $verse.'</div></div>';
				}
				$x++;				
			}
			// Genesis 1
			else if(($str_begin_chap)and(!$str_end_chap)and(!$str_begin_verse)and(!$str_end_verse))
			{
				if($obj_verses->verse_id == '1')
				{
					$verse = $verse.'<div class="zef_content_scripture">';
					$title = JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$str_Bible_book_id).' '.$str_begin_chap;
					$verse = $verse.	'<div class="zef_content_title">'.$title;
					if($flg_add_title)
					{
						$verse = $verse.' - '.$obj_verses->bible_name;
					}
					$verse = $verse.	'</div><div class="zef_content_verse" >';
				}
				if ($x % 2 )
				{
					$verse = $verse.'<div class="odd">';
				}
				else
				{
					$verse = $verse.'<div class="even">';
				}
				$verse = $verse.	'<div class="zef_content_verse_id" >'.$obj_verses->verse_id.'</div>';
				$verse = $verse.	'<div class="zef_content_verse_text">'.$obj_verses->verse.'</div>';
				$verse = $verse.	'<div class="zef_conent_clear"></div>';
				$verse = $verse.'</div>';
				if($x == $int_verse_cnt)
				{	
					$verse = $verse.'</div></div>';
				}
				$x++;				
			}
			// Genesis 1-2
			else if(($str_begin_chap)and($str_end_chap)and(!$str_begin_verse)and(!$str_end_verse))
			{
				if(($obj_verses->verse_id == '1')and($str_begin_chap == $obj_verses->chapter_id))
				{
					$verse = $verse.'<div class="zef_content_scripture">';
					$title = JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$str_Bible_book_id).' '.$str_begin_chap.'-'.$str_end_chap;
					$verse = $verse.	'<div class="zef_content_title">'.$title;
					if($flg_add_title)
					{
						$verse = $verse.' - '.$obj_verses->bible_name;
					}
					$verse = $verse.	'</div><div class="zef_content_verse" >';
				}		
				if(($obj_verses->verse_id == '1')and($str_begin_chap != $obj_verses->chapter_id))
				{
					$verse = $verse. '<hr class="zef_content_subtitle_hr"><div class="zef_content_subtitle">'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$str_Bible_book_id).' '.$obj_verses->chapter_id.'</div>';
				}
				if ($x % 2 )
				{
					$verse = $verse.'<div class="odd">';
				}
				else
				{
					$verse = $verse.'<div class="even">';
				}
				$verse = $verse.	'<div class="zef_content_verse_id" >'.$obj_verses->verse_id.'</div>';
				$verse = $verse.	'<div class="zef_content_verse_text">'.$obj_verses->verse.'</div>';
				$verse = $verse.	'<div class="zef_conent_clear"></div>';
				$verse = $verse.'</div>';		
				if($x == $int_verse_cnt)
				{	
					$verse = $verse.'</div></div>';	
				}	
				$x++;				
			}
			// Genesis 1:30-2:3
			else if(($str_begin_chap)and($str_end_chap)and($str_begin_verse)and($str_end_verse))
			{
				if(($obj_verses->verse_id == $str_begin_verse)and($str_begin_chap == $obj_verses->chapter_id))
				{
					$title = JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$str_Bible_book_id).' '.$str_begin_chap.':'.$str_begin_verse.'-'.$str_end_chap.':'.$str_end_verse;
					if($flg_add_title)
					{
						$title = $title.' - '.$obj_verses->bible_name;
					}
					$verse = $verse.'<div class="zef_content_scripture">';
					$verse = $verse.	'<div class="zef_content_title">'.$title.'</div>';
					$verse = $verse.	'<div class="zef_content_verse" >';							
				}
				if(($obj_verses->verse_id == '1')and($str_begin_chap != $obj_verses->chapter_id))
				{
					$verse = $verse. '<hr class="zef_content_subtitle_hr"><div class="zef_content_subtitle">'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$str_Bible_book_id).' '.$obj_verses->chapter_id.'</div>';
				}							
				if ($x % 2 )
				{
					$verse = $verse.'<div class="odd">';
				}
				else
				{
					$verse = $verse.'<div class="even">';
				}		
				$verse = $verse.	'<div class="zef_content_verse_id" >'.$obj_verses->verse_id.'</div>';
				$verse = $verse.	'<div class="zef_content_verse_text">'.$obj_verses->verse.'</div>';
				$verse = $verse.	'<div class="zef_conent_clear"></div>';
				$verse = $verse.'</div>';	
				if($x == $int_verse_cnt)
				{	
					$verse = $verse.'</div></div>';			
				}
				$x++;
			}
		}
		if(($this->flg_use_tags)and($this->int_link_type == 2))
		{
			if($flg_add_title)
			{
				$this->str_output_preview = 
				$this->str_output_editor = "{zefaniabible text ".$this->str_bible_alias."}".$title."{/zefaniabible}";
			}
			else
			{
				$this->str_output_editor = "{zefaniabible text}".$title."{/zefaniabible}";
			}
		}
		else
		{
			$this->str_output_editor = $verse;
			
		}
		$this->str_output_preview = $this->str_output_editor.'<hr><legend>'.JText::_('COM_ZEFANIABIBLE_TEXT_PREVIEW').'</legend>'.$verse;
	}	
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_zefaniabible&amp;view=zefaniamodal&amp;tmpl=component&amp;function='.$function);?>" method="post" name="adminForm" id="adminForm">
<fieldset class="adminform">
	<legend>Pick Scipture</legend>							   
    <div class="zef_modal" id="zef_modal">
        <div class="zef_modal_type" id="zef_modal_type">
            <label id="zef_link_type_label" title="<?php echo JText::_('COM_ZEFANIABIBLE_LINK_TYPE_DESC'); ?>"><?php echo JText::_('COM_ZEFANIABIBLE_LINK_TYPE'); ?></label>
            <select name="a" id="zef_button_link_type" class="inputbox"  onchange="fnc_show_hide('zef_label');">
                <option value="0"<?php if($cls_button_scripture->int_link_type == 0){?>selected<?php }?>><?php echo JText::_('COM_ZEFANIABIBLE_LINK_TYPE_DEFAULT'); ?></option>
                <option value="1"<?php if($cls_button_scripture->int_link_type == 1){?>selected<?php }?>><?php echo JText::_('COM_ZEFANIABIBLE_LINK_TYPE_LABEL'); ?></option>
                <option value="2"<?php if($cls_button_scripture->int_link_type == 2){?>selected<?php }?>><?php echo JText::_('COM_ZEFANIABIBLE_LINK_TYPE_TEXT'); ?></option>
                <option value="3"<?php if($cls_button_scripture->int_link_type == 3){?>selected<?php }?>><?php echo JText::_('COM_ZEFANIABIBLE_LINK_TYPE_TOOLTIP'); ?></option>
                <option value="4"<?php if($cls_button_scripture->int_link_type == 4){?>selected<?php }?>><?php echo JText::_('COM_ZEFANIABIBLE_LINK_TYPE_BIBLEGATEWAY'); ?></option>
                <option value="5"<?php if($cls_button_scripture->int_link_type == 5){?>selected<?php }?>><?php echo JText::_('COM_ZEFANIABIBLE_LINK_TYPE_BIBLEGATEWAY')." ".JText::_('COM_ZEFANIABIBLE_LINK_TYPE_LABEL'); ?></option>
            </select>
            <div class="zef_conent_clear"></div>
        </div>
        <div class="zef_scriputure_tags_div" id="zef_scriputure_tags_div">
        	<label id="zef_scriputure_tags_label"><?php echo JText::_('COM_ZEFANIABIBLE_SCRIPTURE_LINK_TAGS') ?></label>
        	<input type="checkbox" name="i" id="zef_scriputure_tags" <?php if($cls_button_scripture->flg_use_tags){?>checked<?php }?>/>
            <div class="zef_conent_clear"></div>
        </div>
        <div style="clear:both"></div>
		<div class="zef_label" id="zef_label">
        	<label><?php echo JText::_('COM_ZEFANIABIBLE_LABEL') ?></label>
			<input name="h" id="zef_button_label" value="<?php echo $cls_button_scripture->str_label; ?>" title="<?php echo JText::_('COM_ZEFANIABIBLE_LABEL') ?>" type="text" maxlength="25" size="25" />
            <div class="zef_conent_clear"></div>
		</div>
        <div style="clear:both"></div>
        <div class="zef_alias" id="zef_alias">
            <label class="zef_scripture_label"><?php echo JText::_('ZEFANIABIBLE_FIELD_ALIAS'); ?></label>
            <select name="b" id="zef_button_bible_alias" class="inputbox">
                <option value="" ><?php echo JText::_('ZEFANIABIBLE_JSEARCH_SELECT_BIBLE_VERSION'); ?></option>
                <?php 
                    foreach($this->arr_bible_list as $obj_bible_list)
                    {
						if($obj_bible_list->alias == $cls_button_scripture->str_bible_alias)
						{
							echo '<option value="'.$obj_bible_list->alias.'" selected>'.$obj_bible_list->bible_name.'</option>';
						}
						else
						{
                        	echo '<option value="'.$obj_bible_list->alias.'">'.$obj_bible_list->bible_name.'</option>';
						}
                    }
                ?>
            </select>
            <div class="zef_conent_clear"></div>
        </div>
        <div style="clear:both"></div>
        <div class="zef_scripture" id="zef_scripture">
            <div>
                <label><?php echo JText::_('ZEFANIABIBLE_FIELD_BIBLE_SCRIPTURE');?></label>
                <select name="c" id="zef_button_bible_book" class="inputbox" title="<?php echo JText::_('ZEFANIABIBLE_FIELD_BIBLE_BOOK_NAME');?>">
                    <option value="0" ><?php echo JText::_('ZEFANIABIBLE_JSEARCH_SELECT_BOOK_ID'); ?></option>
                    <?php 
                        for($x = 1; $x <= 66; $x++)
                        {
							if($cls_button_scripture->int_bible_book_id == $x)
							{
								echo '<option value="'.$x.'" selected>'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$x).'</option>';
							}
							else
							{
                            	echo '<option value="'.$x.'" >'.JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$x).'</option>';
							}
                        }
                    ?>
                </select>
            </div>
            <div>
                <input  name="d" id="zef_button_begin_chap" value="<?php if($cls_button_scripture->int_begin_chap != 0){ echo $cls_button_scripture->int_begin_chap;}?>" title="<?php echo JText::_('ZEFANIABIBLE_FIELD_BEGIN_CHAPTER') ?>" type="text" maxlength="3" size="5" />
            </div>
            <div>:</div>
            <div>
                <input  name="e" id="zef_button_begin_verse" value="<?php if($cls_button_scripture->int_begin_verse != 0){echo $cls_button_scripture->int_begin_verse;}?>" title="<?php echo JText::_('ZEFANIABIBLE_FIELD_BEGIN_VERSE') ?>" type="text" maxlength="3" size="5" />
            </div>
            <div>-</div>
            <div>
                <input  name="f" id="zef_button_end_chap" value="<?php if($cls_button_scripture->int_end_chap != 0){echo $cls_button_scripture->int_end_chap;}?>" title="<?php echo JText::_('ZEFANIABIBLE_FIELD_END_CHAPTER') ?>" type="text" maxlength="3" size="5" />
            </div>
            <div>:</div>
            <div>
                <input  name="g" id="zef_button_end_verse" value="<?php if($cls_button_scripture->int_end_verse !=0){echo $cls_button_scripture->int_end_verse;}?>" title="<?php echo JText::_('ZEFANIABIBLE_FIELD_END_VERSE') ?>" type="text" maxlength="3" size="5" />
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="zef_conent_clear"></div>
        <div>
        	<input type="submit" />
            <button type="button" onclick="fnc_save();" <?php if($cls_button_scripture->str_output_editor == ''){?>disabled<?php }?>><?php echo JText::_('COM_ZEFANIABIBLE_SAVE_CLOSE');?></button> 
        </div>
    </div>
</fieldset>
<fieldset class="adminform">
	<legend><?php echo JText::_('COM_ZEFANIABIBLE_SCRIPTURE_PREVIEW'); ?></legend>
	<div class="zef_button_output">
    	<?php echo $cls_button_scripture->str_output_preview; ?>
    </div>
</fieldset>
</form>
<script language="javascript">
if((document.getElementById("zef_button_link_type").value != 1)&&(document.getElementById("zef_button_link_type").value != 5))
{
	document.getElementById('zef_label').style.visibility="hidden";
	document.getElementById('zef_label').style.display="none";
}
function fnc_show_hide(id)
{
	if((document.getElementById("zef_button_link_type").value == 1)||(document.getElementById("zef_button_link_type").value == 5))
	{
		document.getElementById(id).style.visibility="visible";
		document.getElementById(id).style.display="block";
	}
	else
	{
		document.getElementById(id).style.visibility="hidden";
		document.getElementById(id).style.display="none";
	}
}
function fnc_save()
{
	var str_html_button = '<?php echo addslashes($cls_button_scripture->str_output_editor);?>';
	window.parent.jSelectScripture(str_html_button);
	window.parent.SqueezeBox.close();	
}
</script>
