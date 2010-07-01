<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> pt -> mods 
* @version: $Id: rss.php, v 1.0.6 2009/01/10 10:01:09 leviatan21 Exp $
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
    'MSSTI_LINK'				=> 'RSS Feeds por <a href="http://www.mssti.com/phpbb3/" onclick="window.open(this.href);return false;" >.:: MSSTI ::.</a>',
	'ACP_RSS'					=> 'Gestão de RSS',
	'ACP_RSS_FEEDS'				=> 'RSS',
	
// ACP General settings
	'ACP_RSS_MANAGEMENT'				=> 'Configurações de RSS Feeds',
	'ACP_RSS_MANAGEMENT_EXPLAIN'		=> 'Este módulo controla a activação das diversas RSS Feeds, e as definições do RSS Feed Parser para poder ser lido em feeds externos',
	'ACP_RSS_ENABLE'					=> 'Activar Feeds',
	'ACP_RSS_ENABLE_EXPLAIN'			=> 'Esta opção permite controlar a apresentação das Feeds. Desactive esta opção para desactivar todos os Feeds, independentemente das definições abaixo',
	'ACP_RSS_FORUM'						=> 'Activar a feed para um fórum',
	'ACP_RSS_FORUM_EXPLAIN'				=> 'Apenas para um Forum, tópicos e mensagens',
	'ACP_RSS_THREAD'					=> 'Activar a feed para um tópico',
	'ACP_RSS_THREAD_EXPLAIN'			=> 'Apenas um tópico e as suas novas mensagens',
	'ACP_RSS_CHARACTERS'				=> 'Tamanho máximo das mensagens a ser apresentado',
	'ACP_RSS_CHARACTERS_EXPLAIN'		=> 'O número de caracteres permitidos, é recomendado o uso de 1000 caracteres.<br /> 0 significa sem limite e 1 sem texto',
	'ACP_RSS_CHARS'						=> 'caracteres',
	'ACP_RSS_LIMIT'						=> 'Items por página',
	'ACP_RSS_LIMIT_EXPLAIN'				=> 'O número máximo de items para apresentar por página.',
	'ACP_RSS_IMAGE_SIZE'				=> 'Largura máxima das imagens em píxeis.',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'		=> 'A imagem será redimensionada se exceder a largura definida aqui.<br /> 0 significa que não será redimensionada.',
	'ACP_RSS_BOARD_STATISTICS'			=> 'Estatísticas do forum',
	'ACP_RSS_BOARD_STATISTICS_EXPLAIN'	=> 'Apresenta as estatísticas do forum na primeira página da feed.',
	'ACP_RSS_ITEMS_STATISTICS'			=> 'Estatísticas de items',
	'ACP_RSS_ITEMS_STATISTICS_EXPLAIN'	=> 'Apresenta estatísticas de items individuais<br />( Colocado em + data e hora + respostas + visitas )',
	'ACP_RSS_PAGINATION'				=> 'Paginação da feed',
	'ACP_RSS_PAGINATION_EXPLAIN'		=> 'Apresenta paginação se necessário.',
// ACP Individual settings
	'ACP_RSS_OVERALL_FORUMS'			=> 'Activar a feed para todos os forums',
	'ACP_RSS_OVERALL_FORUMS_EXPLAIN'	=> 'Esta opção permite activar a apresentação da feed "Todos os forums".',
	'ACP_RSS_OVERALL_FORUMS_LIMIT'		=> 'Número de items por página para apresentar na feed dos forums',
	'ACP_RSS_OVERALL_THREAD'			=> 'Activar a feed para todos os tópicos',
    'ACP_RSS_OVERALL_THREAD_EXPLAIN'	=> 'Esta opção permite activar a apresentação da feed "Todos os tópicos".',
	'ACP_RSS_OVERALL_THREAD_LIMIT'		=> 'Número de items por página para apresentar na feed dos tópicos',
	'ACP_RSS_OVERALL_POSTS'				=> 'Activar a feed para todas as mensagens',
	'ACP_RSS_OVERALL_POSTS_EXPLAIN'		=> 'Esta opção permite activar a apresentação da feed "Todas as mensagens".',
	'ACP_RSS_OVERALL_POSTS_LIMIT'		=> 'Número de items por página para apresentar na feed das mensagens',
	'ACP_RSS_EGOSEARCH'					=> 'Activar a feed para mensagens próprias',
	'ACP_RSS_EGOSEARCH_EXPLAIN'			=> 'Identico a opção Ver as suas mensagens, apenas funciona se mantiver o registo activado quando navega para fora do forum...',
	'ACP_RSS_EGOSEARCH_LIMIT'			=> 'Número de items por página para apresentar na feed das suas mensagens',
// FEED text
	'BOARD_DAYS'				=> 'Dias desde o início',
	'COPYRIGHT'					=> 'Copyright',
	'NO_RSS_ITEMS'				=> 'Não há items disponíveis',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Infelizmente não existem novos items na página requisitada.',
	'NO_RSS_ITEMS_LOGGED_IN'	=> 'Tem de estar registado para usar a RSS Feed %1$s ',

));

?>