<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/my/admin/before.php");

function url_check($buf) 
{ 
      $buf=trim($buf); 
      preg_match("~(?:(?:ftp|https?)?://|www\.)(?:[a-z0-9\-]+\.)*[a-z]{2,6}(:?/[a-z0-9\-?\[\]=&;#]+)?~i",$buf,$mat); 
      return (isset($mat))?($mat[0]==$buf)?$mat[0]:0:0; 
} 


function combineMessagesHtml($arResult, $mode) {

	if(count($arResult) == 0) {
		return '';
	}	
	
	$arStatus = array('new'=> 'Новый', 'sent'=>'Отправлено', 'delivered'=> 'Доставлено','viewed'=>'Просмотрено');
	$repMessages = 0;
	$strNowDate = date("Y-m-d");
	$strYesterday = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
		
	$avatarSVG = '<svg fill="#BBB" height="36" viewBox="0 0 24 24" width="36" xmlns="http://www.w3.org/2000/svg">
		<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
		<path d="M0 0h24v24H0z" fill="none"/>
		</svg>';
		
	if($TLP_obj->user_info['photo'] == '')
		$myAvatar = '<div class="cnt_image cnt_avatar" style="background-image: none">'.avatarSVG.'</div>';
	else
		$myAvatar = '<div class="cnt_image cnt_avatar" style="background-image: '.$TLP_obj->user_info['photo'].'"></div>';

	$myheader = ($TLP_obj->user_info['fullname'] == $TLP_obj->user_info['name'])?$TLP_obj->user_info['fullname']:$TLP_obj->user_info['fullname']."  '".$TLP_obj->user_info['name']."'";
		
	/*$rcv_from_obj = getContactInfo((arResult[0].from == smuser.name)?arResult[0].to:arResult[0].from);
		var rcvAvatarURL = $('#cnt_'+ rcv_from_obj.id).find('.cnt_avatar').css('background-image') || $('#login_image').find('.cnt_avatar').css('background-image') || 'url(/include/no_avatar.svg)';
		rcvAvatarURL = rcvAvatarURL.replace(new RegExp('"','g'),"");
		if(rcvAvatarURL == 'none') {
			var rcvAvatar = '<div class="cnt_image cnt_avatar" style="background-image: none">' + avatarSVG + '</div>';
		} else {
			var rcvAvatar = '<div class="cnt_image cnt_avatar" style="background-image: '+rcvAvatarURL+'"></div>';
		}
		var rcvheader = (rcv_from_obj.fullname == rcv_from_obj.name)?rcv_from_obj.fullname:rcv_from_obj.fullname +"  '" + rcv_from_obj.name + "'";
		$('#msg_li').attr('data-cnt-id',rcv_from_obj.id);
		
		var last_msg = $('#msg_li .message_line').last();
		var last_msg_cnt = last_msg.attr('data-ms-inf');
		
		var prev_cnt 	= "";
		var prev_date	= "";
		var html_block	= "";
		
		var first_cnt  = "";
		var first_date  = "";
		var last_date	= "";
		arResult.forEach(function(msg_object, key){
			var files_html = '';
			var html_date = '';
			var html_cnt = '';
			var status_text = '';
			var resend_text = '';
			var txtNode = msg_object.msg_text;

			//date_inf
			var msg_date = getDateFromString(msg_object.dt);
			var str_msgdate = getUserStringFromDate(msg_date);
			var strDate = getUserStringFromDate(msg_date);
			if(strDate == strNowDate) {	strDate = 'Сегодня'; }
			else if (strDate == strYesterday) { strDate = 'Вчера'; }
			
			html_date = '<div class="message_line" style="text-align: center; margin: 10px 0;" data-msg-date="'+str_msgdate+'"><hr class="msg_divider"><div class="msg_date">' + strDate + '</div></div>';

			//contact_inf
			var msg_header = (msg_object.from == smuser.name)?myheader:rcvheader;
			var msg_avatar = (msg_object.from == smuser.name)?myAvatar:rcvAvatar;
			
			html_cnt = '<div id="msg_inf" style="margin-top: -10px;">' + msg_avatar +
					//'<div class="cnt_image cnt_avatar" style="background-image: '+avatarURL+'">' + avatarSVG + '</div>'+
					'<div class="message_header"><a>'+msg_header+'</a></div>' + 
					'<div class="message_date">'+getTimeStringFromDate(msg_date)+'</div>'+
				'</div>';

			//files
			$(msg_object.files).each(function() {
				
				files_html = files_html + addFileToList($(this), false);
			});
			if(!files_html=='') {
				files_html = '<div class="att_text">Вложенный(е) файлы:</div><div class="msg_file">' + files_html + '</div>';
			}
			
			//message
			resend_text = '<div class="msg_resend" title="Переслать сообщение">'+
				'<svg fill="#777" height="19" viewBox="0 0 24 24" width="19" xmlns="http://www.w3.org/2000/svg">'+
					'<path d="M10 9V5l-7 7 7 7v-4.1c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"></path>'+
					'<path d="M0 0h24v24H0z" fill="none"></path>'+
				'</svg></div>';
			if(msg_object.from == smuser.name) {
				status_text = '<div class="msg_status" title="Статус сообщения">'+ arStatus[msg_object.status] +'</div>';
			}
			
			var msg_ubody = '<div class="message_text">' +
							'<div class="msg_time">'+getTimeStringFromDate(msg_date)+'</div><pre>'+txtNode+'</pre>' + files_html +
							'<div class="msg_addblock">' + resend_text + status_text + '</div>' +
						'</div>'+
						'<div class="msg_submenu">'+
							'<svg fill="#777" height="18" viewBox="0 0 24 24" width="18" xmlns="http://www.w3.org/2000/svg">'+
								'<path d="M0 0h24v24H0z" fill="none"/>'+
								'<path d="M6 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm12 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm-6 0c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>'+
							'</svg></div>'+
						'</div>'
			
			//creating message
			var msg_html = "";
			if ($("div").is('#' + 'msg_'+ msg_object.ID)) {
				msg_html = '<div id="msg_'+ msg_object.ID+ '" class="message_line" data-ms-inf="'+msg_object.from+'">' + html_cnt + msg_ubody;
				if($('#' + 'msg_'+ msg_object.ID).find('#msg_inf').length != 0) {
					$('#' + 'msg_'+ msg_object.ID).replaceWith(msg_html);
				}
				else {
					$('#' + 'msg_'+ msg_object.ID).replaceWith(msg_html);
					$('#' + 'msg_'+ msg_object.ID).find('#msg_inf').remove();
				}
			}
			else {
				msg_html = '<div id="msg_'+ msg_object.ID+ '" class="message_line" data-ms-inf="'+msg_object.from+'">';
				if((prev_cnt == '' && prev_date == '') || (prev_date != html_date)) {
					if (mode == 'end' && first_cnt == '' && last_msg_cnt == msg_object.from) {
						msg_html = html_date + msg_html;
					} 
					else {
						msg_html = html_date + msg_html + html_cnt;
					}
					
					first_date = (first_date == "")?str_msgdate:first_date;
					first_cnt = (first_cnt == "")?msg_object.from:first_cnt;
					last_date = str_msgdate;
				}
				else if(prev_cnt != msg_header) {
					msg_html = msg_html + html_cnt;
				}
				msg_html = msg_html + msg_ubody;
				
				prev_cnt = msg_header;
				prev_date = html_date;
				html_block = html_block + msg_html;
			}	
		});

		if(html_block != ""){
			if(mode == 'begin') {
				$('[data-msg-date='+last_date+']').remove();
				$("#msg_li").prepend(html_block);
			}
			else if(mode == 'end') {
				$("#msg_li").append(html_block);
				if ($('[data-msg-date='+first_date+']').length != 0) {
					var obj = $('[data-msg-date='+first_date+']')[$('[data-msg-date='+first_date+']').length-1];
					$(obj).remove();
				}
			}
		}	*/
}

