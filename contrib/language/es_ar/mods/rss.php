<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> es_ar -> mods 
* @version: $Id: rss.php, v 1.0.9 2009/02/20 09:02:20 leviatan21 Exp $
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
	'MSSTI_LINK'						=> 'RSS Feeds por <a href="http://www.mssti.com/phpbb3/" onclick="window.open(this.href);return false;" >.:: MSSTI ::.</a>',
	'ACP_RSS'							=> 'Parámetros RSS',
	'ACP_RSS_FEEDS'						=> 'RSS',
	'ACP_RSS_MANAGEMENT'				=> 'RSS Configuración general',
	'ACP_RSS_MANAGEMENT_EXPLAIN'		=> 'Este módulo pone a disposición diversos RSS, analizando los BBCode para que puedan ser leídos por feeds externos.',

// ACP Feeds to Serve
	'ACP_RSS_LEGEND1'					=> 'Canales del Feed',

	'ACP_RSS_ENABLE'					=> 'Habilitar el RSS',
	'ACP_RSS_ENABLE_EXPLAIN'			=> 'Deshabilitandolo este, desabilita todos los Feeds, sin importar la configuración siguentes',
	'ACP_RSS_FORUM'						=> 'Habilitar el RSS para foros',
	'ACP_RSS_FORUM_EXPLAIN'				=> 'Permite ver los foros de una categoría en particular',
	'ACP_RSS_OVERALL_FORUMS'			=> 'Habilitar el RSS para todos los foros',
	'ACP_RSS_OVERALL_FORUMS_EXPLAIN'	=> 'Esta opción habilitará mostrar el feed para "Todos los foros".',
	'ACP_RSS_OVERALL_FORUMS_LIMIT'		=> 'Cantidad de items por página a mostrar en el feed de foros',
	'ACP_RSS_OVERALL_THREAD'			=> 'Habilitar el RSS para todos los temas',
	'ACP_RSS_OVERALL_THREAD_EXPLAIN'	=> 'Esta opción habilitará mostrar el feed para "Todos los temas".',
	'ACP_RSS_OVERALL_THREAD_LIMIT'		=> 'Cantidad de items por página a mostrar en el feed de temas',
	'ACP_RSS_OVERALL_POSTS'				=> 'Habilitar el RSS para todos los mensajes',
	'ACP_RSS_OVERALL_POSTS_EXPLAIN'		=> 'Esta opción habilitará mostrar el feed para "Todos los mensajes".',
	'ACP_RSS_OVERALL_POSTS_LIMIT'		=> 'Cantidad de items por página a mostrar en el feed de mensajes',
	'ACP_RSS_EGOSEARCH'					=> 'Habilitar el RSS para mensajes propios',
	'ACP_RSS_EGOSEARCH_EXPLAIN'			=> 'Como ver sus mensajes, sólo funciona si usted permanece conectado cuando navega por fuera del foro...',
	'ACP_RSS_EGOSEARCH_LIMIT'			=> 'Cantidad de items por página a mostrar en el feed de mensajes propios',
	'ACP_RSS_THREAD'					=> 'Habilitar el RSS para temas',
	'ACP_RSS_THREAD_EXPLAIN'			=> 'Permite ver los foros de un tema en particular',
	'ACP_RSS_NEWS'						=> 'Feed de Noticias',
	'ACP_RSS_NEWS_EXPLAIN'				=> 'Mostrar la primera entrada de los foros con los siguientes ID\'s. Separar ID\'s con una coma, para seleccionar mas de un foro, Ejemplo: 1,2,5 <br />Dejar en blanco para desactivar el feed de Noticias. ',

// ACP General RSS Settings
	'ACP_RSS_LEGEND2'					=> 'Configuración general',

	'ACP_RSS_CHARACTERS'				=> 'Longitud máxima del texto de los mensajes para mostrar',
	'ACP_RSS_CHARACTERS_EXPLAIN'		=> 'Número de caracteres permitidos, recomendado 1000 characteres.<br /> 0 significa infinito, 1 significa sin texto',
	'ACP_RSS_CHARS'						=> 'characteres',
	'ACP_RSS_ATTACHMENTS'				=> 'Adjuntos',
	'ACP_RSS_ATTACHMENTS_EXPLAIN'		=> 'Mostrar adjuntos en el feed',
	'ACP_RSS_IMAGE_SIZE'				=> 'Ancho máximo de las imagenes en píxeles',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'		=> 'Cambiar el tamaño de las imágenes si el ancho es superior al establecido aquí.<br /> 0 significa sin cambiar.<br />La función PHP getimagesize() <strong>Requerida</strong>',
	'ACP_RSS_AUTH'						=> 'Omitir permisos',
	'ACP_RSS_AUTH_EXPLAIN'				=> 'Si está habilitado, los mensajes serán incluidos en el feed, sin tener en cuenta las restricciones que haya configurado.',
	'ACP_RSS_BOARD_STATISTICS'			=> 'Estadísticas del Sitio',
	'ACP_RSS_BOARD_STATISTICS_EXPLAIN'	=> 'Se mostrarán las estadísticas del Sitio en la primera página del feed general del sitio.',
	'ACP_RSS_ITEMS_STATISTICS'			=> 'Estadísticas de items',
	'ACP_RSS_ITEMS_STATISTICS_EXPLAIN'	=> 'Se mostrarán las estadísticas de cada item <br />( Publicado por + fecha y hora + Respuestas + Vistas )',
	'ACP_RSS_PAGINATION'				=> 'Paginación del feed',
	'ACP_RSS_PAGINATION_EXPLAIN'		=> 'Mostrará la Paginación de ser necesario.',
	'ACP_RSS_LIMIT'						=> 'Items por página',
	'ACP_RSS_LIMIT_EXPLAIN'				=> 'Número máximao de items del feed a ser mostrados por página.',
	'ACP_RSS_EXCLUDE_ID'				=> 'Excluir estos Foros',
	'ACP_RSS_EXCLUDE_ID_EXPLAIN'		=> 'En los Feeds <strong>no apareceran</strong> datos relacionados a los foros con estos ID\'s ni de sus hijos. Separar ID\'s con una coma, para seleccionar mas de un foro, Ejemplo: 1,2,5 <br />Dejar en blanco para mostrar datos de todos los foros.',

// FEED text
	'BOARD_DAYS'				=> 'Días desde el inicio',
	'COPYRIGHT'					=> 'Derecho de Autor',
	'NO_RSS_ITEMS'				=> 'No hay datos disponibles',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Lamentablemente no parece haber noticias de la página que ha solicitado',
	'NO_RSS_ITEMS_LOGGED_IN'	=> 'Debe estar conetado para ver el lector RSS de %1$s',

));

?>