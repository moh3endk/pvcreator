<?php
ob_start();
error_reporting(0);
date_default_timezone_set('Asia/Tehran');
//--------[Your Config]--------//
$Dev =1487061355;
$channel = "@static_team";
$logchannel = -1001343369285;
$host_folder = "Https://tech-rashidi.ir/wp-admin/user/pv";
//-----------------------------//
define('API_KEY','1761199454:AAEFeCiIJPCK5VT6firfyXi8w_4nUTwzAa0');//توکن
//------------------------------------------------------------------------------
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
//------------------------------------------------------------------------------
function CrZip($folder_to_zip_path, $destination_zip_file_path){
        $rootPath = realpath($folder_to_zip_path);
        
        $zip = new ZipArchive();
        $zip->open($destination_zip_file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootPath),
                RecursiveIteratorIterator::LEAVES_ONLY
        );
       
        foreach($files as $name => $file){
            if(!$file->isDir()){
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
}
function DeleteFolder($path){
	if($handle=opendir($path)){
		while (false!==($file=readdir($handle))){
			if($file<>"." AND $file<>".."){
				if(is_file($path.'/'.$file)){ 
					@unlink($path.'/'.$file);
				} 
				if(is_dir($path.'/'.$file)) { 
					deletefolder($path.'/'.$file); 
					@rmdir($path.'/'.$file); 
				}
			}
        }
    }
}
//------------------------------------------------------------------------------
function SendMessage($chat_id,$text,$mode,$reply = null,$keyboard = null){
	bot('SendMessage',[
	'chat_id'=>$chat_id,
	'text'=>$text,
	'parse_mode'=>$mode,
	'reply_to_message_id'=>$reply,
	'reply_markup'=>$keyboard
	]);
}
function SendDocument($chatid,$document,$caption = null){
	bot('SendDocument',[
	'chat_id'=>$chatid,
	'document'=>$document,
	'caption'=>$caption
	]);
}
function Forward($chatid,$from_id,$massege_id){
	bot('ForwardMessage',[
    'chat_id'=>$chatid,
    'from_chat_id'=>$from_id,
    'message_id'=>$massege_id
    ]);
}
function GetChat($chatid){
	$get =  bot('GetChat',['chat_id'=>$chatid]);
	return $get;
}
function GetMe(){
	$get =  bot('GetMe',[]);
	return $get;
}
//------------------------------------------------------------------------------
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)){
    $message = $update->message; 
    $chat_id = $message->chat->id;
    $text = $message->text;
    $message_id = $message->message_id;
    $from_id = $message->from->id;
    $tc = $message->chat->type;
    $first_name = $message->from->first_name;
    $last_name = $message->from->last_name;
    $username = $message->from->username;
    $caption = $message->caption;
    $reply = $message->reply_to_message->forward_from->id;
    $reply_id = $message->reply_to_message->from->id;
}
if(isset($update->callback_query)){
    $Data = $update->callback_query->data;
    $data_id = $update->callback_query->id;
    $chatid = $update->callback_query->message->chat->id;
    $fromid = $update->callback_query->from->id;
    $tccall = $update->callback_query->chat->type;
    $messageid = $update->callback_query->message->message_id;
}
//------------------------------------------------------------------------------
$get = Bot('GetChatMember',[
'chat_id'=>$channel,
'user_id'=>$from_id]);
$rank = $get->result->status;
//------------------------------------------------------Buttons
if($from_id != $Dev){
$menu = json_encode(['keyboard'=>[
[['text'=>"🔄 ساخت ربات"]],
[['text'=>"☢️ حذف ربات"],['text'=>"🤖 ربات های من"]],
[['text'=>"📕 قوانین"],['text'=>"📌 راهنما"]]
],'resize_keyboard'=>true]);
}else{
$menu = json_encode(['keyboard'=>[
[['text'=>"🔄 ساخت ربات"]],
[['text'=>"☢️ حذف ربات"],['text'=>"🤖 ربات های من"]],
[['text'=>"📕 قوانین"],['text'=>"📌 راهنما"]],
[['text'=>"🖲 مدیریت"]]
],'resize_keyboard'=>true]);
}
//-------------------------------------------------Dev
$panel = json_encode(['keyboard'=>[
[['text'=>"🗝 آپدیت ربات ها"],['text'=>"📊 آمار"]],
[['text'=>"📬 ارسال همگانی"],['text'=>"📮 فروارد همگانی"]],
[['text'=>"✖️ حذف ربات"],['text'=>"🗄 پشتیبان گیری"]],
[['text'=>"✅ لغو مسدود کاربر"],['text'=>"🚸 مسدود کاربر"]],
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
//-------------------------------------------------Other
$back = json_encode(['keyboard'=>[
[['text'=>"▫️ برگشت ▫️"]]
],'resize_keyboard'=>true]);
$backpanel = json_encode(['keyboard'=>[
[['text'=>"▫️ برگشت به پنل ▫️"]]
],'resize_keyboard'=>true]);
$request = json_encode(['keyboard'=>[
[['text'=>"🔑 تایید هویت",'request_contact'=>true]]
],'resize_keyboard'=>true]);
$remove = json_encode(['KeyboardRemove'=>[],'remove_keyboard'=>true]);
//------------------------------------------------------------------------------
//--------[Json]--------//
@$list = json_decode(file_get_contents("Data/list.json"),true);
@$data = json_decode(file_get_contents("Data/$from_id/data.json"),true);
@$step = $data['step'];
//------------------------------------------------------------------------------
if(in_array($from_id, $list['ban'])){
	SendMessage($chat_id,"■ دسترسی شما به سرور ربات ساز محدود شده ...", null, $message_id, $remove);
	exit();
}
elseif(preg_match('/^\/(start)$/i',$text)){
	SendMessage($chat_id,"■ سلام $first_name خوش آمدید\n\n■ لطفا یکی از گزینه ها را انتخاب کنید :", null, $message_id, $menu);
	$data['step'] = "none";
	file_put_contents("Data/$from_id/data.json",json_encode($data));
	$first_name = str_replace(["<",">"], null, $first_name);
	SendMessage($logchannel,"■ کاربر <a href='tg://user?id=$from_id'>$first_name</a> ربات را استارت کرد.", 'Html', null);
}
elseif($rank == 'left'){
	SendMessage($chat_id,"■ برای استفاده از ربات و همچنین حمایت از ما ابتدا وارد کانال\n● $channel\n■ سپس به ربات برگشته و /start را بزنید.", null, $message_id, $remove);
}
elseif($text == "▫️ برگشت ▫️"){
	$data['step'] = "none";
	file_put_contents("Data/$from_id/data.json",json_encode($data));
	SendMessage($chat_id,"↩️ شما به منوی اصلی برگشتید", null, $message_id, $menu);
}
elseif($text == "سورس"){
	$data['step'] = "none";
	file_put_contents("Data/$from_id/data.json",json_encode($data));
	SendMessage($chat_id,"↩ @t000c شما به منوی اصلی برگشتید", null, $message_id, $menu);
}
elseif($text == "🔄 ساخت ربات"){
	if($data['phone'] != null){
		$data['step'] = "create";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ توکن (شناسه) ربات خود را از بات فادر [@BotFather] را فروارد کنید.", null, $message_id, $back);
	}else{
		$data['step'] = "phone";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ به دلیل برخی مشکلات امنیتی مدیران ربات (`شخص شما`) ، لازم است ابتدا هویت خود را توسط دکمه زیر تایید کنید.", 'MarkDown', $message_id, $request);
	}
}
elseif($step == "create"){
	if(strpos($text, "Here is the token for bot") !== true and strpos($text, "Use this token to") !== true){
		$token = $text;
	}
	if(strpos($text, "Here is the token for bot") !== false){
		$token = preg_replace('/(Here is the token for bot)(.*)/', null, $text);
		$token = str_replace("\n", null, $token);
	}
	if(strpos($text, "Use this token to") !== false){
		$token = strchr($text,"Use this token to access the http API:");
		$token = str_replace(["Use this token to access the http API:","For a description of the Bot API, see this page: https://core.telegram.org/bots/api","\n"], null, $token);
	}
	$result = json_decode(file_get_contents('https://api.telegram.org/bot'.$token.'/getMe'), true);
	$un = $result['result']['username'];
	$ok = $result['ok'];
	
	if($ok == true){
		if(!file_exists("Bots/$un/config.php")){
			$config = file_get_contents("Source/config.php");
			$config = str_replace("**ADMIN**", $from_id, $config);
			$config = str_replace("**TOKEN**", $token, $config);
			
			mkdir("Bots/$un");
			copy("Source/index.php","Bots/$un/index.php");
			copy("Source/handler.php","Bots/$un/handler.php");
			file_put_contents("Bots/$un/config.php",$config);
			$txt = urlencode("*Bot is Running ... | Click* /start");
	        file_get_contents("https://api.telegram.org/bot".$token."/SendMessage?chat_id=".$from_id."&text=".$txt."&parse_mode=MarkDown");
	        $WebHook = file_get_contents("https://api.telegram.org/bot".$token."/SetWebHook?url=$host_folder/Bots/$un/index.php");
			$data['step'] = "none";
			$data['bots'][] = "@$un";
			file_put_contents("Data/$from_id/data.json",json_encode($data));
			SendMessage($chat_id,"■ ربات شما با موفقیت به سرور ما متصل شد.\n■ @$un\n■ به ربات خود رفته و استارت کنید.", null, $message_id, $menu);
			$first_name = str_replace(["<",">"], null, $first_name);
			SendMessage($logchannel,"■ کاربر <a href='tg://user?id=$from_id'>$first_name</a>\nربات خود را با آیدی [@$un] ایجاد کرد.", 'Html', null);
		}else{
			$data['step'] = "none";
			file_put_contents("Data/$from_id/data.json",json_encode($data));
			SendMessage($chat_id,"■ این ربات از قبل به سرور ما متصل بود ...", null, $message_id, $menu);
		}
	}else{
		SendMessage($chat_id,"■ توکن معتبر نیست !", null, $message_id, $back);
	}
}

elseif($text == "🤖 ربات های من"){
	if($data['bots'] != null){
		$bots = implode(" - ", $data['bots']);
		SendMessage($chat_id,"■ لیست ربات های شما :\n<b>----------------------</b>\n$bots\n<b>----------------------</b>", 'Html', $message_id);
	}else{
		SendMessage($chat_id,"■ شما هیچ رباتی نزد ما ندارید !", null, $message_id);
	}
}
elseif($text == "☢️ حذف ربات"){
	if($data['bots'] != null){
		$data['step'] = "delbot";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		foreach($data['bots'] as $key => $name){
			$name = $data['bots'][$key];
			$bots[] = [['text'=>"👉🏻 $name"]];
		}
		$bots[] = [ ['text'=>"▫️ برگشت ▫️"] ];
		$bots = json_encode(['keyboard'=> $bots ,'resize_keyboard'=>true]);
		SendMessage($chat_id,"■ یکی از ربات های زیر را برای حذف انتخاب کنید.", null, $message_id, $bots);
	}else{
		SendMessage($chat_id,"■ شما هیچ رباتی نزد ما ندارید !", null, $message_id);
	}
}
elseif($data['step'] = "delbot" and strpos($text, "👉🏻 ") !== false){
	$botid = str_replace("👉🏻 @", null, $text);
	if(in_array("@".$botid, $data['bots'])){
		DeleteFolder("Bots/$botid");
		rmdir("Bots/$botid");
		$data['step'] = "none";
		$search = array_search("@".$botid, $data['bots']);
		unset($data['bots'][$search]);
		$data['bots'] = array_values($data['bots']);
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ ربات [@$botid] با موفقیت از سرور ما حذف گردید.", null, $message_id, $menu);
		$first_name = str_replace(["<",">"], null, $first_name);
		SendMessage($logchannel,"■ کاربر <a href='tg://user?id=$from_id'>$first_name</a>\nربات خود را با آیدی [@$botid] از سرور حذف کرد.", 'Html', null);
	}else{
		SendMessage($chat_id,"■ خطایی رخ داد ...\nلطفا دوباره امتحان کنید.", null, $message_id);
	}
}
elseif($text == "📕 قوانین"){
	SendMessage($chat_id,"■ قوانین استفاده از ربات ساز نواتیم :\n\n● همه مطالب باید تابع قوانین جمهوری اسلامی ایران باشد.\n\n● ساخت هرگونه ربات در ضمیمه +18 خلاف قوانین ربات می باشد و در صورت مشاهده ربات مورد نظر مسدود و همچنین مدیر ربات از تمامی ربات ها بلاک می شود.\n\n● مسئولیت پیام های رد و بدل شده در هر ربات با مدیر آن می باشد و ما هیچگونه مسئولیتی نداریم.\n\n● در صورت مشاهده استفاده از قابلیت های ربات در جهات منفی به شدت برخورد می شود.", null, $message_id);
}
elseif($text == "📌 راهنما"){
	SendMessage($chat_id,"■ آموزش ایجاد یک ربات پیام رسان\n\n1⃣ ابتدا به ربات ( @BotFather ) مراجعه کنید\n2⃣ دستور /newbot رو ارسال کنید\n3⃣ یک نام برای ربات ارسال کنید\n4⃣ پس از ارسال نام،یک  سلام مای اوج یوزرنیم ارسال کنید\n❌ توجه کنین حتما باید در آخر یوزرنیم ارسالی کلمه bot با حروف کوچیک یا بزرگ (فرقی نداره) وجود داشته باشه\n5⃣ اگر با پیغام زیر برخورد کردید\nSorry, this username is already taken. Please try something different.\nیعنی قبلا یکی این یوزرنیم رو ثبت کرده یوزرنیم دیگری وارد کنید. اگر این پیغام رو دریافت نکردید یعنی کار حل است\n6⃣ حالا به این ربات مراجعه کنید و دکمه ( 🔄 ساخت ربات ) رو بزنید\n7⃣ سپس پیام آخری که از ربات ( @BotFather ) دریافت کردید را در فروارد کنید.\n8⃣ ربات شما با موفقیت به سرور ما متصل شد :)", null, $message_id);
}
elseif($step == "phone" and isset($message->contact)){
	if($update->message->contact->user_id == $from_id){
		$phone_number =	$message->contact->phone_number;
		$data['step'] = "none";
		$data['phone'] = "+$phone_number";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ هویت شما با موفقیت تایید شد.", null, $message_id);
		SendMessage($chat_id,"■ لطفا یکی از گزینه ها را انتخاب کنید :", null, null, $menu);
		$first_name = str_replace(["<",">"], null, $first_name);
		SendMessage($logchannel,"■ کاربر <a href='tg://user?id=$from_id'>$first_name</a> هویت خود را تایید کرد 👇🏻", 'Html', null);
		Forward($logchannel,$chat_id,$message_id);
	}else{
		SendMessage($chat_id,"■ لطفا فقط از طریق دکمه زیر اقدام به تایید هویت خود کنید.", null, $message_id, $request);
	}
}
//------------------------------------------------------------------------------
if($from_id == $Dev){
    if($text == "🖲 مدیریت" || $text == "▫️ برگشت به پنل ▫️"){
		$data['step'] = "none";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ یکی از گزینه های زیر را انتخاب کنید :", null, $message_id, $panel);
	}
    elseif($text == "📊 آمار"){
		$users = count(scandir("Data"))-4;
		$robots = count(scandir("Bots"))-2;
		
		$count = count($list['user'])-9;
		$lastmem = null;
		foreach($list['user'] as $key => $value){
			if($count <= $key){
				$lastmem .= "[$value](tg://user?id=$value) | ";
				$key++;
			}
		}
		SendMessage($chat_id,"■ تعداد کاربران ربات : $users\n■ تعداد ربات ها : $robots\n\n■ 9 کاربر اخیر ربات :\n$lastmem", 'MarkDown', $message_id);
	}
	elseif($text == "🗝 آپدیت ربات ها"){
		exec("php update.php");
		SendMessage($chat_id,"■ تمامی ربات ها با موفقیت به آخرین نسخه از سورس ارتقا یافتند.", null, $message_id);
	}
	elseif($text == "🗄 پشتیبان گیری"){
	    CrZip("Https://tech-rashidi.ir/wp-admin/user/pv","backup.zip");
	    SendDocument($chat_id,new CURLFile("backup.zip"),"🗂 یک نسخه پشتیبان کامل از سیستم");
	    array_map("unlink", glob('backup.zip*?'));
	}
	
	elseif($text == "📬 ارسال همگانی"){
		$data['step'] = "s2all";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ پیام مورد نظر را ارسال کنید", 'MarkDown', $message_id, $backpanel);
	}
	elseif($step == "s2all" and isset($text)){
		$data['step'] = "none";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		foreach(glob('Data/*') as $value){
		    if(is_dir($value)){
		        $id = pathinfo($value)['filename'];
			    SendMessage($id, $text, null, null, $menu);
		    }
		}
		SendMessage($chat_id,"■ پیام به تمامی اعضا ارسال شد", null, null, $panel);
	}
	elseif($text == "📮 فروارد همگانی"){
		$data['step'] = "f2all";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ پیام مورد نظر را فروارد کنید", 'MarkDown', $message_id, $backpanel);
	}
	elseif($step == "f2all" and isset($message)){
		$data['step'] = "none";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		foreach(glob('Data/*') as $value){
		    if(is_dir($value)){
		        $id = pathinfo($value)['filename'];
			    Forward($id,$chat_id,$message_id);
		    }
		}
		SendMessage($chat_id,"■ پیام به تمامی اعضا فروارد شد", null, null, $panel);
	}
	elseif($text == "✖️ حذف ربات"){
		$data['step'] = "deletebot";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ آیدی ربات مورد نظر را با دقت کوچک و بزرگ بودن ارسال کنید.", 'MarkDown', $message_id, $backpanel);
	}
	elseif($step == "deletebot" and isset($text)){
		$data['step'] = "none";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		$id = str_replace("@", null, $text);
		if(file_exists("Bots/$id/config.php")){
			DeleteFolder("Bots/$id");
			rmdir("Bots/$id");
			SendMessage($chat_id,"■ ربات $text با موفقیت از سرور حذف گردید.", null, $message_id, $panel);
		}else{
			SendMessage($chat_id,"■ ربات با آیدی $text یافت نشد !", null, $message_id, $panel);
		}
	}
	elseif($text == "🚸 مسدود کاربر"){
		$data['step'] = "banuser";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ آیدی عددی کاربر را جهت مسدود کردن از سیستم ارسال کنید.", 'MarkDown', $message_id, $backpanel);
	}
	elseif($step == "banuser" and is_numeric($text)){
		$data['step'] = "none";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		if(!in_array($text, $list['ban'])){
			$list['ban'][] = "$text";
			file_put_contents("Data/list.json",json_encode($list, true));
			SendMessage($chat_id,"■ کاربر [$text](tg://user?id=$text) با موفقیت مسدود شد.", 'MarkDown', null, $panel);
		}
	}
	elseif($text == "✅ لغو مسدود کاربر"){
		$data['step'] = "unbanuser";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		SendMessage($chat_id,"■ آیدی عددی کاربر را جهت لغو مسدودیت از سیستم ارسال کنید.", 'MarkDown', $message_id, $backpanel);
	}
	elseif($step == "unbanuser" and is_numeric($text)){
		$data['step'] = "none";
		file_put_contents("Data/$from_id/data.json",json_encode($data));
		if(in_array($text, $list['ban'])){
			$search = array_search($text, $list['ban']);
			unset($list['ban'][$search]);
			$list['ban'] = array_values($list['ban']);
			file_put_contents("Data/list.json",json_encode($list, true));
			SendMessage($chat_id,"■ کاربر [$text](tg://user?id=$text) با موفقیت آزاد شد.", 'MarkDown', null, $panel);
		}
	}
}
//------------------------------------------------------------------------------
if(!is_dir("Data/$from_id") and !is_null($from_id)){
	mkdir("Data/$from_id");
	touch("Data/$from_id/data.json");
    if($list['user'] == null){ $list['user'] = []; }
	array_push($list['user'], $from_id);
	file_put_contents("Data/list.json",json_encode($list));
}
//------------------------------------------------------------------------------
unlink("error_log");
?>