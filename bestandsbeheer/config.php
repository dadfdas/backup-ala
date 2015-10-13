<?php
        // Pad naar de iconnen
$_SESSION['fileicons'] = 'icons/'; 

$FilePad = '/home/kennisbus.nl/www/uploads/';                // Root directory voor het script
$_SESSION['rootname'] = 'Main';


$GLOBALS['FileTypes'] = array(
    'png'	=> array('page_white_picture.png', 'PNG Image'),
    'gif'	=> array('page_white_picture.png', 'GIF Image'),
    'html'	=> array('page_white_world.png', 'HTML File'),
    'htm'	=> array('page_white_world.png', 'HTML File'),
    'jpeg'	=> array('picture.png', 'JPG Image'),
    'jpg'	=> array('picture.png', 'JPG Image'),
    'ico'	=> array('page_white_picture.png', 'BMP Image'),
    'bmp'	=> array('page_white_picture.png', 'BMP Image'),
    'zip'	=> array('compress.png', 'Archive'),
    'rar'	=> array('compress.png', 'Archive'),
    'tar'	=> array('compress.png', 'Archive'),
    'gz'	=> array('compress.png', 'Archive'),
    '7z'	=> array('compress.png', 'Archive'),
    'swf'	=> array('page_white_flash.png', 'Flash Movie'),
    'css'	=> array('page_white_code.png', 'CSS File'),
    'xml'	=> array('page_white_code.png', 'XML File'),
	'js'	=> array('page_white_code_red.png', 'JavaScript File'),
    'txt'	=> array('page_white_text.png', 'Text File'),
    'php'	=> array('page_white_php.png', 'PHP File'),
    'doc'	=> array('page_white_word.png', 'MS Word File'),
    'xls'	=> array('page_white_excel.png', 'MS Exel File'),
    'dot'	=> array('page_white_office.png', 'MS Office File'),
    'pub'	=> array('page_white_office.png', 'MS Publisher File'),
    'vsd'	=> array('page_white_office.png', 'MS Publisher File'),
    'mdb'	=> array('page_white_database.png', 'MS Access File'),
    'pdf'	=> array('page_white_acrobat.png', 'Adobe File'),
    'psd'	=> array('page_white_acrobat.png', 'Adobe File'),
    'esp'	=> array('page_white_acrobat.png', 'Adobe File'),
    'mp3'	=> array('music.png', 'Music File'),
    'wma'	=> array('music.png', 'MS Media File'),
    'wav'	=> array('music.png', 'Sound File'),
    'exe'	=> array('application_xp_terminal.png', 'Executable'),
    'bat'	=> array('application_xp_terminal.png', 'Executable'),
    'cmd'	=> array('application_xp_terminal.png', 'Executable'),
    'com'	=> array('application_xp_terminal.png', 'Executable'),
	'ini'	=> array('page_white_gear.png', 'Settings File'),
    'default'	=> array('page_white.png', 'File'),

	
);
?>