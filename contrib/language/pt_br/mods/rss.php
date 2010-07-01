<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> pt_br -> mods
* @translation pt_br by: Gabriel Cavedon < gabao@completosbr.org >
* @version: $Id: rss.php, v 1.0.8 2009/01/30 09:01:30 leviatan21 Exp $
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
	'MSSTI_LINK'						=> 'RSS Feeds por <a href="http://www.mssti.com/phpbb3/" onClick="window.open(this.href);return false;" >.:: MSSTI ::.</a>',
	'ACP_RSS'							=> 'Parametros RSS',
	'ACP_RSS_FEEDS'						=> 'RSS',
	'ACP_RSS_MANAGEMENT'				=> 'RSS Configuração Geral',
	'ACP_RSS_MANAGEMENT_EXPLAIN'		=> 'Este módulo põe a disposição vários RSS, analizando os BBCode para que possam ser lidos por feeds externos.',

// ACP Feeds to Serve
	'ACP_RSS_LEGEND1'					=> 'Canais do Feed',

	'ACP_RSS_ENABLE'					=> 'Habilitar o RSS',
	'ACP_RSS_ENABLE_EXPLAIN'			=> 'Desabilitando este, desabilita todos os Feeds, sem levar em conta as configurações seguintes',
	'ACP_RSS_FORUM'						=> 'Habilitar o RSS para fóruns',
	'ACP_RSS_FORUM_EXPLAIN'				=> 'Permite ver os fóruns de uma categoria em particular',
	'ACP_RSS_OVERALL_FORUMS'			=> 'Habilitar o RSS para todos os fóruns',
	'ACP_RSS_OVERALL_FORUMS_EXPLAIN'	=> 'Esta opção habilitará mostrar ol feed para "Todos os fóruns ".',
	'ACP_RSS_OVERALL_FORUMS_LIMIT'		=> 'Quantidade de items mostrados por página no feed de fóruns',
	'ACP_RSS_OVERALL_THREAD'			=> 'Habilitar o RSS para todos os temas',
	'ACP_RSS_OVERALL_THREAD_EXPLAIN'	=> 'Esta opção habilitará mostrar o feed para "Todos os temas".',
	'ACP_RSS_OVERALL_THREAD_LIMIT'		=> 'Quantidade de itens mostrados por página no feed de temas',
	'ACP_RSS_OVERALL_POSTS'				=> 'Habilitar o RSS para todas as mensagens',
	'ACP_RSS_OVERALL_POSTS_EXPLAIN'		=> 'Esta opção habilitará mostrar o feed para "Todas as mensagens".',
	'ACP_RSS_OVERALL_POSTS_LIMIT'		=> 'Quantidade de itens mostrados por página no feed de mensagens',
	'ACP_RSS_EGOSEARCH'					=> 'Habilitar o RSS para as próprias mensagens',
	'ACP_RSS_EGOSEARCH_EXPLAIN'			=> 'Como ver suas mensagens, só funciona se você permanece conectado quando navega fora do fórum...',
	'ACP_RSS_EGOSEARCH_LIMIT'			=> 'Quantidade de items mostrados por página no feed de mensagens próprias',
	'ACP_RSS_THREAD'					=> 'Habilitar o RSS para temas',
	'ACP_RSS_THREAD_EXPLAIN'			=> 'Permite ver os fóruns de um tema em particular',
	'ACP_RSS_NEWS'						=> 'Feed de Notícias',
	'ACP_RSS_NEWS_EXPLAIN'				=> 'Mostrar a primera entrada dos fóruns com os seguintes ID\'s. Para selecionar mais de um fórum, separar ID\'s com virgulas, Exemplo: 1,2,5 <br />
	Deixar em branco para desativar o feed das Notícias. ',

// ACP General RSS Settings
	'ACP_RSS_LEGEND2'					=> 'Configuração Geral',

	'ACP_RSS_CHARACTERS'				=> 'Tamanho máximo do texto mostrado nas mensagens',
	'ACP_RSS_CHARACTERS_EXPLAIN'		=> 'Número de caracteres permitidos, recomendado 1000 characteres.<br />
	 0 significa infinito, 1 significa sem texto',
	'ACP_RSS_CHARS'						=> 'Carateres',
	'ACP_RSS_IMAGE_SIZE'				=> 'Tamanho máximo das imagens em pixels',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'		=> 'Trocar o tamanho das imagens se for maior do que o configurado aqui.<br />
	 0 significa sem trocar',
	'ACP_RSS_AUTH'						=> 'Negar permissão',
	'ACP_RSS_AUTH_EXPLAIN'				=> 'Se está habilitado, as mensagens serão incluidos no feed, sem levar em conta as restrições que havia configurado.',
	'ACP_RSS_BOARD_STATISTICS'			=> 'Estatísticas do Site',
	'ACP_RSS_BOARD_STATISTICS_EXPLAIN'	=> 'Serão mostradas as Estatísticas do site na primeira pagina dos feeds gerais.',
	'ACP_RSS_ITEMS_STATISTICS'			=> 'Estatísticas dos itens',
	'ACP_RSS_ITEMS_STATISTICS_EXPLAIN'	=> 'Será mostrado as Estatísticas de cada item <br />
	( Publicado por + data e hora + Respostas + Visitas )',
	'ACP_RSS_PAGINATION'				=> 'Paginação do feed',
	'ACP_RSS_PAGINATION_EXPLAIN'		=> 'Mostrará a paginação se necessário.',
	'ACP_RSS_LIMIT'						=> 'Items por página',
	'ACP_RSS_LIMIT_EXPLAIN'				=> 'Número máximo de items a serem mostrados por página no feed.',
	'ACP_RSS_EXCLUDE_ID'				=> 'Excluir estes Fóruns',
	'ACP_RSS_EXCLUDE_ID_EXPLAIN'		=> 'Nos Feeds <strong>não aparecerão</strong> dados relacionados aos fóruns com estes ID\'s e nem dos seus subfóruns. Separar ID\'s com uma virgula, para selecionar mas de um fórum, Exemplo: 1,2,5 <br />
	Deixar em branco para mostrar dados de todos os fóruns.',

// FEED text
	'BOARD_DAYS'				=> 'Dias desde o começo',
	'COPYRIGHT'					=> 'Direitos de Autor',
	'NO_RSS_ITEMS'				=> 'Não há dados disponiveis',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Lamentamos mas não há notícias na página selecionada',
	'NO_RSS_ITEMS_LOGGED_IN'	=> 'É; preciso estar conectado para ver o leitor de RSS de %1$s',

));

?>