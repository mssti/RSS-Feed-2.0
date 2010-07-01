<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> fr -> mods 
* @version: $Id: rss.php, v 1.0.4 2008/08/18 18:08:08 leviatan21 Exp $
* @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
* @translator : Lhyrwaen - lhyrwaenhe@free.fr
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
	'ACP_RSS'					=> 'Gestion RSS',
	'ACP_RSS_FEEDS'				=> 'RSS',
	'BOARD_DAYS'				=> 'Jours depuis le début',
	'ACP_RSS_MANAGEMENT'		=> 'Flux RSS',
	'ACP_RSS_MANAGEMENT_EXPLAIN'=> 'Ce module contrôle l’activation des différents flux RSS, et les paramètres de l’analyseur de flux RSS pour la lecture en flux externe',
	'ACP_RSS_ENABLE'			=> 'Activer les flux',
	'ACP_RSS_ENABLE_EXPLAIN'	=> 'Désactivez cette option pour désactiver tous les flux, indépendamment des paramètres ci-dessous',
	'ACP_RSS_CHARACTERS'		=> 'Longueur maximum du texte du post à afficher',
	'ACP_RSS_CHARACTERS_EXPLAIN'=> 'Nombre de caractères autorisés, 1000 recommandé.<br />Mettre "0" pour ne pas imposer de limite, "1" pour aucun texte.',
	'ACP_RSS_CHARS'				=> 'caractères',
	'ACP_RSS_LIMIT'				=> 'articles par page',
	'ACP_RSS_LIMIT_EXPLAIN'		=> 'Le nombre d’articles à afficher par page',
	'ACP_RSS_IMAGE_SIZE'		=> 'Largeur maximum d’image en pixels',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'=> 'L’image sera redimensionnée si la largeur dépasse.', 

	'ACP_RSS_OVERALL_FORUMS'	=> 'Activer les flux de l’ensemble des forums',
	'ACP_RSS_OVERALL_THREAD'	=> 'Activer les flux de l’ensemble des topics',
	'ACP_RSS_OVERALL_POSTS'		=> 'Activer les flux de l’ensemble des posts',
	'ACP_RSS_EGOSEARCH'			=> 'Activer les flux EgoSearch',
	'ACP_RSS_EGOSEARCH_EXPLAIN'	=> 'Comme ’voir ses messages’, ne fonctionne que si vous êtes connecté en surfant',
	'ACP_RSS_FORUM'				=> 'Activer le flux du forum', 
	'ACP_RSS_FORUM_EXPLAIN'		=> 'Pour un topic ou un post d’un forum',
	'ACP_RSS_THREAD'			=> 'Activer le flux du topic',
	'ACP_RSS_THREAD_EXPLAIN'	=> 'Pour les nouveaux posts d’UN topic',

	'COPYRIGHT'					=> 'Copyright',
	'NO_RSS_ITEMS'				=> 'Aucun article disponible',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Malheureusement, il n’y a aucun nouvel article sur la page demandée.',
	'NO_RSS_ITEMS_LOGGED_IN'	=> 'Vous devez être connecté pour utiliser les flux RSS de %1'

));

?>