$TLP_obj = unserialize($_SESSION["TLP_obj"]);

$action = $_POST['action'];
$adds = $_POST['adds'];

$msgStatus = array(); 
$msgStatus['new'] = 'Новый';
$msgStatus['sent'] = 'Отправлен';
$msgStatus['delivered'] = 'Доставлен';
$msgStatus['viewed'] = 'Просмотрен';

$docStatus = array(); 
$docStatus['new'] = 'Новый';
$docStatus['transmitted'] = 'Отправлен';
$docStatus['agreement'] = 'На согласовании';
$docStatus['confirmed'] = 'Подтвержден';
$docStatus['canceled'] = 'Отменен';
$docStatus['processed'] = 'Принят в обработку';
$docStatus['shipped'] = 'Готов к отгрузке';
$docStatus['closed'] = 'Выполнен';

$docType = array();
$docType['order'] = 'Заказ';


if($action == 'logout')
{
	$arFnc = array();
	$res = $TLP_obj->post('','auth/logout');
	unset($_SESSION["TLP_obj"]);
	$USER->Logout();
	setcookie('tlp_sid','',time()-(7*365*24*60*60),'/');

	echo 'OK';
}
elseif($action == 'send_msg')
{
	$buf = $_POST["message"];
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
	$arParam = array('post'=>$arFnc);
	$files = array();
	
	if (!empty($_FILES['Filedata'])) {
	
		$fileInfo = pathinfo($_FILES['Filedata']['tmp_name']);
		$newName = $fileInfo['dirname']."\\".$_FILES['Filedata']['name'];
		rename($_FILES['Filedata']['tmp_name'],$newName);
		
		$files[$fileInfo['extension'].'File']['tmp_name'] = $newName;
		$files[$fileInfo['extension'].'File']['filename'] = $_FILES['Filedata']['name'];
	}	
	$arParam['files'] = $files;
	$res = $TLP_obj->datapost('Messages_Send', $arParam);
	//echo var_dump($res);
	$res = json_decode($res, true);
	if($res['errCode'] == 0)
	{
		$msg_object = $res['retval'][0];
		$tz_pos = stripos($msg_object['dt'],'+');
		if(!$tz_pos === false) {
			$msg_object['dt'] =substr($msg_object['dt'], 0, $tz_pos);
		}
		if($msg_object['msg_text'] == '') {
			$msg_object['msg_text'] = htmlspecialchars($_POST["message"]);
		}
		$msg_object['tmpGUID'] = ($_POST["tmpGUID"] == "")?(""):($_POST["tmpGUID"]);
		echo json_encode(array($msg_object));
	}
	else
	{echo '%err%КОД: '.$res['errCode'].' '.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'resend_msg') //TODO
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
	$res = $TLP_obj->telecall('Messages_ReSend', $arFnc);
	
	if($res['errCode'] == 0)
	{
		
	}
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'msg_get')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds' || $key=='mode'))
			{$arFnc[$key] = $value;}
	}	
	if($arFnc['receiver'] == '')
		{return '';}
		
	$res = $TLP_obj->telecall('Messages_Get', $arFnc);
	
	if($_POST['adds'] == 'json')
	{
		echo $res;
	}
	elseif($_POST['adds'] == 'html') {
		$res = json_decode($res, true);
		combineMessagesHtml(array_reverse($res), $_POST['mode']);
	}
	else {echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'msg_getNew')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds' || $key=='mode'))
			{$arFnc[$key] = $value;}
	}	
	if($arFnc['receiver'] == '')
		{return '';}
		
		
	$res = $TLP_obj->telecall('Messages_GetNew', $arFnc);
	if($_POST['adds'] == 'json')
	{
		echo $res;
	}
	elseif($_POST['adds'] == 'html') {
		$res = json_decode($res, true);
		combineMessagesHtml(array_reverse($res), $_POST['mode']);
	}
	else {echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'msg_request')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Messages_Request', $arFnc);
	echo $res;
}
elseif($action == 'files_getList')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Files_GetList', $arFnc);
	if($res['errCode'] == 0)
	{
		if($adds=='json')
		{
			echo $res["return"];
		}
		else		
		{
			$arResult = json_decode($res["return"], true);
		}	
	}
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'catalog_get')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if($key == 'filters' || $key == 'properties' || $key == 'fields') {
			$arFnc[$key] = json_decode($value,true);
		} elseif(!($key == 'action' || $key=='adds' || $key=='list_type'))
		{	
			$arFnc[$key] = $value;
		}
	}	
	
	$res = $TLP_obj->telecall('Catalog_Get', $arFnc);
	if($res['errCode'] == 0)
	{
		if($adds=='json')
		{
			echo json_encode($res["return"]);
		}
		else		
		{
			$arResult = $res["return"];
			$list_type = ($_POST['list_type']=='')?'list':$_POST['list_type'];
			$str = '';
			$allow_stocks = $arResult['settings'][0]['allow_stocks'];
			$allow_prices = $arResult['settings'][0]['allow_prices'];
			$arItmes = $arResult['catalog'];
			$arPictures = $arResult['pictures'];
			foreach($arItmes as $key=>$item)
			{
				
				if($list_type == 'list') {
					//list type
					$str = $str.'<div id="it_'.$item["id"].'" class="item" data-it-id="'.$item["id"].'"><div class="item_content"><div class="item_line">';
					$str = $str.'<div class="col_1">'.$item["article"].'</div><div class="col_2">'.$item["name"].'<p class="sub_info"><img src="/include/stdown.png"/></p></div>';
					if($allow_prices)
					{
						$str = $str.'<div class="col_3">'.number_format($item["price"], 2, '.', ' ').'</div>';
					}	
					if($allow_stocks)
					{
						$str = $str.'<div class="col_4">'.number_format($item["stock"], 0, '.', ' ').'</div>';
					}	
					
					$str = $str.'<div class="col_5"><div class="cart" data-cart-id="'.$item["id"].'">
						<div id="b_minus" class="cart_button"><span>-</span></div>
							<input class="cart_input" type="text" name="cart_q" value="1"/>
						<div id="b_plus" class="cart_button"><span>+</span></div>
						
						<div class="cart_order">
							<svg fill="#CCC" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg">
								<path d="M0 0h24v24H0zm18.31 6l-2.76 5z" fill="none"/>
								<path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z"/>
							</svg>
						</div>
					</div></div>';
						
					$str = $str.'</div></div></div>';
				}
				elseif ($list_type == 'block') {
					//block type
					$im_count = (count($arPictures[$item["id"]])==0)?1:count($arPictures[$item["id"]]);
					$li_size = 150*$im_count;
					$it_name = ($item["article"]=='')?$item["name"]:$item["article"].'<br>'.$item["name"];
					$str = $str.'<div id="it_'.$item["id"].'" class="item_block" data-it-id="'.$item["id"].'">
						<div class="item_block_info">
							<div class="item_photo_list">
								<div class="item_photo_li" style="width: '.$li_size.'px;" data-im-num="0" data-im-count="'.$im_count.'">';
								for ($i=0; $i<$im_count; $i++) {
									$item_url = (count($arPictures[$item["id"]]) == 0)?'/include/no_photo.png':'https://'.$TLP_obj->TLP_HOST.'/Catalog_Pics/prev/'.$arPictures[$item["id"]][$i]["file_id"];
									$str = $str.'<div class="item_photo" data-im-cnt="'.$i.'" style="background-image: url('.$item_url.');"></div>';
								}
								$str = $str.'</div>
								<div class="item_block_icons">';
								if($im_count > 1) {
									$str = $str.'
									<div class="item_block_left active_icon">
										<svg fill="#000" height="32" viewBox="0 0 24 24" width="32" xmlns="http://www.w3.org/2000/svg">
											<path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
											<path d="M0 0h24v24H0z" fill="none"/>
										</svg>
									</div>';
								}	
								$str = $str.'<div class="item_block_zoom active_icon">
										<svg fill="#000" height="32" viewBox="0 0 24 24" width="32" xmlns="http://www.w3.org/2000/svg">
											<path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
											<path d="M0 0h24v24H0V0z" fill="none"/>
											<path d="M12 10h-2v2H9v-2H7V9h2V7h1v2h2v1z"/>
										</svg>
									</div>';
								if($im_count > 1) {
								$str = $str.'
									<div class="item_block_right active_icon">
										<svg fill="#000" height="32" viewBox="0 0 24 24" width="32" xmlns="http://www.w3.org/2000/svg">
											<path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
											<path d="M0 0h24v24H0z" fill="none"/>
										</svg>										
									</div>';
								}
								$str = $str.'</div></div>';
							if(!$item["article"]=='') {
								$str = $str.'<div class="item_block_name help_icon"><div class="help_info">Артикул: '.$item["article"].'</div>'.$item["article"].'</div>';
							}
							$str = $str.'<div class="item_block_name help_icon"><div class="help_info">Наименование: '.$item["name"].'</div>'.$item["name"].'</div>';
							if($allow_stocks)
							{
								$strStocks = ($item["stock"]=='' || $item["stock"] == 0)?'Нет':number_format($item["stock"], 0, '.', ' ');
								$str = $str.'<div class="item_block_name item_block_name_allows">На складе: <span>'.$strStocks.'</span></div>';
							}	
							if($allow_prices)
							{
								$strPrice = ($item["price"]=='' || $item["price"] == 0)?'-':number_format($item["price"], 2, '.', ' ').' руб';
								$str = $str.'<div class="item_block_name item_block_name_allows">Цена: <span>'.$strPrice.'</span></div>';
							}	

							$str = $str.'<div class="cart" data-cart-id="'.$item["id"].'">
								<div id="b_minus" class="cart_button"><span>-</span></div>
									<input class="cart_input" type="text" name="cart_q" value="1"/>
								<div id="b_plus" class="cart_button"><span>+</span></div>
								
								<div class="cart_order">
									<svg fill="#CCC" height="22" viewBox="0 0 24 24" width="22" xmlns="http://www.w3.org/2000/svg">
										<path d="M0 0h24v24H0zm18.31 6l-2.76 5z" fill="none"/>
										<path d="M11 9h2V6h3V4h-3V1h-2v3H8v2h3v3zm-4 9c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4h-.01l-1.1 2-2.76 5H8.53l-.13-.27L6.16 6l-.95-2-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.13 0-.25-.11-.25-.25z"/>
									</svg>
								</div>
							</div>
						</div>
						</div>';
				}	
				/*$str = $str.'<div class="item_main_info"><p>'.$item["name"].'</p><p>Артикул:'.$item["article"].'</p><p>Код товара:'.$item["code"].'</p></div>';
				$str = $str.'<div class="item_info">Цена:'.$item["price"].'</div>';
				$str = $str.'</div></div>';*/

				//2 type
				/*$str = $str.'<div id="it_'.$item["id"].'"><div class="im_lines_2">';
				$str = $str.'<div class="image"><img src="'.$item["image"].'" /></div>';
				$str = $str.'<div class="item_main_info"><p>'.$item["name"].'</p><p>Артикул:'.$item["article"].'</p><p>Код товара:'.$item["code"].'</p></div>';
				$str = $str.'<div class="item_info">Цена:'.$item["price"].'</div>';
				$str = $str.'</div></div>';*/

				//3 type
				/*$str = $str.'<div id="it_'.$item["id"].'"><div class="im_lines_3">';
				$str = $str.'<div class="image"><img src="'.$item["image"].'" /></div>';
				$str = $str.'<div class="item_main_info"><table><tr><td>'.$item["article"].'</td><td>'.$item["name"].'</td><td>'.$item["price"].'</td></tr></table></div>';
				$str = $str.'<div class="item_info"></div>';
				$str = $str.'</div></div>';*/
			}
			echo $str;
		}	
	}
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'catalog_getItem')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if($key == 'filters' || $key == 'properties' || $key == 'fields') {
			$arFnc[$key] = json_decode($value, true);
		} elseif(!($key == 'action' || $key=='adds' || $key=='list_type'))
		{	
			$arFnc[$key] = $value;
		}
	}	
		
	$res = $TLP_obj->telecall('Catalog_Get', $arFnc);
	if($res['errCode'] == 0)
	{
		if($adds=='json')
		{
			echo $res["return"];
		}
		else {
			$winSizes = json_decode($_POST["sizes"], true);
			$arResult = $res["return"];
			$allow_stocks = $arResult['settings']['allow_stocks'];
			$allow_prices = $arResult['settings']['allow_prices'];
			$arItmes = $arResult['catalog'];
			$arPictures = $arResult['pictures'];
			
			$arProperties = array();
			foreach($arResult['properties'] as $curArray)
			{$arProperties = $curArray;}
			
			$maxSize = $winSizes['width']-600;
			if($_POST['listType'] == 'list') {
				$maxSize = min($maxSize, $winSizes['height']-130);
			}
			else {
				$maxSize = min($maxSize, $winSizes['height']-80);
			}
			$imSize = ($winSizes['width']<1070)?(500):($maxSize);
			$imSize = min($imSize, 500);
			$str = '';
			foreach($arItmes as $key=>$item)
			{
				$im_count = (count($arPictures[$item["id"]]) == 0)?1:count($arPictures[$item["id"]]);
				$li_size = $imSize*$im_count;
				$str = '<div class="item_detail_info_header" style="font-size: 20px; text-align: center;">'.$item['name'].'</div>
						<div>
							<div style="float: left; height: '.$imSize.'px; width: '.$imSize.'px; margin: 15px 15px 0 0;" class="item_photo_list">
								<div class="item_photo_li" style="width: '.$li_size.'px;" data-im-num="0" data-im-count="'.$im_count.'">';
								if(count($arPictures[$item["id"]]) > 0) {
									$i = 0;
									foreach($arPictures[$item["id"]] as $image)
									{
										$item_url = 'https://'.$TLP_obj->TLP_HOST.'/Catalog_Pics/'.$image['file_id'];
										$str = $str.'<div class="item_photo" data-im-cnt="'.$i.'" style="width: '.$imSize.'px; height: '.$imSize.'px; background-image: url('.$item_url.');"></div>';
										$i++;
									}
								}
								else {
									$item_url = '/include/no_photo.png';
									$str = $str.'<div class="item_photo" data-im-cnt="0" style="width: '.$imSize.'px; height: '.$imSize.'px; background-image: url('.$item_url.');"></div>';
								
								}
					$str = $str.'</div>
								<div class="item_block_icons" style="width: '.$imSize.'">';
								if($im_count > 1) {
									$str = $str.'
									<div class="item_block_left active_icon">
										<svg fill="#000" height="32" viewBox="0 0 24 24" width="32" xmlns="http://www.w3.org/2000/svg">
											<path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
											<path d="M0 0h24v24H0z" fill="none"/>
										</svg>
									</div>';
								}	
								/*$str = $str.'<div class="item_block_zoom active_icon">
										<svg fill="#000" height="32" viewBox="0 0 24 24" width="32" xmlns="http://www.w3.org/2000/svg">
											<path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
											<path d="M0 0h24v24H0V0z" fill="none"/>
											<path d="M12 10h-2v2H9v-2H7V9h2V7h1v2h2v1z"/>
										</svg>
									</div>';*/
								if($im_count > 1) {
								$str = $str.'
									<div class="item_block_right active_icon">
										<svg fill="#000" height="32" viewBox="0 0 24 24" width="32" xmlns="http://www.w3.org/2000/svg">
											<path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
											<path d="M0 0h24v24H0z" fill="none"/>
										</svg>										
									</div>';
								}
					$str = $str.'</div>
							</div>
							<div class="item_detail_info_table">
								<div style="margin: 15px 0 0 0;">
									<div class="simple_button ext_selected" style="padding: 10px 50px; margin: 0 25px 15px 0;">
										Общая информация
									</div>
									<div class="simple_button" style="padding: 10px 50px; margin: 0 25px 15px 0;">
										Характеристики
									</div>
								</div>
								<div class="info_block selected">
									<div class="item_detail_info_list">
										<div class="item_detail_info_header">
											<div>Артикул </div>
											<div>'.$item['article'].'</div>
										</div>
										<div class="item_detail_info_header">
											<div>Категория </div>
											<div>'.$item['group_name'].'</div>
										</div>';
										if($allow_prices) {
											$strPrice = ($item["price"]=='' || $item["price"] == 0)?'-':number_format($item["price"], 2, '.', ' ').' руб';
											$str = $str.'<div class="item_detail_info_header">
											<div>Ваша цена </div>
											<div>'.$strPrice.'</div>
										</div>';
										}
										if($allow_stocks) {
											$strStocks = ($item["stock"]=='' || $item["stock"] == 0)?'Нет в наличии':number_format($item["stock"], 0, '.', ' ');
											$str = $str.'<div class="item_detail_info_header">
											<div>Наличие на складе </div>
											<div>'.$strStocks.'</div>
										</div>';
										}
										if(!$item['barcode'] == '') {
											$str = $str.'<div class="item_detail_info_header">
												<div>Штрихкод </div>
												<div>'.$item['barcode'].'</div>
											</div>';
										}
										if(!$item['item_url'] == '') {
											$str = $str.'<div class="item_detail_info_header">
											<a style="text-decoration: underline; font-style: italic;font-weight: 400;" href="'.$item['item_url'].'">Подробное описание на сайте производителя</a>
										</div>';
										}
									$str = $str.'</div>
									<div style="margin: 15px 0 0 0; display: inline-block; width: 100%;">
										<div style="font-weight: 600; font-size: 16px;" class="item_desc">
											Описание
											<p style="border: 1px solid #CCC; border-radius: 5px; padding: 5px; min-height: 100px; font-size: 14px;">'.$item["description"].'</p>
										</div>
									</div>
								</div>';
					$str = $str.'<div class="info_block">
									<div class="item_detail_info_list">';
										foreach($arProperties as $property)
										{
											$str = $str.'<div class="item_detail_info_header">
															<div>'.$property['property_name'].'</div>
															<div>'.$property['property_value'].'</div>
														</div>';
										}
						$str = $str.'</div>
								</div>';
				$str = $str.'</div>
						</div>';
				
			}	
			echo $str;
		}	
	}
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'documents_getList')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Documents_GetList', $arFnc);
	if($res['errCode'] == 0)
	{
		//$DOM = DOMDocument::loadXML($res["return"]);
		//$arResult = downloadOrders($DOM);
		if($adds=='json')
		{
			echo $res["return"];
		}
		elseif($adds=='json_html')
		{
			$arMessages = $res["return"];
			$arOrders = array();
			foreach($arMessages as $key=>$order)
			{
				$col_3 = ($order["sender"] == $TLP_obj->user_info['name'])?($order["receiver"]):($order["sender"]);
				$msg_date = DateTime::createFromFormat('Y-m-d H:i:s', str_replace("T"," ",$order["date"]));
				$str_date = ($msg_date === false)?(""):($msg_date->format('d-m-Y'));
				$str = '<div id="or_'.$order["message_id"].'" class="order" data-order-id="'.$order["message_id"].'" data-order-sender="'.$order["sender"].'" data-order-receiver="'.$order["receiver"].'">
					<div class="order_content">
						<div class="order_line">
							<div class="col_0">'.$docType[$order['type']].'</div>
							<div class="col_1">'.$order["num"].'</div><div class="col_2">'.$str_date.'</div>
							<div class="col_3">'.$col_3.'</div>
							<div class="col_4">'.number_format($order["sum"], 2, '.', ' ').'</div>
							<div class="col_5">'.$docStatus[$order["status"]].'</div>
						</div>
					</div>
					<p class="sub_info"><img src="/include/stdown.png"/></p>
				</div>';

				$arOrders[$order["message_id"]]['html'] 	= $str;
				$arOrders[$order["message_id"]]['date'] 	= $order["date"];
			}
			
			echo json_encode($arOrders);
		}
	}
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'Catalog_GetCategory')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds' || $key=='rtype'))
			{$arFnc[$key] = $value;}
	}	
	$res = $TLP_obj->telecall('Catalog_GetCategory', $arFnc);
	if($res['errCode'] == 0)
	{
		$arGroups = $res["return"];
		$str = '<div id="ext_pan_topblock">
					<div style="padding: 20px 0; text-align: center;">
						<div id="it_counter" class="filter_button fb_active" style="margin-right: 10px;">Найдено товаров</div>
						<div id="cancel_filters" class="filter_button">Сбросить всё</div>
					</div>';
						//<svg fill="#26A69A" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
						//	<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
						//	<path d="M0 0h24v24H0z" fill="none"></path>
						//</svg>
		if(count($arGroups) > 0) {
			$str = $str.'<div id="it_toplevel">Выберите категорию товара</div></div><div id="ext_filters">';
			foreach($arGroups as $key=>$item)
			{
				$parent_id = ($item['parent_id']=='')?('_zero_'):($item['parent_id']);
				$clevel = ($item['parent_id']=='')?(' it_clevel'):('');
				$str = $str.'<div class="it_category'.$clevel.'" data-itc-id ="'.$item['id'].'" data-itc-parentid ="'.$parent_id.'"  data-req-filters="0"><div class="cat_name">'.$item['name'].'</div></div>';
			}
		}
		else {
			$str = $str.'</div><div id="ext_filters">';
		}
		$str = $str.'<div id="it_filters"></div></div>';
		echo $str;
	}	
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'catalog_getQuantity')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if($key == 'filters' || $key == 'properties' || $key == 'fields') {
			$arFnc[$key] = json_decode($value,true);
		} elseif(!($key == 'action' || $key=='adds' || $key=='list_type'))
		{	
			$arFnc[$key] = $value;
		}
	}	
		
	$res = $TLP_obj->telecall('Catalog_GetQuantity', $arFnc);
	if($res['errCode'] == 0)
	{
		$result = json_decode($res["return"], true);
		echo $result;
	}	
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'catalog_FiltersGet')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Catalog_FiltersGet', $arFnc);
	if($res['errCode'] == 0)
	{
		$arResult = $res["return"];
		$str = '';
		$filterCount = count($arResult);
		$expanded = ($filterCount > 2)?(""):(" it_filter_expanded");
		$display = ($filterCount > 2)?(""):(' style="display: block;"');
		foreach($arResult as $key=>$item)
		{
			//$parent_id = ($item['parent_id']=='')?('_zero_'):($item['parent_id']);
			//$clevel = ($item['parent_id']=='')?(' it_clevel'):('');
			if($item['filter_type'] == 'enum') {
				$str = $str.'<div class="it_filter'.$expanded.'" data-filter-group-id ="'.$item['category_id'].'" data-filter-type ="'.$item['filter_type'].'" data-filter-name ="'.$item['filter_name'].'">';
			} else {
				$str = $str.'<div class="it_filter" data-filter-group-id ="'.$item['category_id'].'" data-filter-type ="'.$item['filter_type'].'" data-filter-name ="'.$item['filter_name'].'">';
			}			
			if($item['filter_type'] == 'enum') {
				$str = $str.''.ucfirst($item['filter_name']).'<div class="sel_enum"></div></div>';
				$str = $str.'<div class="it_filter_enum"'.$display.'>';
				foreach($item['enum_value'] as $key=>$value)
				{
					$str = $str.'<div class="it_filter_value" data-filter-value ="'.$value.'">
						<div class="checkbox"></div>
						<div class="enum_name">'.ucfirst($value).'</div>
					</div>';
				}
				$str = $str.'</div>';
			}
			else if($item['filter_type'] == 'boolean') {
				$str = $str.'<div class="checkbox"></div>
						<div class="filter_name">'.ucfirst($item['filter_name']).'</div>';
				$str = $str.'</div>';
			}
			else if($item['filter_type'] == 'string') {
				$str = $str.'<div class="filter_name">'.ucfirst($item['filter_name']).'</div>
				<div class="filter_string"><input class="filter_input" type="text" value="" placeholder="Введите '.mb_strtolower($item['filter_name'],'UTF-8').'"/></div>';
				$str = $str.'</div>';
			}
			else if($item['filter_type'] == 'float') {
				$str = $str.'<div class="filter_name">'.ucfirst($item['filter_name']).'</div>
				<div class="filter_float">
					от
					<input class="float_input" type="text" value="" placeholder="Введите '.mb_strtolower($item['filter_name'],'UTF-8').'"/>
					до
					<input class="float_input" type="text" value="" placeholder="Введите '.mb_strtolower($item['filter_name'],'UTF-8').'"/>
				</div>';
				$str = $str.'</div>';
			}
		}
		if($str == '') {
			$dop_str = ($arFnc['category_id']=='')?'':' для выбранной категории товаров';
			$str = '<div class="empty_cat">В каталоге нет настроенных расширенных фильтров'.$dop_str.'</div>';
		}
		echo $str;
	}	
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'Documents_GetById')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Documents_GetById', $arFnc);
	
	if($res['errCode'] == 0)
	{
		$retVal = json_decode($res["return"], true);
		echo var_dump($retVal);
		//
	}
	else
	{echo '%err%'.$TLP_obj->mistakes[$res['errCode']];}
}
elseif($action == 'requestPerson')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Contacts_RequestPerson', $arFnc);
	if($res['return'] == 0)
	{
		echo '<b>Приглашение успешно отправлено.</b><br><br>Ожидайте подтверждение от контакта.<br>До поступления ответа, контакт будет находиться в "Канале приглашенных контактов"';

	}
	elseif($res['return'] == -1)
	{
		echo '<b>Контакт успешно добавлен в "Канал общих контактов"</b><br><br>Публичные контакты не требуют подтверждения на приглашение.';

	}
	else
	{
		echo '%err%'.$TLP_obj->mistakes[$res['return']];
	}


}
elseif($action == 'setUserGroup')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Contacts_SetUserGroup', $arFnc);
	if($res['return'] == 0)
	{
		echo '0';

	}
	else
	{
		echo '%err%'.$TLP_obj->mistakes[$res['return']];
	}
}
elseif($action == 'getPersonInfo')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Contacts_GetPersonInfo', $arFnc);
	if($res['errCode'] == 0)
	{
		//$DOM = DOMDocument::loadXML($res["return"]);
		//$arResult = downloadCntInfo($DOM);

		if($adds=='json')
		{
			echo json_encode($res["return"]);
		}
		elseif($adds=='html')
		{
			$arResult = $res["return"];
			$arFields = array("user_name"=>"Пользователь",
							"email"=>"e-mail",
							"user_fullname"=>"Мое имя",
							"phone"=>"Телефон",
							"user_group"=>"Я в команде",
							"company"=>"Моя компания",
							"user_status"=>"Мой статус",
							"address"=>"Наш адрес",
							"information"=>"Информация о себе");
			$rdFields = array("user_name"=>true, "email"=>true, "user_group"=>true, "user_status"=>true, "company"=>true, "address"=>true);
			$msFields = array("fullname"=>true, "email"=>true, "information"=>true, "address"=>true, "company"=>true);
			$arStatus = array("buyer" => "Покупатель", "saler" => "Продавец");
			$readOnly = true;
			$mySettings = false;
			if ($TLP_obj->user_info['name'] == $arResult['user_name'])
				{
					$readOnly = false;
					$mySettings = true;
				}
				
			if($arResult['photo_id'] != '') {
				$im_url = '/my/ajax/files.php?a=detail&i='.$arResult['photo_id'].'&'.mt_rand();
				
				$size = getimagesize($im_url);
			}
			else {
				$im_url = '/include/no_avatar.svg';
			}
			if($arResult['company_logo'] != '') {
				$logo_url = '/my/ajax/files.php?a=detail&i='.$arResult['company_logo'].'&'.mt_rand();
				
				$logo_size = getimagesize($logo_url);
			}
			else {
				$logo_url = '/include/no_logo.png';
			}
			
			?>
			<div id="cnt_photo" class="cnt_photo">
				
				<img src="<?=$im_url;?>" data-width="<?=$size[0];?>" data-height="<?=$size[1];?>" data-change="0"/>
				<div id="im_line">
					<?if ($mySettings) {
					?>
						<div id="add_photo" class="active_icon">
							<form action="/my/ajax/upload.php" method="POST" enctype="multipart/form-data" name="fs_form">
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/add_photo.svg");?>
								<input type="hidden" name="operID" value="usr-addPhoto">
								<input type="hidden" name="usrname" value="<?=$TLP_obj->user_info['name'];?>">
								<input type="hidden" name="usrid" value="<?=$TLP_obj->user_info['id'];?>">
								<input type="file" name="filename" value="" style="display: none;">
								<input type="submit" style="display: none;">
							</form>
						</div>
						<div id="clear_photo" class="active_icon"><?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/clear.svg");?></div>
					<?}?>	
				</div>	
			</div>
			<div id="cnt_logo" class="cnt_photo" style="display:none">
				<img src="<?=$logo_url;?>" data-width="<?=$logo_size[0];?>" data-height="<?=$logo_size[1];?>" data-change="0"/>
				<div id="im_line">
					<?if ($mySettings) {
					?>
						<div id="add_photo" class="active_icon">
							<form action="/my/ajax/upload.php" method="POST" enctype="multipart/form-data" name="fs_logo">
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/add_photo.svg");?>
								<input type="hidden" name="operID" value="usr-addLogo">
								<input type="hidden" name="usrname" value="<?=$TLP_obj->user_info['name'];?>">
								<input type="hidden" name="usrid" value="<?=$TLP_obj->user_info['id'];?>">
								<input type="file" name="filename" value="" style="display: none;">
								<input type="submit" style="display: none;">
							</form>
						</div>
						<div id="clear_photo" class="active_icon"><?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/clear.svg");?></div>
					<?}?>	
				</div>	
			</div>			
			<div id="cnt_info_main" data-usr-flname="<?=$arResult['user_fullname'];?>">
			<?
			foreach($arFields as $key=>$fvalue)
			{
				$value = $arResult[$key];
				if($key == 'user_name') {
					continue;
				}
				if ($mySettings && array_key_exists($key, $rdFields)) {
					$readOnly = true;
				}
				elseif ($mySettings) {
					$readOnly = false;
				}
				
				/*if($arResult['allow_contacts'] == 0 && (!$mySettings) && ($key == 'phone' || $key == 'email')) {
					continue;}*/
				if($value == '' && $readOnly && (!array_key_exists($key, $msFields))) {
					continue;}
				$safe_value = '<pre>'.$value.'</pre>';	
				if($key == 'information') {
				?>
					<div class="cnt_headline">
						<div class="cnt_hd_x1" style="width: 100%;"><?=$fvalue?>:</div>
					<?	
					if($readOnly)
					{?>
						<div class="cnt_hd_x2" style="width: 97%; min-height: 70px;">
					<?	
						echo $safe_value;
					}
					else
					{?>
						<div class="cnt_hd_x2" style="width: 97%; min-height: 70px; border: none;">
						<textarea class="svd" id="inf_<?=$key;?>" name="<?=$key;?>"><? echo $value;?></textarea>
					<?}?>
					</div>
				<?}
				else
				{
					?>
					<div class="cnt_headline">
						<div class="cnt_hd_x1"><?=$fvalue;?>:</div>
					<?	
					if($readOnly)
					{?>
						<?if ($key == 'address'){?>
							<div id="address" class="svd cnt_hd_x2" data-crd="<?=$arResult["address_GPS"];?>">
						<?}
						else {?>
							<div class="cnt_hd_x2">
					<?	}
						if($key == 'email'){
						?>
							<pre><?=$safe_value;?></pre>
							<a href="mailto:<?=$value;?>">
								<div class="mail_icon active_icon help_icon">
									<div class="help_info">
										Отправить e-mail
									</div>

								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/mail_outline.svg");?>
								</div>
							</a>
						<?	
						}
						elseif ($key == 'company')
						{?>
							<pre style="width: 90%"><?echo ($value=='')?'-':$value;?></pre>
							<div id="cnt_company_info" class="mail_icon active_icon help_icon" style="right: 26px;">
								<div class="help_info">
									Показать карточку организации
								</div>
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/zoom_in.svg");?>
							</div>
							<div id="cnt_company_dld" class="mail_icon active_icon help_icon">
								<div class="help_info">
									Скачать карточку организации
								</div>
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/cloud_download.svg");?>
							</div>
						<?}	
						elseif ($key == 'address')
						{?>
							<pre style="width: 90%"><?=$value;?></pre>
							<div id="cnt_adress_map" class="mail_icon active_icon help_icon">
								<div class="help_info">
									Посмотреть адрес местонахождения на карте
								</div>
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/map_place.svg");?>
							</div>
							<?if($mySettings){?>
								<div id="cnt_adress_map_edit" class="mail_icon active_icon help_icon" style="right: 26px;">
									<div class="help_info">
										Изменить адрес местонахождения
									</div>
									<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/edit.svg");?>
								</div>
							<?}	
						}	
						elseif ($key == 'user_status')
						{?>
							<?=$arStatus[$value];?>
							<div class="mail_icon active_icon help_icon">
								<div class="help_info">
									<b>Покупатель</b> - контакт, у которого в TELEPORT нет своего каталога товаров/услуг для продажи.
									<br>
									<b>Продавец</b> - контакт, у которого в TELEPORT представлен каталог товаров/услуг доступный для просмотра и заказа.
									<br>
									<br>
									Если Вы хотите загрузить свой каталог товаров/услуг и принимать заказы в системе TELEPORT, то перейдите в меню Каталог.
									<br>
									<!--<a style="color: #26A69A; text-decoration: underline;" href="">Подробнее...</a>-->
								</div>
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/help_outline.svg");?>
							</div>
						<?}	
						else
						{
							echo $safe_value;
						}	
					}
					else
					{?>
						<div class="cnt_hd_x2" style="border: none; padding: 0px; width: 71%;">
							<input class="svd" id="inf_<?=$key;?>" type="text" name="<?=$key;?>" value="<?=$value?>"/>
							<?if ($key == 'company') {?>
								<div id="cnt_company_info" class="mail_icon active_icon help_icon" style="right: 29px;">
									<div class="help_info">
										Показать карточку организации
									</div>
									<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/zoom_in.svg");?>
								</div>
								<div id="cnt_company_dld" class="mail_icon active_icon help_icon">
									<div class="help_info">
										Скачать карточку организации
									</div>
									<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/cloud_download.svg");?>
								</div>
							<?}?>
					<?}?>	
					</div>
				<?}?>
				</div>
				<?if ($key == 'user_fullname') {?>
					</div>	
					<div id="cnt_info_body">
				<?}
			}?>
			</div>
			<div id="cnt_company_card" style="display: none;">
				<div class="cnt_headline">
					<div id="back_main" class="active_icon">
						<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/arrow_back.svg");?>
						<div style="display: inline-block; font-size: 14px; vertical-align: 7px; font-weight: 800;">
							Вернуться назад к основной информации
						</div>
					</div>
				</div>	
			<?
			$rdFields = array("user_name"=>true, "user_group"=>true, "user_status"=>true);
			$arFields = array("company"=>"Моя компания",
						"company_INN"=>"ИНН",
						"company_KPP"=>"КПП",
						"company_OGRN"=>"ОГРН",
						"company_account"=>"Р/сч",
						"company_bank"=>"в банке",
						"company_BIK"=>"БИК",
						"company_coraccount"=>"К/сч",
						"company_address"=>"Юр. адрес",
						"company_phone"=>"Телефоны",
						"company_chief"=>"Руководитель",
						"company_buh"=>"Главный бухгалтер"
						);
			foreach($arFields as $key=>$fvalue)
			{
				$value = $arResult[$key];
				if ($mySettings && array_key_exists($key, $rdFields)) {
					$readOnly = true;
				}
				elseif ($mySettings) {
					$readOnly = false;
				}
				
				/*if($value == '' && $readOnly) {
					continue;}*/
				$safe_value = '<pre>'.$value.'</pre>';	
				?>
					<div class="cnt_headline">
						<div class="cnt_hd_x1"><?=$fvalue;?>:</div>
					<?	
					if($readOnly)
					{?>
						<div class="cnt_hd_x2">
					<?
						if($key == 'email'){
						?>
							<a href="mailto:<?=$value;?>"><?=$safe_value;?>
								<div class="mail_icon active_icon help_icon">
									<div class="help_info">
										Отправить e-mail
									</div>

								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/mail_outline.svg");?>
								</div>
							</a>
						<?	
						}
						elseif ($key == 'company')
						{?>
							<?=$safe_value;?>
							<div id="cnt_company_dld" class="mail_icon active_icon help_icon">
								<div class="help_info">
									Скачать карточку организации
								</div>
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/cloud_download.svg");?>
							</div>
						<?}	
						else
						{
							echo ($value=='')?'-':$safe_value;
						}	
					}
					else
					{?>
						<div class="cnt_hd_x2" style="border: none; padding: 0px; width: 71%;">
							<input class="svd" id="inf_<?=$key;?>" type="text" name="<?=$key;?>" value="<?=$value?>"/>
							<?if ($key == 'company') {?>
								<div id="cnt_company_dld" class="mail_icon active_icon help_icon">
									<div class="help_info">
										Скачать карточку организации
									</div>
									<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/cloud_download.svg");?>
								</div>
							<?}?>
					<?}?>	
					</div>
				</div>
			<?}?>
			</div>
			<?if($arResult['user_status'] == 'saler') {?>
				<div id="cnt_addings" style="display: none;">
					<div class="cnt_headline">
						<div id="back_main" class="active_icon">
							<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/arrow_back.svg");?>
							<div style="display: inline-block; font-size: 14px; vertical-align: 7px; font-weight: 800;">
								Вернуться назад к основной информации
							</div>
						</div>
					</div>	
				<?
				if ((!$mySettings) && $arResult["delivery_possible"] == "0") {?>
					<div class="cnt_headline"><div class="cnt_hd_x2" style="width: 96%; font-weight: 400;">
						<?echo '<b>'.$arResult['fullname'].'</b> не оказывает услуг по доставке заказов.<br>
							Более подробную информацию вы можете получить, задав интересующие вас вопросы, непосредственно поставщику в разделе обмена сообщениями';?>
					</div></div>
				<?}
				else {
					$arFields["delivery_possible"]="Возможна доставка заказов";
					$arFields["delivery_info"]="Подробнее об условиях доставки заказов";
					foreach($arFields as $key=>$fvalue)
					{
						$value = $arResult[$key];
						if ($mySettings && array_key_exists($key, $rdFields)) {
							$readOnly = true;
						}
						elseif ($mySettings) {
							$readOnly = false;
						}
					
					
						if($key == 'delivery_possible') {?>
							<div class="cnt_headline">
								<div id="inf_<?=$key;?>" class="checkbox <?echo ($value==true)?'checkbox_clicked':'';?> <?echo ($readOnly)?'checkbox_disabled':'svd';?>"></div><span><?=$fvalue?></span>
							</div>
						<?}
						elseif ($key == 'delivery_info') {
						?>
							<div id="delivery_info" class="cnt_headline" style="<?=($arResult['delivery_possible']=='1')?'display: block;':'display: none;';?>">
								<div class="cnt_hd_x1" style="width: 100%;"><?=$fvalue?>:</div>
							<?	
							if($readOnly)
							{?>
								<div class="cnt_hd_x2" style="width: 97%; min-height: 150px;">
								<?echo $safe_value;?>
								</div>
							<?}
							else
							{?>
								<div class="cnt_hd_x2" style="width: 97%; min-height: 100px; border: none;">
								<textarea class="svd" id="inf_<?=$key;?>" name="<?=$key;?>"><? echo $value;?></textarea>
								</div>
							<?}?>
							</div>
						<?}
						
					}
				}?>	
				</div>
			<?}?>	
			<?if ($mySettings) {?>
				<div id="cnt_settings" style="display: none;">
					<div class="cnt_headline">
						<div id="back_main" class="active_icon">
							<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/arrow_back.svg");?>
							<div style="display: inline-block; font-size: 14px; vertical-align: 7px; font-weight: 800;">
								Вернуться назад к основной информации
							</div>
						</div>
					</div>	
				<?
				$arFields = array("payed_orders"=>"Кол-во оплаченных заказов",
							"public_contact"=>"Публичный контакт",
							"duplicate_messages"=>"Отправлять копии входящих сообщений на почту",
							"deny_msgs"=>"Запретить обмен сообщениями",
							"deny_files"=>"Запретить обмен файлами"
							);
				if($arResult['user_status'] == 'saler'){			
					$arFields["deny_orders"]="Запретить прием заказов";
					$arFields["allow_stocks"]="Отображать остатки товаров в каталоге товаров";
					$arFields["allow_prices"]="Отображать цены товаров в каталоге товаров";
					//$arFields["forward_to"]="Переадресовать сообщения";
				}			
				foreach($arFields as $key=>$fvalue)
				{
					$value = $arResult[$key];
					if($key == 'payed_orders') {?>
						<div class="cnt_headline">
							<div class="cnt_hd_x1" style="width: 210px;"><?=$fvalue;?>:</div>
							<div class="cnt_hd_x2" style="width: 17%; text-align: right;">
								<? echo ($value=='')?'0':$safe_value;?>
							</div>
							<div style="display: inline-block; position: relative; top: 6px;" class="active_icon help_icon">
								<div class="help_info">
									Пополнить баланс, чтобы ваши покупатели могли отправлять вам заказы в системе TELEPORT.
								</div>
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/wallet.svg");?>
							</div>
							<div class="mail_icon active_icon help_icon">
								<div class="help_info">
									Для приема заказов от покупателей вам необходимо купить пакет с любым удобным количеством заказов.
									При нулевом балансе, заказы от ваших покупателей поступать не будут.
								</div>
								<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/help_outline.svg");?>
							</div>
						</div>
					<?}
					else {
					?>
					<div class="cnt_headline">
						<div id="inf_<?=$key;?>" class="checkbox svd <?echo ($value==true)?'checkbox_clicked':'';?>"></div><span><?=$fvalue?></span>
						<?if($key == 'public_contact'){?>
						<div class="mail_icon active_icon help_icon">
							<div class="help_info">
								<?echo 'Каталог публичного контакта виден всем пользователям в системе TELEPORT, даже если ваш контакт не присутствует
									в списке контактов пользователя. Также публичный контакт не требует подтверждения на приглашение в список контактов любого пользователя в системе TELEPORT';?>
							</div>
							<?include($_SERVER["DOCUMENT_ROOT"]."/my/data/svg/help_outline.svg");?>
						</div>
						<?}?>
					</div>
					<?}
				}?>
				</div>
			<?}?>
			<div id="buttons" class="cnt_headline">
				<div id="cnt_info_docs" style="display: inline-block; min-width: 150px; margin: 0 46px 0 0;" class="menu_button">Файлы профиля</div>
				<?if($arResult['user_status'] == 'saler') {?>
					<div id="cnt_info_add" style="display: inline-block; min-width: 150px; margin: 0 46px 0 0;" class="menu_button">Доставка</div>
				<?}?>	
				<?if ($mySettings) {?>	
					<div id="cnt_info_settings" style="display: inline-block; min-width: 150px; margin: 0;" class="menu_button">Настройки</div>
				<?}?>
			</div>
			<?if ($mySettings) {?>	
				<div id="cnt_info_save" style="display: none; min-width: 150px; margin: 15px 0 0 0;" class="menu_button">Сохранить</div>
			<?}?>
						
		<?}

	}
	else
	{
		echo '%err%'.$TLP_obj->mistakes[$res['errCode']];
	}


}
elseif($action == 'deletePerson')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
	if($arFnc['block'] == 'true') {
		$arFnc['block'] = true;
	} else {$arFnc['block'] = false;}
		
	$res = $TLP_obj->telecall('Contacts_DeletePerson', $arFnc);
	if($res['return'] == 0)
	{
		echo '0';
	}
	else
	{
		echo '%err%'.$TLP_obj->mistakes[$res['return']];
	}
}	
elseif($action == 'unblockPerson')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Contacts_UnlockPerson', $arFnc);
	if($res['return'] <= 0)
	{
		echo $res['return'];
	}
	else
	{
		echo '%err%'.$TLP_obj->mistakes[$res['return']];
	}
}	
elseif($action == 'acceptPerson')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
		
	$res = $TLP_obj->telecall('Contacts_AcceptPerson', $arFnc);
	if($res['return'] == 0)
	{
		echo '';
	}
	else
	{
		echo '%err%'.$TLP_obj->mistakes[$res['return']];
	}
}
elseif($action == 'changePassword')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
	$pass_hash = $TLP_obj->telecall('MD5',array('input'=>$arFnc['pass_hash']));
	$new_hash = $TLP_obj->telecall('MD5',array('input'=>$arFnc['new_hash']));
	$arFnc['pass_hash'] = $pass_hash;
	$arFnc['new_hash'] = $new_hash;
	$arFnc['username'] = $TLP_obj->user_info['name'];
	
	$res = $TLP_obj->telecall('Users_ChangePassword', $arFnc);
	if($res['return'] == 0)
	{
	}
	else
	{
		echo '%err%'.$TLP_obj->mistakes[$res['return']];
	}
}
elseif($action == 'getNewStatus')
{
	$arFnc = array();
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
			{$arFnc[$key] = $value;}
	}	
	$res = $TLP_obj->telecall('Messages_GetNewStatus', $arFnc);
	if($res['errCode'] == 0)
	{
		echo $res['return'];
	}
	else
	{
		echo '%err%'.$TLP_obj->mistakes[$res['errCode']];
	}
}

