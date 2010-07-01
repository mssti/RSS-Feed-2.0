<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> fr -> mods 
* @version: $Id: rss.php, v 1.2.0 2009/04/10 10:04:09 leviatan21 Exp $
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
	'MSSTI_LINK'						=> 'Flux RSS par <a href="http://www.mssti.com/phpbb3/" onclick="window.open(this.href);return false;" >.:: MSSTI ::.</a>',
	'ACP_RSS'							=> 'Param&egrave;tres RSS',
	'ACP_RSS_FEEDS'						=> 'Flux RSS',
	'ACP_RSS_MANAGEMENT'				=> 'Configuration Générale des Flux RSS',
	'ACP_RSS_MANAGEMENT_EXPLAIN'		=> 'Ce Module met &agrave; disposition divers Flux RSS qui analysent les BBCodes pour les rendre lisibles sur des flux externes.',
	'ACP_RSS_MIN'						=> 'La valeur minimale autorisée est %1$s.',

// ACP Feeds to Serve
	'ACP_RSS_LEGEND1'					=> 'Assigner des Flux',

	'ACP_RSS_ENABLE'					=> 'Activer les Flux',
	'ACP_RSS_ENABLE_EXPLAIN'			=> 'Active ou désactive le RSS sur tout le site.<br />En désactivant cette option vous désactivez tous les Flux, sans appliquer la configuration ci-dessous.<br />Ajoute aussi le flux principal avec les messages des 7 derniers jours.',
	'ACP_RSS_NEWS'						=> 'Flux des News',
	'ACP_RSS_NEWS_EXPLAIN'				=> 'Affiche le premier sujet des forums aux IDs suivantes. Séparez les IDs par une virgule pour plusieurs forums, ex: 1,2,5.<br />Laissez vide pour désactiver le Flux "News".',
	'ACP_RSS_NEWPOST'					=> 'Nouveaux messages',
	'ACP_RSS_NEWPOST_EXPLAIN'			=> 'Flux semblable &agrave; "Voir les nouveaux messages". Pour les utilisateurs, affiche les nouveaux messages depuis leur derni&egrave;re visite. Pour les invités, affiche les messages des 7 derniers jours.',
	'ACP_RSS_OVERALL_FORUMS'			=> 'Activer le Flux "Tous les forums"',
	'ACP_RSS_OVERALL_FORUMS_EXPLAIN'	=> 'Affiche une liste des Forums et des Sous-forums.',
	'ACP_RSS_OVERALL_FORUMS_LIMIT'		=> 'Nombre d\'objets affichés par page dans le Flux "Tous les forums"',
	'ACP_RSS_OVERALL_THREAD'			=> 'Activer le Flux "Tous les sujets"',
	'ACP_RSS_OVERALL_THREAD_EXPLAIN'	=> 'Affiche les messages par sujets, rangés dans l\'ordre des derniers messages postés.',
	'ACP_RSS_OVERALL_THREAD_LIMIT'		=> 'Nombre d\'objets affichés par page dans le Flux "Tous les sujets"',
	'ACP_RSS_OVERALL_POSTS'				=> 'Activer le Flux "Tous les messages"',
	'ACP_RSS_OVERALL_POSTS_EXPLAIN'		=> 'Affiche les messages, rangés dans l\'ordre des derniers messages postés.',
	'ACP_RSS_OVERALL_POSTS_LIMIT'		=> 'Nombre d\'objets affichés par page dans le Flux "Tous les messages"',

	'ACP_RSS_EGOSEARCH'					=> 'Activer le Flux "Vos messages"',
	'ACP_RSS_EGOSEARCH_EXPLAIN'			=> 'Flux semblable &agrave; "Voir vos messages". Ne fonctionne que si vous restez connecté apr&egrave;s avoir quitté le forum.',
	'ACP_RSS_EGOSEARCH_LIMIT'			=> 'Nombre d\'objets affichés par page dans le Flux "Vos messages"',
	'ACP_RSS_FORUM'						=> 'Activer les Flux par forum',
	'ACP_RSS_FORUM_EXPLAIN'				=> 'Permet de voir un forum en particulier.',
	'ACP_RSS_THREAD'					=> 'Activer les Flux par sujet',
	'ACP_RSS_THREAD_EXPLAIN'			=> 'Permet de voir les nouveaux messages d\'un sujet en particulier.',
	'ACP_RSS_ATTACH'					=> 'Activer les Flux des pi&egrave;ces-jointes',
	'ACP_RSS_ATTACH_EXPLAIN'			=> 'Affiche les pi&egrave;ces-jointes.',

