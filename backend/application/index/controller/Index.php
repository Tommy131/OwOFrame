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

namespace backend\application\index\controller;


class Index extends \backend\system\app\ControllerBase
{
	public function Index()
	{
		return
		 '<title>HelloWorld - OwOFrame</title>'.
		 '<style type="text/css">'.
			 '*{padding: 0; margin: 0;}'.
			 'a{color: #2E5CD5; cursor: pointer; text-decoration: none}'.
			 'a:hover{text-decoration: underline;}'.
			 'body{background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px}'.
			 'h1{font-size: 100px; font-weight: normal; margin-bottom: 12px;}'.
			 'p{line-height: 1.6em; font-size: 42px}'.
		 '</style>'.
		 '<div style="padding: 24px 48px;">'.
		 '	<h1>:)</h1>'.
			 '<p> OwOFrame [<font color="blue"><b>'.APP_VERSION.'</b></font>]<br/><span style="font-size:30px">洋工总能磨出针.</span></p>'.
			 '<span style="font-size:22px;">"总有一天, 你能开发出属于自己的后端框架, 尽管不起眼." -- Hanski Jay</span><br/>'.
		 '</div>'.
		 '<div style="padding: 24px 48px; line-height: 1.6em; font-size: 32px"><h5>Hello World! This is a example for developer to use a backend Class.</h5></div>';
	}
}
?>