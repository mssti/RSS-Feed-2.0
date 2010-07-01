<?php
/**
* @package: phpBB3 :: RSS feed 2.0 -> language -> tr -> mods 
* @version: $Id: rss.php, v 1.0.9 2009/02/20 09:02:20 leviatan21 Exp $
* @copyright: leviatan21 < info@mssti.com > (Gabriel) http://www.mssti.com/phpbb3/
* @license: http://opensource.org/licenses/gpl-license.php GNU Public License
* @author: leviatan21 - http://www.phpbb.com/community/memberlist.php?mode=viewprofile&u=345763
* @translator: CitLemBiK - http://www.bizimpencere.com
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
	'MSSTI_LINK'						=> 'Rss eklentisi <a href="http://www.mssti.com/phpbb3/" onclick="window.open(this.href);return false;" >.:: MSSTI ::.</a> tarafindan yaratilmistir.',
	'ACP_RSS'							=> 'Rss',
	'ACP_RSS_FEEDS'						=> 'Rss',
	'ACP_RSS_MANAGEMENT'				=> 'Genel RSS ayarları',
	'ACP_RSS_MANAGEMENT_EXPLAIN'		=> 'Panonuz icin rss ayarlarını bu panelden yapabilirsiniz , biçim kodları , gülücükler , resimler gibi fonksiyonlar desteklenir...',

// ACP Feeds to Serve
	'ACP_RSS_LEGEND1'					=> 'Rss secenekleri',

	'ACP_RSS_ENABLE'					=> 'Rss\'i ac',
	'ACP_RSS_ENABLE_EXPLAIN'			=> 'Panonuz icin rss fonksiyonunu acip kapamaya yarar.<br />Rss fonksiyonunun kapalı kalması durumunda aşağıdaki seçeneklerin hepsi göz ardı edilir.',
	'ACP_RSS_FORUM'						=> 'Tekli forumları rss\'de göster',
	'ACP_RSS_FORUM_EXPLAIN'				=> 'Belirli bir forumun içeriğinin rss\'e eklenip eklenmeyeceğinin ayarıdır.',
	'ACP_RSS_OVERALL_FORUMS'			=> 'Tüm forumları rss\'de göster',
	'ACP_RSS_OVERALL_FORUMS_EXPLAIN'	=> 'Tüm forumların içeriğinin rss\'e eklenip eklenmeyeceğinin ayarıdır.',
	'ACP_RSS_OVERALL_FORUMS_LIMIT'		=> 'Rss ana sayfasında gösterilecek en fazla öğe sayısı',
	'ACP_RSS_OVERALL_THREAD'			=> 'Tüm konulari rss\'de göster.',
	'ACP_RSS_OVERALL_THREAD_EXPLAIN'	=> 'Tüm konularin rss\'e eklenip eklenmeyeceğinin ayarıdır.',
	'ACP_RSS_OVERALL_THREAD_LIMIT'		=> 'Rss "Konular" sayfasında gösterilecek en fazla konu sayısı.',
	'ACP_RSS_OVERALL_POSTS'				=> 'Tüm iletileri rss\'de göster.',
	'ACP_RSS_OVERALL_POSTS_EXPLAIN'		=> 'Tüm iletilerin rss\'e eklenip eklenmeyeceğinin ayarıdır.',
	'ACP_RSS_OVERALL_POSTS_LIMIT'		=> 'Rss "Iletiler" sayfasında gösterilecek en fazla ileti sayısı.',
	'ACP_RSS_EGOSEARCH'					=> 'Kendi iletileriniz seçeneğini rss\'de göster.',
	'ACP_RSS_EGOSEARCH_EXPLAIN'			=> 'Kendi iletileriniz seçeneğinin rss\'e eklenip eklenmeyeceğinin ayarıdır. Sadece kullanıcı panoya giriş yaptigi zaman aktif olur.',
	'ACP_RSS_EGOSEARCH_LIMIT'			=> 'Rss "Kendi iletileriniz" sayfasında gösterilecek en fazla ileti sayısı.',
	'ACP_RSS_THREAD'					=> 'Tekli konulari rss\'de göster',
	'ACP_RSS_THREAD_EXPLAIN'			=> 'Belirli bir konunun içeriğinin rss\'e eklenip eklenmeyeceğinin ayarıdır.',
	'ACP_RSS_NEWS'						=> 'Duyuru forumları icin rss ayari',
	'ACP_RSS_NEWS_EXPLAIN'				=> 'Duyuru yapılan konunun ilk iletisini rss\'de gösterir. Eğer birden fazla duyuru (önemli konular , haberler) forumunuz varsa virgül işaretiyle forum id\'lerini vermelisiniz. (1,2,5 gibi)<br />Duyuru vs... yapacak kadar önemli bir forumunuz yoksa bu bölümü bos birakabilirsiniz...',

// ACP General RSS Settings
	'ACP_RSS_LEGEND2'					=> 'Genel rss ayarları',

	'ACP_RSS_CHARACTERS'				=> 'Iletide gösterilecek en fazla karakter sayısı',
	'ACP_RSS_CHARACTERS_EXPLAIN'		=> 'Rss sayfasındaki iletide gösterilecen en fazla ileti karakteri sayısını buraya verin , önerilen 1000 (Bin) karakterdir.<br /> 0 dEğeri ileti katakterini sinirsiz yapar ve tüm ileti içeriği rss\'de gösterilir , 1 değeri ise hicbir ileti içeriği göstermez.',
	'ACP_RSS_ATTACHMENTS'				=> 'Eklentiler',
	'ACP_RSS_ATTACHMENTS_EXPLAIN'		=> 'Eklentileri (Eğer varsa) rss sayfasında göstermeye yarayan ayar.',
	'ACP_RSS_CHARS'						=> 'karakter',
	'ACP_RSS_IMAGE_SIZE'				=> 'Rss\'de gösterilen en fazla resim genişliği. (Pixel değerinde)',
	'ACP_RSS_IMAGE_SIZE_EXPLAIN'		=> 'Buraya vereceginiz değer dogrultusunda rss sayfasında gösterilecek resimler yeniden boyutlandirilacaktir.<br /> 0 değeri resim boyutlandirmayi kapatir ve resimler orjinal büyüklüğü ile gösterilir.<br />PHP getimagesize() fonksiyonu <strong>Gerekli</strong>',
	'ACP_RSS_AUTH'						=> 'Izinleri yok say',
	'ACP_RSS_AUTH_EXPLAIN'				=> 'Burası aktif edildiginde (Evet secildigi takdirde) kullanıcı izinleri yok sayilacak , ve isteyen tüm rss içeriğini görebilecek , yani kullanıcı izinleri rss icin yok sayilacak.',
	'ACP_RSS_BOARD_STATISTICS'			=> 'Pano istatistikleri',
	'ACP_RSS_BOARD_STATISTICS_EXPLAIN'	=> 'Rss ana sayfasında ilk öğe olarak pano istatistiklerini göstermeye yarayan ayardir.',
	'ACP_RSS_ITEMS_STATISTICS'			=> 'Içerik (öğe) istatistikleri',
	'ACP_RSS_ITEMS_STATISTICS_EXPLAIN'	=> 'Rss sayfasında gösterilecek ayrıntılı içerik istatistikleridir.<br />( Gönderen + Tarih ve Saat + Cevaplar + Görüntülemeler )',
	'ACP_RSS_PAGINATION'				=> 'Rss sayfalama',
	'ACP_RSS_PAGINATION_EXPLAIN'		=> 'Ögelerin sayfa başına olan limiti dolduysa , diger sayfaların gösterime açılıp açılmayacağının ayarıdır.',
	'ACP_RSS_LIMIT'						=> 'Rss sayfası başına öğe',
	'ACP_RSS_LIMIT_EXPLAIN'				=> 'Eğer sayfalama fonksiyonu açıksa , sayfa başına kac rss feed öğesi gösterileceğinin ayarıdır.',
	'ACP_RSS_EXCLUDE_ID'				=> 'Rss\'de yok sayılacak forumlar',
	'ACP_RSS_EXCLUDE_ID_EXPLAIN'		=> 'Buraya vereceginiz forum id\'leri rss sistemi tarafından <strong>yok sayilir.</strong> Rss\'de görünmesini istemediginiz forumunuz/forumlarınız varsa virgül işaretiyle forum id\'lerini vermelisiniz. (1,2,5 gibi)<br />Verdiğiniz forumlar ve bu foruma ait içerikler asla rss tarafindan gösterilmez.',

// FEED text
	'BOARD_DAYS'				=> 'Panonun kurulumundan bu güne geçen gün',
	'COPYRIGHT'					=> 'Copyright',
	'NO_RSS_ITEMS'				=> 'Hiçbir rss içeriği mevcut değil',
	'NO_RSS_ITEMS_EXPLAIN'		=> 'Girdiğiniz sayfada maalesef hicbir rss içeriği mevcut değil. Eğer panoya giriş yapmadıysanız giriş yapın.',
	'NO_RSS_ITEMS_LOGGED_IN'	=> '%1$s fonksiyonunu kullanmak istiyorsaniz panoya giriş yapmak zorundasınız.',

));

?>