// ACP General RSS Settings
	'ACP_RSS_LEGEND2'					=> 'Configuration Générale du RSS',

	'ACP_RSS_LIMIT'						=> 'Nombre d\'objets', // par page',
	'ACP_RSS_LIMIT_EXPLAIN'				=> 'Nombre maximum d\'objets affichés dans un flux.', // par page, si la pagination est activée.',
	'ACP_RSS_CHARACTERS'				=> 'Longueur maximale du texte dans les messages &agrave; afficher',
	'ACP_RSS_CHARACTERS_EXPLAIN'		=> 'Nombre maximum de caract&egrave;res visibles pour chaque objet dans un flux, réglage recommandé : 1000.<br />0 signifie "illimité", 1 signifie "pas de texte".<br />Si vous manquez d\'expérience n\'utilisez que 0 ou 1.',
	'ACP_RSS_CHARS'						=> 'caract&egrave;res',
	'ACP_RSS_ATTACHMENTS'				=> 'Pi&egrave;ces-jointes',
	'ACP_RSS_ATTACHMENTS_EXPLAIN'		=> 'Affiche les pi&egrave;ces-jointes dans les flux',
	'ACP_RSS_IMAGE_SIZE'				=> 'Largeur maximale des images en pixels',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'		=> 'La taille des images affichées dans les flux sera changée si leur largeur est supérieure &agrave; celle indiquée ici.<br />0 désactive le redimensionnement.<br />Fonction PHP <em>getimagesize()</em> <strong>requise</strong>',
	'ACP_RSS_AUTH'						=> 'Omettre les permissions',
	'ACP_RSS_AUTH_EXPLAIN'				=> 'Si cette fonction est activée, les messages seront inclus dans les flux sans prendre en compte les restrictions que vous avez configurées.',
	'ACP_RSS_BOARD_STATISTICS'			=> 'Statistiques du Site',
	'ACP_RSS_BOARD_STATISTICS_EXPLAIN'	=> 'Affiche les Statistiques du Site sur la premi&egrave;re page du flux principal.',
	'ACP_RSS_ITEMS_STATISTICS'			=> 'Statistiques des Objets',
	'ACP_RSS_ITEMS_STATISTICS_EXPLAIN'	=> 'Affiche les statistiques de chaque objet dans les Statistiques du Site<br />(Posté par + date et heure + Réponses + Vues)',
	'ACP_RSS_PAGINATION'				=> 'Pagination des Flux',
	'ACP_RSS_PAGINATION_EXPLAIN'		=> 'Limite le nombre d\'objets visibles s\'il y en a plus que le nombre d\'objets par page.',
	'ACP_RSS_PAGINATION_LIMIT'			=> 'Limite d\'objets par page',
	'ACP_RSS_PAGINATION_LIMIT_EXPLAIN'	=> 'Si la pagination est activée et que le flux affiche plus d\'objets que cette valeur, le flux de la page sera divisé en plusieurs pages.',
	'ACP_RSS_EXCLUDE_ID'				=> 'Exclure les Forums suivants',
	'ACP_RSS_EXCLUDE_ID_EXPLAIN'		=> 'Les données provenant des IDs de ces forums et de leurs sous-forums <strong>n\'appara&icirc;tront pas</strong> dans le RSS. Séparez les IDs par une virgule pour plusieurs forums, ex: 1,2,5.<br />Laissez vide pour afficher tous les forums.',

// FEED text
	'BOARD_DAYS'				=> 'Age du Site en jours',
	'COPYRIGHT'					=> 'Droits d\'auteurs',
	'NO_RSS_ENABLED'			=> 'Les Flux RSS ne sont pas activés.',
	'NO_RSS_FEED'				=> 'Flux RSS introuvable.',
	'NO_RSS_ITEMS'				=> 'Aucun objet disponible',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Malheureusement il semble n\'y avoir aucun nouvel objet sur la page que vous avez sélectionnée',
	'NO_RSS_ITEMS_LOGGED_IN'	=> 'Vous devez &ecirc;tre connecté pour utiliser le Flux RSS %1$s',

));

?>