<?php

/*********************************************************************
	 _____   _          __  _____   _____   _       _____   _____
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/

	* Copyright (c) 2015-2021 OwOBlog-DGMT.
	* Developer: HanskiJay(Tommy131)
	* Telegram:  https://t.me/HanskiJay
	* E-Mail:    support@owoblog.com
	* GitHub:    https://github.com/Tommy131

**********************************************************************/

namespace application\index\controller;


class Index extends \owoframe\application\ControllerBase
{
	public function Index()
	{
		return
		 '<title>HelloWorld - OwOFrame</title>'.
		 '<style type="text/css">'.
			 '*{padding: 0; margin: 0;}'.
			 'body{background: #eee; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px}'.
			 'a{color: #eee; cursor: pointer; text-decoration: none}'.
			 'a:hover{text-decoration: none; padding: 3px; border-radius: 10px; background-color: rgb(0, 127, 152); color: #fff}'.
			 'h1{font-size: 100px; font-weight: normal; margin-bottom: 12px;}'.
			 'p{line-height: 1.6em; font-size: 42px}'.
		 '</style>'.
		 '<div style="margin: 100px; padding: 24px 48px; background-color: rgba(93, 137, 179, 0.8); color: #fff; border-radius: 10px;">'.
		 '	<h1>:)</h1>'.
			 '<p> OwOFrame [<span style="color: rgb(51, 65, 241)"><b>'.APP_VERSION.'</b></span>]<br/><span style="font-size:30px;">洋工总能磨出针.</span></p>'.
			 '<span style="font-size:22px;">"总有一天, 你能开发出属于自己的后端框架, 尽管不起眼." -- Hanski Jay</span><br/>'.
			 '<div style="margin: 20px 0; padding: 10px; background-color: rgb(0, 99, 152); border-radius: 5px; width: 320px;">GitHub Link: <a href="https://github.com/Tommy131/OwOFrame" target="_blank">Tommy131/OwOFrame</a></div>'.
		 	'<div style="font-size: 32px;"><h5>Hello World! This is a example for developer to use a owoframe Class.</h5></div>'.
		 '</div>';
	}
}
?>