<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com

************************************************************************/

namespace backend;

use backend\system\utils\Config;
use backend\system\utils\Logger;
use backend\system\exception\ExceptionOutput;

final class OwOFrame
{

	public const HTTP_CODE = 
	[
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	];

	public const MIMETYPE = 
	[
		'ez' => 'application/andrew-inset',
		'csm' => 'application/cu-seeme',
		'cu' => 'application/cu-seeme',
		'tsp' => 'application/dsptype',
		'spl' => 'application/x-futuresplash',
		'hta' => 'application/hta',
		'cpt' => 'image/x-corelphotopaint',
		'hqx' => 'application/mac-binhex40',
		'nb' => 'application/mathematica',
		'mdb' => 'application/msaccess',
		'doc' => 'application/msword',
		'dot' => 'application/msword',
		'bin' => 'application/octet-stream',
		'oda' => 'application/oda',
		'ogg' => 'application/ogg',
		'prf' => 'application/pics-rules',
		'key' => 'application/pgp-keys',
		'pdf' => 'application/pdf',
		'pgp' => 'application/pgp-signature',
		'ps' => 'application/postscript',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'rss' => 'application/rss+xml',
		'rtf' => 'text/rtf',
		'smi' => 'application/smil',
		'smil' => 'application/smil',
		'wp5' => 'application/wordperfect5.1',
		'xht' => 'application/xhtml+xml',
		'xhtml' => 'application/xhtml+xml',
		'zip' => 'application/zip',
		'cdy' => 'application/vnd.cinderella',
		'mif' => 'application/x-mif',
		'xls' => 'application/vnd.ms-excel',
		'xlb' => 'application/vnd.ms-excel',
		'cat' => 'application/vnd.ms-pki.seccat',
		'stl' => 'application/vnd.ms-pki.stl',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pps' => 'application/vnd.ms-powerpoint',
		'pot' => 'application/vnd.ms-powerpoint',
		'sdc' => 'application/vnd.stardivision.calc',
		'sda' => 'application/vnd.stardivision.draw',
		'sdd' => 'application/vnd.stardivision.impress',
		'sdp' => 'application/vnd.stardivision.impress',
		'smf' => 'application/vnd.stardivision.math',
		'sdw' => 'application/vnd.stardivision.writer',
		'vor' => 'application/vnd.stardivision.writer',
		'sgl' => 'application/vnd.stardivision.writer-global',
		'sxc' => 'application/vnd.sun.xml.calc',
		'stc' => 'application/vnd.sun.xml.calc.template',
		'sxd' => 'application/vnd.sun.xml.draw',
		'std' => 'application/vnd.sun.xml.draw.template',
		'sxi' => 'application/vnd.sun.xml.impress',
		'sti' => 'application/vnd.sun.xml.impress.template',
		'sxm' => 'application/vnd.sun.xml.math',
		'sxw' => 'application/vnd.sun.xml.writer',
		'sxg' => 'application/vnd.sun.xml.writer.global',
		'stw' => 'application/vnd.sun.xml.writer.template',
		'sis' => 'application/vnd.symbian.install',
		'wbxml' => 'application/vnd.wap.wbxml',
		'wmlc' => 'application/vnd.wap.wmlc',
		'wmlsc' => 'application/vnd.wap.wmlscriptc',
		'wk' => 'application/x-123',
		'dmg' => 'application/x-apple-diskimage',
		'bcpio' => 'application/x-bcpio',
		'torrent' => 'application/x-bittorrent',
		'cdf' => 'application/x-cdf',
		'vcd' => 'application/x-cdlink',
		'pgn' => 'application/x-chess-pgn',
		'cpio' => 'application/x-cpio',
		'csh' => 'text/x-csh',
		'deb' => 'application/x-debian-package',
		'dcr' => 'application/x-director',
		'dir' => 'application/x-director',
		'dxr' => 'application/x-director',
		'wad' => 'application/x-doom',
		'dms' => 'application/x-dms',
		'dvi' => 'application/x-dvi',
		'pfa' => 'application/x-font',
		'pfb' => 'application/x-font',
		'gsf' => 'application/x-font',
		'pcf' => 'application/x-font',
		'pcf.Z' => 'application/x-font',
		'gnumeric' => 'application/x-gnumeric',
		'sgf' => 'application/x-go-sgf',
		'gcf' => 'application/x-graphing-calculator',
		'gtar' => 'application/x-gtar',
		'tgz' => 'application/x-gtar',
		'taz' => 'application/x-gtar',
		'gz'  => 'application/x-gtar',
		'hdf' => 'application/x-hdf',
		'phtml' => 'application/x-httpd-php',
		'pht' => 'application/x-httpd-php',
		'php' => 'application/x-httpd-php',
		'phps' => 'application/x-httpd-php-source',
		'php3' => 'application/x-httpd-php3',
		'php3p' => 'application/x-httpd-php3-preprocessed',
		'php4' => 'application/x-httpd-php4',
		'ica' => 'application/x-ica',
		'ins' => 'application/x-internet-signup',
		'isp' => 'application/x-internet-signup',
		'iii' => 'application/x-iphone',
		'jar' => 'application/x-java-archive',
		'jnlp' => 'application/x-java-jnlp-file',
		'ser' => 'application/x-java-serialized-object',
		'class' => 'application/x-java-vm',
		'js' => 'application/x-javascript',
		'chrt' => 'application/x-kchart',
		'kil' => 'application/x-killustrator',
		'kpr' => 'application/x-kpresenter',
		'kpt' => 'application/x-kpresenter',
		'skp' => 'application/x-koan',
		'skd' => 'application/x-koan',
		'skt' => 'application/x-koan',
		'skm' => 'application/x-koan',
		'ksp' => 'application/x-kspread',
		'kwd' => 'application/x-kword',
		'kwt' => 'application/x-kword',
		'latex' => 'application/x-latex',
		'lha' => 'application/x-lha',
		'lzh' => 'application/x-lzh',
		'lzx' => 'application/x-lzx',
		'frm' => 'application/x-maker',
		'maker' => 'application/x-maker',
		'frame' => 'application/x-maker',
		'fm' => 'application/x-maker',
		'fb' => 'application/x-maker',
		'book' => 'application/x-maker',
		'fbdoc' => 'application/x-maker',
		'wmz' => 'application/x-ms-wmz',
		'wmd' => 'application/x-ms-wmd',
		'com' => 'application/x-msdos-program',
		'exe' => 'application/x-msdos-program',
		'bat' => 'application/x-msdos-program',
		'dll' => 'application/x-msdos-program',
		'msi' => 'application/x-msi',
		'nc' => 'application/x-netcdf',
		'pac' => 'application/x-ns-proxy-autoconfig',
		'nwc' => 'application/x-nwc',
		'o' => 'application/x-object',
		'oza' => 'application/x-oz-application',
		'pl' => 'application/x-perl',
		'pm' => 'application/x-perl',
		'p7r' => 'application/x-pkcs7-certreqresp',
		'crl' => 'application/x-pkcs7-crl',
		'qtl' => 'application/x-quicktimeplayer',
		'rpm' => 'audio/x-pn-realaudio-plugin',
		'shar' => 'application/x-shar',
		'swf' => 'application/x-shockwave-flash',
		'swfl' => 'application/x-shockwave-flash',
		'sh' => 'text/x-sh',
		'sit' => 'application/x-stuffit',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc' => 'application/x-sv4crc',
		'tar' => 'application/x-tar',
		'tcl' => 'text/x-tcl',
		'tex' => 'text/x-tex',
		'gf' => 'application/x-tex-gf',
		'pk' => 'application/x-tex-pk',
		'texinfo' => 'application/x-texinfo',
		'texi' => 'application/x-texinfo',
		'~' => 'application/x-trash',
		'%' => 'application/x-trash',
		'bak' => 'application/x-trash',
		'old' => 'application/x-trash',
		'sik' => 'application/x-trash',
		't' => 'application/x-troff',
		'tr' => 'application/x-troff',
		'roff' => 'application/x-troff',
		'man' => 'application/x-troff-man',
		'me' => 'application/x-troff-me',
		'ms' => 'application/x-troff-ms',
		'ustar' => 'application/x-ustar',
		'src' => 'application/x-wais-source',
		'wz' => 'application/x-wingz',
		'crt' => 'application/x-x509-ca-cert',
		'fig' => 'application/x-xfig',
		'au' => 'audio/basic',
		'snd' => 'audio/basic',
		'mid' => 'audio/midi',
		'midi' => 'audio/midi',
		'kar' => 'audio/midi',
		'mpga' => 'audio/mpeg',
		'mpega' => 'audio/mpeg',
		'mp2' => 'audio/mpeg',
		'mp3' => 'audio/mpeg',
		'm3u' => 'audio/x-mpegurl',
		'sid' => 'audio/prs.sid',
		'aif' => 'audio/x-aiff',
		'aiff' => 'audio/x-aiff',
		'aifc' => 'audio/x-aiff',
		'gsm' => 'audio/x-gsm',
		'wma' => 'audio/x-ms-wma',
		'wax' => 'audio/x-ms-wax',
		'ra' => 'audio/x-realaudio',
		'rm' => 'audio/x-pn-realaudio',
		'ram' => 'audio/x-pn-realaudio',
		'pls' => 'audio/x-scpls',
		'sd2' => 'audio/x-sd2',
		'wav' => 'audio/x-wav',
		'pdb' => 'chemical/x-pdb',
		'xyz' => 'chemical/x-xyz',
		'bmp' => 'image/x-ms-bmp',
		'gif' => 'image/gif',
		'ief' => 'image/ief',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'jpe' => 'image/jpeg',
		'pcx' => 'image/pcx',
		'png' => 'image/png',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'wbmp' => 'image/vnd.wap.wbmp',
		'ras' => 'image/x-cmu-raster',
		'cdr' => 'image/x-coreldraw',
		'pat' => 'image/x-coreldrawpattern',
		'cdt' => 'image/x-coreldrawtemplate',
		'djvu' => 'image/x-djvu',
		'djv' => 'image/x-djvu',
		'ico' => 'image/x-icon',
		'art' => 'image/x-jg',
		'jng' => 'image/x-jng',
		'psd' => 'image/x-photoshop',
		'pnm' => 'image/x-portable-anymap',
		'pbm' => 'image/x-portable-bitmap',
		'pgm' => 'image/x-portable-graymap',
		'ppm' => 'image/x-portable-pixmap',
		'rgb' => 'image/x-rgb',
		'xbm' => 'image/x-xbitmap',
		'xpm' => 'image/x-xpixmap',
		'xwd' => 'image/x-xwindowdump',
		'igs' => 'model/iges',
		'iges' => 'model/iges',
		'msh' => 'model/mesh',
		'mesh' => 'model/mesh',
		'silo' => 'model/mesh',
		'wrl' => 'x-world/x-vrml',
		'vrml' => 'x-world/x-vrml',
		'csv' => 'text/comma-separated-values',
		'css' => 'text/css',
		'323' => 'text/h323',
		'htm' => 'text/html',
		'html' => 'text/html',
		'uls' => 'text/iuls',
		'mml' => 'text/mathml',
		'asc' => 'text/plain',
		'txt' => 'text/plain',
		'text' => 'text/plain',
		'diff' => 'text/plain',
		'rtx' => 'text/richtext',
		'sct' => 'text/scriptlet',
		'wsc' => 'text/scriptlet',
		'tm' => 'text/texmacs',
		'ts' => 'text/texmacs',
		'tsv' => 'text/tab-separated-values',
		'jad' => 'text/vnd.sun.j2me.app-descriptor',
		'wml' => 'text/vnd.wap.wml',
		'wmls' => 'text/vnd.wap.wmlscript',
		'xml' => 'text/xml',
		'xsl' => 'text/xml',
		'h++' => 'text/x-c++hdr',
		'hpp' => 'text/x-c++hdr',
		'hxx' => 'text/x-c++hdr',
		'hh' => 'text/x-c++hdr',
		'c++' => 'text/x-c++src',
		'cpp' => 'text/x-c++src',
		'cxx' => 'text/x-c++src',
		'cc' => 'text/x-c++src',
		'h' => 'text/x-chdr',
		'c' => 'text/x-csrc',
		'java' => 'text/x-java',
		'moc' => 'text/x-moc',
		'p' => 'text/x-pascal',
		'pas' => 'text/x-pascal',
		'***' => 'text/x-pcs-***',
		'shtml' => 'text/x-server-parsed-html',
		'etx' => 'text/x-setext',
		'tk' => 'text/x-tcl',
		'ltx' => 'text/x-tex',
		'sty' => 'text/x-tex',
		'cls' => 'text/x-tex',
		'vcs' => 'text/x-vcalendar',
		'vcf' => 'text/x-vcard',
		'dl' => 'video/dl',
		'fli' => 'video/fli',
		'gl' => 'video/gl',
		'mpeg' => 'video/mpeg',
		'mpg' => 'video/mpeg',
		'mpe' => 'video/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',
		'mxu' => 'video/vnd.mpegurl',
		'dif' => 'video/x-dv',
		'dv' => 'video/x-dv',
		'lsf' => 'video/x-la-asf',
		'lsx' => 'video/x-la-asf',
		'mng' => 'video/x-mng',
		'asf' => 'video/x-ms-asf',
		'asx' => 'video/x-ms-asf',
		'wm' => 'video/x-ms-wm',
		'wmv' => 'video/x-ms-wmv',
		'wmx' => 'video/x-ms-wmx',
		'wvx' => 'video/x-ms-wvx',
		'mp4' => 'video/mp4',
		'avi' => 'video/x-msvideo',
		'movie' => 'video/x-sgi-movie',
		'ice' => 'x-conference/x-cooltalk',
		'vrm' => 'x-world/x-vrml',
		'rar' => 'application/x-rar-compressed',
		'cab' => 'application/vnd.ms-cab-compressed'
	];
	public static $charset = 'utf-8';


