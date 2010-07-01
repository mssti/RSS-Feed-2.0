<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> it -> mods 
* @version: $Id: rss.php, v 1.0.4 2008/08/18 18:08:08 leviatan21 Exp $
* @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
*
**/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACP_RSS'					=> 'Parametri RSS',
	'ACP_RSS_FEEDS'				=> 'RSS',

	'BOARD_DAYS'				=> 'Giorni dalla data di avvio',
	'ACP_RSS_MANAGEMENT'		=> 'RSS Feeds',
	'ACP_RSS_MANAGEMENT_EXPLAIN'=> 'Questo modulo consente di regolare le impostazioni predefinite del lettore di RSS, e la configurazione del feed RSS per essere lette da un lettore di feed',
	'ACP_RSS_ENABLE'			=> 'Attiva RSS',
	'ACP_RSS_ENABLE_EXPLAIN'	=> 'Disattiva questo, disattivare tutti i feed, non importa la seguente configurazione',
	'ACP_RSS_CHARACTERS'		=> 'La lunghezza massima del testo dei messaggi per visualizzare',
	'ACP_RSS_CHARACTERS_EXPLAIN'=> 'Numero di caratteri consentiti, caratteri raccomandati 1000. <br /> 0 significa infinito, 1 significa nessun testo',
	'ACP_RSS_CHARS'				=> 'Caratteri',
	'ACP_RSS_LIMIT'				=> 'Oggetti per pagina',
	'ACP_RSS_LIMIT_EXPLAIN'		=> 'Il numero massimo di feed da visualizzare per pagina.',
	'ACP_RSS_IMAGE_SIZE'		=> 'Larghezza massima di immagine in pixel',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'=> 'Ridimensionare le immagini se la larghezza è superiore al stabilito qui. <br /> 0 significa nessun cambiamento',

	'ACP_RSS_OVERALL_FORUMS'	=> 'Attiva RSS per tutti i forum',
	'ACP_RSS_OVERALL_THREAD'	=> 'Attiva RSS per tutti gli oggetti',
	'ACP_RSS_OVERALL_POSTS'		=> 'Attiva RSS per tutti i messaggi',
	'ACP_RSS_EGOSEARCH'			=> 'Attiva RSS per tutti i messaggi',
	'ACP_RSS_EGOSEARCH_EXPLAIN'	=> 'Come vedere i loro messaggi, funziona solo se avete collegato soggiorno quando la navigazione al di fuori del forum ...',
	'ACP_RSS_FORUM'				=> 'Abilitare il RSS di forum',
	'ACP_RSS_FORUM_EXPLAIN'		=> 'Consente di visualizzare il forum di una determinata categoria',
	'ACP_RSS_THREAD'			=> 'Attiva RSS per oggetti',
	'ACP_RSS_THREAD_EXPLAIN'	=> 'Consente di visualizzare il forum di un particolare elemento',

	'COPYRIGHT'					=> 'Copyright',
	'NO_RSS_ITEMS'				=> 'Nessun elemento disponibile',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Purtroppo non sembra esistere alcuna notizia della pagina che hai richiesto',
	'NO_RSS_ITEMS_LOGGED_IN'	=> 'Devi essere Conesso per visualizzare il lettore RSS Feed %1$s',

));

?>
