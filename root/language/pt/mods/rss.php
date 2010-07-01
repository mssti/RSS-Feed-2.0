<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> en -> mods 
* @version: $Id: rss.php, v 0.0.1 2008/06/26 06:26:00 leviatan21 Exp $
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
	'ACP_RSS'					=> 'Gestão de RSS',
	'ACP_RSS_FEEDS'				=> 'RSS',

	'BOARD_DAYS'				=> 'Dias desde o início',
	'ACP_RSS_MANAGEMENT'		=> 'RSS Feeds',
	'ACP_RSS_MANAGEMENT_EXPLAIN'=> 'Este módulo controla a activação de diversos RSS Feeds, e as definições do RSS Feed Parser para poder ser lido em feeds externos',
	'ACP_RSS_ENABLE'			=> 'Activar Feeds',
	'ACP_RSS_ENABLE_EXPLAIN'	=> 'Desactivae esta opção para desligar todos os Feeds, independentemente das definições abaixo',
	'ACP_RSS_CHARACTERS'		=> 'Tamanho máximo das mensagens a ser apresentadas',
	'ACP_RSS_CHARACTERS_EXPLAIN'=> 'O número de caracteres permitidos, é recomendado o uso de 1000 caracteres.<br /> 0 significa sem limite e 1 nenhum texto',
	'ACP_RSS_CHARS'				=> 'caracteres',
	'ACP_RSS_LIMIT'				=> 'Items por página',
	'ACP_RSS_LIMIT_EXPLAIN'		=> 'O número máximo de items para apresentar por página.',
	'ACP_RSS_IMAGE_SIZE'		=> 'Largura máxima das imagens em píxeis.',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'=> 'A imagem será redimensionada se exceder a largura definida aqui.<br /> 0 significa que não será redimensionada.',
	
	'ACP_RSS_OVERALL_FORUMS'	=> 'Activar a feed para todos os forums',
	'ACP_RSS_OVERALL_THREAD'	=> 'Activar a feed para todos os tópicos',
	'ACP_RSS_OVERALL_POSTS'		=> 'Activar a feed para todas as mensagens',
	'ACP_RSS_EGOSEARCH'			=> 'Activar a feed para mensagens próprias',
	'ACP_RSS_EGOSEARCH_EXPLAIN'	=> 'Identico a opção Ver as suas mensagens, apenas funciona se mantiver o registo activado quando navega para fora do forum...',
	'ACP_RSS_FORUM'				=> 'Activar a feed para um fórum',
	'ACP_RSS_FORUM_EXPLAIN'		=> 'Apenas para um Forum, tópicos e mensagens',
	'ACP_RSS_THREAD'			=> 'Activar a feed para um tópico',
	'ACP_RSS_THREAD_EXPLAIN'	=> 'Apenas um tópico e as suas novas mensagens',

	'COPYRIGHT'					=> 'Copyright',
	'NO_RSS_ITEMS'				=> 'Não há items disponíveis',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Infelizmente não existem novos items na página requisitada.',
	'NO_RSS_ITEMS_LOGGED_IN'	=> 'Tem de estar registado para usar a RSS Feed %1$s ',

));

?>