	public static function init()
	{
		/** 兼容php6 */
		if(function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc())
		{
			$_GET    = self::stripslashesDeep($_GET);
			$_POST   = self::stripslashesDeep($_POST);
			$_COOKIE = self::stripslashesDeep($_COOKIE);

			reset($_GET);
			reset($_POST);
			reset($_COOKIE);
		}
		set_error_handler([ExceptionOutput::class, 'ErrorHandler'], E_ALL);
		set_exception_handler([ExceptionOutput::class, 'ExceptionHandler']);
	}

	/**
	 * @method      setStatus
	 * @description 设置HTTP状态码
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @param       int[code|状态码]
	 */
	public static function setStatus(int $code) : void
	{
		if(isset(self::HTTP_CODE[$code])) {
			header((isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1') . ' ' . $code . ' ' . self::HTTP_CODE[$code], true, $code);
		}
	}

	public static function getMode() : string
	{
		return !is_string(php_sapi_name()) ? 'error' : php_sapi_name();
	}

	public static function isRunningWithCLI() : bool
	{
		return preg_match('/cli/i', self::getMode());
	}

	public static function isRunningWithCGI() : bool
	{
		return preg_match('/cgi/i', self::getMode());
	}

	/**
	 * @method      stripslashesDeep
	 * @description 递归去掉数组反斜线
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @param       string[value|字符串]
	 * @return      string
	 */
	public static function stripslashesDeep(string $value) : string
	{
		return is_array($value) ? array_map([$this, 'stripslashesDeep'], $value) : stripslashes($value);
	}

	/**
	 * @method      checkArrayValid
	 * @description 检查目标数组是否缺少某个元素(仅限二维数组)
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @param       array[data|需要检查的数组]
	 * @param       array[needle|需要检查的键名]
	 * @param       string[missParam|返回缺少的参数]
	 * @return      bool
	 */
	public static function checkArrayValid(array $data, array $needle, ?string &$missParam = "") : bool
	{
		$data = array_filter($data);
		$result = false;
		while(count($needle) > 0)
		{
			$temp = array_shift($needle);
			if(!isset($data[$temp]))
			{
				$missParam = $temp;
				$result = false;
				break;
				return $result;
			}
			else $result = true;
		}
		return $result;
	}

	/**
	 * @method      isMobile
	 * @description 检测是否为移动设备访问
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @return      boolean or string
	 */
	public static function isMobile()
	{
		//获取USER AGENT
		$agent = strtolower(server('HTTP_USER_AGENT'));
		
		if(preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $agent)) return true;
		elseif(strpos($agent, 'windows nt')) return false;
		else return $agent;
	}

	/**
	 * @method      getClientBrowser
	 * @description 获取客户端信息
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @return      string
	 */
	public static function getClientBrowser() : string
	{
		if(!empty(server('HTTP_USER_AGENT')))
		{
			$br = server('HTTP_USER_AGENT');
			if(preg_match('/MSIE/i',$br))        $br = 'MSIE';
			elseif(preg_match('/Firefox/i',$br)) $br = 'Firefox';
			elseif(preg_match('/Chrome/i',$br))  $br = 'Chrome';
			elseif(preg_match('/Safari/i',$br))  $br = 'Safari';
			elseif(preg_match('/Opera/i',$br))   $br = 'Opera';
			else $br = 'Other';
			return $br;
		}
		else return "获取浏览器信息失败！";
	}

	/**
	 * @method      getClientIp
	 * @description 获取客户端IP
	 * @author      HanskiJay
	 * @doenIn      2021-01-10
	 * @return      string
	 */
	public static function getClientIp() : string
	{
		if(getenv("HTTP_CLIENT_IP"))           $ip = getenv("HTTP_CLIENT_IP");
		elseif(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
		elseif(getenv("REMOTE_ADDR"))          $ip = getenv("REMOTE_ADDR");
		else                                   $ip = "Unknown";
		return $ip;
	}

	public static function is_serialized($data)
	{
		$data = trim($data);
		if('N;' == $data) return true;
		if(!preg_match('/^([adObis]):/', $data, $badions)) return false;
		switch ($badions[1]) {
			case'a':
			case'O':
			case's':
			if(preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) return true;
			break;
			case'b':
			case'i':
			case'd':
			if(preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) return true;
			break; 
		}
		return false;
	}

	/**
	 * 宽字符串截字函数
	 *
	 * @access public
	 * @param string $str 需要截取的字符串
	 * @param integer $start 开始截取的位置
	 * @param integer $length 需要截取的长度
	 * @param string $trim 截取后的截断标示符
	 * @return string
	 */
	public static function subStr(string $str, int $start, int $length, string $trim = "...") : string
	{
		if(!strlen($str)) return '';

		$iLength = self::strLen($str) - $start;
		$tLength = $length < $iLength ? ($length - self::strLen($trim)) : $length;

		if(__MB_SUPPORTED__) $str = mb_substr($str, $start, $tLength, self::$charset);
		else
		{
			$str = ('UTF-8' == strtoupper(self::$charset) && preg_match_all("/./u", $str, $matches))
				 ? implode('', array_slice($matches[0], $start, $tLength))
				 : substr($str, $start, $tLength);
		} 
		return $length < $iLength ? ($str . $trim) : $str;
	}

	/**
	 * 获取宽字符串长度函数
	 *
	 * @access public
	 * @param string $str 需要获取长度的字符串
	 * @return integer
	 */
	public static function strLen(string $str) : int
	{
		return (__MB_SUPPORTED__)
		? mb_strlen($str, self::$charset)
		: (('UTF-8' == strtoupper(self::$charset)) ? strlen(utf8_decode($str)) : strlen($str));
	}

	public static function __strToUpper(array $matches) : ?string
	{
		return strtoupper($matches[0]);
	}

	/**
	 * 获取大写字符串
	 * 
	 * @param string $str 
	 * @access public
	 * @return string
	 */
	public static function strToUpper(string $str) : string
	{
		return (__MB_SUPPORTED__)
		? mb_strtoupper($str, self::$charset)
		: (('UTF-8' == strtoupper(self::$charset)) ? preg_replace_callback("/[a-z]+/u", [$this, '__strToUpper'], $str) : strtoupper($str));
	}

	/**
	 * 检查是否为合法的编码数据
	 *
	 * @param string|array $str
	 * @return boolean
	 */
	public static function checkStrEncoding(string $str) : bool
	{
		if(is_array($str)) return array_map([$this, 'checkStrEncoding'], $str);
																			// just support utf-8;
		return (__MB_SUPPORTED__) ? mb_check_encoding($str, self::$charset) : preg_match('//u', $str);
	}

	/**
	 * 检查是否是一个安全的主机名
	 *
	 * @param $host
	 * @return bool
	 */
	public static function isSafeHost(string $host) : bool
	{
		if('localhost' == $host) return false;

		$address = gethostbyname($host);
		$inet = inet_pton($address);

		if(false === $inet)
		{
			// 有可能是ipv6的地址;
			$records = dns_get_record($host, DNS_AAAA);
			if(empty($records)) return false;
			$address = $records[0]['ipv6'];
			$inet = inet_pton($address);
		}

		if(strpos($address, '.'))
		{
			// ipv4, 非公网地址;
			$privateNetworks = 
			[
				'10.0.0.0|10.255.255.255',
				'172.16.0.0|172.31.255.255',
				'192.168.0.0|192.168.255.255',
				'169.254.0.0|169.254.255.255',
				'127.0.0.0|127.255.255.255'
			];
			$long = ip2long($address);

			foreach($privateNetworks as $network)
			{
				list($from, $to) = explode('|', $network);
				if($long >= ip2long($from) && $long <= ip2long($to)) return false;
			}
		}
		else
		{
			// ipv6, https://en.wikipedia.org/wiki/Private_network;
			$from = inet_pton('fd00::');
			$to = inet_pton('fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff');
			if($inet >= $from && $inet <= $to) return false;
		}
		return true;
	}

	public static function isDomain(string $str, &$match = null) : bool
	{
		// return (strpos($str, '--') === false) && (bool) preg_match("/^([a-z0-9_\-]+[\.])?([a-z0-9_\-]+)[\.]([a-z]+)$/i", strtolower(trim($str)), $match);
		return (strpos($str, '--') === false) && preg_match('/^([a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?\.)?[a-z0-9]+([a-z0-9-]*(?:[a-z0-9]+))?[\.]([a-z]+)$/i', $str, $match);
	}

	/**
	 * 获取文件类型
	 *
	 * @access public
	 * @param string $fileName 文件名
	 * @return string
	 */
	public static function mimeContentType(string $fileName) : string
	{
		//改为并列判断
		if(function_exists('mime_content_type')) return mime_content_type($fileName);

		if(function_exists('finfo_open'))
		{
			$fInfo = @finfo_open(FILEINFO_MIME_TYPE);
			if(false !== $fInfo)
			{
				$mimeType = finfo_file($fInfo, $fileName);
				finfo_close($fInfo);
				return $mimeType;
			}
		}

		
		$part = explode('.', $fileName);
		$size = count($part);
		if($size > 1)
		{
			$ext = $part[$size - 1];
			if(isset(self::MIMETYPE[$ext])) return self::MIMETYPE[$ext];
		}
		return 'application/octet-stream';
	}

	public static function getMimeType() : array
	{
		return self::MIMETYPE;
	}

	public static function getShortClassName(object $class) : string
	{
		return basename(str_replace('\\', '/', get_class($class)));
	}
}
?>