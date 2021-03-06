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
$cls_player= new BiblePlayer($this->arr_book_info, $this->int_Bible_Book_ID); 

class BiblePlayer 
{

	
	public function __construct($arr_book_info, $int_Bible_Book_ID)
	{
		$params = JComponentHelper::getParams( 'com_zefaniabible' );
		$str_xml_audio_path = $params->get('xmlAudioPath', 'media/com_zefaniabible/audio/');
		$int_player_type = $params->get('player_type', '0');
		$int_player_popup_height = $params->get('player_popup_height','300');
		$int_player_popup_width = $params->get('player_popup_width','300');		
		$arr_mp3_files = '';
		foreach($arr_book_info as $arr_book)
		{
			$str_alias 			= $arr_book->alias;
			$str_bible_name		= $arr_book->bible_name;
			$str_xml_audio_url	= $arr_book->xml_audio_url;
		}
		$arr_mp3_files = $this->fnc_get_playlist($str_xml_audio_url,$str_xml_audio_path, $int_Bible_Book_ID);
		$this->fnc_get_player($arr_mp3_files, $int_player_type, $int_Bible_Book_ID, $int_player_popup_height, $int_player_popup_width );
		
	}
	protected function fnc_get_playlist($str_xml_audio_url,$str_xml_audio_path, $int_Bible_Book_ID )
	{
		$arr_audio_file = simplexml_load_file(substr_replace(JURI::root(),"",-1).$str_xml_audio_url);
		$x = 1;		
		foreach($arr_audio_file as $obj_book)
		{
			if($int_Bible_Book_ID == $obj_book['id'])
			{
				foreach($obj_book as $obj_chapter)
				{
				//	$arr_mp3_files[$x] = JURI::root().$str_xml_audio_path.$obj_chapter;
					$arr_fileinfo = pathinfo($str_xml_audio_url);							
					$arr_mp3_files[$x] =  str_replace($arr_fileinfo['basename'],'',$str_xml_audio_url).$obj_chapter;					
					$x++;
				}
			}
		}
		return $arr_mp3_files;
	}
	protected function fnc_get_player($arr_mp3_files,$int_player_type, $int_Bible_Book_ID, $int_player_popup_height, $int_player_popup_width)
	{
		$doc_page = JFactory::getDocument();
		switch ($int_player_type)
		{
		case 0:
			{
				//JW Player
				$doc_page->addScript('media/com_zefaniabible/player/jwplayer/jwplayer.js');
				echo "<div id='mediaspace'>".JText::_('ZEFANIABIBLE_BIBLE_ENABLE_FLASH')."</div>".PHP_EOL;
				echo "<script type='text/javascript' src='".JURI::root()."media/com_zefaniabible/player/jwplayer/jwplayer.js'></script>".PHP_EOL;
				echo "<script type='text/javascript'>".PHP_EOL;
				echo "jwplayer('mediaspace').setup({".PHP_EOL;
				echo "'flashplayer': '". JURI::root()."media/com_zefaniabible/player/jwplayer/player.swf',".PHP_EOL;
				echo "'playlist': [".PHP_EOL;
				$x=1;
				foreach($arr_mp3_files as $playlist)
				{
					echo "{'file': '".$playlist."',".PHP_EOL;
					echo "'title': '".JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$int_Bible_Book_ID)." ".$x."',".PHP_EOL;
					echo " 'image': '".JURI::root()."components/com_zefaniabible/images/bible_100.jpg'},".PHP_EOL;
					$x++;
				}	

				echo "],".PHP_EOL;
				echo "'playlist.position': 'bottom',".PHP_EOL;
				echo "'playlist.size': '200',".PHP_EOL;
				echo "autostart:'true',".PHP_EOL;
				echo "repeat:'list',".PHP_EOL;
				echo "'controlbar': 'bottom',".PHP_EOL;
				echo "'width': '".$int_player_popup_width."',".PHP_EOL;
				echo "'height': '".$int_player_popup_height."'".PHP_EOL;
				echo "})";
				echo "</script>";
			}
			break;
		default:
			// flow player
			$doc_page->addScript('media/com_zefaniabible/player/flowplayer/flowplayer-3.2.6.min.js');
			?>
                <a href="<?php echo $arr_mp3_files[1];?>"
                    style="display:block;width:<?php echo $int_player_popup_width;?>px;height:<?php echo $int_player_popup_height;?>px;"
                    id="mediaspace">
                </a>   
				<script language="JavaScript">
                flowplayer("mediaspace", "<?php echo JURI::root();?>media/com_zefaniabible/player/flowplayer/flowplayer-3.2.7.swf", {
					plugins: 
					{
						controls: 
						{
							fullscreen: false,
							playlist: true,
							widith: <?php echo $int_player_popup_width;?>,
							height: <?php echo $int_player_popup_height;?>,
							autoHide: false,
							loop:true
						}
					},
					playlist: [
					<?php 
					$x=1;
					foreach($arr_mp3_files as $playlist) {?>
						{
							url: '<?php echo $playlist;?>',
							title: '<?php echo JText::_('ZEFANIABIBLE_BIBLE_BOOK_NAME_'.$int_Bible_Book_ID)." ".$x;?>'
						},
					<?php $x++;}?>
					] ,					
					clip: 
					{
						url: "<?php echo $arr_mp3_files[1];?>",
						autoPlay: true,
					}
				});
				</script>                     
            <?php
			break;
		}		
	}
}
?>