elseif($action == 'setPersonInfo')
{
	$arPermitted = array("user_name", "user_fullname", "phone", "company", "duplicate_messages", "public_contact", "allow_stocks", 
		"allow_prices", "information", "deny_msgs", "deny_orders", "deny_files", "forward_to", "delivery_possible", "address", 
		"address_GPS", "delivery_info", "company_INN", "company_KPP", "company_OGRN", "company_account", "company_BIK",
		"company_bank", "company_coraccount", "company_chief", "company_buh", "company_phone", "company_activitytypes", "company_address", 
		"company_logo", "photo");

	$arParam = array();
	$photo_filename = "";
	$logo_filename = "";
	foreach ($_POST as $key => $value) 
	{
		if(!($key == 'action' || $key=='adds'))
		{
			if(in_array($key, $arPermitted)) {
				
				if($key == 'photo' || $key == 'company_logo') {
					if($value == '') {
						$arParam[$key] = "";
					}
					else {
						$arexp = explode('?',$value); 
						$arParam[$key] = base64_encode(file_get_contents($_SERVER["DOCUMENT_ROOT"].$arexp[0]));
						if($key == 'photo') {
							$photo_filename = $_SERVER["DOCUMENT_ROOT"].$arexp[0];
						} else {
							$logo_filename = $_SERVER["DOCUMENT_ROOT"].$arexp[0];
						}	
					}	
				}
				else {
					$arParam[$key] = $value;
				}	
			}
		}
	}
	$arFnc = array("request_JSON"=>$arParam);
	$res = $TLP_obj->telecall('Contacts_SetPersonInfo', $arFnc);
	if(!($res['return'] == 0))
	{
		echo '%err%'.$TLP_obj->mistakes[$res['return']];
	}
	else {
		$arFnc = array('contact'=>$_POST['name']);
		$res = $TLP_obj->telecall('Contacts_GetPersonInfo', $arFnc);
		if($res['errCode'] == 0)
		{
			$arResult = $res["return"];
			if($arResult['photo_id'] != '') {
				$im_url = '/my/ajax/files.php?a=detail&i='.$arResult['photo_id'].'&'.mt_rand();
				$size = getimagesize($im_url);
			}
			else {
				$im_url = '/include/no_avatar.svg';
			}
			
			if($arResult['company_logo'] != '') {																																	// Добавлен код
				$logo_url = '/my/ajax/files.php?a=detail&i='.$arResult['company_logo'].'&'.mt_rand();								// для сохранения	
				$logo_size = getimagesize($logo_url);																																		// логотипа организации
			}																																																			// на
			else {
				$logo_url = '/include/no_logo.png';
			}
			
			$TLP_obj->user_info['photo'] = '/my/ajax/files.php?a=prev&i='.$arResult['photo_id'];
			$TLP_obj->user_info['company_logo'] = '/my/ajax/files.php?a=prev&i='.$arResult['company_logo'];				// сервере TELEPORT
			$TLP_obj->user_info['name'] = $arResult['user_name'];
			$TLP_obj->user_info['fullname'] = $arResult['user_fullname'];
			$_SESSION["TLP_obj"] = serialize($TLP_obj);
			echo json_encode(array("fullname"=>$TLP_obj->user_info['fullname'], "photo"=>$TLP_obj->user_info['photo'].'?'.mt_rand(), "company_logo"=>$TLP_obj->user_info['company_logo'].'?'.mt_rand()));
			if($photo_filename != "") {
				unlink($photo_filename);
			}
			if($logo_filename != "") {
				unlink($logo_filename);
			}
			
			
		}	
	}
	
}
elseif($action == 'FindPersons')
	{
		$arFnc = array(
			"name" => $_POST['new_cntname'],
			"limit" => 20
		);
		$res = $TLP_obj->telecall("Contacts_FindPersons", $arFnc);
		$strPersons = "";
		foreach ($res['return'] as $key => $value) {
			$strPersons = $strPersons.'<div class="contact_inf" data-usr-name="'.$value['name'].'" style="display: block;">
				<table style="border-spacing: 0;">
					<tbody>
					<tr>
						<td>
							<div>
								<div class="cnt_avatar cnt_avatar_small" style="background-image: url(/my/ajax/files.php?a=prev&i='.$value['photo_id'].');"></div>
							</div>
						</td>
						<td>
							<div class="cnt_text">
								'.$value['fullname'].'											
							</div>
							<p class="cnt_add cnt_mail" style="display: block;">
								'.$value['name'].'											
							</p>
						</td>	
					</tr>	
					</tbody>
				</table>
			</div>';	
		}	
		if($strPersons == "") {
			if(preg_match("/.+@.+\..+/i", $_POST['new_cntname'])) {
				$strPersons = '<div class="contact_inf" style="text-align: center;"><span style="color: #FF0000;">Контакт не найден в системе.</span><br>Приглашение будет отправлено на адрес электронной почты.</div>';
			} else {
				$strPersons = '<div class="contact_inf" style="text-align: center;"><span style="color: #FF0000;">Контакт не найден в системе.</span><br>Введите корректный e-mail, чтобы отправить приглашение на почту.</div>';
			}			
		}
		echo $strPersons;
	}



?>