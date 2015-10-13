<?php
function MsgPopup($msg=''){
	if(isset($msg)){
		$print  = '<div id="popupcenter">
		<table width="100%" height="80%"><tr><td valign="middle" align="center">
		<div id="popup"><div class="bar"><table><tr><td align="left">'.$msg['title'].'</td><td 
				align="right"></td></tr></table></div>'."\r\n";
				$print .= '<div class="PopupText">'. $msg['text'].'<br /></div></div>
		</td></tr></table>
	</div>'."\r\n";
	return $print;
	}
}
?>