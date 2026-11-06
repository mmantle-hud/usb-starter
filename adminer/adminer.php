<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.3.0
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.3.0";error_reporting(24575);set_error_handler(function($ed,$hd){return!!preg_match('~^Undefined (array key|offset|index)~',$hd);},E_WARNING|E_NOTICE);if(!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"))||ini_get("filter.default_flags")){foreach(['_GET','_POST','_COOKIE','_SERVER']as$X){$vl=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($vl)$$X=$vl;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Qb=adminer()->credentials();$K=Driver::connect($Qb[0],$Qb[1],$Qb[2]);return(is_object($K)?$K:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$Cf=substr($t,-1);return
str_replace($Cf.$Cf,$Cf,substr($t,1,-1));}function
q($R){return
connection()->quote($R);}function
idx($Aa,$w,$i=null){return($Aa&&array_key_exists($w,$Aa)?$Aa[$w]:$i);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_user_type($V){return
in_array($V,idx(driver()->structuredTypes(),lang(0),[]));}function
full_type_sql(array$k){$V=$k["type"];return(is_user_type($V)?idf_escape($V).substr($k["full_type"],strlen($V)):$k["full_type"]);}function
is_searchable(array$k,array$X){if(!isset($k["privileges"]["where"]))return
false;if(preg_match('~NULL$~',$X["op"]))return
true;$V=$k["type"];$oj=$X["val"];$Sa='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Sa~",$V))return
false;if(preg_match(number_type(),$V)){$C='-?\d+(\.\d+)?';$Eg='';if(preg_match('~IN$~',$X["op"])){$oj=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$oj);$Eg="( *, *$C)*";}elseif($X["op"]=="BETWEEN")$Eg="\\s+AND\\s+$C";return(bool)preg_match('~^'.$C.$Eg.'$~i',$oj);}if(preg_match('~^(small)?date|^timestamp~',$V))return(bool)preg_match('~^\d+-\d+-\d+~',$oj);if(preg_match('~^time~',$V))return(bool)preg_match('~^\d+:\d+~',$oj);if(preg_match('~^bool~',$V)||(JUSH=="mssql"&&$V=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$oj);return
true;}function
bracket_escape($t,$La=false){static$bl=[':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5'];return
strtr($t,($La?array_flip($bl):$bl));}function
url_escape($R){static$bl=[];if(!$bl){$bl=[' '=>'+'];foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$eb)$bl[$eb]=sprintf('%%%02X',ord($eb));for($r=0;$r<256;$r++){if($r<32||$r>126)$bl[chr($r)]=sprintf('%%%02X',$r);}}return
strtr((string)$R,$bl);}function
min_version($Tl,$Wf="",$f=null){$f=connection($f);$Dj=$f->server_info;if($Wf&&preg_match('~([\d.]+)-MariaDB~',$Dj,$A)){$Dj=$A[1];$Tl=$Wf;}return$Tl&&version_compare($Dj,$Tl)>=0;}function
charset(Db$Db){return(min_version("5.5.3",0,$Db)?"utf8mb4":"utf8");}function
ini_set($rh,$Y){return(function_exists('ini_set')?\ini_set($rh,$Y):false);}function
ini_bool($Xe){$X=ini_get($Xe);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Xe){$X=ini_get($Xe);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($L,$Fh){$Zf=(int)ini_get("max_input_vars");return($Zf?(int)floor(($Zf-$Fh)/$L):0);}function
max_input_vars_error(){$Xe="max_input_vars";return
lang(1,"<b>$Xe = ".ini_get($Xe)."</b>");}function
sid(){static$K;if($K===null)$K=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$K;}function
set_password($Sl,$O,$Kl,$F){$_SESSION["pwds"][$Sl][$O][$Kl]=($_COOKIE["adminer_key"]&&is_string($F)?[encrypt_string($F,$_COOKIE["adminer_key"])]:$F);}function
get_password(){$K=get_session("pwds");if(is_array($K))$K=($_COOKIE["adminer_key"]?decrypt_string($K[0],$_COOKIE["adminer_key"]):false);return$K;}function
get_val($I,$k=0,$Cb=null){$Cb=connection($Cb);$J=$Cb->query($I);if(!is_object($J))return
false;$L=$J->fetch_row();return($L?$L[$k]:false);}function
get_vals($I,$d=0){$K=[];$J=connection()->query($I);if(is_object($J)){while($L=$J->fetch_row())$K[]=$L[$d];}return$K;}function
get_key_vals($I,$f=null,$Gj=true){$f=connection($f);$K=[];$J=$f->query($I);if(is_object($J)){while($L=$J->fetch_row()){if($Gj)$K[$L[0]]=$L[1];else$K[]=$L[0];}}return$K;}function
get_rows($I,$f=null,$j="<p class='error'>"){$Cb=connection($f);$K=[];$J=$Cb->query($I);if(is_object($J)){while($L=$J->fetch_assoc())$K[]=$L;}elseif(!$J&&!$f&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$K;}function
unique_array($L,array$v){foreach($v
as$u){if(preg_match("~^(PRIMARY|UNIQUE)$~",$u["type"])&&!$u["partial"]&&!$u["invalid"]){$K=[];foreach($u["columns"]as$w){if(!isset($L[$w]))continue
2;$K[$w]=$L[$w];}return$K;}}}function
where_function($ae,$d,array$k){if($ae=="md5")return
driver()->md5($d,$k)?:$d;return(in_array($ae,driver()->functions)||in_array($ae,driver()->grouping)?apply_sql_function($ae,$d):$d);}function
where(array$Z,array$l=[]){$K=[];foreach((array)$Z["where"]as$w=>$X){$w=bracket_escape($w,true);$d=idf_escape($w);$k=idx($l,$w,[]);$Bd=$k["type"];$jf=$k&&(is_blob($k)||preg_match('~binary~',$Bd));$K[]=$d.($jf&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$Bd=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="pgsql"&&preg_match('~\.~',$Bd)?"::text = ".q($X):(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Bd,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($k,q($X))))))));if(JUSH=="sql"&&preg_match('~char|text~',$Bd)&&preg_match("~[^ -@]~",$X))$K[]="$d = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$K[]=idf_escape($w)." IS NULL";foreach((array)$Z["col"]as$r=>$c){$X=idx($Z["val"],$r);$K[]=where_function(idx($Z["fun"],$r),idf_escape($c),idx($l,$c,[])).($X!==null?" = ".q($X):" IS NULL");}return
implode(" AND ",$K);}function
where_columns(array$l){$K=[];foreach((array)$_GET["null"]as$w)$K[$w]=true;foreach(array_keys((array)$_GET["where"])as$w)$K[bracket_escape($w,true)]=true;foreach((array)$_GET["col"]as$c)$K[$c]=true;return
array_intersect_key($K,$l);}function
where_check($X,array$l=[]){parse_str($X,$hb);return
where($hb,$l);}function
where_link($r,$d,$Y,$oh="="){$lh=($Y!==null?$oh:"IS NULL");return"&where[$r][col]=".url_escape($d).($lh!=first(adminer()->operators())?"&where[$r][op]=".url_escape($lh):"")."&where[$r][val]=".url_escape($Y);}function
convert_fields(array$e,array$l,array$N=[]){$K="";foreach($e
as$w=>$X){if($N&&!in_array(idf_escape($w),$N))continue;$Ba=convert_field($l[$w]);if($Ba)$K
.=", $Ba AS ".idf_escape($w);}return$K;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),[";"=>"%3B",","=>"%2C"]);}function
cookie($B,$Y,$Mf=2592000){header("Set-Cookie: $B=".rawurlencode($Y).($Mf?"; expires=".gmdate("D, d M Y H:i:s",time()+$Mf)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($B=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
php_error_text($j){return(ini_bool("html_errors")?html_entity_decode(strip_tags($j)):$j);}function
php_errors($Za){$gd=[];set_error_handler(function($ed,$j)use(&$gd){$gd[]=preg_replace('~^\w+\([^)]*\)( \[[^]]*])?:\s*~','',php_error_text($j));return
true;},E_WARNING|E_NOTICE);$Za();restore_error_handler();return$gd;}function
get_url($Dl,$Ib){$gd=php_errors(function()use($Dl,$Ib,&$K,&$ue){$http_response_header=null;$K=file_get_contents($Dl,false,$Ib);$ue=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);});return[$K,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($ue,0,''),$A)?$A[1]:''),(array)$ue,($K===false?implode("\n",$gd):''),];}function
json_decode_exact($qf){$qf=str_replace(['\\\\','\\"'],['\\u005c','\\u0022'],$qf);$qf=preg_replace('~"(\\\\u0001[^"]*+")|"[^"]*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$qf);return
json_decode(preg_replace('~"[^"]*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$qf));}function
json_scalar($X){return(is_string($X)&&substr($X,0,1)=="\1"?substr($X,1):$X);}function
json_encode_exact($X,$Kd=0){$qf=str_replace(['\\\\','\\"'],["\2","\3"],json_encode($X,$Kd));$qf=preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001[^"]*+")|"[^"]*+"(*SKIP)(*FAIL)~','$1$2$3',$qf);return
str_replace(["\2","\3"],['\\\\','\\"'],$qf);}function
get_settings($Lb){parse_str($_COOKIE[$Lb],$Hj);return$Hj;}function
get_setting($w,$Lb="adminer_settings",$i=null){return
idx(get_settings($Lb),$w,$i);}function
save_settings(array$Hj,$Lb="adminer_settings"){$Y=http_build_query($Hj+get_settings($Lb));cookie($Lb,$Y);$_COOKIE[$Lb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&session_status()==PHP_SESSION_NONE)session_start();}function
stop_session($Nd=false){$Gl=ini_bool("session.use_cookies");if(!$Gl||$Nd){session_write_close();if($Gl&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Sl,$O,$Kl,$h=null){$Cl=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($Sl=='mssql'||$Sl=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Cl,$A);return"$A[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Sl!="server"||$O!=""?url_escape($Sl)."=".url_escape($O)."&":"")."username=".url_escape($Kl).($h!=""?"&db=".url_escape($h):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($_,$pg=null,$Q=303){if($pg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($_!==null?$_:$_SERVER["REQUEST_URI"]))][]=$pg;}if($_!==null){if($_=="")$_=".";header("Location: $_",true,$Q);exit;}}function
query_redirect($I,$_,$pg,$Li=true,$md=true,$wd=false,$Nk=""){if($md){$Yj=microtime(true);$wd=!connection()->query($I);$Nk=format_time($Yj);}$Tj=($I?adminer()->messageQuery($I,$Nk,$wd):"");if($wd){adminer()->error
.=adminer()->error().$Tj.script("messagesPrint();")."<br>";return
false;}if($Li)redirect($_,$pg.$Tj);return
true;}class
Queries{static$queries=[];static$start=0;}function
remember_query($I){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$I:(preg_match('~;$~',$I)?"DELIMITER ;;\n$I;\nDELIMITER ":$I).";");}function
queries($I){remember_query($I);return
connection()->query($I);}function
apply_queries($I,array$U,$id='Adminer\table'){foreach($U
as$S){if(!queries("$I ".$id($S)))return
false;}return
true;}function
queries_redirect($_,$pg,$Li){$Di=implode("\n",Queries::$queries);$Nk=format_time(Queries::$start);return
query_redirect($Di,$_,$pg,$Li,false,!$Li,$Nk);}function
format_time($Yj){return
lang(2,max(0,microtime(true)-$Yj));}function
relative_uri($Cl=''){return
preg_replace_callback('~^[^?]*~',function($A){return
str_replace(":","%3A",$A[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($Cl?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Lh=""){return
substr(preg_replace("~(?<=[?&])($Lh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($B,$fc=false){$Dd=$_FILES[$B];if(!$Dd)return
null;foreach($Dd
as$w=>$X)$Dd[$w]=(array)$X;$K=[];foreach($Dd["error"]as$w=>$j){if($j)return$j;$m=$Dd["name"][$w];$Vk=$Dd["tmp_name"][$w];$Gb=file_get_contents($fc&&preg_match('~\.gz$~',$m)?"compress.zlib://$Vk":$Vk);if($fc){$Yj=substr($Gb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Yj))$Gb=iconv("utf-16","utf-8",$Gb);elseif($Yj=="\xEF\xBB\xBF")$Gb=substr($Gb,3);}$K[]=[$m,$Gb];}return$K;}function
get_file($w,$fc=false,$lc=""){$Gd=get_files($w,$fc);if(!is_array($Gd))return$Gd;$K='';foreach($Gd
as$Dd){$Gb=$Dd[1];$K
.=$Gb;if($lc)$K
.=(preg_match("($lc\\s*\$)",$Gb)?"":$lc)."\n\n";}return$K;}function
upload_error($j){$hg=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(3).($hg?" ".lang(4,$hg):""):lang(5));}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
utf8_length($X){return
strlen(preg_replace('~[\x80-\xBF]~','',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$A);$Lj=strlen($A[3]);$K=number_format($X,0,".","");$K=preg_replace('~\B(?=(\d{'.(strlen($A[2])?:$Lj).'})*\d{'.$Lj.'}$)~',$A[1],$K);return
strtr($K,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$T,$w){$X=idx($T,$w,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$xa=($w=="Rows"&&(JUSH=="sqlite"||$T["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));$X=in_array($w,["Data_length","Index_length","Data_free"])?adminer()->formatSizeValue($X):format_number($X);return($xa?"~ ":"").$X;}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($S,$xd=false){$K=table_status($S,$xd);return($K?reset($K):["Name"=>$S]);}function
column_foreign_keys($S){$K=[];foreach(adminer()->foreignKeys($S)as$n){foreach($n["source"]as$X)$K[$X][]=$n;}return$K;}function
fields_from_edit(){$K=[];foreach((array)$_POST["field_keys"]as$w=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$w];$_POST["fields"][$X]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$X){$B=bracket_escape($w,true);$K[$B]=["field"=>$B,"full_type"=>"","type"=>"","privileges"=>["insert"=>1,"update"=>1,"where"=>1,"order"=>1],"null"=>true,"auto_increment"=>($B==driver()->primary),];}return$K;}function
dump_headers($He,$Hg=false){$K=adminer()->dumpHeaders($He,$Hg);$Hh=$_POST["output"];if($Hh!="text"||$K=="tar"){$Ab=($Hh!="text"&&$Hh!="file"&&preg_match('~^[0-9a-z]+$~',$Hh)?".$Hh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($He).".$K$Ab");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$K;}function
dump_csv(array$L){$ll=$_POST["format"]=="tsv";foreach($L
as$w=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($ll?'\t':'[,;]|^$').'~',$X))$L[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($ll?"\t":";")),$L)."\r\n";}function
parse_csv($Tb,$yj){$K=[];preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Tb,$Xf);foreach($Xf[0]as$L){preg_match_all("~((?>\"[^\"]*\")+|[^$yj]*)$yj~",$L.$yj,$Yf);$K[]=$Yf[1];}return$K;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?idf_unescape($X):$X);}function
apply_sql_function($p,$d){return($p?($p=="unixepoch"?"DATETIME($d, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$o=@fopen($m,"c+");if(!$o)return;@chmod($m,0660);if(!flock($o,LOCK_EX)){fclose($o);return;}return$o;}function
file_write_unlock($o,$Xb){rewind($o);fwrite($o,$Xb);ftruncate($o,strlen($Xb));file_unlock($o);}function
file_unlock($o){flock($o,LOCK_UN);fclose($o);}function
first(array$Aa){return
reset($Aa);}function
password_file($Ob){$m=get_temp_dir()."/adminer.key";if(!$Ob&&!file_exists($m))return'';$o=file_open_lock($m);if(!$o)return'';$K=stream_get_contents($o);if(!$K){$K=rand_string();file_write_unlock($o,$K);}else
file_unlock($o);return$K;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$z,array$k,$Lk,array$di=[]){if(is_array($X)){$K="";if(array_filter($X,'is_array')==array_values($X)){$wf=[];foreach($X
as$W)$wf+=array_fill_keys(array_keys($W),null);foreach(array_keys($wf)as$tf)$K
.="<th>".h($tf);foreach($X
as$W){$K
.="<tr>";foreach(array_merge($wf,$W)as$Ml)$K
.="<td>".select_value($Ml,$z,$k,$Lk,$di);}}else{foreach($X
as$tf=>$W)$K
.="<tr>".($X!=array_values($X)?"<th>".h($tf):"")."<td>".select_value($W,$z,$k,$Lk,$di);}return"<table>$K</table>";}if(!$z)$z=adminer()->selectLink($X,$k);if($z===null){if(is_mail($X))$z="mailto:$X";if(is_url($X))$z=$X;}$X=driver()->value($X,$k);$K=adminer()->editVal($X,$k);if($K!==null){if(!is_utf8($K))$K="\0";elseif($Lk!=""&&is_shortable($k))$K=shorten_utf8($K,max(0,+$Lk),"",$di);else$K=highlight_matches($K,$di);}return
adminer()->selectVal($K,$z,$k,$X);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(0),[]));}function
is_identity_always(array$k){return$k["auto_increment"]&&(JUSH=="mssql"||$k["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($Tc){$Da='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Dc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$ci="$Da+(\\.$Da+)*@($Dc?\\.)+$Dc";return
is_string($Tc)&&preg_match("(^$ci(,\\s*$ci)*\$)i",$Tc);}function
is_url($R){$Dc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Dc?\\.)+$Dc(:\\d+)?(/\\S*)?(\\?\\S*)?(#\\S*)?\$~iD",$R);}function
is_ipv6($ja){$q='[\da-f]{1,4}';$if='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($q:){7}$q|($q:){6}$if|(($q:)*$q)?::(($q:)*($q|$if))?)$~iD",$ja);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($De){return(strpos($De,":")!==false?"[$De]":$De);}function
server_parts(array$Xh){return["scheme"=>(string)$Xh["scheme"],"host"=>(string)$Xh["host"],"port"=>(string)$Xh["port"],"socket"=>(string)$Xh["socket"],"path"=>(string)$Xh["path"],];}function
parse_server($O){if($O=="")return
server_parts([]);if($O[0]==":"&&!is_ipv6($O)){$Xi=substr($O,1);if(preg_match('~^\d+$~D',$Xi))return
server_parts(["port"=>$Xi]);return(preg_match('~^/[-\w.:/]*$~D',$Xi)?server_parts(["socket"=>$Xi]):null);}$mj="";if(preg_match('~^([-+.\w]+)://~',$O,$A)){$mj=strtolower($A[1]);$O=substr($O,strlen($A[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$O,$A))return(is_ipv6($A[1])?server_parts(["scheme"=>$mj,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4]]):null);if(is_ipv6($O))return
server_parts(["scheme"=>$mj,"host"=>$O]);if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$O,$A))return
server_parts(["scheme"=>$mj,"host"=>$A[1],"port"=>$A[3]]);return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$O,$A)?server_parts(["scheme"=>$mj,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4]]):null);}function
count_rows($S,array$Z,$kf,array$q){$I=" FROM ".table($S).($Z?" WHERE ".implode(" AND ",$Z):"");return($kf&&(JUSH=="sql"||count($q)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$q).")$I":"SELECT COUNT(*)".($kf?" FROM (SELECT 1$I GROUP BY ".implode(", ",$q).") x":$I));}function
slow_query($I){$Mj=driver()->slowQuery($I,adminer()->queryTimeout());ob_flush();flush();return@get_key_vals(($Mj?:$I),null,false);}function
get_token(){$Ii=rand(1,1e6);return($Ii^$_SESSION["token"]).":$Ii";}function
verify_token(){list($Wk,$Ii)=explode(":",$_POST["token"]);return($Ii^$_SESSION["token"])==$Wk&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],["","same-origin"]);}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($R,$sc=""){$ta=array_flip(str_split(compress_alphabet()));$x=strlen($R);$Ol=($x?13*($x-1)/2-$ta[$R[0]]:0);$Sa="";$Xi=0;$Yi=0;for($r=1;$r<$x;$r+=2){$Xi=($Xi<<13)+$ta[$R[$r]]*93+$ta[$R[$r+1]];$Yi+=13;while($Yi>=8&&$Ol>=8){$Yi-=8;$Ol-=8;$Sa
.=chr($Xi>>$Yi);$Xi&=(1<<$Yi)-1;}}if($Sa=="")return"";if($sc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,['dictionary'=>$sc]),$Sa,ZLIB_FINISH);return($sc==""&&function_exists('gzinflate')?gzinflate($Sa):inflate($Sa,$sc));}function
inflate($Sa,$sc=""){$Jf=[3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258];$Kf=[0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0];$wc=[1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577];$yc=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13];$K=$sc;$G=0;do{$Id=inflate_bits($Sa,$G,1);$V=inflate_bits($Sa,$G,2);if(!$V){$G=($G+7)&~7;$x=inflate_bits($Sa,$G,16);$G+=16;$K
.=substr($Sa,$G>>3,$x);$G+=$x<<3;}else{if($V==1){$Sf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$zc=array_fill(0,30,5);}else{$Rf=inflate_bits($Sa,$G,5)+257;$xc=inflate_bits($Sa,$G,5)+1;$D=[16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15];$wg=array_fill(0,19,0);$vg=inflate_bits($Sa,$G,4)+4;for($r=0;$r<$vg;$r++)$wg[$D[$r]]=inflate_bits($Sa,$G,3);$xg=inflate_table($wg);$Lf=[];while(count($Lf)<$Rf+$xc){$nk=inflate_symbol($Sa,$G,$xg);if($nk==16)$Lf=array_merge($Lf,array_fill(0,inflate_bits($Sa,$G,2)+3,end($Lf)));elseif($nk==17)$Lf=array_merge($Lf,array_fill(0,inflate_bits($Sa,$G,3)+3,0));elseif($nk==18)$Lf=array_merge($Lf,array_fill(0,inflate_bits($Sa,$G,7)+11,0));else$Lf[]=$nk;}$Sf=array_slice($Lf,0,$Rf);$zc=array_slice($Lf,$Rf);}$Tf=inflate_table($Sf);$Ac=inflate_table($zc);while(($nk=inflate_symbol($Sa,$G,$Tf))!=256){if($nk<256)$K
.=chr($nk);else{$x=$Jf[$nk-257]+inflate_bits($Sa,$G,$Kf[$nk-257]);$_c=inflate_symbol($Sa,$G,$Ac);$gh=strlen($K)-$wc[$_c]-inflate_bits($Sa,$G,$yc[$_c]);for($r=0;$r<$x;$r++)$K
.=$K[$gh+$r];}}}}while(!$Id);return($sc==""?$K:substr($K,strlen($sc)));}function
inflate_bits($Sa,&$G,$Nb){$K=0;for($r=0;$r<$Nb;$r++){$K+=((ord($Sa[$G>>3])>>($G&7))&1)<<$r;$G++;}return$K;}function
inflate_table(array$Lf){$S=[];$rb=0;for($Ta=1;$Ta<=max($Lf);$Ta++){foreach($Lf
as$nk=>$x){if($x==$Ta){$S[$Ta][$rb]=$nk;$rb++;}}$rb<<=1;}return$S;}function
inflate_symbol($Sa,&$G,array$S){$rb=0;$Ta=0;do{$rb=($rb<<1)+inflate_bits($Sa,$G,1);$Ta++;}while(!isset($S[$Ta][$rb]));return$S[$Ta][$rb];}function
script($Qj,$al="\n"){return"<script".nonce().">$Qj</script>$al";}function
script_src($Dl,$ic=false){return"<script src='".h($Dl)."'".nonce().($ic?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($jd,$me,$ya=null){$_a=[];foreach(array_slice(func_get_args(),2)as$X)$_a[]=json_encode($X,JSON_UNESCAPED_UNICODE);return" data-on$jd='".str_replace(['&','<',"'"],['&amp;','&lt;','&#039;'],"$me(".implode(", ",$_a).")")."'";}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($R){return
str_replace(['&','<','"',"'","\0"],['&amp;','&lt;','&quot;','&#039;','&#0;'],$R);}function
nl_br($R){return
str_replace("\n","<br>",$R);}function
checkbox($B,$Y,$jb,$xf="",$b="",$pb="",$zf=""){$K="<input type='checkbox' name='$B' value='".h($Y)."'".($jb?" checked":"").($xf==""&&$pb?" class='$pb'":"").($zf?" aria-labelledby='$zf'":"").$b.">";return($xf!=""?"<label".($pb?" class='$pb'":"").">$K".h($xf)."</label>":$K);}function
optionlist($th,$vj=null,$Hl=false){$K="";foreach($th
as$tf=>$W){$uh=[$tf=>$W];if(is_array($W)){$K
.='<optgroup label="'.h($tf).'">';$uh=$W;}foreach($uh
as$w=>$X)$K
.='<option'.($Hl||is_string($w)?' value="'.h($w).'"':'').($vj!==null&&($Hl||is_string($w)?(string)$w:$X)===$vj?' selected':'').'>'.h($X);if(is_array($W))$K
.='</optgroup>';}return$K;}function
group_system(array$Pg,$lj=false){$K=[];$ok=[];foreach($Pg
as$B){if($lj?driver()->isSystem(DB,$B):driver()->isSystem($B))$ok[]=$B;else$K[]=$B;}if($ok)$K[lang(8,'')]=$ok;return$K;}function
html_select($B,array$th,$Y="",$b="",$zf=""){static$xf=0;$yf="";if(!$zf&&substr($th[""],0,1)=="("){$xf++;$zf="label-$xf";$yf="<option value='' id='$zf'>".h($th[""]);unset($th[""]);}return"<select name='".h($B)."'".($zf?" aria-labelledby='$zf'":"")."$b>".$yf.optionlist($th,$Y)."</select>";}function
html_radios($B,array$th,$Y="",$yj=""){$K="";foreach($th
as$w=>$X)$K
.="<label><input type='radio' name='".h($B)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>$yj";return$K;}function
confirm($pg=""){return
on('click','confirmClick',$pg?:lang(9));}function
print_fieldset($s,$If,$Wl=false){echo"<fieldset><legend>","<a href='#fieldset-$s' class='toggle'>$If</a>","</legend>","<div id='fieldset-$s'".($Wl?"":" class='hidden'").">\n";}function
bold($Ua,$pb=""){return($Ua?" class='active $pb'":($pb?" class='$pb'":""));}function
js_escape($R){return
str_replace("<","\\x3C",addcslashes($R,"\r\n'\\"));}function
js_escape_re($R){return
addcslashes(preg_quote($R,"/"),"\r\n");}function
pagination_href($E){return
remove_from_uri("page|next").($E?"&page=$E".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($E,$Ub){return" ".($E==$Ub?($E?"<b>".($E+1)."</b>":$E+1):'<a href="'.h(pagination_href($E)).'">'.($E+1)."</a>");}function
hidden_fields(array$_i,array$Le=[],$si=''){$K=false;foreach($_i
as$w=>$X){if(!in_array($w,$Le)){if(is_array($X))hidden_fields($X,[],$w);else{$K=true;echo
input_hidden(($si?$si."[$w]":$w),$X);}}}return$K;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$Bl){$Bl=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($Bl?on('submit','uploadProgress',ME."upload=$Bl",SESSION_NAME."=$Bl"):"");}function
file_input($b,$Xi=""){$bg="max_file_uploads";$cg=ini_get($bg);$hg="upload_max_filesize";$ig=ini_bytes($hg);$oi=ini_bytes("post_max_size");if($oi&&$oi<$ig){$hg="post_max_size";$ig=$oi;}$jg=ini_get($hg);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$cg,lang(10,"$bg = $cg"),$ig,lang(10,"$hg = $jg")).">$Xi":lang(11));}function
enum_input($V,$b,array$k,$Y,$Wc=""){preg_match_all("~".driver()->enumLength."~",$k["length"],$Xf);$si=($k["type"]=="enum"?"val-":"");$jb=(is_array($Y)?in_array("null",$Y):$Y===null);$K=($k["null"]&&$si?"<label><input type='$V'$b value='null'".($jb?" checked":"")."><i>$Wc</i></label>":"");foreach($Xf[0]as$X){$X=stripcslashes(idf_unescape($X));$jb=(is_array($Y)?in_array($si.$X,$Y):$Y===$X);$K
.=" <label><input type='$V'$b value='".h($si.$X)."'".($jb?' checked':'').'>'.h(adminer()->editVal($X,$k)).'</label>';}return$K;}function
input(array$k,$Y,$p,$Ja=false,$zl=false){$B=h(bracket_escape($k["field"]));echo"<td class='function'>";$dd=driver()->enumLength($k);if($dd){$k["type"]="enum";$k["length"]=$dd;}$th=($k["type"]=="enum"||$k["type"]=="set");if(is_array($Y)&&!$p&&!$th)$p="json";$qf=($p=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($qf&&$Y!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($Y)||!$_POST["save"])){$Kd=JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;$Y=(is_array($Y)?json_encode($Y,$Kd):json_encode_exact(json_decode_exact($Y),$Kd));}$Wi=($zl&&is_identity_always($k));if($Wi&&!$_POST["save"])$p=null;$be=(isset($_GET["select"])||$Wi?["orig"=>lang(12)]:[])+adminer()->editFunctions($k);$b=" name='fields[$B]".($th?"[]":"")."'".($Ja?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$S=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($be[""])."<td>".adminer()->editInput($S,$k,$b,$Y);else{$oe=(in_array($p,$be)||isset($be[$p]));$Jd=0;foreach($be
as$w=>$X){if($w===""||!$X)break;$Jd++;}echo(count($be)>1?"<select name='function[$B]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($be,$p===null||$oe?$p:"")."</select>":h(reset($be)))."<td".($Jd&&count($be)>1?on('input','skipOriginal',$Jd):"").">";$Ze=adminer()->editInput($S,$k,$b,$Y);if($Ze!="")echo$Ze;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$b,$k,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($qf)echo"<textarea$b cols='50' rows='12' class='jush-json'>\n".h($Y).'</textarea>';elseif(($Kk=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$Y)){if($Kk&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$M=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$M'";}echo"<textarea$b>\n".h($Y).'</textarea>';}else{$pl=driver()->types();$nl=$pl[$k["type"]];$Aa=preg_match('~\[]~',$k["full_type"]);if($Aa)$kg=0;elseif(preg_match('~date|time|year~',$k["type"])){$ri=($k["length"]==""&&JUSH=="pgsql"?6:$k["length"]);$Ud=(preg_match('~time~',$k["type"])&&preg_match('~^[1-9]\d*$~',$ri)?$ri+1:0);$kg=($nl?$nl+$Ud:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$A))$kg=(preg_match("~binary~",$k["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$k["unsigned"]?1:0);else$kg=($nl?$nl+($k["unsigned"]?0:1):0);echo"<input".((!$oe||$p==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!$Aa?" type='number'":"")." value='".h($Y)."'".($kg?" data-maxlength='$kg'":"").(preg_match('~char|binary~',$k["type"])&&$kg>20?" size='".($kg>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($S,$k,$Y),(count($be)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$t=bracket_escape($k["field"]);$p=idx($_POST["function"],$t);if($p=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($p=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$Dd=get_file("fields-$t");if(!is_string($Dd))return
false;return(JUSH=="sql"?"_binary".q($Dd):driver()->quoteBinary($Dd));}$Y=idx($_POST["fields"],$t);if($Y===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($k["auto_increment"]&&$Y=="")return
null;if($k["type"]=="set")$Y=implode(",",(array)$Y);if($p=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($k,$Y,$p);}function
jump_input(){$_f=['select'=>lang(13),'insert'=>lang(14),'search'=>(support("table")?lang(15):''),'schema'=>lang(16),'database'=>lang(17),];$K="<span id='jump'><input type='search' form='jump-form' name='filter' value='".h($_GET["filter"])."'"." placeholder='".lang(18)."' aria-label='".lang(18)."' autocomplete='off' autocapitalize='off'"." role='combobox' aria-expanded='false' aria-autocomplete='list' aria-controls='jump-list'".on('input','jumpInput').on('keydown','jumpKeydown');foreach($_f
as$w=>$X)$K
.=($X!=""?" data-$w='".h($X)."'":"");return"$K><input type='submit' form='jump-form' value='".lang(18)."' class='hidden'></span> ";}function
search_tables($I){$_GET["where"][0]["val"]=$I;$xj="<ul>\n";foreach(table_status('',true)as$S=>$T){$B=adminer()->tableName($T);if(isset($T["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($S,$_POST["tables"]))){$J=connection()->query("SELECT".limit("1 FROM ".table($S)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($S),[],$T)),1));if(!$J||$J->fetch_row()){$xi="<a href='".h(ME."select=".url_escape($S)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$B</a>";echo"$xj<li>".($J?$xi:"<p class='error'>$xi: ".adminer()->error())."\n";$xj="";}}}echo($xj?"<p class='message'>".lang(19):"</ul>")."\n";}function
on_help($Kk,$Kj=0){return
on('mouseover','helpMouseover',$Kk,$Kj).on('mouseout','helpMouseout');}function
on_help_value($Si="",$Vi=""){return
on('mouseover','helpValueMouseover',$Si,$Vi).on('mouseout','helpMouseout');}function
edit_form($S,array$l,$L,$zl,$j='',$I='',$Nk=''){$uk=adminer()->tableName(table_status1($S,true));page_header(($zl?lang(20):lang(21)),$j,["select"=>[$S,$uk]],$uk);adminer()->editRowPrint($S,$l,$L,$zl,$I,$Nk);if($L===false){echo"<p class='error'>".lang(22)."\n";return;}echo"<form method='post' enctype='multipart/form-data' id='form'>\n";$Rc=false;$cm=($zl&&!isset($_GET["select"])?where_columns($l):[]);$Jb=(count($cm)!=count($l));if(!$Jb)$cm=[];if(!$l)echo"<p class='error'>".lang(23)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ja=!$_POST;foreach($l
as$B=>$k){echo"<tr".($cm[$B]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($B));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$Ui))$i=$Ui[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$Y=($L!==null?($k["type"]=="set"&&is_array($L[$B])?implode(",",$L[$B]):(is_bool($L[$B])?+$L[$B]:$L[$B])):(!$zl&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$k);if(($zl&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($Y,'',$k,null);else{$Rc=true;$p=($_POST["save"]?idx($_POST["function"],bracket_escape($B),""):($zl&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$zl&&$Y==$k["default"]&&($k["default_sql"]||preg_match('~^[\w.]+\(~',$Y)))$p="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$p=(in_array("now",adminer()->editFunctions($k))?"now":"current_timestamp");}if(preg_match("~date~",$k["type"])&&preg_match('~^CURRENT_DATE~i',$Y)){$Y="";$p=(in_array("now",adminer()->editFunctions($k))?"now":"current_date");}if($k["type"]=="uuid"&&$Y=="uuid()"){$Y="";$p="uuid";}if($Ja!==false)$Ja=($k["auto_increment"]||$p=="now"||$p=="uuid"?null:true);input($k,$Y,$p,$Ja,$zl);if($Ja)$Ja=false;}}if(driver()->primary!=""&&!fields($S))echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(["null"=>isset($_GET["select"])]))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Rc){echo"<input type='submit' value='".lang(24)."'>\n";if(!isset($_GET["select"])&&$Jb){$tc=($cm&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($zl?lang(25):lang(26))."' title='Ctrl+Shift+Enter'$tc".($zl?on('click','ajaxForm',lang(27)):"").">\n";}}echo($zl?"<input type='submit' name='delete' value='".lang(28)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(["check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]]);echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($ci,$x){return
str_repeat("$ci{0,65535}",$x/65535)."$ci{0,".($x%65535)."}";}function
shorten_utf8($R,$x=80,$ik="",array$di=[]){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$x).")($)?)u",$R,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$x).")($)?)",$R,$A);$x=strlen(isset($A[2])?$A[1]:preg_replace('~\n[^\n]*\z~',"\n",$A[1]));return
highlight_matches($R,$di,$x).$ik.(isset($A[2])?"":"<i>…</i>");}function
highlight_matches($R,array$di,$x=null){if($x===null)$x=strlen($R);$K="";$G=0;if($di&&@preg_match_all("((?|".implode("|",$di)."))su",$R,$Xf,PREG_OFFSET_CAPTURE)){foreach($Xf[0]as$A){list($Kk,$Yj)=$A;if($Kk!=""&&$Yj<$x){$Yc=min($Yj+strlen($Kk),$x);$K
.=h(substr($R,$G,$Yj-$G))."<mark>".h(substr($R,$Yj,$Yc-$Yj))."</mark>";$G=$Yc;}}}return$K.h(substr($R,$G,$x-$G));}function
icon($Ge,$B,$Fe,$Qk,$b=""){return"<button ".($B?"type='submit' name='$B'":"draggable='true' tabindex='-1'")." title='".h($Qk)."' class='icon icon-$Ge".($B?"":" jsonly")."'$b><span>$Fe</span></button>";}function
copy_icon(){$Mb=lang(29);return"<a href='' class='jsonly icon-copy' title='$Mb'><span>$Mb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(')c(GViDWPFloQuDeeQUO^DgIF+y?4O<cX<jDcJA*[iuQ/YeF96jOuG0x[iu&n>{/Lt(peel""twnUSV4BjfRQHIaBSj#}4}^YKK<$BQMFP!+g0;Oaei]~TuoHWgJWsnlo=Q<zp
uKaG7hs1<!+Oi==O^}->/9Ea]~3synPV<Bm<c_L,Wm<:hI3>D]aslZ$SmG>H4Jg&]_G3uM_JP:Uxi;FAxIn_^J<D92mCE[jOq`wQHqqX[abvndl6
BuYT?K{gCgwCMO.[Bh>G.8O8j4cIh5,JaCXgWkoIQ!Xe%"4xCJ:#SsY.cQ)wF)[oAV@<X0jd2+]KMoD[>B4-v8{,,5LHUM34%n.ryR|:FR{0:aICtMI@=)$E^(E*:+o^~E%/JJVY<6`t<36)7mKyhI2:4Q%)Fo6^*oXn"r:NoSCF7=8WtpI91EqD&q]%mr`%zJG0Us@Lmi0lzD?Jm_.!%96.
05-6$9gJ5ax@d2@W#%4dOJ)dKrIhH<fII!d{G[[I[ajMju&sY5,h-_prPbj1%#EWXF1Gq.K+lD:sy4U_,<r&5^8e0eNI+ZclG?lfYe9UG!7:$;LSenK}0zA}Hr"r6Hk&7{RDK%2"Lqg8d<iC84Z1HzadoTQIv^Nm637v.T[>ekSylqlA2,Sd0UOW[GOnj]?I(rr3D?<fp#f77<J++3-&%V.~ZnmNJH[y]`-^0uG@G4iId}ogQQE
<?w%giV}f@vlXb
MvCbX8f:=d3Ax]qKEp-m^PAQ60knO9)+,(K`(k_*pRZtYK2oEe>T<T19
8/KE0QRqMQ2Zi{y|s_*Z*3nf*SKkX~pV^ZNE&eF+E=*;AA"3>99[b}$CN/W3C*?dXP.!6E:7&
8DN`6p1]rtfE4<_
f(P[Ux2ry0nf<PY]XTU^Ll=8vSj8kmWn;8oo8dfJYdbKvrr6d%L0F6vmh%?=RDtJ>od.Kf_;-K&lem4.[s6-U8?_qZ!30>e
#,<5F60=.DmmVj*!6Y,vLqPc2443S*-I32v==ZWdo4dR<xcq=.Tq,7iqik(&DOhm=Qoqt*5cDK;$y{"Bm
kF?)=ch!$5TIt8QN<9$a#R+(O1$s#aZNShtE-(.7_F``i:"}kxp]Yx6^OM^;e0M+??x79M7Y<Lg41Q@!!`Lo[[?s0%`hIXW7A?*rG&c)dt_!.k<P
!LH.#D0r[5j!nBvJFZ/
zca<%!yWK?IJCS-HJ[HNqf_!.pUz%_:@h&2bU2SnD&W"o>tH
SFubJ3(pod1+"-L.aeP6<C%N^VY:W1T:9YL&[C!X#~VLBaTF!aM@x"Jn8001@ofj_y.fRnEo1ooO_No)K-@D.)JN_QZ_@-sT5;N|.j%#r(hxf|D;JD6gJr<CqQY;E{2U=~eId)KBcs(+yT0AG0eX3;AyoGZwhlhi#X4G&W=<dBZR&)ef4xhwP:[9Fil*UQth<COFaql94jP34`J&C8]sY{C"^rVW_HOH.);{<5fuu&Y}TX?f(q#UcrQ!)3<7mnY=?5U@i!/D$K`n_rXT%vKD<dPBL6rr(n;?JKZH`1wlat.z
Y03*&^jP<h[>f)AYOQRdzD,NHD"(deXs0vAr*f>SWjb?2>=-2oqYE4&Zhc3i&9YW7i2xqjc%VA1,";Capf7<gW?OGIeLR`3<nPzQ(8=JL5w!xe.V/cY`mrrc}A6Fgcr&xo?&xe`6ljlw@SE_5wYVUy.&NfuXTfGCwJTe@Y/WAb8Z8/3Lx-=NNR)Y=o25@MDn8aS&"jKgv#2p_hBN3hAr%<Q`JWRR[$_C5-!!^4U@%kl=[#P9K,FIEY%EOt<5uRdQAZ]t%R<si6<O?wWEAEBIp($eb.HAC_>3a@yshOWmJQqipBLsG8~>`UTgfGN:mhdI<PLDzGUDLGlNV0hA`jKjhEpV1&bq7.!K)Nu?(9y%@Pe[
<(Gh8*RG8+k&wG+veu
^WerIBNNs76r#jb)_E+X*32W:U:C;LH1zcPJ?1YOKvWWz`bQH,/[+eF@mS/dvD}s5b~g<z$9=-2OaA%7
y[sakM[_h"r[F4`P(1cLxT*bQR6=%kJ`*6oM!OKX#!EX]QZI#Kt
s!o}vHn(C(^&ORC_h<IV`obwa|3p=F3(_bm~w4^:5xY!/k1GpX/vn=^W-{%
GaD:^K2-"5_"3b/ySXCP:|s
@FrGD*3U@=$Q=;lsPAH1tBP2oA70;4NeQOC+3@Cv"NM>.[hG"*F]PB8(/J0T81l2>*+XJ#qy,39V:%BQ=
w~7~!`O]bR>:?hLBCh>_5%S!.PS(0Y77oUkv00%mYYWbD-gEukB
RUv&iYZguO9%y@NcD[VnR>bAlY%F(4%K^U)3`-URp3[{no*.3,<S=4#c/`?v5~H,FHL|uAbdq.cwM,_=s[
:ct);`0B"xvqM?V_S+kl!2KQPpL<2Xp`&3pFXaV=RII(Z4GM<mY5-/2:SFUU*oUj%wF4@UK@i3_.OFgmNY0d~0Dv[<]0`q024;@%m=sg!/VP,b<G[o<;+n8@fGew.<gZ<Z#1OPEc{Y5i(I;<BAT9>gT`63MUwH(659gsG.dnuC1TU`/76G0rhbHodRo4,t>kG%,XA*1;neXg1w7))*bsl5*BMEGaaX)`3">,Y"?b><33K7gb-_n`)rn_qITk:GFFhgflkRq9qIr!@,/rOYBQ8i"Pv.b4]ASemkfhi>d:9_ihN:?x})m%9QgIA(BmAu1N8F#3H8R$U!GcERwKc4,v#C#Cg`nY&.ONKBXuolK]c]"l$#m-Et7N}B^@N6JcH1)Eb+3I-A$aP<Mxx1uU`[b#cH[1uxN8H4$mcifjxAXWI)8E[x:,Z+!HJ(K=jIDmZH_k4awgVS;["dZkEvG:G7QwF_egMN@DK1J,@f4dO3`Tpr09uH2M"ve.U8za~1dm]`[>XLzTf)k0KZmx27V
n^;m[an?.biQyvKlUxbAVa0FdXieVZ?9E2H$fuY6ma-s3b[_"326ck{nvuX_hMrf{Lsw
ZI5%v:M^S<rpc0,uI%e]F0clkXr)T>>E2!B$FKc$4`3PuwV5odughhv,7xlv]Z_P2$!6fmyihSd-K4J|u$;8Ejxc%JNF/&q_UUwdv,Y|dI3fbC_8buxQ8s3Vvvxo=AM:cpc<iJxcvdY`cRq$58E)]<jJZBOuO6__dG/&>c)AmSPkJmDf=DwEf_D@hiH~%_l8cPeCK+sRI]i2g*,Yh!B$tX2ia1/(iTek3P3^_8PSoH7q@2*.T0_xu)&}>CD55)EJ=.rXXzx0A}n<i70"Bh$uS(*{$8e5$x/Z1#lDnYR3C%a.lA9>?Y@B?8a,otCSGLjw,SnwbDy+&Auy58#|.fsEMOP-Erox.K_!:8:bU~(MGM;62!]a$j>j1>;^!7*/PY?Pc0<@W5>io>e-^0n{)Uq)h`gL%L=RM)IdGx"l5B*:ZFt]@&3Lo>`:=OiZ<w6}B:R"LFwB0[8jgwK<G}tmu"o&V%0~`[B`08yu8{wz6eR#b>e5f;u.s2KlMX8mgwn@iHocv]l=vygi]>/z8]ATOe>+",*hrjX.u8Q[B(=n]DC|6lEN8c9|n%J,[kJ~b?-sDJ]a#HL[VX%H)?O(FX=h`[Z+@Gqr$NG-*K=iO"p@!:,Ys<fl/&M,7e)9043fy,?*wg-(`<4aok0>nu>wL+=16U-_D?c(y2YX
;AfVIMj18WoK!a0HJR4cvQzYXijD@,oCeg5]9q9$kJi(Hhe-qO|-bvAu=uECp"M^g/jN1E7DvtyvYf@emMaUd$b/DhVnuhxi$xS?LL3-Z=}_|B``Yq+5qcGW;B%!7$S^=T8H3Y>OHC~qi1O+^ou6:EZWR<*HdExTUXCER(OG(l.AuO0p$3;Mv,fKzSL.)aL!d_RFLd+.$qprx#hZKS!^Eo%k_o
M;Cg/~F:
$.hx,o_DVla!~
~L!d|;6QUrukX!3_&*DOX7sc*ImaFKk,_+J$U_
@4vupHxO][ERVMahT6sm,,WN*$3ymI^f.**1Ne:Y5V;2m5ki&Lc1l[K&v_f1HA#:F.H3DJSqeh^=D0G@Ew:m9pW&>XdMwfp.yAm_l,+JT[;FJ[WyVi3u^S_v>s3!qg&eXk?Ka`c%^"*xC(6a*T^P7WTrc=e`uFkiw7A}fZ%=kqHz<+Lgi5stD%N8bphObsozc>y%/vAD1Hh=TlG%:E?rFoLw.[+DX2Jei"NvDAVH/QuzSXmCO)M&>"jztw8.y5hK:-_5]xq{rwnU[<&#aK8-j^P<0*]*7Fl>2!rH_WVG,c0KBxFiY[ha0b/y!b#d9;W3:,mL+d"j8_gdV>0GLHtDZy[-,M^3+Tm(+}g2R_Z(3xlhxA!ir|:U^O`UP?+wkn#z"U=M1"=3-3aQPDC>6k;mN+XC4`h6tK8hCJ+}0-u%`!*}V1RR#076"hR[(0x&;NtCJ^J<vQ+dV[J
W2`YynG1#O==l~)t&w@.*X28+-TV9C(8gh4Ie}kF"Ghs[5V8xg0#.!^Y3zE27qK5H"nO`_k8Y[<krL#]vvkC!$BFXZW0[e"Ha)+5dA:{Xw=J"!bJKWtx;D9|>,eo*u$UDkDEwd--DmJ(`5W<Qo-Q*0C]"P$H9JrsJIWKCO3s1;<aV_x;m6RwsZ=%$Cqub|p609udYkZ5!]DAGFK"_p*&1>X?G%jujw?_w/
g4vbfR*hZL-p1mZS=X~vKJ$=ho/_J
j*"V.eX*/B3Q)s,JU?Snr$r0y.}/=`a).n(;}Q~WY?/v`2AX^mm<QZt*g=.3N1m,.7=GB%DgeSx-QL)fpBX5cH9E;?9Vil^UrPT;qMkMxk:PtGe"<`1g{:LV9cKXLcmxE&=C)QnqgxX^i3Y-42-_/emXUHhkzdOr?!A@]Axrzvy23in;k>b1]3s5NxF=,7}U3xml=)yUu&OM(Bfxd');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('#OsbOb3V?!K0U*,j#-4P2Bz+35Y(O]|GIGFDsyf"RV_CWG&(<hG7poe1|](vZ=6gKb*-e*bfu`XNvNZUFTxP<kqOjr%%43Ge]*BE&fsS#$XGQs=jy?$Jqw)[=$pZs!#`BwhTS`Hx]c/
gWB0SBBXi)^FyF(MQLF,

CntAU.ADCkT^/<4hx@Sg^
Q:{DP1JO0$3OTgwkiNq>)k;xr&Qh*bS(@Bze[?PjZu!*h&UmI7<lxWc9)SaQvxR#;%$<pQAd+6,HWq@]vu3^lY1Y-Ns9y3L5H!=QP`I(rW5Hmx2spr4^XPbOa]@XKH,y^@Lq?=.-z7}qzeIF=B7yM-[7f*y?wDLK<WkFEA"t,#vW6>r<#Kf"]LY,kcXWxcX43
Ass`W9ARbvKD)]-@s[:$!rwYav_$?oR*(xrD_R;EA_gOpnVg]m(S7@|g#&qK8`wv8>Ve+uJAe<BCTZ=x251_N$KrVP!Q@9e&?&z={f4CNVgErN_i?URd=Xht|Qr-%P%uCSWYu<D86`]A3;1;d$x"Gm))J17Q
ZpWmu{G[:S:^$855HJcKZKE;S1-dlyr~:<jt
vN|yIqN`"?)$N>~l|SM3|;g7*iHMqkDXK,u=-Hto:2;IM[pN!44bS5#B},0<$3}je*,c@B7eav8x71@N{#iT(Z;#k)kUdlPj<1JMoyDm5@QZU5N:f5nPG96r~P]]M=:)mv=gMBhrpK&S2geLF@mf&uke2lkXlf8RiG~[JJkKov&yU$W
^-NPh_0Dd/,;3Lh5Xqqi+oH&|*hdQ%wk_U3(QJAG!:|Lxc<M4!tXBPGbzHyeN$$4U![h!BOH|/uZL0S@0?`M=qbV;Bk,}@3+n@Eq"FY;GKX`tx."s9E7uY,[6Rh;.v
(<8kBLH"Ke-/4^wv
dI2k`b#;@i+L}<oMML3kc
T*|N]?c(A]J!zj7Z6v?+[v"xR^qVr&HXOk_>=2=n10?w,o5J{Y;tk.Fnt^[!s$xmrAHN.V+S6?wVou8,Nt:OJ3#T6');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string(')`KbOh%Z51pK,ikTs?)"H;V,IIr:rC[t5,_F@ncm`@yoj3"_:"vA_VBRR$RI?[f_[w-[fNe2X4^yyl=Tq
x8x"wGoLxJJW6("@rvP__?ee$FXD
,"hNFt[01(c0@O1M
7cgR,9];2Vqh$;omxg<Z1+I`g40]
N"NMpph0J>.4woWEa9x,H~KyMz)^$~&=7y&,)3Zi/:#LuN9`cwOcNF<?p+mWOvqk^)lzHt%*ysfUs!_&74]IKI[=7l6yD%Ojl.PWvS,{1$@RW%HFpi8pXIotu
9vqP3l
9@k@b"CyS)^]UvEFq)f"lY^5p7"nPgWQ|pw)Me6V7oIeN2NFbY<NqbStJb7Y/
.1tWaHzr=-GDr&>Go;tQ.BK^T*:x^0PT1y1DsH*Ok=QJ*/~dv*qfQbO(X(XUDZ&"5,P2;yGI)3n2<9R_CsTh0T!%bPMYYTuaTDM0`+(IC/M>Ypv60-@gK7Nie
gFAOe]e__m8_~"l^9hki:Kc(#BF92R-4~W#A5_>QMyFx"hZln3g`T;WJ(ArvB=^V5?XpDYVi6mv_S=LnA8NT2=F=2o&v{X+mYJXnTc`STxNxcIF`+p1uuxaXiV;pE7Z7}5y.LFYknDJs1Vv7h_RQvkg#>M+odggKSiS2@[g/)6IBFW^%bi^PMv/l<MSO+1#T@&^*61^B"
7K2vVu=I.w#DxX|7Q%TUVc,CdL7;*;$(Xi?QbS2vl%+,e+Y,<goAui+:-iO-h/H;?1O/W%!AwZ7*&_y@U9:1otdYJ)5B};Kg&kZpbtxOm*ubdOP/awC!L)Kd<NJ[@d&rgb+(ojc^?S};RQvMZQ8qPeTUnE}3hA4WaX$Fb6[w7#j.|M^<&a8`(p2mPhBA?brn6`gKMc[:v=CJ]3aP`;")-8Xo^yiGyc]PGjFD`N~69p$di
LlR!SStMMcS>5&VM#cXL]FCfd
bNz/a,:Zr6([8BbJu1fjKHrj9Ea(;?qaiOr1^>nWp5)8e,{xGYpdr^%qs)
cLthctKR;6(sk5h$xbP)C]
@.:.J&$k;#GpyjqU?N1s>)=]GMKF-&+TQOhc,NAfX9N:&&2d)+CccK,KG`ptmluSbEVs+AQB#W1-c&*Ut/2m8#YSB]!#39x9TEM6RY
vqDY7Xw0wvfo9Y.nT9GwVR
%+UVSjIw$&PoJ7+5IYF:Qr+m2+:M`c<a6`AB!wpXBU8_5!^$-*6%i8:6$>)>mu4N9!Px
`Ga|m:>qTYNg9(WiXNAFl7lzYn97$]p{K,@*$WyQ,"#G=biZ[_g`wxNI`ACZm#
0!/$.$kG0pg`vU"y>KXxvwLDXT+&bTDeBWg5>j~]G,z@_AcBsWM"HW,=7rnR0TrYs4_HJCaye0FVbbAZS$
h6U"E-MAI
_GR1Olah,&WSed$fUtSWa%Osh@0)C8
iScU(]c?C<mdfbb9>+{8m:|Tz7ydRYzhX%5Q]XaMm(ypvytUeJ-n1K}yd#n(qJ]y^K{sd6km2=7eBLmPGeiK<al;LuBn}^X#Ip+IIlrV-Fn=jYG*sd$RvO]:[qQLeX~"i2E,,_mlwgPXkK*0EKkccF,P!bsOE"`XDEKGgATQ?9B1g9bJc
KyME)+LwPWk]SED>4oTJ6<%WlaYdOu2;I#aX#XStlD)=@>>USXpe7oCF/Tk@?6Oel0guR/k>]Q70<Y#45&5LE<>3f]#&U0?f!`/Xw%4
jDUJpWS>28V5T=M*~C9B?,Z(_&98ini6R$;@cSF"a)}sN2&c/Dfjb9GwNih<6!lhl-:[rF.cPo4&nP29{AI$m+r4J_B3HS#cQS{J=uehbK=b|.e;f3@6_IAGrGy+G:_pXFo`^_=WoPY>AOEW~/h8F/y]bQ&dc0HND-|UVbKjWeKq?dh+Z+(hh6Liiivq4H*i~Oob;sp"&`Reqy6%(eZIDv1ymiLP
vTh@ortB=K&}0ithe#BmfgXq(WjkX
G)*fd2f_fh@Pr=3PDhsda1iLIXM&ww16Q"G6gZ;YLjs>j?,.c~J8vma;6P)koE-)y]5rmv_+qsYk[Inzada/2JtMyc>fKIc2Kqy<Jl2VS?JoG&
!cpVIMP[~c^Gm1Fe>j6DN_Wdg$KGR*>5Z+_O?UXc_e)lmnL0t6}S~(A]qDR"t%eAXlM3>,zfJ0vZpp$_dZ,,zt..wTFU~!r"s8522AgB+>YDDI4T]lnolcFU;Lo]^;Sgp4Z(c$0oW=ZOP1PNvY1^?4q;l-ovUs<,1Y^8D%Rc)5dB!&9[.EeM^1mT1)Bg1/_jv<RllYu^]hurh)VlF>>ttiu!_&B2R-#(h%D#]Sw@g28+^i|"YB!_cBEa,fz)Ckf@&`ukf+#^x1MBv31,4d7D#yCOdVLZeq{<]2w*.+$m0r#3)wd)#<KJqOvs_73
<,?Pf]r2S]OK5;/-DX<u#
Uvc,ZX&DnNq(t2,F>#0H2ak.^"Rr=:&r~(4yuU_OIHq#YaS*=?c[dBn+
vpX3=v1I!Lg%k)tyCMRvJI;dxvmcC-M0/tJQkmlu!S>joCKgbh9v/iY@S
rB
R9p".6dPEe/KZsh8dC/C?S]5d2aR#d+`mrV1JYyx]6<vtS>lkU]j#t
j(vHx:=@HVX
/Fo@E#qrOe.ZD^"+$]DC#_&FHjMqN]yr$Tl"49j5e;A5A.V0P|ZiNbS>gW-`&PPr4H=Q8I&//7z"6sxM%_NYct,ofp*P#Lmi#j;__-wIqO=na1rrw~8q!5=m,0q$#M0,,X[W%/_HR#k6JTao6`wf@(
)z(UsmzkY_@LwUW$c:OQm9BorP1]/_y1,%yj>@RU*F}bN>e==
T)}S%^43Ilb73?E`1hGL`cWhPqH6I.GZ*CvI`/N0/i`)H9A#]05.F]A9Zxy[W"zTj#U8fI5P5UGUzSGNIN?.V
Rt537;Q-b=Lv>/f*$HQw;"DtT"pl7>.FpT}j%JVv1Cz-4<<hL#1o/)Mj3i;,qogAC=qUD
qn;Hnx(Pn^.nDht&*M&k^QM.rT
]aGhkf"`3)A8Yu2L*VF[R(nhV>=A.&giZ1rC<;l8v*Q=fn,IRr83MGO,-Rl<ThcvR;QSk(J--DWLFoyg-M#n?Z+kpoG<MsC^.s,(,Y=Zhew%(Aep^L4*39Cg3q<:2(>(k7O#
l%5*swkC<83^I6JVG
~wWJK5&x|d>3HnC"A^:wKRXbH;kWg/O6t6G4>sW:
vLL!W#D6_#CyQe+;$$JaCk<"0#RWu?mJ2elz9{U%lCxXpoM[q_lSsOwr<(yJsXlDN`nZ1%lIFW,9g%<"lv_if&xG`qdAcd.df<iI)H#~04XHr>NZ/Hdx!U1c"%y!RPXl1[3,btUAxpn3jyxiM3W,wJW7lE72G}8ZuZs7K)w)4k(%hXvN"RN9/Ts%hQ<~1t#O<>SpcG%Zd-ZRj"gFN:T:U=)W8i8omD0riCX7)>BJSsRV13%NT~,YYI"p$Vm@:3.>HiPv?C(]6#HCAky71lRN34kcb1ts3eCzEDY1=Ij*kDEU()rU/:WVj1WW821:g+h<G5cui=tO_~[XQ#nzRttfvJ55=wIflTW"6#&z)zCj(VCqR"VpNnR!b@Y_-<.Rr<M#NzSCx)"K8IA#+*Y6l},YeY_WB[[5Q5riJ4Ib*+Vhj(TI0,G$.xx*:t)*fbqsL0xyG`,]UlBPhm8;nZ;qpFz)t_tWw$M~H0feaG%961uRdKm.gPrPLT-
BIqbnzD<SPvMy{M,UJy~+|d!m"y<Yb
H.p%T^I==y
=@cSDmTo`NYgVi4FC[_j]v[C9kmgi`46.IiP8hQ^DH-mh?!]h=X"yUW=Mm`@5v*;IR(Lw3cse&l%Go=$tn0
n5Rxr>Sm#:[|;Sx>F$W
P4r9&RjKP2>6P~
7&Ni7^%aWmuamqU>]y7t[NBI*xZg,i#_v.J<$#gj3;"4/A0RAq5[#7+f%&H:(ww0;!
u(BS`7jmqF0ft99g=`&*?t)BZR`e+ru-f@"<c.o,QH<UDe0xbptroB92=sl";.yMrNJ(Y<WYf?Ns*FL|x6%?j`p~x~q*%"K{;"uTA",b,8>ah-`UiT1,E$iRiuDGQeqL)#;WVe%wlh!8fIH&Dz&Yh/$zG}_($Dv)nAJ@v/cu$"_;u/i~gKNhEf.x?:x3)q7)tgnP=2)xcBs"Dd)W2ycImsMuJ->$AUd6Sr)Z]MkL>>F*G[HK*=]"i{$tV0N->a`N9`jBMbVmhDhYNZ(?b^G9#7:~1
n#T~BMH^lJN^9rmJbMJaa!G}W~K$1zP<]cyHiUC22K[_I.G&X
K"v5Ha``!gqR_d=^s6Cup6rE?Fc~#">Boj^zcoh7,Tx%wq-B/m@(I=bu&Ia=O>e.Kye:CyoTwuv25$CE(wB;]]pemQnJ:rv{u9XVlsDmqxG_Uz]912RZn060Z3gEDyo
/oFfHjt-!vr8KwQ.2H
EIcG~i[y,)d:2R
XyWo<]2
W8tShKa"Y&<)c>yx1hu7C~@UQ#WC)u;dDl:/o*kGv|^A+_ecc|iK*<KTF,2$1bQ.D~b63bdRkE2Vh?I)>_-[Qo:fEcu$W=ugI9ZL"`C7H[uK!"pQ>3D5A/T}]te0F#f:_ry7M{.$`2:2b,lYEV+Tjk[[UXlY/8<rlPC{3+9&:t[mDiY=@H#A(AR>`_,([{xF8{=]R"IaP]!$AaX_*=I;3fL4Gp!=>.h_^y#*f!/"h9BlmE_)rXPf9NN3PL:L+mc/YH
M.8Z[d3;I@daqQRjzlQC3%TFVDe_IS|E"91nmaM"p8vsovQfyiQyB:G]_E:t[=p.>LR6T,
$6TPZS_h&>%Ar-J;C}[1&R)VsK-NbSHbcIA`QKBV1V<xR,c$bM-))wY[mEUc1:]tYW_~Lc.{9_f-;5A_l]fI-/WBy}4jnRa&Q-O_WnSQFD7,4!C*VNIu/d+W7%d/?l.]$3Xe!;W_7[E:-P";TG^%>ynRbUT5MIjsij@N$hv:
mGLo_.z
d$Wu;lZ2`P<7VgZ.HO-jF]()YTW9_N<*sPStyD{j#ffW-leZvZixA0a#4`If878O6]@&YSDK}x*pX1iO>g4ITAJ6$Oh<%XvmuSUdR=705F@T++eDa@ADKe$*93g.zkH`!a(GTQ`qD*6q~TO#8N!F|b=hhj/56CCObQG1H%Ew^v]>1D~#OE/7:UWX0Me2
_(jpZFk)V)Ge%ic7%
!)U0O/1"HS(_WWwzyCyE@shKq?!#M6`;
V2UajI-"rthK%/C-)OPo59BL3N#a}8ACJQT`V%f%C7@7|-%O[n+"ol7FZQKX3UE$)(O5B4eLy`{[+M+*82Q6?WBQxQ&epu(a{L7QYijjppkv1!FyUZ1.,A
/P+t7..)glu>R>[QQF3*`k
G>7Y5c%$?gvFedsFKrvE(O)OIXz_cdMfO(W=wjyCi
y[J^@&Pk.89C.^K[XORHia!0m.+o]XyRvfH%|$M/*srk/1lwyi|AaM$oe3#efkb2fcx9;q#n;])1nrm4?Kw@qY
n5(tnE=MUK%,B6)Wd}<XnDmT,6-GEX,TdqR%ON%4bov2jOz)mh81Vz4,s/)jG4^/Up
kc$UNEf]gqH6Qu~`s6!%VLsotnndz`-sbR?X^$,9S6[FDWIvelBQOVPsVaDEC&Phmoec4cJ5hkjqV.wI3-9/=sdE,;<YmW|#/<n*]g8PT+l$Wys[ryp97<{9kPG=m8V#&.NSOy4Aia17pXwa88bNcZ.G3[Np$,N;0w:/:+]*vUxW8al^jOr9+"
C}kZ6[kTa5s,I.2*&;hZG?p)p@*3z#dNDKUdSL%sb#:vS+aK/0mbv"]7.cn~`>lU+P/J93kn^&+@*5U(uJ@E#/<B/:g]G[YG*r_a7vwXB_gC5JrBasu44c/fJ+k1jC1BMH6K@=J2>)ERXd1Cf2nb;Y&-N;_KuQ?kRlmfFgfp`0RI<u(pW3eC-Qo>p5:fCo?Za~.U7[!4jg:f#XO`cY/:PGr/&_2eQ@xl)Cy5t*J&OP<2Vz
LIPD4$u@umM#X%msUlze32_+4mJ<{JN2,%-F)s%/
.{M]H-+~IZ2Yx;1"OJvOG:rVVMc
lz2+]vVAR*M}7eKJCi.lDn-#taG:oA[n;|fR101Yt-OYn8v=KuU5Uc]tj(J~9]l-V(&S1;D~=)XEcZQdkN>Q.gTe2GQ.U%)G=qW<G6!E`KIiU{;b["FF
b83)(qqju<`+vvaW/n;fb7p$w/MedWb?:6MY?20QefZXW*U?t5*(lr+wo45DPaayGX/[RiN6@w$=WoV7$HU)L-)g(n8I5*<N@o`eN*FjPGco*o<dvMfEKiJkrxU!J5VBR1hni,fjikn9}1^uhN^-N$mrwLO_-)LDJ-^Y._Y2BmurMGhPVh3]^ALQr(I6ym(`^Rjn$AOq4U|3`
X5G[1=~!cy"bMM$K@gD69jYZ89X3XbRRn4eSh,vxtw6*jR.o7<`@K7P=<iOWdK*In=9ZT.uI(5}M-$;km.*A(RbjRCc
7TM
N!b(A.uY2Bqip?;w8]ue/8
J!`kF
SYsUbIG^cu35F]%J!0S]!gtXv@vMGj
_=e"hQj:59QZ{xVWQ!~8Mn0/)N5/z8JqqnYqKUfA-p9
xUdX~W80=kE:Gh{=SQX_L(UOU/*Ko@*TOo0_!/!4pHzFqKuM*729fIk/B:7*TVsDTFOoBt7Fm^&Ox9ib4K.[U)0$|+gb-[M9Phgk,;eHZ4(^Htr@%m4r!nlWGw%A/5,6y29W*Qm)1dU)iF<_xTOZhI~3Y`2W{,^Y1$JZ"`nU0Q!!9?Nf*
f(jte7z`B%v71&Cx$0au@jPk5`%Lh*HFG,5]H(4.#@DVmmYO8RPd8-GS7fV)g39y3<(bc
`9))[DMEs_j&;=x@pyy7qnBDwSWBtJh<yoFVP,^m>ooq4,2JG3un9/SUS%5&12,pughmTDL.9YS(G`?nW&gO?t!lG(G<Z"pC;,:XW)biV"9!T7fFzU+OFYPVJ&/r5+*1D&2"U6d<d:s]aAv9oDCMJ2n>1TPWkw,^:^ldq!6UcOc?M!oR`.u%c]F$"u^s5NqEsKHV{p+9iX7_bR`*0"slbB.i>P
]INGG#qr6BK_FvCR+,w7;Q3~qQHP@3&wu!mmk2KE-q<##
xfJN^N_[`5m.RX&&,aQS)"D)L

mE9(T>.!~N:8>jo;r!N$h@%JveqX<r;;Hs<.m:)Ei<B<0aUfa
H-tmKRB)00VYp$y]zTHLW3(72O3;htH^4L^s/0z?fo@cwfd0;%_NyfbGCaPqbiVt269fPTWJbb)>HfxmWQ5F%KOYUY~JgEmMClo%U45kDpT#$BjFMT1ya1?H,;6NVTo)e0l4-8UJ+M5MQ(]dnU5cqKzu`OAU=nr2DK]0Wb7wkJN$:Y+vJ8,h&y:hyuQ8!T6M&9}1P5F+4_$LabuFd`+3@Gd$f]O#]O>_QDl[LVq4V-B^sEOs6;3n/8_R-;c#:waY;fw_NIx3{gy8wC!%Hm61Lj01k"RdK%w`?Jgm[RqYQm%49N%%
CA5#E<C]&`WbsNdH(dc]FmL[pK=^tq0AgvR?g*wSmD)$A3`P64JQ`kWrlr]N(cX~V>g#e#8g_DyZ4-ZV>f({3N)OjX!9jb!1whhj]F)7TT:4PVE^=BO&,<k^tIY2DFWjZjW;aP7k`n8W[9OTELeFI-@Sk$u:csqW?(vAQdvlLex=)`o;YssPN~@id/m|S?F0WvaZ/6@.0T&J?>X[^6Z9&!f?)LGb">.!=dqX5-)$T2Gm%~mffvdAyw/)NgQ]f/[w(L8V3-oF$J>;[YZn2(FD9oM$i[gQ-HS|p0ldH0`]vRVgUGt,)[Q0YpF<Y-c>(Q]]?gKgy01qxyj-aC*V;0fQ5~N))3poqN1
&@Tp?]Q>wBz#9~4_f%=>Lb.n8j:WO.j4:j2tJpN5mTUWS;:yk`/M(&.B:!U.nHA?-?Px8T2VVSGOTU4WPPi{p)AB/irBXn,Pu*"=U:t]T2M4(S+pobo%+l:]Sm
TZ5fCg-p/`!ftgHcA^MePfw)p_:i>?lU|?z0*>U=A[f9B[Rg`gYpVu{4%M&)Jt-j&Lue|W^%-@btFM6%(^VM)*3ayO[!{y5-#]|PMW-.8,/qDB`K~h*ek&hK;%?&KEn
5cVonyoiZaSMl6.=D0[B4cFXZ)jw0[Py@nOZ%.QYw
c=`K!Y&g}M`^JxP54!oon`>tyHaHGDS3@8Rx0q=%)EVR^w?@dt#2r=NA0r"p9Z<np8K;eZ.A)8X4~(M9.0Q^MH#Fuo->Hck#n;g+Kco:zUy)8Y(R!Al_%-CjK&Sn9VYv+?=!U`iuoH<gX=/C_!r2Vtv:_5^dS@BX"v)`5QJ/Z_39T>_A8+L`6+sDuv$;s$J%goJ>p)$"Ay@XHf0yvYXhlhb::i[-jqv9@M;Me:-2<;r9g#/YAr_%!h!dS
J
UdvCom3#Yn.i,vC/v<9p5G^lviKFcD`:m;ir8t/8ghk6KkClcA
3~Ef2=LCa9VqE@hRk*ejM(SoVTgT8VLLV1QQteGkDDXVE1<IB8dOhwU[JonSjl=e`#`MUcHt
CNa,331q84/`fG(S0&94VF-YVpspfQ46@cNmO^JR9I5abJM5YLMYzDf^Ixt[Jtm>e<A4_@F;$CHRC!l[mM!&~fW5.a
;yf>l%+sZND*M*?@d!/HVy8|FaRk#MT$e1u|w;?]D^$$t1MUjhUcMoaS=>s=[lUgl{RHlHkP!+v[N%M9]*UgE1mkB"H:=ZE94^f0&IR&?6Z`[xR)a39,MnA,2X4_G2kst:K7xnIb)alL&d6iQzJ{n:5ht;gl%lxM*l!dB:oXm`hCESUBb7_x`_42:]5!*dRx_y7|47n%XhFe:[88<H.!UjX<Dx>1E|GNYoj/(rA`FLK0GG=2aB_fU`EB&7_5t744t`mn6[fn`DVHPW:9m4u?X5k[5{t}qCIR1=Heah
`GN[oTM)UmnJdUuL49`F:dC*CF-B1b4Ta@00cT4DP!.>Yf(rELUw:w&x"lwiVyzu>s*
GKNZ3kQ,E@m>Yp<$0waa%yv?%J"k>Nu%y`f"epVvv#dbC*x5iTYP4$LFAC)qdtOX!W@(Mb?y%o*-#VK;sp^gpabmU]mBjt)G"+8G;g+ua,R],!HPnm<=%Jj.8Hy1p@xV2in")@&VUUvN-aX&N/KRN*m+PnQY{".Q3ub5[I$k<n8Z3`5a6clQNSK::+.l=yP1$ZrpD..]4$4#:cBDclJ
*=sEwX@Z~EU(*uNGhWGE*QT`USb.:d@3$jh[x-nrm#6]rg1n&X.XFc=9I*!g?Twcib/`4!57R2X[U3c*M!%=zvI6($hba@SrVQ;_vxA]O3G2*aXm:h3@I?,oN#CW7VGrlBzY&=Zs)TlJ}%0ws/8v}>bE*X!.LOli-FV2m4`${erjZ6lTXuh=<6j.Zy1baDXr8[N)E$
]iV;WM,<;9!EfnSi(di&:V$HIW_YZ`/6;hgM?S;bc#S;S[xWG}+-_*C]%t<~++k#t83urZ)m.Gmc-yw<1O^>#_*f)]aWX&<~8Gaz8V.AN+uSTis:sD(C7cC<On>:=}-tc2W$-;R$UBqHi$BSUV;D%B1#E3;;FDrEBf$.LVLA*QO`GBpp:gVk!}iZA:"j7BU1g@W=94c_&<r|uFalNWtrwe@+1-NiZ9$[+>&Wd!O[J&GSB@d(#rIc1+nu(@fZA9H^Arb6F|D/MuOx(o4<uT+@S#JMQV2RcQ.?Jc$Y1{@@_hLy%;]((zLu^vU}
i/X.prqYan{P5H&/8PV8%Y^[y#PS!#`Pvhy#(33T@,dVs[o(z]w/<JR4R2nY*EQcZ"Tm
UTZ
!X3%KMcpYJdydNJ*=j9hp&eH`m_?`t*4u+(>Hx":3yAby/??[s"=1k1,0)srNCkuGpO(b)e9Ew*k`3(`y*]R1ZhL[b
&R$pK$uP*z!z"W^P.Y4[.h.ehdhDppcf6wMEGgOnv3G/)s+&^HMI>:DucS++5Tk-5?7@Q?8uXWmwd=iL]@6P1UvbW_0!JhI(],ekPl-Zj;+)#B=CJ
C:{<RCU4:.ZLo1O3+d{#e`CQ[=jG{SM(oUu<3NCgb]<PMRi+:S?r.qe6+J#*Z>sPz62S]!T@_k"1`T;##@_3BMlQ+K#`Fy1MbQlR~ar``R*<Uy1nDAt0-sZI6H~qRDtF=dpQ_O;4Prcd0iU^"fU&JiR5<5$x}<a%)atw^pnw[0h:s<zH_d/Z]^"<Ak9>LI(BzCp8*!dF$Z6pR@G_eW3H2rr1
4(+KykywSK,aCd<x[$Ng/o8`gzn3,Gf;1P&%i@"=PF@NTDd37=vl=?QPq-)aNx!ni,S9Lc
z.}:j.o)X_[ox5DU5RzNt-!arq1?zXy?PPpW6?/W*,J!TYnf;dX]sXHsktonxC]inw,%4hB%jB@!}+A_m>I-UkJlw/~[E2#;h28h6"[3jo]8F*<(1B1vzbSfLq(tTEOOvL"qP.oJ>m(-JTidL]P(#TxC;%F=vbB1Fq}b<Z"l?_3Ehi<Q|y`EYKM&JdwS6fsjQBM0K2*P=A7bs=SsqPDkDiaT`#L&s/V]-7,a|2MHK
6VMs~$73!t3>x`UIfQcR,*m8M7~67+pN]fI&d_(eNx*O$!2[v]e0k6k*KDw+kj)P4#XsLGZ(NbeZc"TABFYAAWttjNSY;Z%Z/SVAd-0W`szQyk,ErgR!{>#^
UJ_JU~1G`p@h,[+02.+wLba/YXXStkVLf/KT76;q%UVX^J:^JX?m-wPylRU+QMTZmYA/$0uj&(S[U84_X=G(Lt=YY{ionXikhgW]D]fP6S!9^:#F=to&)0T`P$c;INZ0O3)@O?5dkOQ&jGc>2wfElU6RuIqoUVno5A+x84vNi2onF5x75{EPVtCtNWfBU@/NCgKju^0@<n((uQUB`0RQ0jV;V?S&Zr;J<&yx:bwP9Or_&E;Q@`u<sV`s7a:B&l-J-KXaUUv:*<mP5%1(7;6Q[Eo?,;c%!mw/a)*7D~QtaWXY4mg"h*_]fCk)L*BvLE]&57nO&`cJ"LTMul9Sk@5>VmQ
<-7zWRY~/$5q`%x=mD95ab;@0O;IY+y_]s)kf:v!%[a!vZ
0gurMH1Y_3}@YB"y.GJm@C4Cv(+0!E5*OL"+U$9+ke
BPUxP*E@<bUwaw2%ML$}Wzqxsl)JxS!7
7F-MyI#__cL*.ZYf_a%aQ)RYzY0YUyK8f?Nc=Hu/+E}KOE;#tR`k__AN]ci/+(4Q3d:TQop
pZvtyePMR#$khpr6kyzb[00mP27i?ENyZ4TO%l5m];Z^qdj6`,nZKNOY}*<ET!KxkNmv|`*mVwIL{`H3;;R2^Vav.e121=t(}yn86l8K
2|%H6-h~#T53,(DvFG_v!oUzimFC:d8Fy4d8GW$`].!Y?U-/%fe5)N:J?bS~Y)--[[qBZ^_4=sA^#Tsuh$LoP}P_k^IV
qll/`7<#SU"?c&p2).v]I&OmX9kY3Yl:|fWg"db5UEwR0@-WxqQ+Vq/NE1]Qqs,*duSD$OrZYJlwk=oZ#b#vbOn-OW+Fr+766(^CSXqAJ`q2Ub1_*aS?+Dzc.b}^<@mkrd!t[am2lE!QX@c`TC=X/3v.c^9l?JL"rKSWXEcADwKNgG,8uFY=8xjb9w!bbW$T#wPx1l;w8qkxPBq^AiRHVnDm-9+(z`_&Hd>yuwo!(DY(_:`jtDl^A"?E4IW7l!LKy5ck1@so_tEbtv4V7%x-GM|TWGf["S]Y{f"5w?V-:Qp
a;ZFPi1_8;H6d#}R+ue@hBeD"j#,um$ihp%]zx])+gpuG:|?[W^`ZPVB3ecqu;JKx^svjMHF;ix/@T(rJXJZUmTvxaJR1+Sf`s%N%0-H9mn5=ejYyRfw1l-Jai/m&fg[x_ghl8-()eY2bAE`r"<fa8dS+$lL2dpH&oy&)$<Bd24=O;j1`oRs!s9^p`c%(L~E[+0e9M}tW0nna<_&jEA[I!}`^@4,c&]P$2{FhX$46loh
^S=0^s?Oip2UMGSC`iKZ*cMLJ{>m@3P}U=AKEbF-y/ONGMJ?)Wg]/-#%t*)X0HHi:X&i,gyXPu/g:v-NI^#YFRFr$sTxPqMsNPo0e;
3Ppe_`g7A[^YqrH)jD$7?,ZNhph7U,(8?^,84XppH"v4KX0mh>o+bn)6}w4;Se3L<>zB{w6v
>tvB/|%L)XOuA+ZyJXP5>3OGQ^Cp#y#{aAUrS(!"v4CScC<-oZjK`rZMv~nAGVOO$ql-j8d(gQ
h9F)Ks<_ri21EVyrcaBWqmV*GklZaVfN;?YiVE@Unc,Ae)KA^`iuWLtKE1_<@oK;YTM1?YP3G
S$UqmdfFc&$5
W7nuX[d%$`[py<aF7
4^r)p7Ux=*SY)>-(EN+|D0LgV=u>[aLZifXf!OG-K?G,,a"Fy;q|RG!L]42HbK[;4R2:T87beViZa<T4r+"W!dsPmI-#MQasmLM)>8AzBN`18Fcpiwp@6[J,b
!s%wF"B&]U"U>NyWILqaFG1N"|g>)04Gu^>"n4QL;]C%_3KJ<bFzUJ*8fg=.]_4-15s;=,d-aPN`rU5VSs[YT{j;?~*prp+cE>;;a}]uP"]/1%KJ(N`[^lEDQ%Hm5FWan;0,tp[R`QLwA,]*o1B!!0LZ2de-Q`5!Z./Qebp-mPA(t)D$lmxkxVAA[>B},!y7+^&Yz%#Y"11p=i+MU?vj)q>v
jwrh2.&XAk%jBHam"1RRoQ!HTfXQn.HjEGW+Pk;m^"Pm4nun}QR4?Aey-<BqQD`F`k|h&Z,"Hyw&D9[`njW/aNby&*V<!I9;5qDaw_*$D2EI(XeH|1Zu5+n^/cLD.y|g(H-pXc|EOg;d-EvWS5&(GOh+=$p_qN7^f-2>hue]%JNZ}Pp&wA%JNGn
hcGFxervl%8OK@Gf=u`:#a3J+W/i|ksRWk-(}_&;Csrn{F+Wu=p;/qL>h4@>}:XQZ7mVp0sxw0Jg*O)NuR
Tx+H"bdSR<s{CTtz_i
:K
Jxc!+5jm*]+%2ExN`&GnI(fZ;5"A;-Et6;gQL.dEZ{rF57(k9!b^<MDp,hn}L3BP^RHAK
9xH3aixs<CtqLB7wt[&Kiab"u|IsY`*23"sJO&XhbwnVv?3l-]mzLuUJ5vbbOB_CHJnI9W+CahsMtc#*e7P4D>OEj23YJg!M)UyEfOAi.2"6
ECf48D(W"(eM4KMXk7&&6#"b|O[1N2t3/5OI6Gt4t>Y<:c2L16FUHsgZr/t1NB"y&-zIn
d"&f}p7VGVIJeG|rn@|/=
it[F#AGF#`aueAYkce@7Jt#:&voMLs
QB/Ekc]4A)*2MXvO,YK6UrG4J9A9M_.2TS"3?g8f`i2)U/_m"S`eMu+R
Q`x`W/)iA,|2p4dBiHOd1a;yvAEByiK5ZiJl=o0,z]AG?:~F!50V?_Qe_+N0z&P-PZlX{=<+)a:UU8FSv(unsc4M]k(,Ja]%3P0+[2
/oQOVC%WNzK^e|BmL/Y6Bq]iI_rhqlQS@6tAlzXEIxIC]19fN*llbkM0kVGr[?2=v5
aSj^$Qf]1f$vO4MJt]<de=ZEiNS#0$>%uSD[lO*9MQAN)-G>33_VZ?M;A`pp)gz04$})!9rg;##dwImTD
Q--k[bb*UKwD88iUFFe)wFAPg:=lf]6uyN)N)j@31QPiub2*uY:nQ+@S8M5x)2I>Cw!R6G=ELBXE:=IWv1J*9m7__]"BCcAK;C"cxnrhc+qf![l/@5I*z"a?I/zSwbR">$q4K0gtj3v.nL>,}Cl1mSj"Qr8C0a"=pnx)F(}4xT9(7+x?.#:);EE1:W5PUH!=l,NNcd8@4F7.|p~&^FI!^bwmb;.&hPH]"R;1c-qOj=hdHcFRXGj4aIixw6UTl`cez,5Y&n]`zgIc!wB%gBh>V">muR}je$KLpI$Rgc;%!b`o8CrwH-k3hEBy&ZDtL+?OvR
(>D<R5.pP.8=Ec3<.*33yiw;qq6ZKnHbWBnpoq&g(:f5yU
/c-wY4*/GM#aQf+6M/JfY7Mpo-(%<4@!9X5+K>*sbRZM(_Ca@fb[=vz6L^:Hbmlk5eo>y;3reY`Wr?;HqPBE9[Jwkdnnm<hq3NP6
U^"&=cyG2)>oelr9(rh713i%E7Wr`JuO+U7~ikZR4dengyZyH?5yu|%
.EJ#g.^a//8c/Ixmk]!i;Ca&wI4t6Rt$_^Fh6%22dOfo7%m{sU%+nm+"C5Q`24+Va@XDA;/A/7hg[Q7qPYAMD(eDGbvq5wy@lckgc1nG1sHggCqhYJ1eT*YIx@.cIr5=sB%tq[,*fahvcF"#4
_D.7`n_!jZ)WPp(?&4_]W1s%9Xs[Vw0lj}QI,T1CHDdp/ZJ;q3g.<@+Ic+kvuDD)KV`^)mNm&2b?S9+FP0p0VhdfyXc7-SFo[uQ]<xA,,E(Y/QP%+
#<$]%i,s%dNK*yqdFo=0ukj]J?iy1`d%M%dExHYjTrg!(F&m"A)n*jcr]Uwz!mCZ18-~lK?I#Ihd!ee.,W[@.gC8o=d,c!HL*+2J#DMj*OD_QZlFjE,4=kitY@+iTp2BnD/L#]+db,snFc+32H81xb
DSc1/L!-Y3L;;q+ueSsT9"{eFwjc_H0&XiW<`.|@XCJ/bnZm61:FS3u_wDsI5,}B^<~g=$
55#kt|k&dcy7-]p=rbt;T(*w0#=ToclAROiu=02ur#Cs>,9G*)<3X@ne6a:B.:dPvG"U%5j!!`>zTm.4(
@NT7HYB)a^bc,EyELTO7ht62.TfY%VXMTGt2i]N><A(dt`e~:``CvJEykg_V@<Tpq]*x*]kv@J9`XAxPa<"[IV#yqvPSC?]*Uk3"!-gg:s3`^7q~n(?8cIhV7Vm^&m^5$4RY_!q.Zc0wh|2&n5,hJUH2VMZx.k&qw#X
i6pH)y^^74$@H2GaoGx7$qN
vCs@_w
3mSNOHt6Dqs[Q$LLu=gT5G)v$WfW%;tx<p3UNJoZ[qn^xEh88:|b4L;RN1KD[b$S3:?:mtBz)u4l;pl
0pD
eX+yiW"Amid!sflH$rE)[8bm^)5bq#zbzs/HhV6)9sZJ}X#MPH}D6)fhX[WGRnb/Mp6anh6;!aL!an0w)QCrxK+:gT$89Y,#$K$N!N#k*/G3uL%a;o}T>!{OrV2SwpIf0HP4I8(v+S]nFFVx1s
E*Ky5rDls)BmJehN_V4v]3/*^u&vNu4}J$rmlEJDY+PC%v<==Ui<EqX0XHFvSNXe*G@Ui^Ue*+r/]DfWn|?)
aTQ)ZwRLUl*.2,bv@C!$Lq#5F4*=u1$9`-t=>r6bKDndzb
>
f-3X$p:x-nlvF<Dpck
6Sp&`c>82?e0joy`s[R[ERRA
xI]*OvdvWw(<!9mr0ANY"4R75M^Sk=bPHz9-4,6Q1PM_b"BYkjSY,@Vr%Fe,:Km/RdIU+~%eP2D]CWEU4},>?bIX#cBbqpo(N^ls0nG3fo=~"=@UXBS-sU/WQT>%-VwV=8R6O^l)1(HG<_,M=OGUN&T8<5?!/9s&/j"a*IfH$uND87p].uU2^uaD2?8_)upKVkK1p/#iJZn&XM>0--/oD>Rsa]N";AXd6|0~%x5l0jSYy!k$R$MZx"k<;/K5v_V[+fsrBj"SD-)w?vMULnwp>39i>[`B:>vTY-O/;|5gmS#;J%*y!&2[2IA+0G
yVB@@fiRxd{NhJp$rY#/Pwsq!Ey2mJx%8K}k8h{(ceI#.#:/)tqm"tmMc:X][ZkJC3}(RvE)M0g)Ffu0x,@=&*I=d6Zi#tb
E7gpe:*F@ImcPxR<W7:hAiCJB!4j2.K2XIS/J42"K(Q^X(;A59_+VsBO[U%iJQj8%!>#jOfWwe%I(F62G[;XI<z1vNMjRtbR#?Uj~%Nm,oy?P-$pG_C!1GwY@di:G`J*]BB3DG[#:4GH8mP4NT6pv
*?O)_3MSj<fpT`-@=*/rAjTk]KGYQ@8dSb#*tgf@o->^hJnWdXd;WINr/=%AXQ2iQ!G80g!#dW/PB&eFYhz3<w?"X")SlpLFmM|PXXEdTJSBV5.RS3@(F13qC[{Y-TwNCCJ*ve8#y7^d(`w<T-Gqw+j$H$/N59"4hQO.A(8v-wF=)@X%1o0qNK@CEK%#GuDYTkMd#%dG
P:J?"Hv!F;+_OQBKna2Dt:!j0(H"agR2@J?b6b@6H?<fXsDvxUk>jdHh&QW4W!PzXz9}d|_:eOds(7-$LrkNmBP0Sw6`g#c
`WBS5<8e]LpsH|&ov/SON,uCD*?yu+F}B.2uX^
a8d8p
2PyVu3njBmWe3/AnS#b#Wq
AQ]}D[788``SgAZ1CCR*Vvw+v{#(m>Dz,1pK@,dmO_u6`:*D-Im9E?nHCJNL8W#J<PB,y1D
ACOB0KRC-#j4uag)f7dB5cMO)cv)E<Y#=]p5P6OU<`ijQ=-$3kAHfw15<VL_At`dA;YEWh^_)47OV3<Dcy)[DVQ(ts
mm!c:`ljQF&SOd^J7f|:h/A0R=vfi?<!FWKZAWe9z([.Uc!<R#"jS@3rcmO-0F{(G6P"HYx+.I[`wcra{F,9u^=Pr*]p"N)py&bLaBMKUfLIu`{Y[/2^;v~O4$@I}&lm|GEjqEVAdCdoaHXhBL#FD1:syoe&_ol]k=q#A:tkU#N5u`|WyUT."ZNG_+cD)7/q}Kd*WQYf1
}gALpGE$gX0v*76JNf&"d&(sZz"<F56jI&giLyBrrZQ3{jTPkPMyrtkBpSaerUBR#htG][B..H$^CYnLpt8GATP0P;@Y{$-h**js.*):1"AC!Pdj"=:kFpgcO1N!2vw)F_TX;3Nl~(z^LRxtx6UeX25`VoesFXO!96~T9Qu5vhm=rizA(DYwe`=&Y&B!iZ^v|07y?8Y;;joq{@CXCyO3
e)#lJ9]0AT]*I~4Y:FOKc>(p9b]!18daq33e7-rk(f5JD~WL<xgR]6b=d$&.');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('"hc]htEDS1ptW"s7HFw/o#;i"6|L8/20B;DWpCQf0JhAw$z#{U<*K-4:RMPM^tCYr1S3hT^G|n|KJU0x9@&roB,].yX?_iFnBROur?+Kjh<4YMzotml(~UNTwsM)|jI+=MrxbKSI);FTq4Fjp)~ffAZf"HNj.&@nUc~Y21e+/&:k?F)d:wrGA8ONMK:H16[ojx~=XykKM0qKA"YoC"pEz_1t3MRUqF&Jcx_yrtBd#]^`tH|Se,QP0uQ$.Z$v<;U3*S-7+u:>I[FTT###=_o/GJWFBHORO.a/]5]nhD*MNej.gC%$.Mtg-7IPdl}$|eE+ufn5<]5N%;%yJv>VpIq>l0ZD&Z%*q(B$1nbPe=cMNU:BW&:]#y?+Ga,w$-5=KA?R*Q6w>^,"&rXMZ4tR)q^]"(:uRi,b`W|LJfgMG%CLZ.9-rTht[^D5)H{Q2wz58pWbMiNUBc2?MTi,dl~dqhzTh_,t;gcp=n6!`9;i]/,Fj9UA[N<5[Hr52WsZ76
FW;/?@K-tRGfew+q5z+*5+tOP|%R^Iytame&ckVvl7X+$Bb`Mn1{Ad5.uvItoF9O2gW|H#n#"QgZFne*C1Hfjs8QYqoacSsC(L&nBA"i+p9
;|-#kAqGg8H&PL./`NBpx{2ocV-tXxIWD/dZ$mRiFGUQ%e:5Ue5-qb@<"[EZMJ0LfF6`.FYsn>!S[AN<nQl[LI-19)gviXZJ8GL^V9`TT79Mt3qoHNF#!>E8lp?9ayfd?iN3!fxII%:=;g<94M&T0
T8BY>AX4j?*N
#EG5um
"$HZ3q>xB?HM"G"cNrCWA"yFb@+3Fwxsc**@W-h5A?97bM#eaifBV.oS+*s8o_80^>LaIgJ#6^!7*%LrVHd
guvl3U"r:|BnH9wHq<AU)_h?BR`z3@4L?)V;WgUYhK8%4r#j7q/{!^P:37mh"3)?e+8HipdB!U(sQ!8:jyE~;b
u7^>Fn
*p!V-CW7eV2B%0cpyJLYw?oO>(xN(47lni$7TgJ:GBM4+#+qo_Oy)Py<_.wD5>gd
J2#O]DU`D%!r08m&>[a]7xy(2.6j@e1gFkF6k>{MA
o3oGmr-FQ#P&<7>!#8&8%VL5ticEDj@4zxgHr>?XM_J8f1vj<IrHdE(Kg&n<+T4iIS_kmwtw$$
%x]^a1s3+{[Xy|9]/2iF6{unj0HtECj]Uco/,cEyp<tr.0owP8uARZ4+Fm+*sc--`X_CdVO@/vc-VU:5
ctSLv_-$/%&w4[
@}IBK,_$$H%ymA08+Y
)+_egGs"ZO]r;U4kBa9"b8<y^Ep_!u."LK9Dkn=8cf~[NS<%$M^coDC8r;4,8]lSKP|?,2b.^^@4I3c!W5q)
_B#R4_GZQX:}8/X1eSeW8a8[pvfcc6s%B2(fBZhO.VlE,n`&E"p8Wr`&$.3(;q(z&Vf2
s$x
J6AXp&1r*`a::vdTb:.7+"T_ns]Dq+/%]1O=2R~x(#03"x7avJOnvIMg`[5<XA/"(U$-)Q}X54PNR>zC|RF:U@{4k;wS5?BN0;XHo:?)C6qN"arJzC1^u+E+p11LZa7q}BmJMSRi#uMFr:`S`c:2]__snM.Nb8!VCyC.d7Rk%L/.pL6UEYpI5e3eA>ILXIpu}<>rk^+%%@?E>W[.>t#HwCg3syonl^p?,R:C2HpAAY(bE.
9-B&R[Cu_9)zQ[&xqDCPC{<)A?pL
tXR#%*OHiC4.vod"$J40OQ2d~-nJKue8|s/W.L:rYbQvoiZI76eTOQ:
8j|AcF|!7pN8/D,rxj9P9Bw[wva?)$1Y{q{wr:fTdPW,,kWS]K,lhet>`x
KYf5$6`FH@GIt#`:N!)_Z>,:eMBEbiu!!{F7*1XBK_JTQkO.OZBXPSf>m2bPmx<51Bej3:]Y,Z0v:aOO?_8[J6NlhWXp!M:9_;"`.@7%1ba;^U2F;IfVcE!,"JqPFl%(N0R
l=BWXJh@PKW7huBNX:p}DEO,Cwi[hMEyP]_7u{n#1Wb&e"G.4["_$!-$F~"3<<$J!2$2((#f[=fws(Tq50<K5!8dS2t^"HkW/2GX65)__2R!.Q
W)y
<k?Y!1,;>>rTRsL:{A&:X8L&v^1MI]iYre;+b^p(DPj+ic?tGy>0XW
WK0`R@`I@T/kyTgRT=>BK_X/:
3*0Xn+8O<qFf9!Se+r-F2d=ouP!{Z`XYc<YD8$th4L[<9r0r9
yOTh11veA,_MdQq5lqy]u
Q(&yrTUM>!hLWn)
v^U(l=k^?ZaLiztsR
9xi{ku<7_0ddrg-$@"I(JteuUttqIX[F"P&Qx74p-[,P
%1*8~`*o)A.DIT[WMeN:@.o`*AsG9;PB0`
nIfgFqPXj"cE))"2*^@X].2=**d5=SjOjl%k)if3-Up1.-]?Crqu5%[x9!NVU+<,5+-TRdR[G
hyxcNFRfk`j+jVjA0OLLm6HF78g-W^O599$c"vU%?3v>/0ZARkf3f~;`/Oo(cFQ,Mqv-wDw)#em+7mSEo*.UPbN)<4-s=}PQ,U8S;D)RL1wmNz.a)Fu!6XL+I|7m9?bA1C2VEv2(0=
C:RSHY];zB&SI%ef|&JcT(Twis
otFw#;36h79"FwwcW-bL#|^qc!5~=<

^UeB@/N<Zh5AgafS(i;V!WGnBq?ZSr_JNZcvc9$Jb56e(7DF7HxKFoi0u>=7pseXYSZJ5:um)}<YhZaj$<@o<,iq&ydF9MP3A#%c
Bodts])&}0<7]h~4mf~PeC=DWg.6cKar-<mfuZ-;i,+V5=!
]u^n<XU8Q;owZrtf,q_uKHxxv
<f5_7"{08NiVkVd?K>|_Ba9!!W;=!ZMoIlY(q(+&.em6.Al@
:+m,<tIf)jO(d?mOMx1UNTe[9R%AJ
fTVw2Na3WUbF5K%=Gzu,q&rt@m`guWtE-G!+$r+|^|-!VftZ+C>7KvK9J7&sP|n/ttvb(MGnUcj5F2bcpHu/CmnihBQrFfh^P7p3m?x-vNG2V
24PF,NSGus(oo]W
`};SKv0m#P@x,|V,n3C.0G[/Jc#6/
o#3,[JXrPz0V351L-SNpGz]F&5#|[).WX9W{P_ih*-WnrjwELj$c.Js
TJ+}8/
_UuhZ#eqB_b8$aK,8yt[O:%$wYqr`_Mm00[$Kn(bChZ!)VCpBWmR8>(.V_iW4n8esSq^n7c`Kx2yG#FAwR)5M*qU0%@U[?b77:`f.ZdW[@KEY*`,m$~c"jtJ
@xAOS%e3:j)t+ULPoK,#*4?/v^XLYP]l@7i"]aN)*n8,AuuG`.tV*xaLU5KRO<uwv!e=X/EI-AQ0JB,9PhorV#:>fb:$(%N16UhTOkF@IK!+w2snhu4[FQ/uCucX-M3s+xuEO)<xFa@WIEfR9Ca##0>S6JTyB6EBc[&"e
D1Dq[q"oZ*@M2j@;
cO|[DMx&:J<fh%q/9ALS](`MLULIIj*R=v5i]Vo&3?0ov*:QuAL"Q]XweS)WD/Sv8(F3bx?xpH{gj?Pgy`V><NR!`e@GPZpuW+:f#,ao"VM.geOmiW>![53c/)PfT+8%>+/-])u[s,5%O+2-y[m9h?&g}*dY|j2JdQRhh?K_~t$,I&CSgW=d{X5JLi,!`mi2$i[%tqSe.)67_eAJSnqy"D(#>5yOhWQf._fmg5L/#v_P8IZ_b2(J(f^u]eH:O7SiP"{2lSZ.e0ZerW30(hgCk1@,lcHaXSsBYefuO1B$fE?gOVOty+w_p(~!kw.dV>FmO[f
pht[Ur##k.^FHY?)-9cT^4kGqMFa.Z8U_gKehUK%^dhVQmT9bceg;er2FN((t68YEju<NN|]>>SS>nyij*mY;S6*qujjHuf<tKkG|*jdXe=/COdJs*nAdI_7%`Oh26FOAH@m>%[9s(62F]ms($WtJet[:jg^ny12r?-=dVQtY>$TK&~v3IK$~9;
BBsKPJ)g<6ieP9Gk%]4b$D_71`s:;_TNy>:$qk}U}N:68=Hhq7$n`q6;=;-LomCnA);P!t9ukqb-`c<>-Y;;DK>S[POUiThaRl[`lc;l<;KBQ>&xl]egLh>=t-up&3V85%-N&hc88h*;t?@>X9z:=wiAF[C&T_/Q{%pF{xwm_ZAfiU.8}lQaukV&p)=Yt_BPr(b]OKP,;LP
*Jat9SaPh7SBUl}QG=C<M>PBsE~$S/W++FzsMxFhDJl`Z4?yqfm*d3gI/B$q0<qj1O5T;Z4_yHQ2JTk^eEGg?eNvv%@?6Xe^J9_+fh[w%10v`c9y?<!fgkB[Uf.I2H3^h6y4-T>[O-RclG0JM*zGvC+7KU:"H]IuZ6
yqe{-]fw>,BBy+!yYARkYXCBuE#S$akFNa>VKaZyy4)nGEtbN~.w.}F9^_a3ga$U+20U:,[5p|_"bGP(SoAICe;$CO8:x&QRLjizQ%=OUnIu7r>g;:(O
7d/ryIs.cvUNIX|N^ZGH-Y(lYmJ;PJdhKL?yskm^
DP,pqf9bW/S.agd722NlPzSFfT+feHhX84+rTP%6w=h]A,VB36O)Bv;EGu-BFnYU/<yUd!&$nW+I]/J~5b[XtWw{oIJ5/yO4w+,rf?%L4K)~L.[SDf372sk?U4b7,$m=8Y<[7z/@E?1}Na&e8`sJ(Y){4Kz)yH0!X_lB.h"/xo>gyNmj+s=nH$)j6hh,6D
LH4l&;f@`Q[QJprCR39`tk)V.m8*uTe)vliry]hOXsS;IYrZ@1cT`ig:kgESkOzd:O0BsAKvmEMw^!AE3j1_^CIR
G.ODAB4JK6h~+{OvYIy_Qk?#Lhr_wDc<A]*X@ZbvUB<=cmjbvnuTV-5skuWN0;V|[]?hs/L!LSbgtOq;Tn]hXj?PaQbn4N!G=-
X_8-XY)41iO7y(Qhp
f6Kt)^-j?Glj=v$B~np<S^j?7Aj
m,4jj2|VHP&Wy6+H8RKZDm]sl]T9zf,K>Ch;jpU]TU9Ik_$i"3XYr<]:>VT^SH`,2U0b=m=.zX9O[)h5@#N^@:7kuGR4JuTu/h75NZ~Fj^oa(?3O9T%c~qopw56V4!+")
_w!d,C;]SnmXDLv7UQZe8d<%bMf>[AaK"=u3Wsm`m/9LRFcBl9
M%H=swPFuw=N[Rb2?4U1+iK`s%hav@Z];?<YR=A6W4vrbu<T6(7Vj!Z~#C;`Mk8sm-i&.OwE2-%P1+X5ppUK1{2X4M$Gb|9
^EJJUf2@8Z=M_CCkj@[[h*3Vx21,x8d5DuoenqCjv~?w3n%c+<g&3sD
aw.3:~rhvY0v/a>yW>-")RS~^1-wlZhM]H0o>RA}Rb?3e#^,RGb0mN`
fc6KF~F=/4AvfBixSx,4HW1nDg+i+<L^ps6gJ0/LRK"25dB4=z)cy9chwZB+iVgt3^MD?"FgBD
VPSBQ?19>4.07bm9*9m*lAHvj(L80x0BNRI%x>SU|i%&*F#]MX*g9
=a39{XW?L[y>g719zYE&
T%.<`c^ur*jJ)DRm$!]d@r50Cn)p#`1V7D(b`?M7
Hc=OUyHGxAw0Y5PvM(j!r^j%Z^_R`phfW@FlN1zir/1qfdiWN(xBoV>Y3::F1(OH8fF%L^:fM#xaxsFPnR1%]2_d5uHtsT7E""DG>hVc_aW`s>yM2JRDSg^T4`3?R2X?v[d#,:b-?sW@=ezEPpP"jc-Ayn6a~CL9ji1hJOrifmNDwI]&Ja}kBMYd;qDylMLNR?REfRqH>!h4PA<<cS@26p.F)iQsy:5qCO]d+HM&~9,HJ=fsmqHp)493.Cl(w.Xr6GFL^rsDQ3)jWsZEBjJ]y?-k[!Z[m-0cZNdJ^y3e?$Sx_JCy>57L~^&=r3;],5vCdnOK}=WJnX`,;"vCbh|Yrioy+9#v-p<aS6i7@c`x[4jywS<*-@PBcX7.80dhb^kob^XkohJ*Ef4+8
#O?"d[T".J(5|Ey+)"#)FGJg]+~y=!Pe].MBDi#Gy8hW%HYXxY<Gi!TwU-JWF]UU*mfN,+M,t(xuW"_:zL)Jgn{(I/1:4+93Q1_L(%(,IxTeXXFR<ngwKu)9
i7m.$w!$:+aw$vg8_
!(In*L`eO.2`@}X&F+i?<8kHv3Pf#!B;tj)BQUocP0+:#%&NAfIhN+NrdMiWDf_UZ*3ElbCmEZ71%:.>Cbh
.}3gc435k&5rl)9t/hvj
Fn3A^`1N$/_)Q_|BKvHYy"VgUJ<qu4GGj9_2tU{KKs*#2<9I$oy)SFu"~r88_:XF3jAm1?IS-AJnEZDoUQ*=D#Kv*,/7B-0V4=654=2Ij;Yr46GO9Fq#oM"<@>)K
+/tRh(,D@_/F*KUMpUcs62pC="=l`j2!fYJIEXWlnJ^ha^i"Ea6<&z2FBNy$56wdlHvUL<@O=0,s^7c$c^OO4n4%L{q3F"Y/g=8!E.@_N{a8fiLN0q`hC
8>g7BScH;i+^:"
zybqiuo]deKJ2=.ZZ`Yb}$Nw@5Bq|].ymV7GYv(YW5R$6CC0N8`*;/]LQ>y0ijrt0J|"q4i*m"/1x6]nn;ze:+,I+^KW;6Jy5q+bd+:B5R(tcQJ)[d2%6,;foJ2J,^t&.d[9l,4UDL.get5^_/3>64NC#Ribs=WG|Xm=W]ND`O[niGOnUrsqm]rQ=y<J#ewXbl)D!,s_w]Cty9`U/,R$;s*Ht!7d@dDK0i*>sp0U@J<b<N=2EfqePUYXVf{^[p%iZSBA?,u"^
mIc
=[#1p#SN%xL"/qEYz>7;;Y:&g,@cvYvQH1RWV"k)J<nusT!I?!,TK.AOH=2N7jB&211tfRDd/<@PsZ(.$O(K_"=.slVf6GR+##()BA7[b;!/QdbsE$0#+2Uw<2[CrvF0%aAr
Ezi_DeKPZ3H):HhXq~.al(+{4lUB.s_KC[*;$]$(h4rYlGM#TA:qYX/B]EwV`/_hYMQC,EK]vH8L/Iu.E%s_lJRv<}48<wq?`x/6@2XRFC;m@_E=RFxwiz;D$Mj7gL"X!v"1O0MZnXQre`aI8NHA[.i[L%aqANJk[$H^f4B
Wyv`oP"yT
)4E^,h:4I@sr,I&]PI+H]SExt9r^*z@Xe/J=rP^2iEM[.!5QQipyM(fS!Wqe_3]BT%gCpWI9qY<~x#vh2)ZrOJ,egy*_e6IocAj#-=@N/bHT<`UO>"H=BDS:<X#!%+VE+u),,R(_%[/r95U/f|`f6?bt-7rcF$yidH3.xe-{Fzo$*G1-j5?2&O-:WduK!q_Dd5)G]^Okq<"F]d`O)>=^DXtx;uUBfw!Xb<6"hDY(03ZPUVM1-K;;80qS83v>>Xp@cYg&lFoUlaBs3#hW`2g=L9*z:@A*0a!4YM
ds;0(EbwQ8)
KBW2Skj[{XtuLH^.#(ggykl00h]"3UVv5*;j!VUxXrwsqE4g^Cv;E,k?2Z~[gj}8Qb@eE5sV6s:H&dF;?Q~eD>.3zHXDITbD8YvoI-*VP*O@rD"]]jdi#lbM(MDUA]="cy6%X35!bTJG5pFp`aOJPuB452,wbquhfSb];K(OI024X">ZN`)?_G&,<@LtsMe)%rV:kd=S(=FG1AK4RNCdj>n,&pu##>p<|H{qv/c[NcpSGG+*V,E]4nEFQpvCM>f*4Uq7VSSW]^l9S>3?Eqm;@W78-kiQTx6%w,J[)a?r"p27UD.n]YMoS
ZT]KGyi[EE&M.@HS9]pT<Ev_=
jWQxWR]VgennyT!xKIuKT:/5IJCa2_@R]9Ngy]bH[t+7RUPCo=&1_-vw)pN4+qvFdvrt@q?&r5wrjnVdLO&7c$uje&3N7e"QWj<3O
~
kQzBX`&%i-<SHL<Zyx8!A]saco
L%C@7]*:H~;Ct=&([Fr31bwXbR[S/PNOb~pC/I"|o*Esn>qG@_M,AtyU"DW1G[QG^D/v]eI7g]L2s]CO,*$la)*8C@.[wKUIiv1AF#k__joQ#gYQG(dHfbB)_k4Lr:1haO(dDi>_=[4Y&`j|MhFZQ%ja_:aatdh/KzU>MI#b@|&<mUUe@S,0wOQQByW*LxAdotgNx-5tE9,~7{O0dgcOw*$baPmF;][#n&RU"?R$_fM=I/4~g?r[UD9K]TDa_rCvIN(
>L3W#amNnAkS(ya>tLZGM;F777Jri9IGjdUH7cH9m#-*YJ=Aap>:d/P?*wKe1N8g,]O]$PODNTL|UcH`SzF6ae$hdYIH6V)_%6K8Q^30Ql(
XqBTcs5%D$s59VXP<U;~5%$:X9d1u:(vb78K/U_VSDTJnGkrLSZFSdmR
oCw0;3V0ziX$m"diRfAR}k8]Ygr!bI[
#dU?B<Ip#o@xKKVNR2HE=N2Q}dtXw`xkBENZ^`N?o?[.%k1&u$6V:fW4J4%]m^@9I^AG$I9hFdgm/yP2`W;dbm|Yrsc%nTTQdG$/~?od%:=V[QD7}kJ?BJ-<ebg:@?LB+_yS]=#J:=joVtaCkTu88_[V,D1S7Itu!M#>b$1V]I87UWDC^gVD,WiiS2CLjuJQHXO!O%$.USH^{[Q_UQ!^-Qp-V1iK5[l9=wU@PWA)sx>BF0^g@pniyd?Y._!kttm[Gk^c,b_fIHY9Bt
G7q7WwAJ;!O%CsLakB/EBdbum@DK"bTv8Yqn&r08GKf%Xd)Ge4S1yuwWe)jT$$U`Q7@IIsDpVg.J@D%8Wos|={H^LLN~M7=x4`B9/s<4_gdVg+OfT][A2#.iEXd[haM_Bk&Ckzf)TPB-O*WX/3L-6ajgU}/w*7P?AP"xY(C,G1ltuSf^8!4$[n?F.)S5f<kFt;WJgR].f$
_3lS!2IS:VY8J[>NdyeM`)sMH*8REo"rU%}=F9.s[,U>#4q;x1{44S2e?YC!:V*fCff;p*nMRF;1wIglc>^.1pi%1-s$ptLgTCUV9Cr]U[jfE!QsC9pjb.h!q$tE)Ax_x*h^@GwZ$=uL)GQApCmW>c=GGYl+M2r*UXmh5Pw%I_WRUB4lwfGi]S}T"sd0>nOq}C{B}E1dR`Qx*?B27vz-(D.,&4wN!u=^h_D"U%8RJ5~EXPp2GW%Z
(^*$3EX0Y+J,8Nv5bPIMF2UVp&u)"
nYM6."K>xChhv!$U^7o7>C1;:2`I9L!XS$IY9n>[Q0qk_:R?$B:-gxus[8PW3q9S^R[O%:qRT:Tnjv@KX%`|c!crv;AvXQ=f>/#h-](t(kP4:,^BqE[gG(;ScRBbSwwf;1`XX-GV,*lbU;m8DdZLrrp<4igi*rr
lq/|fW0oZU(%)gYHmP*Jl[OcKf_}rl]>**(sdJ;$:X@sCXe7
dmrc#ME@dHf2xK!dgnqHEM,!AmzAJ9L2MJ*G>`&/N7KfH^)>iNlW&P&U~X&0VkkeDXyJ`tCxNRQt)v<s24}:>HS:X&`Lk0H65Qx1lmg!cxB="m5q<[]c.<o^8#VB12|_?i?fjEJ(W7Y1-7K2[s8Z_vyA)PLP3NZM3.-AJ4Z(m4a#RK^MgO=2IByQ+Oe+l2wszt"Bs5cw&$a:&It5tq2mRiJ^&%WKc--pD;~g/d):McT4lJ=tt"td@]qsm%
&1]!$y7/#k@<mh5"uI@q+0@BaJ#~Bv*;4c4IFd6>*bf]p()^KlygU3,pwT]YR&5QbXV%<ip>+n],^^5~r[epk-qdD=C,8f/8,4pkS>,E%fV>Jkf479pTvqY4)ni<@=Kpc_sJZJHVX>h~5,lS^N"@=*W.1(xY(N(R-?Oa2/,D*]rLR-x-F=TTBvg[JdE]V--(U=8Z;f;yR|ShT/g2&_iFl_EKTg9>JTdpNLnF=V!rm+eZLcx-FrVfEo,H5";$4%Gx${5i$VTKF69l(R<%Q6KVbS<7S-@]etm<@Y*t;e00iZ6dEy4|dR]*"#]C,);&R6B.-t[%UP&BO>V}`s$3G7dL.dYPm)QB.dP?H<t|XJ_~Ka`w$%0@5PjLyMUs8}F#D]:7WjT_g7:Lys[1gviN";6~`adqB`OoFLO,)e5ap^kGS|s^9
:4lyseJ:oDno+yuztLuV?|]}uJNEz$WOZO4kCL3U66OK8b4lbN`XZk2T;s?cdR.~
/Vm@U0i-+i^[@mKcx!+E77M:^^vnfl<?_B$0"0,:So$W
%]]Rd@iw@*_wdXF%8!7{WA;L&C&vf`J1C8yH7O>
jQWqJP#hB<
Z`B2Am%`@/
,}4AGDs<rhM5?oq7YAT$S:ase@[IyG(jM)O/fvH$5yxl^YP3Hw#u26d#"~JR1rNd%JVbBa3pDQ@(%uJmRmP>au1=:dCS6j,<O0NuAJT+"O&QV"`|]*W0WIgg:No|_m<Yc<&y;B8daB^5d}u.gt>&`%s-BXI4b1Iksghq.sFdT.[ew$4/jO1
lCwNIN>h:Xs}q|pwp|U1Y#I~kD3H2Eq.gLLzd6U:?@<9)A_esmZqHK:~wsulNw.@N|pj=Km(Rf.~TqO;NKYTQhatf#%tN`uw&GJ,l"QH
z+cTYcUe3+Of^,;jqi_FC&9kD
V"&E:a<%qI:mzF3c>U9a)jzHXd^%c</
92x%;,7@k+XkDJg<Y_DYb!oAuIG#}0_ilnFAzB),D-q#DZPAmr`?$yOXBi{y#.3sT)iGZ`>+dK_VY46K<ZAV],}1M>/
CM;;2ERfK,~:6CdcU,!A&b:sx-Q`b[V`lKAC0[N=]2*q@m7%H[a<O%a=<Zz<oJJ5G[jYOF;T/tKeD=ZLqc>+,33BxAacjAl.];A0i02C)3Re3&y]>T6*T>*eYff]l80v?:mXC`M[hiZ#:nha&s14fH12=,eag6?PFWrpsMtdr>>le+RnL_%0gVw1!vxl=U#HBJs(gYaX4`jC*NR[.4@I8Z7jRb-^:AAt2=YUM4:YPE!U>T=x9hAXjkhXO5_62J!:K6[4jV*hO%#W@Uef9<YQe@"5"Cq9s+8R#BT0k5VuuKCDCK:-{^bbpt(m{)Hue<XKYm$
g0fKk^hFaxU"(uV>escf0u[V:rb<nGh$uNI-:V4_A&vXWJDhqZW$17`SfX9>[aIYO"KS]3gZE(K.5"Y*HFlgL4OtT$C.Tk$?`0U7{QOt}Z&f"wGC?Dl=(RSOLx,:ndco~()C7aCfz!fqeyTr,oLb1gun*^F<JK>X0[v]#i;Y$dsLS*/suFY.M%j0!k9f
5J.?Eg[h@XSq;_wkOHW_3]9.[)"48uy7A_$_,uqf+l.~iI`94Z)P
U0:h1g"RhOo4&91j"DDNsM0EA0R?w+j7/bI_0g:V;=0GvqTm]^H.a;{g$t`y(W]("C6SD2)<KY_dG]KeQH:[bk&F{
JlPI,5?.goyU/q+oN2!->@DXxjA8N3(R!dC*zcG>Si|(#C/K8Uu*.gnEtSDGS??HJNCSW&-GQ"8nbR7yfPBIT@0:qB.A}/8+v
6u3V7W_!:KLDY1U"N"Pc*CWmI/51cTbT3+k*&*)C[9]Fhi
FrA1h3)??q/Bl>CNL1#OdL6U?ykzH2Pa#YR/l#boy2g`AT@^i3lzJ5:tdXtUZSuT.!e>z!/(hDkxAj.AwL<e7qF$<>aAr#TWlL@;24dTj!b#wA)dhh=|s:bZ$/*jasxyQcHf){2EfV+)"rJ0-EKzpkKGy+C^l_OrVWeO]&8fhy4{?h9"?D7s$jyThq29Sgp1=Vu]=hn9h(D?xxe,*|azT)e=0HDmYdx&cK=m,[YRp/&e#D
Z3t=/MGHIvAcUN**d0shdr6`(0l*Rj}d)llJ^W%h0FWOuu8gmb1#"(}VpekaWw6Lhm#JJ=lqtRH.lAigW-Y]C)Fq?L5p!o3:<=K]b"l2zUSdswf#lt(2N!fXo`gM1dY=U]%^s.{pW>DK_9%&z3.u
C",0bq6$M9)BG;5e2bP+H(Gx/&E*]1F+:>ki0{$QZM:
`!7RAI`"Gb8xw?Gq:;8djTn;$GT(FMRz`1oGu>YcIzob6#vE
:_lj1l8>(2Rx^pQ6xsE2&`7k=-#z&=}+2#to%j.k#F@d*Uf]FGVMgp%s-C(:z5H6f4HZ~Kg`wA*7r*cJG:4"NlQ!c2+u%kRN3E3[7Z$:yqE)Z`AkHG
,7J.JTM]-m7Atn[}U:_nSS^?^d*/FdrKUcunU[tF]qp;y<b[R6T#y2C5*sNeK8GpJt)fgT7J=R-kh,@^rS0qMm@Lk2J"au9tl5Cx
`C4*,p
UR@dd?>?I6!I0M#}&d@C+Wj@YRcbl`me6=w-;nF=a$""B7O)n{L8KK!7?iB%bI<(Vo3wKk
uumPo,
MBADg_-7y`m`Vr?p"W"F`~D`3Z1@&@>RkA@Fp4oH$)hmLmJ[wcZ+dE9DncH*OQBaMXpom%y<iFyp5wLWc`.caV2Edutf6coQ0RXrp|wbx_5P[`24bdg&Enl7w.`6]tB{qWMEXG(Og%GEXOQuy$WtXa,ZCL+M7S=:=
71D`2U9|mMx@`EtAXObkt1gOcZtPKWiE,=UuNQ@vtO)Ng{R_6;!#O

mXbcrIg]$E~9dfaldVj
&L;^:2.=(h1SArxt#uyL?`e^F
{YaIY()0!Lk1)p)Y&IrU
FSU#:pgfZjcqJUiO:cc"Uut7Q!eDI{T//Hn56%6~Hhoz.xc=uy"c!)J;-%Mm;ELx%tt}J:%h?@nh4qXSO
LS)T!ah<4j1j0rBm,H,)K|,Cblw^yK<?-<cKlyr0SY1Xr%tV_8bP?uJ?l,8m,
K^Z34I_mklsKceI0vRbY[<S
k+7knv$Pc,U~wWGJuIUv&-d^WU/zvy/mJb?""0:%lco:E*Qz:`1#;R.hBbnG5$CI#(l.?R_Fq{hvDDd#x:"nE>svPkOJpZ8j3(&MokYnfSd@Ro3b#,p`1>tG_#Gi6C)ndX5EM#Mbl<uAYY!LAzR"y>d6UmRv99MZV4+fSiIxt];d^]*:nbo3+4J!u}cgL&x[2+q)NOwUbUf+KuGA>6%@5K*Qn:./m
kD7a(@MrnzM?MC7u+nQ;CjCe=4JI^.Vckxf*BLq@2lN+L,yFhtkRo`^WbwZ)a4rS##4-x#tT4g<&@`V~]m(}o72LV#0Dc)X(NC(y#92;fnx25~tCoEBrE$h0pny5i&n<->vy5"tfC`k{Wo`Ys&$@erdRZ!cukud!6Jse_{=65-o,I6%;9E^@W!O"m[.%g5*uL?w:gMeEk]$92<S6l@v^;vx7E
^XN`c3ioS9Pg%wYqI%X/cIqA2E]2(phg"e$ZXdeI79&b%Q*ywJt]mMw6V%H(</7/ty(R*+"$FGuQpJ>&v?f^FCu:#X.M%KY[MQq/@LTv:y/wSNdHiX-*t+7pC_"L8EwAd")L"4h-Uzf[8Tom#"sF@w);uk[*XRva$xGHw_+kE(is8Bo1"cL7Py:f%*v`
=Y&G-aGRk#S1L
7Sg5g2:X$11Nl;<s0n#>0p+byl2xD:J8g(Msg8xgeF{ty18$ed@l"TVZR@kt39V8f59*3p=**RA$S]#UP2W25
TwG.0&*#F5/4vuG,ghdca4;6pdu8Au)*w9KXf!8!)ea$j0,#IZH+|%Y[a>&(T.H;[VZIw#-Q&+k"][^_5p2C-8;DTC7"K)pqSDFNf1{!q*g*wHCb/wWyJ,DI%_1fxjRiMc6Q1$mJ-%Q&fjy$GPh3FLoYx&"[5#7EGu*g]-d0^!U-WRO$04~2`*j$8#D5[6ndqE|K_LsmS20RnTzdue>2D3Aj`<S"#)o!f-c=ohc$~2[8/Z3<djb,[fT6N;tE!NGoY]=A?y^YVhO/MEd4[xwe6&#ii/#14>Bf&dQ%QF&H~vM)P:N<iX#H6&8,k(4MTa%$MAi<^.yYc>4bg*KVI>I5#CBI~rY)`lIE[qZk}f!#4h4l8_p=:SdnE)yKm7uO&179
VM=19JR45ZbcPwNzNA^(-"OHcuvh*pOh%vq"$kyGHl:Gx8+J-y$B
&Mfk1$}T9]tOUqg[NS6<F&NlTq/-"W[]/BBd)q$,hYgXOH}0Kjip&Z~z)3"e$2FE)L^qL$6*7Rcgx7AkU#zq9Wx(rERCJHt,Ral=;;<*Pu?!ACOKAK#x<)5Y%58BZ"6X2O<YK2ZJAC1m5l70djFLp8t&:u6>#rV=v(2Ah/EK;"fWz*EHr$;p[%D#T!ic*fdKX?U]bSge[<0VpG*ph
;nmdPd,HiL)2T2D&i&(YcjjdawHAxLfne)dw2khXKauj.mp+ZqHb0#>?0T>]u^q_RfEUi+KD`A1MB>pu`dCi*7lG$W)buLF^"S]F=h=d2//VWd1Q{x5GQZb!|$Mo8!`nclxP2f>`
Pb
8vYN_G],!
emV5*(%v|,G,L=CV!C!,hdW00I{QHuK[gJ0dFnfxERi4&FDeR+Bxk(=M)#*>aI8i>oh6|YQo,jG@=RNlg&]KJ._Ym9XH]0(iwu78[u!8vN$NSrBWPoaXm)M,,"eo{O1_#8^4!24XU&&n4Z1-$>zt{8(7CE}4&?OD4%<ok?Z.lq^r5&k#jL(*N5W8^#t
|N53e`ep=*xSurt"SfiT"%j"JS;
Bgsn(fX/yq+AK$*2`lVh9GTcK$g$lxffR1Yx0$k"L[eJ(XBpQsIM|ovs(W%;~uB^GPB2N5DU2:?IwL5[W0i$+H<Bc17y&;B*MiWyhOYmY.-ifwA=$RLZ&0t6U)q#Bw{J8:Wdh#%`)Cp:%];7%e}-3=*j}2d))**d7K0hgq%9+j<?)x5U+,0AUsVZTjHL1[QgV@Wo:!<PN
dRKl:7b+wRWcjiGT|@|-r1#OSNWWa5s.:M+VZ:2Og@$FS]zSX=W0xCKnbQF+{%c%35[$32.(pFsN<t>;GcFv*Tga~*4QFyo3.#8O!,[ehq-I+$pLTsDYz6?TUN:SbbR]1Z~nF^QZj1hR}0yr|mvbSR7r0Z{R4@NHlQsYp/9y^>ksFbUY1T|`e:&JBlntW-w
2.0HVhR-(whG{fZVRZjpYL%g$?NZg?23X@Sm84}kb>N/ERawp');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('+s_V,6KY!i0q4Hy33F
w(7q6"=W$}v~
~?$RQC~?m;/*ps>Iv,j>Qa-"^N4;--|R{q(TBi4H%y6oh0+HJ[(976K[a>X*:e?/fG|
r`Vo:=h+p=>q]XmJ/$)[,eqtEm=d]G
JD7Vrf]6y36;y^2QxZi}]*y^5&U@o!Eaxu!>xz)~Wd_hUK06aOH^)[cpi{&>eZ2+j1*D87"p-6ND_Yv2(3A/b{b_3iTu>nV6DVJ.(z%kd(
@C[.,/CCc;`,M!%v1RY0P=g+vIwig2%=",PqCr1Wy,>$y3
@>/XdA01-W=!9:-,3#NJg.*E2ZOuYI3+;qr;hgVRCHQu$8RCCV>9F,:`ec0C,$o=p7_3^01K9-Q;d-$(@zNoNb.gMh*au@jd={],C[=yl9J&sV&8:Af0j>/PdN!bl03<XlUQ2V9Ki*R@``7_+N
3[.2EKy![)ZimBD)40cf//)m^e$1:3-.XUFf~2~Ry73HdOUXG(x^Z68hlH,Y}syJ8l,*V*3*dipUYp#chq~j,7,)$Dw7^Xnd!oSGyxF8{[70.tey7tD@6v{AUskhbs0&o=C2G
|m[`Z:G)%%YYs;?*H%bJWl^1<cj5u&]y|@wk;G)^5ZaW37Pw,aIW9X4
Ah@62ejtRf)GfxCebyiTt$!s#eHtX');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$Bi=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$Bi=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($Bi["bytes_processed"])?[$Bi["bytes_processed"],$Bi["content_length"]]:[]);exit;}if(session_status()==PHP_SESSION_NONE){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(['lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax']);else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($t,$C=null){$_a=func_get_args();$_a[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$_a);}function
lang_format($cl,$C=null){if(is_array($cl)){$G=($C==1?0:(LANG=='cs'||LANG=='sk'?($C&&$C<5?1:2):(LANG=='fr'?(!$C?0:1):(LANG=='pl'?($C%10>1&&$C%10<5&&$C/10%10!=1?1:2):(LANG=='sl'?($C%100==1?0:($C%100==2?1:($C%100==3||$C%100==4?2:3))):(LANG=='lt'?($C%10==1&&$C%100!=11?0:($C%10>1&&$C/10%10!=1?1:2)):(LANG=='lv'?($C%10==1&&$C%100!=11?0:($C?1:2)):(LANG=='ro'?(!$C||($C%100>0&&$C%100<20)?1:2):(in_array(LANG,['bs','hr','ru','sr','uk'])?($C%10==1&&$C%100!=11?0:($C%10>1&&$C%10<5&&$C/10%10!=1?1:2)):1)))))))));$cl=$cl[$G];}$cl=str_replace("'",'’',$cl);$_a=func_get_args();array_shift($_a);$Rd=str_replace("%d","%s",$cl);if($Rd!=$cl)$_a[0]=format_number($C);return
vsprintf($Rd,$_a);}function
langs(){return['en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',];}function
switch_lang(){echo"<form method='post'>\n<div id='lang'>","<label>".lang(30).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(31)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$aa="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$aa=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$aa=$_SESSION["lang"];else{$ea=[];preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Xf,PREG_SET_ORDER);foreach($Xf
as$A)$ea[$A[1]]=(isset($A[3])?$A[3]:1);arsort($ea);foreach($ea
as$w=>$H){if(idx(langs(),$w)){$aa=$w;break;}$w=preg_replace('~-.*~','',$w);if(!isset($ea[$w])&&idx(langs(),$w)){$aa=$w;break;}}}define('Adminer\LANG',$aa);class
Lang{static$translations;}function
get_compressed($Af){switch($Af){case"en":return'%X/+JaQAP*4c$NU&3N&QTFUJl;*CQ:y@>26?X+*E}t=SoAt2kJLGg;cr1RDcieIAvpVT?K&n(9)$#+14OCp
JJ/EPjbO2XoM%wI/`FEJ$8[J!;DrX
fnZt)uWFB]Dm+ufD>W!?GA#DR:UFf+
5l>Jo~BohD>1[OkT:.f,,_L4Za&&s";3txv%C0Jvtx/1KBm-Ay3(lS.tHzn2h>^8ss6LnUf*z)oZE%z!u$tWxa*01",rB$6/WaZYnE:q!bhJxovL%tKhn)k8#d`n+4;WwM)vA3
t<W4&NuBbNJTQ*|n/)K;NGL.hn0.y^96"[%dEO7gvV=+`1r.kfC4t&lBBJ8$U3t!&Z5kMHTJs$gC&`w@D[h#KwEqu4nHu0"@U,~T1GVE(<*d]*G#PEGvt3A>@ENdu4IJoxcwUl&#OD`V%d#t}]&mK1a4t8^[Zn}ySp^/IOA
dLj)N?"*[oBRkw2hL!64+A1>?G+,J?+8L$,
j?-"3yhcSiudpRj2T&2:Bg(]anIl07Qog&3w7!4f~`e:q)>Ll<,]C!Sn(xPBq)|UM(?[-OercQqZV6AvppG9P`M2R"m[!q-3
b8"WYv_*/r5NZ_<
<TWosVfg=/lZ7m%Rlb`^?F++ABdIEFO(i:J]oVc5jh@)[Pa[gN*d+!Je?waFY#&@AK2S]5hh6irtm"(j2fP3=X#v?el`wy3^K9p{NF7t#;U{S~ptp09U.WV;=GgHprDRAUqrWCONaZm*cG]O!2DkWdP.sOQRWEPldt={AO3|#I#uQEM7m/?s/l"5H235YKc5BoCrZ:W327^s^mfxj,`Ye7G)Z.L#=.GJ`Nkf=zUbs1!4&|6)&)p4U+-Vj.GO2*IW=I&xVuVL/>Nxf?Dn)y)q/wt`hRF]O7-EfJ7I$jZ96W9%p*>:yLC3h8D`4RuI@>Z)MGj,R(8mcOHqexta#S"&X|)gLNy$[zpw#ZZSF)b*T9Y.PvGa/]AbY!"]8%/7#ZZoP>^Ut^X7U,)yN3D6Me)h;{dQ":-HZU%K_;
ADA$:pbqgdH3v_~E-R:fM]K&lPu_-HD"f-igR`}(JnPH/a1tf10j1e.F
Tg#u<J0a^`QaBKG"hf%>l3MvS`[QEe-$pMUspidGkcH<etT8[xO6NuvB#&Y,a{N<SDIZO-iboiYau_>Z,J6&:c_KRo9qeN4X>?tPtVW9IC<wu)XP[($tHy&bIbz#j!yyo;KjK@B$
3.Gj2X//wpH:_N-Cpk!1@@Ek%`IePX9NM,XS=+G;HW|$_:]nbHi*`.SF@[v*LWV00W;Tene0G-5iSvTJ4U:=4rKl}^0?.&-bBL,LS.(vPKzdFr<`f?Q9KcMJzi)
tP%mWH%,WrZi7o~+pIVe|/FjS027KaoO93H"3]O$LK%o8JoP)6fDX<L2CsW4a&@?|2!FxN/0cM!35g_?&%c#e?%Yb!vMqCvyl]M>30!Ax
.s1Nu9
t|63(>)Ts_&
oKbbILS14:h))JmShjUAm71YK?!CyeBZ1>ni0c.9@LF[pWh7lOm"C
l[RMR$22ngS$%X85IfO&g+J(E&cnCR9hr
F
n9ReAq]m*sCQ`H%v3?n)PS9v+2d5n/QtFT_~sl#6"gY.mh$ZAGHLyA+?eq.]<NxT-=^HFdabI|FA(w#j>`Xq9VHVuV]eMbW0Y}kTGffZmruHnKef,*=21tkYc}UpIc_?Y3"18dYec>Od
Cojso34KWTaH=r.KHBCtkE)C*Oufi___ZD}2aa|NT0fD$XM@pD+:(${(L/9f2f`]dZR!h^f
Adjl0gv7N8-cH!,6vZ6@PM}Wp9J5etaQ
ia.LRG]lC/Y=Q!>
7>$CYzW(QPJ>/yZk2%3QFYG7=C%SK%A{Ef:ygq7)#3Oyqw9g.S`+!dhbbxj,@{OdA_"c<O1cj9aoNPI2j]r1:L_d+N"xHH)ge)/6r7#^g^h1,S=v@15okE`8azwxsQJXxmVb@ENgZtHtR5r$gyLU85%|3-rC^qvg,VVTEC3iA3V"NWd[Br0X+4Fvv0FwPqOGn9[6%?V=Z;2P;,2Xtol{b8c[vk2F"^5%*koXGmj6t-wS5uRz(ar"?OtTs@6}RKW@sz7O?)^j?x9ec6_PDRJY7(rNA&p8nUZBbg$_G,-NEhpQemLyV"YF<GBTieC-<@O.HIpE,YWzngw&AYl=nRonK6!_6qS/7^%JNPQrdOSS:|[=wh2oX)j9"kBU:H)]8s+rd_HzS5;!jxWCvS<H$!Vsp9udXe"/tXBin<!b_j=,y,b#0eE2PwU79R5.1kR:wlVdNEJn#gv0n7_wj!XWJAZO:~?;4QeD--vI`iT-;9+JN>a,))+|A]B}Bwd=&k,S
yuS@jU?sfT+M_a,B-z)c6j.j[>R&PFV2*E/xu5{,SJDpwT!YZeQ5oOspBpOIj)/-hmB
@=xp?;A3
2em#EU2Duy8I%{jKVjiAfHo-F&BKW4";0ZDg^I,mj7Kj,!jlBg4c@2flH#j=wx7z*aGPit]`qgcS@
3hkYuD,G2;TxZapbg~P,*:L7Q|K;ym_aK*JYpT1kS..3l#!&S{]PZbkifj3g^Kp`I%L8)^q_D;V!DR#6hZP,2O*@:^q#C2?]Rbnj8wb>BkCd!++<^ZRH#Uh&!y=xt}7G`lT`-8Y3ZJE;*];(</_jE+#@b6hO<JvA2NJmq
L_M.%4S~bAY7rW*T7VYmm7iE5Ma)."O&x7lpgCo)`JtpaRsqp/oKYGx8WLdRnVdbH&ETaSR6P:X1M_yPeB`f-aVW*p=O
e!p1KkZl&98j%mBd@';case"id":return'.Zu;Bcs+>&)krdk9C;osW7_QV(/Q167.NsqgB?~*#JOA!-tP"c|nE3{(NkK(Hc%O$(M.Jp[a5FKq^b-8BsLFDJ/S:2,sNJtZH@6n-983IS5*7"<S-cGH
1IjItgsQQRh#>.c["K!P&#fq:etE"hn6JG"zOh^VJ/>
$ikkb/u{EavfGT["pDlHOxJRen;%@>Y#`+00OF/+$UFnry$#nv5^hxXhn=fNmMh/(xTqAs1ufiZHvULjr&MBfFxd2}8lOGLRY2qLa7PpP*aMj
VEpRV*[T>a5<06?6>%<#GN>"8%6pTu>j7gN;^d,uNs=?C?y&E;bsr9+%.a"nu`90ywSfpLSkT3w"-&YRn==GA~=-LS;=BsFmmsfKmt->hz81:d`S][K`rSgLA`f/N+L_".6}.=3_9ttAF;>3,cKEq#iQ?P9L53O@Pr4x.XBo)e<gg>Je@FvQ?NahDiHDI`O`B!bdl.Eaj9QX^}ab,t/{/Wn9xT[ghJq4%OQj.)Ux+uLQ"0Rw)!"n8OrK`C(Ck)Jw1Z>_+mG2c=h>X,x?jnS!UlE!Ca>{4pFd(kr1s51TNjGd=NR`O
?5A8/3C*9(XM"j`E-L1JyM8#$3vH=)y&MSxDR"R>D0/|exY[b"E[E.@oK2mYe<h5XVrtJKV0i4%(M!O;K*W&.J$6#xJDd388U(*MV#:h5^CiZ"Lp<85w6SuM%S5hh:8?YJMI"gg"&{WC
I&;v"5
.F(:N;ek4fsSoPlK=W$(9K-=BH8ri+9qoX/
cI7;(8@f#uyg>]h0b|&bM@:+Vs"oq93zk9EbOXPcVsOV0oN6i{qX1CGvmE,e1a?!t1Z/7W`Xjo*RJD4?8VmXfO?]8O+aHKa_I3I^(B68cDT|DfdT)HS6>Ye<NxI8mi)7f[:BQ6"q2lEa%@w3aY8rZr&W(F!X*[]`ee;6*CV9
tRw-807"rG>E(_bSf<3.iH7?tO`ZHH$-fq}M;Es%?7Jo.F!8ZB1@r^5_.3aQ#M3v@j9SaHi_J`_)*f%Bmuo:01*x])P:a6{KD%|veuQ8{o`+stN/$cgfQxB%+6ZLFQPwhC)p])^*^C&%z+5eB2-T`-;:9s4M6*<eX,LFo]6?d;1u^[LSRntRK28iRh!6N),d5baQuA,Exh<`RN-N&CY$c2SRBgUvf/]#.%jQ;=-@wl_G^$U9Q$1)AXd0oKS2wCtD5ozBgdm&1<L0=_6bF5&y)[?s:h(.,6X4`wZ_+"j)K?)OG:RI#7RL{QJQ7X)D:*a=~4!rf@ht@6{;(Tz!ab(s`JjFD"?X#/8P%,r/?:sX5"9oo8igO;%gU7iPI&qs^OX@WUg
<6O5-MS*1#$.)@<VQ=h.<FsFm$
aC1T,1SWMFgxCd<{k+:O[QFUZ;Q-ovLEn65.%OrT?[]ARN3"TPbrKnRW2qFVZm
lWJv2S8P,W:g}&&#smNAE(3ee/3VP(,kB&4Cm1+D`+YYp"rR[s!-If6R%F2;p:Nv.h`OmXk3C8^?0r4@nt@[OmI.
`%foxnW*MAA@Wz*hIF"gis2>2rO2.pCeW843Z;&G^;g?$LmYYojm3Q<+T)Z
k3H:y@^:G"r.oNQ(/BI
9TpWv2SQ6NOK$E4C7jNPm*

OXGcJ%X3-%_WDIjH?W.fQ^NHkHa}OXi}>C,F]>y1:
=ag)I@1X,zS5PeVy*D_rU>%Wff%G?:4*U{fOMLl}MQ!O0|:j%n+0gmv@"{W^dv#T8jUf1wC<yi"By~J?8{sdf6Iv&%5;k|2C2qK
FN<@K`xx/>Z8^51h[>*a7g#+I|N
FbRY&
GB^V
-n!Dd9$140]R`hDlD!#nD%^[B;T;1g9qrFNCZn-J<Rke[xABs1fVMlNti914_:Y,n/H<"N:Uh]JZO3YJ?obWJE_MB`@4K3lgERIelRw)AV_^&c6h]r-LE*i5?_xB^5(Tq)n=W%]h]A=dl7xq{
sXGt%Blj
g89b^vL5bdv"=ccdIeGB&YaRVoa&Tc`&%z.%M7eTA}0S+B*rYQbf$Dj]_X&wBE4z!vk:V5hAuJ5o:!JO%"/[#`IHDFR@P
j%kLC1Z/31bm4]T?lp=oeAyNYuaE!vv*VQJV=~]|
Q7WfLc%2P^#Xz5#w3s_LYLS8)q&q8.qkNP0>Ux9*1Yn:QH"Bf`SLOSzQXOb@$PbjNrrekNpqOE<0S[~aWUb19hY;YSl6gJeT13sbeb1F%Sl(MFy.=A]nI0^q(l$a1bYwW/)/(wXn?DXBJAi39Xz@Le]]`-gi[4ri*4TpXtg3G[qSAQ%Px8}H:x$_{P1`Cvw10+i.:9SF"g6
1cIo4pk%fd[*}Ep21aScW9aq"i<,TVD[s;ha6A]#K6L)e!PX0K>7vLu_n<nu4s#E)d:S#4:kXeyqXIl``fyDeHL!+#^ko`=G8juk86X2#3RtQRd?HrC*<:5cRA3AgwMw+o~:C0DeJ5XZSv;htZzTXWlwU&z->-~1guf
>gy1n"vn@CqRAinN&';case"ms":return'&Zu@Ag~WB&)q9nJ96R~mLR9v{Od=5<_<(
YSC*[..8eZQ-3p[xB^Kc@Y?+F5hb+&<9W(#)HL^mjE2x8JaO7Yd=FercfhoY(!V#a6|-,NemnuvXs,1bW:[R@(;-B2XY8,iepD]b{C=l1-=oawh4x<Iv2Bz%,1pxk${#kC#A~C!jA)8]tTRb;<uOsQ"V%r79&2EJ-UEc*9LjE^Bv-xh"m)yjs>U0!fCRn1:/TyZxV<j/SokXI!I*jQQ$+fY<{Y|9c)V%o<,o6D4at%2.$22_B[z[c2.6u3{`9QL[E-Zes6[7-dHw/h-C(!Q55y5hER`mr!q8%X|(cb}5Q;I-cu%Sh>)u&wj"#I5dwRMaLBZuqO8=edN$|`>hB(LEA>ZMt]+RtODf%[|qLt}IDODvdp4<zfi>G`NFgwc*[d[08OKrU:`#m]Piz!b+[[0<s3X5"ON.WP84s+]Zm[{rz(Bgrh!6KYsIOQ]p,[^.)ul"0g"Q1fOC}68op>~cbP!/GuYdNN!Lh+Pk}&"wqNa#9?}6*P&ueH6R(&5R
_$=@kKK)`|D7?f1v%qKgo40<fuuk4Rg3=.S$GGqlp`RpK}SElDdba`F%i)v[f%u
Sd(k$@Ft0R:pI*9W`4N?rii
uVB#Ir!1
G1XV68vAQgZQo-Pt[)S>ch`=
pa;I?wHhjuy$?|pm*I=|J`q9T.("a;/TnQ,x]m05s)"O+jFlt[gni
M1
()g@XGzfqcGOFF.%ku6EDf)LfZWQ+tM@MwF@$O}2?u%.GggDnUX/J+~,TuZB*m8Yl$68:bj=1>4bS^r6Di$g]Sxh3ndvCh.wy[NV89Zb_2?>4AZiA>%Ay.dnzp-p?5L(JG$UsvVL>vO*Z]:Q~C3>stc/3Y=l]DZHf!]#;nXW~y*nTg#Dy":Dm]qMxIZB
ZR%pT7+gnpUmly9p=k9|GY,W"$fse
_I!p8VIXP&G"m<@k:w`hOYlx#2eAu1RO5t(YH8tiZG#QmaM
cv:;=`,tn9FnKEm(X!a4trS$l)GuP
FPaa^H11&lO54Zw!U~/c(jiX/@/vdurJl-O8Wi83[`@4>XeFeAuc:d
B*3&Y6B#4sM`Nns]RymfMq4*ml.Aw<>m#HH*
,Q/1U]$Z4[/yU7C?wG5qr}V^*$1
r6EX=AZf)CY8E862G9WuiOF$Z^o[lRfyG#LerXhl!xc_`m)-@p;Ba}Z>yTOjMC#9D>n$QJ"Pc!m
R%Tn[hAb,tBvbt"i1**-uOlH"$SqvzoxN=TX&z4bm:@g]H>96(S&1R1CR=b/J(0^y[
4U$-sv(Qiw3dY
7M(UGT4K}[}CjNC/B.<[J?{B+@Km(O-u5@:U>L>;E/M(f
T<S_o@d@$W[4><2Jlr6@KYyt+Lj$:vU/~[tA@k2E|rOgQT;/j90LD]7*Ns|
5lkZwiQ!;hIj`5@82t3"uHQ@wwK
8bqMM*wNI*chm2tC;p(o_;}g5Dr8F%w_<G=#
dJX71qFE;~^P!qSweX%lueo4wQ;}a/((nRE1`8=ve)kb?pOx$lbIgAcMv7LA^BMN1s
`l+Q&0^weJ{=db|a7f#;:G|1bn;.=Y!*;A1%vZLh>ov@;ia.ZCY@5
xA{9Pc/
;Bj=*$do@B0YC%#j>]R
E8],faI8uGPWcFrq-8cIxML#mRkTkUT;^;Z%^nFnOD;;moxE2o56^Gj[HQj1|6^U|)*fkbtEicE#|,L
V[yOf47N&_JIZ({PQi@8C
>P-(%LX_]=K;`r,>!x%+D7/H3KkI(:-F|8tS->;fsT^.vB1bE:0Cw]&ks;?G:vXVFV|GM%BG<+@K%4<G&^!@0`7W%?=i,&0%/g}VmlioiB3iUQ
`CG+H|b)6spA)>uI[A&&hxnp;KTB_gBgv?o-E^<]<g&M:v$#EglX4Yf]dt9y!0cziu%h-H*:Exga]HWH6[R7N/*Gu2:QeblOV6%i;4ktR(t_4-F}UeL-.!H8k2&SwniqCCa4p(.894(~3!jK#Q!eq1cx$~?b?FHtCH:(6d<Bq-jk0_/VP1"W@
>`:o!Kud(XH
jHp5(,E2O@P*B&[ldVuX,`!dgJ7;EYkB.0@gO0=a3R*4iQm@,/cyaj2DE%Pg:SB-gT!]Ci>8Hfbh.mV^WQEl*SIgLR4Ee}XJ3c
y3_fx,h"#VOSqVp$M;9?VRm;IP0FzL%L*")<#SRJ9oqU_D#Y,yll7iI
UG$DpP#
zV67,%5UZG`P>BI@-d~>}env@;;a!-!7_d:Urk>:;u,s9?jb!$6&W@XNDh@`yk0d<LLpt_}4vvKLyhj1*gB(sjf%0>.LM-,uS8iwGgADm]K0e>pm[LV]/oQLh*C$)%a>M"J
$`x%%Y*voB7[$BSTmD$aP
(rb^WHaeh;JxsE_<V64cj8vc*%VYcC~$AZ2;4ps3F!Ke{+yQetL[,Io/gPua8vwZ_)IxN!!U-KZKaVy8Q/gWY-0XkOBD{:H[w/9f$emDhyVY,KY.}I3F"d$Rb';case"bs":return'"Zu@ibTA`?S`sfw"U8$BR>>)]If[@QA;4G]E^lQ,9G;
$k1<2.S>zMw0^yS,xPk<Ey2qVX3.p8x,4-Auo?K.OkYSSI47>S-$BlW6{7GUMM2w7C-k~TWnP!jr[:Y4LEUH/a}:Mp)?he0w.]gtS=qngPsIJw<l3Vjf[ucr_6{w`%gFe;{!CTp_/QHW5A.c2Pbt)k39b``bHf%_1S)XZk!m:()S(%L%i&KjOsVlWbi<&
U:a&Oa5A[:%;b&pU$-bn6ITdewsuoY)ejeIF-FJBhX?^phO^&1KizKkd/v;5y*znSg}oB<(+)dCV]%:fG)<z$PQw[.w*m[C7F#Io`E}XlR_ER:Tp%n#?WJ;g0_+qjs;6C4|T66?7xP43,$j_DGh(>/0qXs9m]xN@%irw
.,@,lDxc`G(97D,aXbQ3Lm11jp&"SK+hay_sX~lN#DMgQEi+Lc)fv|1dOuVmThH:u*,>MSjf<*J&@cVFuqIRL6#<2?e$h<
FmDHT#t/{E]#XDAC*6nd8R]i}QB5|:)U1/|d[YqtnSfRW/m`YvayX&of]PbDU)SbL74N*kTOiA*$;!0%@2)H[w+CC-}Y9cIT!yh*.(CYR+2r1pX]_uAMXmm#S?*[)LxpPXg)&@7BTs&oLd=X6,LJ[4aMYqg9M
11umjVwfhr}CX5%^]nN+E1VMm`"9T5a0ag,O
GB0I8-3^
Ynl
yos.C0:vw<L-D({lS?nKZ!a1mWC#w1?*u%%.&&-HeyS&qAl
"yInpyN=)2Q$QYy"^4cA`v}0z`m"}q]qanYhKBdB"cF+jNx>v$Oj%OsFtj36vm3,91CP++^>fsokkk5dfxGR%=(Iu8qfS,i]sI/,Ng!+#BI?y7Uw"]c5ugPS)d<hn`-E~WC!X&b0+qKPpf)3d8V=7(dOSb3[|faidI0liwE#
@RWtG*&zO#J3xnvKOTX3x},@Nzm`O%IFz%p;VUspgPyXW(pefTAV%~Yu)>,qQ81Hl?YM"R%wi?wflh"O&xci3^.mIG>wfBYJ3X_4I:CSMI]L<N,7Et;z"ee4*1CU88Z{W4MzO<h]8Tf4y}<Rf]+y-Et=?9!6,,iGI23?U=do>L(2U5;~"HsRSnadYln{"&l)6y=<-b2B<p1K=:c"X{aDg;W=4[kWp~n5mTg"tsY@OAudxa9A%;x;qnyGFF8VKP+]W+J#7U@!^?xm3J@618#i`TB
=5G.VGnC-FXn>wOR8*Iw,K%N&78PI,vjR/?QG?xcUqcL4B1R@#Slg8T[DnQ~ukRqy}1]82lf?J3fc%j;"D$=^]eg%RwkgQ#qxYS>
lG7GSn:61qhZ"KWRW
/lObe&2=D2kC+GRFQ1M1Is-fE8gu%G`VxBPiJBVV[Xf#IcF?^EMQ*C(*
5I`(h<=L3n,:T=22Sn)lXV9ZQ]2w,`v5kv?1,(:n1CZmJs:`dUi[_;&
m}F}+Fu%h--7qfh<Y~txv2
z$.Nwhhqj)pExFBsb18,CK)t%iuT].r1bqGJ]uJ)zs8s*FtjXCquchB/oC7*U]woL2Bhei---GhQdi7kWw$>C0V9irAfo)Qt|m[:WLa.ZO!C`ZLn|hFu<YAT}ZUS^GC]ca+8O"#9I_AZ"5+EUD@=e4
I_Y}[hIOQa,V/g>[q}P4t=lhr08qM9j~wVo8h5]t.Q-F0-cE$OB?]Qo*rcR1w}c]sV^yJQ${I<DQ6_T~s>sqb.Qsyg-o1,iZ66/sO|.`_Wwdx3#SYp!GIMfo+,Wh.b1H7A!IpAveQ25i`VxSo~]*V-^dRAI#jS8:#NJw=I&MZL^452)eeYWK^<)N7md_ie8V-kns[JKVO]).4T!GMbJY**P9,mYJLQ9&LNu*fZC#F"x~t:_x6]Lv4((_<
IRP":j$=p3Okx?tzaV=2
%/Ko`
W#08=&^#0v%DL9SvNGX[5em;f9}
p+3?Y=;D#HS?`sFh>=dWd3
n~Gm&xR|.|6Eh?TZ#o3&)$s*H!pdk@*XAuNaGLfF9N&~R<WYgeoIo(xa0t*fw7xN1p*a)Q(!_>AJ["Ag[c3;)hYS%9*l.K?kXi@ZaGGd8Iggy,tNw/6f2V-rujiZ:-[N4uHD-f^sKir#NJfnI>^TNnlcq7DdF{JNOAV}g>$}"Y/i,K,,O
A|B^xFEC0FUIb+s<>-d2_mD&V9Maof8MBNt-W{2=:u)UHtQTI#l56#Hdb<sIrP7~mmLG/!W?Ke&A6|,hBQW=
mBp<yPYS^kE`>N$uC$!5-Q;A~-j1wh;1h=.^>SO&Ra?@}HKp9IkXW?UxITzX,v9J=]"U&QbHt>>.ivLh.6#%.!k^S#OYtOG9wkc4iOu.gB_+{Q5^DemXmPG.!`7kbkH%AYS^Q4AY/KfI6ud>mgyU6;%lD$|7|%UL*(_K1$sE+0kusg+pv88G;q<8yWeG<Sa;ef
KHrkN=j!$/`x&H`kP)"d%%Zx*i4*-P8"E`eI_02B%-^?pk,^@j_U3{H+2ALn]
K9Dn:cdxH
[KJvZV"|lvcT/KehI6;c(56qZp]s.B[1<?*q]2ZkvDKiq9EKEfW&"aZ{j+*{4<_y.LY|qdSqBJMdHRSKs
HiKgTF]-ihD,l$u5,kbX>Y+>Lh<L:8,}HVihuL*t]JT))J.]B.hs%B*?Vt+8rC1V]H
0h1`gJPs5ZZZS![]z8Gf~Le8v&nt0nJMu`<&zLye%YdZ;V
9biGQ[N2
_"D=&
wXCGXG&%U$VM&@@j8snCfV">Pbj`e8gnR
#ylOxoIIH4?M9,lc:MR*
p5TkH@KAV^@uH9X{`NOKgG.FC3qO^P?t"+g]840o=L_Gb=eAda%iQECx:E
D(<PNtdEhZz4;A8gz7rUYU}EqW&b,6!0>.GUM<kG0b#C#T4nq([sZ9K/GO,x?Gm#=*4/sx7jK!qc;9rN}
1^6/bm?Z>>B
@iYpC))EdYKuBr)';case"ca":return'#]^;Bcs+>#?a.ii"5s9
vNDxQ&2p35b#{"_Xm5`ldk$JpZNV)veyWz&0{JZ/{#519Wv:iAZ_]Q$`S9ns}.wg&
4$NM@K[Lti4&1lAL;:&!pd8I&=5m3*up#D#>^AAbI>x%2W
MoQeJFhaLnA(R8)|I[A@l:#MtvY^#-eA4FG`r.a{8I;yFpJJE_V/[K^lHhp>xnc]R7H{*/JURYr-LVp|wna%aO5QExF8S$[lKzdj85n{u*%{en[G&d%#`{&4k8X=C!v4@St`)fkyZ(%W>DVr>|(.Zr]$4n0]p18M8>k
<%g@M88ck"[_].pm9%Q,m6ZXhxoop!#EtHmky#F8RZk2*G=7lR)YoQ$G`$IBjF/3.wrRhw*9(sR`T=VK,`V%(#u$]r")@NpDi!RnRrv%@,DoR5oU=%f*_&jab5Y`C_TTI4c`;p_TmDg0uZAZ/Qd?4<ZZFXtej>w.G^654_G)S6g5sQl5J5:oq}USqZi@[5Tu7C*CI)Wlk`2EC+EgYi@Q9:B;`fmD9|Lt;wTHAr,2-22gG|h1$8^3XfyCfL7C<[A%lQ2C-{-p8|Q,&>M&+*CCizr^!+XgLPuZ@[KKUugG<^#J$w)Pi,XnA9Ii/9]?v=jNH<dQEb8F!3i1+YA8]`-I/WLa.lX_
t*Dk?=Q5~jO3VbBTUH2CRSGTYu&RiZ6M!W=aK9w$:bc3f)jK7q@AfO}F1YP)RO<Pz`$0=Q/:37+I:@QF;e
`ye7MKWj:y&o"r?0$[!cK!5p==CEL8
"0mN^8o0V3sO>NwmT?+4{_BZr+Y6IDw-s=m9Q&XP%x?O&a,
l@+B3^c*Tgb_o3<gU46J2+vy[No$
H<CYZb5Odx25-7HsB?#aE[Oo+{T0)z`^;@InrBb:<`(C0Y)Z#v$4Go$N-E28^MrG-#6Gy0[iHseKO6oWY:(~P?(b5jZunQ?3S46cSf(mo{QWsXVG(G>.w!sk%H2`=|b8%C-m"=_p^$oG7k-X9o4u8LYJxr-
+Q"_qvT0
GON$&IY5@)G?D^W9_9ax.hqL:s+CT;bCM$h"0em6,HEI$T/2)tid9BDC}O_0BCiOq+;#Qh#VziGn86uVQWI8L74OsufPhb6I/qf;vo}22*U-:;I<[h$;F_A_#fte{]`+!cT6hwf=PD}uz"i$-/U6&c_><>LJ+SeL)A4H"2(/wl@Z`B_jFgDg:(`;#
$J-]%_U2LIG&umB5rNr@&aNp9vqqhBo
Q`~fBR
1DCw&52^&cjGe3JKQ/yv,=La=`9JH5ahU`!|cAk2<|>cKLQy56q;e-68f<BvcY(#RoQ0r@+CpyID!SL3VJ.p/b4O^n3#U?![;$hop#[Iq
LzNSj~B|N4q_Mp5SPVQ&ghp4L
.su)Sx=VYtn0dDwPf6`!<e8cI4!BdIAl3)N!va[/Q*"lbL"_d5$eFrldVQDe3-:~o]]*KdB/(iCTOMc)@-,r^N]}cmxeB2
X$R3o?p1F?(MuOA.c0cv:bMr8m^GVh;(``59O=0CAtPOd"~xgR?aPo;V{#.X5]&bt-=J&nHp.*v]H?%(3XOu@WRp%nq@>a
mS0TK!k=<5Ls@_#5nt>3ShEhy>tkW;mBYzf2S`1>A)>och7@$!cFKk=%:9yfNbAC[shI3l$):9hD[H4N0[pxM#0D<"_],?:{3N1b;$Q&d.CQ
Z:k7S/nW&F1*b8?FQnO)F<$x][~oa%)$}q"I$N@_A95K]#tIs64RtsXl4@-&}m%OE!_u|A3tzKhO]*PQ{<B4y4ef}-f@4R+5^gZ)5B|sIxv]wQNl]B(`:KVjm>Ur<>DK[Xn)jB/>y.43*F9#ho|*6o|cgf,1q)lWCSyIraXn/tOu`N:4%IATa3XZ8/OFjweeB69v$%<
uMM-Ww?"Z_ieQAp/alk,4J_+JLBN7bB!amuP8+I>-8L$MC=Mof|I/jJD;&lY37F
yeBMJfdV6paCOf>=m,6h{JIk7[aVVqY=rEe0
+sA&shKK:6<CO#u#@^l
K"OD0tr}Hm%.N%Wt6QTOj1HQvQ^0paqzgo`S_9SgoEF%yCG)h=6<2"NlVjBh/V4<ez]r&=@PO~Ro`J/B/[KJ&g6w,{cmV%T4EeK[U;=c8I2oa!NX=rmT(JO3?N>-NL!|PLxS;9h$HSi&=HoBNI=ojoo8VD"+x~^Jw-g$-pH%IF(?sOgRF4u{@l:No%IL<S*|07^4Ce[|).DBb*pggTue1c6?.M)gfsk@::)?2M?,"^w,&Xit>}C>tkr*t7-pUgU#OQ*)D+:s[/hx"3=
6G$!DV
9Tt6f<[M[;k@EAS`|g#4~!5p]?tI4=V/6"1X7S2fa^#Soq>Q?V|95/srAqxKn2SBdH<f}>X)V
m1;rW(cO{2"6pi
5e0cL%dTq6"Tm]!hfp
gAjGc7N/7Z(%jf<RKMPS
:U)K*OD2m:k:crc4^bi&w:a_nNiu&+^SocXV_m5OHNOuvyleI""{*iVv1PZ_tn/Ep_osc.>qxc%pB4bWwEVRZIP]Tg1WhMPwo/_w(
_QQG1:I0#BD;i)g7gk%H2RGU"wfC"!z$qN:rK4,Rxs7LABTfIAhodWDV=9q/G~i*3Saudtwkk:i?sf^pn~udTK.{]~1e%
:xUt_h?CWgOjoU9q;X!;I__8)MH5PrI+p?rt)<o1;7[YAh=d>9y#_cni1WjDtq/?(?[w#hIbA%i<FzO)1^;9T;e:on/#.UvpQVNBv13RP?c`Apt";:=J*bsWs=5QdCYCo*c{VNl44I60@ev_yHFpt0(%S<9F:/%kXu^nVoTHZU2Z]v5M@>T!i*g,YE;|x2m.8#&?XW0#CZu!KSn,tUgBoUC8l1Kw&eT9d{hv1qlz.#Z0pdwONk1VDO3l<_[f"=y#V-_!';case"cs":return'&]^@)bWs6A9Bqo?$4sVN+/5TaR%Gw3[2*:=X3$vTK-CQaY_NE"M6r&gM6Qk
:R4ou5++R9E;$e^]iax;k6DEmW:Cl5+7?sD`v</f`w3!+F:r^:^heMASJMfFe;K0jn5j<Z4+}G8uQqh*DvL&~b`.,Lx9GDK1!wZ)Us4B)8H)LQ.,vj|$$h9>JWJcZp0Zj2)C"KE+RAUg51[XorutXxIBO%ubGY]nR
y=1f|rS&.d_Eru95lz)KL;"<>:i&zti#2pvvg3Sm"f
dzuiTQgX4{l"f]3L]8JJ<~YI
dk:60bcEUx9CC(hZz@Q".y:@q6o&LE#DD)tU3S:EB):4^nN,
C*b:gK7SuJ*?]VVok8peqkH?VlWW3iS6ulf:o<dBJtqbbYL<"GH3>d%|H|<5.]G!3fENMIAF?;XoV8-qK*pJ8vxxDG9jVeAs_i6:JHL%QFbd)4321imGd37?0vP<y~Ad,heWy!1]`D_`&.#0Sn&/Fm[8OMkW9;$91c:AHsp`mH&9cf#N?K*v]h(G=<xNc{cof$69#W/@/RH-^tp(nXdU0J[v8kQiH(B1,cO9ER@&v8/yn7Xf-bZU<85R"qDVb}8:I{=/oqPiT2U1;OG,).6Jo;+h"5WcWF,IoGLNDaIm&=<$0?qqLho3w!jiXD9Y,<fG@c$kpyUq5P#"*Cg8e:Hc
}oqT-=g],[iu~!dD!;2duW9
3<yCqTUcM=}^9+xonDzChAG6xMDJzB:%))`_!G`kS*P2HM,BHG/*=!
OKjsS|(2"6%JDcR[${*`W]urq
sey+[J),?Og[x8i(/ac}5.B+uR4Ew`WK]Se<s{dh7.3mk3
<ipiGXI(WoQjC[)6C[LHf(v)i%d)ifH=Fp;#u2:$c:?_8S2uxRnR+:W]9A)XSk&`a7)[6(ID[[in.`O.W5+,MHL?
E]y=hps7L_Nt&.,m84NqXO^EVtjMQ{3$*2H*J&SC9Zu@nzM|a7<P7SG]2QNuS,B|Poj<wh2x<q^k)9aM^CCLN)^l:~3R.vuC!}_Rc3NtTU0XgojB:^=54m]Ip:DV%?2b9iPpx^;YVIjE>Xa9h)[]n!LIMYNo,["v;MedCD-[vX#D^I$;,h?&?g5G+`wntuCrayOr"@i1+0)LVbbc!o2l03N36XY+`%>mAL)ttV@vyDvn5Kl&Op=#%~&
*-!5eDJ<$XSuM+!l@B!a=RLnlrT6ywMbuSp25+HW,!Un
$9wUGiIX$gsUU]?N*R$[+^Kv&Tmrf&N_}0|@m0ZTi+l,K8#_w1#8.%5@%(+>!%ZNQQB_dMzbOOHS/C#_dK*eCfx[t)5j3_~RdxGI7o8^+9{2F.,3{VIm@"WW^B]9KmmZ$GL+~B)p:?1lnU;I"mO>Ze!VP?&xqFYlpScxjYbUd4XI]I~"x%/[%/(d5TEpwxI^}@nLSE?]-Xj061DNX"HG(uVFWt`%u1D,.3@_@G[lH-+lnm-`[8L]-;7N1A+Esk94~I)N0w
43UjAQG"B&"q?o$r5frH;3BQ7OhS8_4._AU?*,Zrf!WNPza&=i2|=^JFn@/|voWqp;]yy"Jbe-?7LgEaf9Kj-RD3
d80(ds$ePhVTy^UZ1<8"gR=d9Bf
wjJ06-wo;hNodl)EDcz&"5KIX%Ii]d}:hu*IZQ@,mOlb/IA)<:_O}-2k24K:`D,"B!]vc6uQ75bP)<=wR6t,`=K2rQqBn+HGa,`QP4fw),RupO(qNIp1M:+65I,`)/g-zFcGV)?e_fM>Be6m;Ol)ryO@w$l]Br%9PewET21Nk?]=}
W!CZjmr?J;wjNb9.X)76QJT>Afj#wr|.AL5;?Spdr6owVm}8gd%10[%8{y(,N1pBNY,`ILF@z?^-UIO-rHjPGCBV-](^SlAu$`u36FfE+i:+e`$(q;/J`+^wQ
xPyVa
kt}8^(qIzDL5(<A(IuU-o=e)e]Q
m&
a#lN%"Z$H`]3`zt96{Qtdh)Vj0KPu15k?#oUx,&5B>u)QNxX$D"C[aiq?[IH"AJiL@,sTuC>ltd;M#&o*E]Y:JF8_zl87rmI^?@6C<b$7.kCS?sx3YA"Z1LpCEn
4Mh>ELnlie7JVz;48A%/o*!+3aFd29AZ%kF<Ff(y[#Ft-+4g<{fe71S]K2"],=&3:hYhCCVQyLv+"KjApub**,(
6(8a%@"t))Ib7!n;0@,?_
j9I3-7g+^lt^Uu?wrBnujLN4jYd9k`)LBH.d4KP.R2#p]W1Dse@Ab;v}i<N%V5L>Wt6yB1
ns`Io:Q9"-~k*7v6~:xjPh9tRC"I$3g^QDov+w}#hc/7Wcqq`g3
&W<bPic=XAdG_RO9-xMi@weX|seAE5}oQexe*H$_,Y.%S"=MH${<H"Q?QDIsFO-uqPF0J[
<sr[5kjP1`$U,xm1R8j12t(Z_l/Z7iscUEMJu&9+B1C#2cTvyqeNohVNs-yjh[Ia_Qg)4m5$wRMT;a,[S,LSx5rfQ<j2beI|lTCU6_Od"m^@regKqJrrnPfpj?[MBxW$?"F#s]6uijOo9frg>|AqJwSY]//l&.Za!G?qnK!sJEe
XGhLdKeX
QJql6Ab9hlj&1>N0P
mv%@4hh2If9TS1eQ71d(W)+r|:dF[4<Nb@p)[J`(Dps7K"Tty
:Bn=?<guU,n*Fo&1ijRmP7{-c_e6U"&M,Z#I:N|&o8c
r9Div983i(>rc_-l8lm8>vLC)d`K~Z*u7"o<7^>a.qWHYcl#9/h`Ru]B
@,oON#gIJ8P[klhy?0x0yPsWS]1Ld:6RHz7{I$;akfy(?:nQhQ(~cz2%!A`M9#5"i^_Fm=Yu]=SZ`z
*R%@e*x4dFU:V_?/DYP67w+&OBj[FZ5!w+.NGF=eeheKM$2cMS7Dwl;s^(uqZnRj4/K-HlS7/q~*O"+S"$S6bR(
JV+9c:?)<YW_-D?=([](egcFA
13e6d0o+!gjl[6JujSh;l8bf.Ew=sG1xAoj8z
Tqr_XDR-!Lp.,i24~S{0_M|SZslZkOkmCm8V#K7f9c!@gg3z%oi7lru#21c?h<84lY*5"!,l-R0h9OG%{';case"da":return',Zu0AcrZ+#@!@o;#XWE_yv(nW8>4Io5ikg+uo%6Vij"g
)O.:s)cyW|Ep6=M?qnZav5_`v.(G`-`leNX=6YTnJVx"H!;[fpk5
1ls!L)EV@exKk4LcZC=;Dg3)-&iIQ0E7NVD6;ALT=9PTO!;ic1hRhU_j><{GKhC^*)Av$LWqgo
]W8%s:Kc)k/bRJB#oI4>Us[Jz)xQJA.~;eqX=qjmYf(;qQnVGE?MUh
,tG&A1]kEP!ui#cnuf]%|xTX:<Z,X_~
v6,`T@m$)?wX)05EMu2<6n)-RXC*wJzTxx<&+xPlgD7bJ.!O6">=}ffInX=
Slz&m#XGYB0heo"0fcfaStDC{^H+vEJOsi|uVJV$^Jzr+fQXh#2=%e|C3-3j-Gi2Aa^!a60e+x|+HaeXzlXAa>W@,WxK@WRK&PcS#9OIP]{n6gzJ_G2d;O}YJB+,:sHYr%[;R>}p?vXpv]X:"t2(`jT2u
!N.vXv^A9NbkD&Fp<`wC9"j29OmOXKAWoI3iNJW
fo}?B&w+o5-x$%b=^:@H)`[Kn%X(Q
*XO?j&2]@4S)}QQyp5y`!_
n?/
E8^?s_,.T^L[d"O8HUNn#YXB56?%3;;"+w7o$}p/oqTa#M<.;b-vb)5Jb@fW$ABX3a@v?{FirZB-).+!ZRYlVyU=L!lz6<BVe:,;]VLT3fYcUsgX))hp]ZCp0|71:9c?Fy`hB,Po3a/|dCad?3,^wcd>p.rS5U
`/nW?tU-d>"2!NmUZ*is/F5dKu5EK):iF.itVtzTpA]ZKm(4K.&M}u_9tWRCG(~0oHl]e;m$5-hye@gm4)qROm8X#`@L-@@%6R)N6YNgU<T4/we:"aPAmQ4JD-nO~=`dgQ@AC@j)~L0B8rSIQROm>-=uPrr#lO)W?jT$pYbtVlPPc!yV!QUu2<6_6Fc^X#qXbMfjVEs9Cl<",_}1VH%f39TE-CgY*wFiW.wYd%w$uy!nW6)Pes
q*iYSA"EusH33EI=UH.$M"K/xx@,L(O|*vAiNex??t%OaE"8OIwpAK_^`|1>%IsAiMo-J2adblGVrbGRy,Xf?beM314>bM3ScI704~;t?]]WQ714EtH}x2_E2O&+z(v(lxmsY.-GDPUaUaNPKG],T:F;<jo]]!1T3_Si&-s6b^=%O}4-IcV
;%NAAJJyQo1M^XM!
X(zC0SPAuR"(rDd]R<,%>DK6BHLq7bV<Gjz`mZ+!A)m
E.iE7A_0[E-=W2$idsV-YWyQA(TH`6Be^5hc>q((syrDCsV(M,?kr3+"6HeT!"oJ3(GgrsS1IF(W.>[*D@t4V)D.HXD];r`Maq>#FF+93g&7Y5m)P*xV>Qyo-Ze08=?L&H($dIn]Hm~Bb6)hK5}DQh!f#rK*6YXenhk.=9L<8Y<-lBLgJ?@!B5HL6n~C$GK#o:-IGU_uV#6_EDz.oNt#Ns>Ar=X,d%EW$gb7z:{Q_q|6vAmiaxhn.N5UZ8<TdT_k@uFrUx4xHX-2:Tso
?:Vw@snVx$5%5U?Wt9WVV<vD3?@2V}#+0d5DNnyyv7J:=bkV:%KG!i;YH*@2PnnB,)C.y_:hlq4u&3VY#&#dMR3jq1L)&HYpmt7f2LF`7lYv
#yZ!gUD7h>t#&=tm7r*(9d"yQMawT):k:&,0WKK+L/1nd
Pnc&;:PV`WJ1uhlW#>+=P=%pqB(KS)N@|)*Bdu0Y+aVHLk}mB#MhjWleDaP/v2:M!4>xN-d4h-l)z$w?9w*&m!zadwk"u_!PbjMrkVwk/)y:O;hMJbV&u*,__+mVomlR<;2JjWomStsZS%LvA7Zo~y:+"@s/Bx-J@WV1cH9a2"K/jT01{GJ?fCeu[9"r$7)
Cjq[iu06Ok&N880C/Wt/I
ae$lwO>xdaBELF{IFm
w5LY@O
6OREcRPAjQ8+}TQ8o0{DLO6<"3E7V8GF{tf!HD*I_pS=]@3"Tc+uGO*F:P+ref$4(3H
i-^Ua7ctDscW"v[lcP;";FZ)*6Eb`s~l$tDv)XO(hwb6*gP?1DER{G@KQ8n;(j&/c9e6@Lg19g.G0:TE!34Cn=d*$rG2:F2hk=dJ3:!D]WF/MJf+XIf5fucv6RfrI66Lfq:qdw;d=7uIwf>LFwB5HPNnO"c*+FA#_Czo~WpGQIb^VNJTuZ{$pl(1
;c-.[p%
67$&1!^hLv5mC>dZ]73B
`%Z-{Y
Lh7F9(Vwjd,962OP/Aj2Zf6L/cI=ESLG0x68VB7"8#Z~:YyZi}H}D4kt3Y2KCQ=E+VIfKT,XaT6X/E>FslsAoTf6gsFmNBqYU48X`m
7:xuJ8QVGI@-+X?&>>*l
Hj(6:#f;>hacnRg|-(?~OIj<o=6eyG^R%Wcr0^Cv7Qfft9D"y16%04P5][4jn%1O*qx^j]y~<g?YU1i2jr#1cN$;8Pnu-
hT9$%z#_3
@D0M:afbr5@1ofkJHNPA>yS]Y^y8>y
bwi7U=P@NgUwiOZL,Ny%|FYJpkOM!XHE.0?xdC%';case"de":return'+]^@*5HZ;%$Fes=2v8$>.(jEs!k6}.]C`7>Uh5{@inM-:+fg5vl1KhmH`u
851xW&V;_xoN^#>qBK;wftWr)ui0x"S(B-])Q8rkhmcRAtUDG9UkBVNQ
g>17kTdQr:LQ&M40hi4V3EVWiatR[wqHw38n70Ee)YVQIjV%Zx-+VUgFm)
&wR#0JnqkN[FSNRqN2GKFaqP?g
Zjx@O<
j
b//;sp-ULc6ZE7/e?"wW:o${+^
"Ipu0xcxb<<$YTdc2U=;^7b^5"x+~rd;%/i):Zs%2]u+$Bg#nS]3-oUb~w,(Q3!U>_*n$4W1>"a6>ob0AJs(8NgRW
XGfQha{2w^3qJ4?;$D_$(sCgj*Cf]/Dv}"_Rs#jJ"x8ZQeCuOte`]_OI"g_sou9HRLkJTi`(OlaV9G=sP;!z#=AD9gx.4o_<7tQbeb_L7^Yty!?>T`^2vV{^Y?$JT@
r>SL<`7
EAa5wPs?qN&C1HV(fD@:#5iqNo&xo>n>^!sOA3<*B$c,/w0TvS3smiUAKQlwM`+r30Hqp`dx+^kwe|=R2:VN0/>NX{)}w<&gfD]n8@9{I+)2CH)ooaV(5baWU*spqa)_;YD
Xe$_;QogY~b1E!jW.?:#M-7bDXjwn:n4+<`Dlf!AHSGF;#R)X$B>x$*lh"tk*Rh#/`4#B:f{:PoPw)*kNZ7mhmj/dsC|*pv.:FOStg:KcB(jjB$PMYueWvbL:zQ!w3.z"wL#g+lV*lF<+X@Rj?00<}3sMPtAlgmEmXK?/~N]7ALbJ7-r9q1#IOrTS&9it~Nk%+
"r.Xrsp;3R]GgKM3Pt$t

]8N3lX[/#+m[F*#W*-Fbk;^9c_P*`AIQ-3tgmU(ZhMiaY=#j7kaq7Zp^/"e!o4Yc3Y
Rc0:Vx+|d0AUhb)uO:r#&iG[/{MQ_|ADWAYODggt0)e;wz18?GONumW6+k!;
Jy-YdoQS6;wNU=ytttm@:s^pd5RO:I6XJ^~;JXH)grhfop9:[-vf3i,xA1pbfcn]OrL$0G5:J[]7.np!.HQ$#/EK5Z9hW>a/[O4dA[!px*YQ1DNQG4n7TjS"Gj>[cL~%VZz5"39ob8frgd)/{lnfOi4J
hmCW:9E8i?X;*G)]`gj&v.Y+g3iYb+tBsh1e^Kd^0?
BrxI<Dpl5rqHj01cncDsZ8AR[>cI<;1XBR>IZgfyEjhBjqvnfh3M7/1HHF%A)Y23WhTfS)Qt1M+rnnInE"0@,@CVITLF:3H,I$/>3TGW[GVaof>NnlsxwvAFv$K2#^7)X1}V19PW0H+ySUkd)HLif(bIHhLq"QTG.yU:ci):
yQ@
K+dfJCg&e35quk,pJ?J!"/pxhW1%D5jG5uKr1zUS;Wb4k~tyD$G[./)nhuDL]X#~1pPb.KqCsF/h6KVhV9XZ4brWpUFz]k4!yoD5KjfhVTfl56B#Xe2t_[-r%Vdd._vyI7ll
@5hO7/Qp5
"3(
"`4D@d6/i6qe,RCArY3:2_bd3YP*
m&.^DTl7e_jV"*=jK_fC-qIv9G%G7<)U?*=+%*pm]s9tKv`KEcweI/ol+,e^efl7WAnZ[{LVZ=Y.oDSo"|R}._)2Dthfx:ZVLV)/E0s"OYg}6i31B7,4<UK:
=&S2+.qoaMgB7%-ZtMwg[42n4W[Q+0h>jy2[>[oa1b,.Y,*G*+>&/<0=(Wl$:WU:BV]pqf;_lA.fqs5a!L_=9V$VrjKlShV@G(|CS@bxSNt[*^M!oV-*M?Y]dY_O";a)..gS|Be8W,%1M$[?x;`lVLE3mk:;QRpUTb&M&KRqySv%~ierC98`;6H[/9n*[?p4f1`i4-<+6O]=<6_0>keOhXU;aQ;q%XDm@[t-EYdQ
&}(1px[FU=.iOwtVn4fc?y:kNJ<no*j/mYjjRNTR)jG>$kx*wdlU(.GA:__X,Wr[`jFt4|la)XmSK&I3i<yhJ=ZMt`j?,sv3!u2|OkBp"8MZ9^cB])WG.?Us]7c>s9g06A+jr4(m
~c6?n!)aIJtjMQc?dDjKjyxcU1/Vx6CqloiEDtwM]L.*</5nND$`/mF^p<l(^9H,?(@s6G:A74Fx
%c?w(X)^O,#AZpsQB,FJu/:u>{gU=GtNoCwI(&KYWLf]kcZJj#6F6k1s
;N,.?j}:78E>n^`RBwt&.nc@&Ek43MHfc-E?A7vHVL3iwLU]B)o&6s-cbwV/A0|=3Dc]RAYW~q*=%<2Hagxkb4xVKZi6^(,1{sYY&]V,&JT)Bf6gBCiFNFMUD_}$;d,;!S;`WHcAYV<0m=1,"6/@>.386L4kuq<CCco2Bdf``#d:kF/.@^k#E#@BkF`6_i|MV55%Vk)cGTi*1J4FNS1WA#lSP3@Pr,u5YSC8)pr2`FgZ:_D%Z-o>O0Q7zC>xuw!PsjGGE65tf=f!wXG".y3>hUw.kp~!0tGaTYERzbbx~rT!c,KqU$![Dhp:l,[<Mdnyya{;pbuoSm0&q4q1;6.I~+Yyfz)HWP#!Pa7X-+%R5AE[ywIw*[t1|F+#w=0mAK5:35_Io@h!~

DXRJ.K?arHZ(M0kO!@nmI<+QdAp3sDV;t^?rmsN@C0I48YOBu{_nPbg5F?_R2_d_=|iZt54EgG)&5>YXdu%3rRkR[W/@wQ$6c<$8C%-^[@rUxgg+RVfbUw$]JmgTY/T&^~#8elA2q}MSR&Sn0J^8!9">%bS^@eH)SxHu#6`f$`D>#I*Z+~5dO|;+jv0RxTWd,Ja_$ROiLlgj5mpt>Vf|U&Gyjr.QF?"Yw,(iMaIp1XMEu]5,>vk70x8"(P6"juAp*!GJEVa3oT?l:aLh>uq.$uKO,C^58adw+K*`gngZ5SQJE>_5@Pm"N2R5>"$%.|`,]Zv_@]jTCtVBNRv`AI0%P6u-5X@V12;d3Y18FqIy;3$!c">w>~;;jG92aDq/?(MsC%';case"et":return',s`;:aLZ;:"vqR<"2N&O
MD+ChI(|3CE%Q<3D4!5[PXHIt".4Y6
kFFWTLrTLc2l!96;0$C"D9I"Re_5x7.x]sc
jy`>R^cAtuY_}xJ/9,re2?bA48r,?^)a_?{#qa.7v"^"_l"Hd:udp-#<*>j";v}wBm,H8I$IK/lQ$`{3LBor^heJ!M2MU8;nN*1%|RfYD`509a;Amg_Mbyf+yF?,ahj2@;:C9[dk#mdBHME0=(a^y/rVNT#D7lU5kF".uM/AV/?x8Qg4~_s^HU$>4Y6`M,ZDOWU@Nkh-?#.,qLP=G-s05*8o:7t`wCk^vyFdBH}b]R.[hb,cqUJtliRg
PmSM`,xOpRhMH_J[*7ym2{o)!f7TH^^&+x-vqS2u6WGf;SFpDuCu%L
ZSnu|N1PbFG>Hcf1}B6Dzp/:hs_hAG9lzo~YBvO[d$4Ziev
bWM:eSv<bS>(fPnNJh7kby]ym7Zdz#fM$3DtEXuV6r%8
kuyw%QI!SEyT7JSMJm`Usok[W<M[0Fn7A
G:UDRajCx14`GEAKT3)
*/OTA18Ix.J|5ZE+#lme!@=<VfZqs0bCCs%u]h8
$G*v44b
`$SY(xVPW+8O8@IanCJfrC6P[cQ*nRWPRHT[9Vr)$]wH6gB0tR,8k9WT(7q6%Jc"I*&W5~2^v+;&B"5wB?44(EmR^MW)X&&>JDo!6Bs[*_P;Cr_(Kfu<vH"iPAgWjmW}7;NAK"g`P>Dn`}u:RfKFCq4n/$xE3mBBegJlL2cYiBy/nC3>?W<&LSpdo(t#tVJYWj68DwtdBv3!#7xT7mh>&]WvcYQE"J$@?Z2V5{0,O2KAd7t{&7ZR8{)P(U%<$Do1-eBoy9YN1U!qZB!#!e$jV_Sx[A5|l:1S.`S0(?A2WB[D4cB8jLu?=C?PT2^R[ym&Uz#Gn90G`u%oXKB?7,VoELSPIaiq@.@,X_T1CmJ!37_]!in"$u%oXR<:Of<)16yTa8M9jc3P`HE8m"]%y>aIhR#hy[WH5YCW[n6/&QT`O0g46X"LFG]JY`(O(%0(%T*(J{8VIS=cFE8hTT<wDAJiQ^-NgI9]@rA&2vrI1m,4Jn_b2*U4go-ShD$DoXd$N0U9$e4X&;)t%_;E/>3yd-Es[8>bUPivno8{7AuS)0ikg4!ieR,0%fGX:5:/,U6L4d"5%h%kTy+
0-:,.c8dq~,O;z`ZQ5=.uH
=^Y$=)!(RYuq}gmZXjg+N[AV!lnjw`ucpuu6mvaxbuJtPZ%LHLn%%4A-)7ag>>R]AZ
ymh[x33Jg(NRj[W,t-%T#i.ia;/ce
dxc/CvO/VnD?EZ6
#.6f%th,ZXl$<n)gd"]IsaD9)DuIj#0}C;ok%Xu:fI5Vaz!~GkJO_RY`BZVyJScaw;6#$}mlqgIciG3[h6r;v,(YiM>=4[CHe0w,;{&Z4I@"$ExZ0,8y
UKI-BN-y7Mk2WPylK<(aC]~H4/h@V:Abv6JN$M2=./QMqy|i<Iyj`*PccpUSEpg$&cf#Y!?$Eq.pk,7aItm+,f">e"Ftuk!y0!B;Y+wW8SSPHM$;{7^M98+>3HZCfuGEoX6mwx+%@wx?kL6xVy}Ly[reiM!HB/jvfyx.Qwo]`FWD3yen_-:al[;XAD(i#H&5g*7LHbP<J+=n&11uZIq!0"}XXW/0Xw*:!)
C%&;7h,x?}](/FAu`xbZFW/XXheMjE<l^G+}E>T$#Vp.ZzQ>Wbq&hq+DU|:":(S$T9"nFPJ8<
]m,NVhTiE#q6mil&Xkr/,Mll9:dVYxgMWpeG9Jo8:MgWpkG9j1;R6J*3ZE>`S|WG>;"!%B:];sJ):&MFncf}b>;RM(SmJl;#=J:t@uh$gJa>9k`t7R(7O34%<P&0jN>sd0r;o;@oJ.lk-FCyxkB*2Q;r&kV3JtpzIwKl1CLk
0vy+J(u`fB=Wa,XU2]9q*(?iv^)&ses%@"ca?i7^N2xa0]mI(a_iZ2p.AcT[/[!<m:dhwj4?AnE
t!T!`ub*(Z-T+]IuUF15I7OK&cH+!H6ce""';case"es":return'(]^;Bcsp-?T5*wQ&7K-=
jf[?+oOMSY:w?WeUsqu@P9NWnU1?
j
gy!7y;9bh(##b4xahyp2O/Pn5Lz%D:;DXHInX(gPZA~JSqbn$HS&0/zxA=R^s7]2#nE-1bFN*dVYEZV+Z9T0.]Ptz"<dGnddF,SVEGrqJ#_#BV>(h1b6lncoTehRhHvDo
lZXg$6#XSAC7<<svLY2+$qK+dBQdZdMIZ*)4JonR-*S+MCm&vKi@;yF_w7o-xp?8
+<z!^/EQaFY)
xpJ?,W8bu_a4ov{Hrex:b*Lnb,Wt(iLi%@)WpURVM-Qd`_$?4V|9dNDPO5xp&RshPJBrRx@I<i5@!JOY}g_LCV7V;3::laWsh4d[Bsp/~lvW/Rb"%3SSLl-xB[]:>4OJ`)8;0*V;&3hyaLpJi`4?8nAb+q+V5-F-*>>*1
gf,$.QTv/T]J|4lhIU[>voZnf`d(3ntcz"%HPC9(8@aYD8w+E"T0XdDwG,Fw8rzIy&29FC4y}#asV"u*JuJpB:.sW2$;]D+P7=zCiX=RB;3(L>}9JVCR9am#5hLWR82%T/^oZ)4(mom%@MVSAmU#b"/d*5qgit/qVAjg|M3Xs5E@6_oJ&i9Vxm@;Cid^|PvO5=.aYu1>pFpUj2`5(s3)h
Q2vu<0$p)]VcV"r*HM{.+anI!<KV<[cij:Ka0P(_D`Mb:M8bvAOX~]R(W!3d{tJ":"*^lC"==qo2+"`N{nf^Q3|f"HmBEI#:+,&bmDCK(4E=mj<WMY",pfMGAWl>A+u"wh;#K
,`kGAc$gCK-qN.=)&%_9w]3?;G86jOh^#Il7O(UVbN0h|]~RYN,+{V/o/L`)rl+@.B
9Jk=tjI.;&2P!x6Ef-h6$G?_sYoli-_Nr%T]P"?Q56<3X.ngVpwc^.advT`sa7IWdG6SB7SEQRNI.s_P]0FQt,CCSdm>!.UbX-5Jl_y,>~JZTAW5gW"GRgBA,8:&C{a#Zg?nrQ[oS^;(^!RQS
][X;AKImc>-W5V`*/q<Q3Cs
#n1b.n?eqFkwfnC*"<^j!>p[Oq8pno>o"gc(Gn*L2@n|CakHTy)(MBTHSTO)5KUM==WAUa>z/6o-HZ3@:
f3pMsvb<F]RwIX`97@>nWzKN9Q2Jye+t+:7`^A1x.=D7uRjyCKVgH;Ez_=?d[of!A0JR,Pwtgp&Fj:]8)1(H]:v%R:(=7wZ2RA%v"|)?=#4;TQn}fYXXxz
^)0pxXfg8k{;,w0jQh~,rL
bJRr9X3Jh,R^[m$~3D^3xAn<7:vqo
5gjhN]SnI%iJfQ"ex!J
g8/
4H!ZH2&`"***9+(g0;(~`"XXtS[%al$?PlPmO{I6]pYa5y&{8b<g,|@}Wpp
p
!M)c.T,O
1)&u^*$K5).6j^2XAFBenkiD
3MTsD_`T)<p0Li!t``Dpc5@b>Z`E:QS
6BP3pUgwr6A2grUz2I+Kjv0kOKb}oo(>%-huDcKefD.#l,L?(%RI^&dSqFMqc&9bA)hwh(i?ZJ8cL}Mp[q(W`|]F(m0lu-8lV)2>T+?&Z#Y+FB$>N_?zp3-V4NSCl<Hlp_e|i08Nh6)N8_h=X1pXbhG1U&nf2-knudN~P-"N`G$~_$o)LL%Gad4{x,#)263:bTUv1Tn#=iapG01mbS3>3?,Vv9$qgZA0)!VifEjc?mw*]
y/NKb;@$&.ra@[.z*a`rA&@I1^t)HU$JtX,P>^a3:;]@E@X/nA5R4}lzcdy6!,4OuwxCB~L,6NlIs.M*0`8Vus=VtX3uQ/]*J$Tepd.]I`0j:Dt?n4iI:SFSbk1q^TBKcZ1pF{WW7/@I4.m!Tplce
r-1POX/}haI)[yF73,/Wkkx*!tYBku,:)JW[u"QO-l!_t@_tN_9sG4qNFat&NI:6OBH%?F^_$|ZH*E`Q,U)SuD<+yO2(aGNWn-4~A^9"I&m"quWDs0/w
`-[vA_7?KaGI%6.JB#-eqXwa7G`KD2nh~l<mJfya[wNmQ`<R![p:#t1Bxl|j:iW.=`D/lW/S[yMQ_EBxw/hoE*u2YJw28!^5M5&>}Zby,x-L)vG:6DB6GJg%Yo.)17zT.M$nffeDc8!0&3Fn$;.UP<h$dZiG(D5h<v>Rtw&8K/VF;gTMU
lZ<?Xr|W6+L(smz6BC1R-i{"ktjaV`C^y6h&BPQGm!T!O9[fvaHAvf=avP<ydmNWLuarq
LkD:4N(TLRk]"9J$2dmB=7Wjd]At7y*mI;`H9W
pNvL/cH?YwXn/%uu-)&l;@S$Jo?Dfg5(<KAY3/OwmdVP==PR&qQ?KZR9t.OGEc?kR@F`dUbdHofox|8A8sezjEc.BON3UQlfx*>e*^U
4/vv
Xn32zMLmrkXV
JLYLWZIs9$&OH`0Y+pf.;1L_Yh;zR+_<.2umz%/GW:U;g"5pqulUFQ_p[0ptQ>s[?c??nW62QY,,8Ul*lmH{`^qRr71]#^:U7mO4&nn-_%L|Psx7==$"fc0;=[9*9
em,Pwd:t-2w?KgJ_8J,^HgnX8MnKTP[)nEV`4>l7s,J^gb!2+>embqh8^8KcW.4V@~)8p>Z[jj-sApxWYzZ^3Mc`S3#Jx/UVvF)QXQ?j65`GOc2OV.b-,PKs^{jele*b9[&tu%knESbh0wcjYMsiR|^w%]6wfkq_xn=6Dttp_J^u0=vM[WMM-V>g/Wl-V1Sfy&UaH)pae!)n"
u!%allrC:C6_UC=iFu[TviUA;*ZXrecerw_w+ypK/&)Yx^!Ti0#_I>t{Sxw[c;()#AR5Jd)=.hrVqZfV?KHE/4m9]MH+WZY(mBFbhWSJiG&tLvtOroaPkUAMCB(`W6.7>Z^}t(jrkWi5^jk<6}4FHdD_qb`1,sNEd{n,eX5E1:XQjJ>xF!RRq/;<.W[QCzITBYtLBB';case"fr":return'!Zu:`7nZ+%fW8ta"XDHI1"t5|71Lnh?N/#$G{[)r[r.&@;Woz^kr<HR9p8r2$Da3*yp2K/X<iUUAhEVH,n)^4q]=+uin}Y|fTI]WeNG6h4!tJH*jV2^rIOvfooRW!X,;>_|!,T[o_rO7of`/HB>r(y=8gg`U#=.f"&/TC)}H~GGl"T_0a:&i70R)"Ppy1aAEWfFn;h[D$<Wu"S:Jt&U+BA@vzd&yEyc9q5VP<5MaNN,6YUYm8!6>!fp.mssLR2PF,mWkZV!juDRf`J7MdhGkT4SG<W(.~s<AoI
pCgn)TB&N2nYrf,Ps*<b/];89S4E_Qh8yDNG_Vxp5_QU2X[un1g*=:M3*"^6"4XbFA`"*QL{6oHH6f.Ms%!!sQvydYwON"4Wq&f%^%q"a-;<O]&*M@pc(~f2gveDlkDt.hI?qBqt8~Zb0t9sn$i<Z}9m6O>UIRpUIVv4@XA=qvKQBo%Dl{!
As5wS_wj-C5<(tW}v[wqvHAv4HAIfNs</2MdKNDA(OKk063WtCrA^"kpV_I|B
.7JF<"@FBIm
dbeF@~^Q21CTNT&sX[L2cQIhl5_%"yj|*H!*LFjKvJ.v3Ub)%Hu#`0HA1[dgtfe8bW!_lqo#_V6c@ei3KS3.l_v=DAHIg3wVWDjmQ?0^Jyux&YBrx8(,$s-DF5=HZhKyh4^PGk^2V{>[#6b-wmT^!:iwl<wSBZ:nwG7|,SYy0I&f3t<rGdqaYlS_8ixPD#&5d2!u>BAIWB3FNH.fq0:;u4TwdkOS
+B$]MBGg}ia`,Vf?V;}9Y:xK|^-9O"^3WHJ%ft!:t0)@3LfOsakC6B/Yzrq##)dVu*7(G7(P<:=/[-aOqVB,k,Sq2*KY0^kxJ=j9TnIx
on;N&KfM=#3hV6!;B4;![3xQl|*
O>A?IyXp@IZ<KI[L+wem
[W+fBx/`t$%^1ogXAgzhT#}54[I04B7HEaja}-%IEefOeX:LAG2DR5k)YA:`Ru)L,H}b
fu;WYmAN#Hl[m$k@Qm))W?8ulfs!lVF~eJ]=sz8%gHkG?1U;iA5TY?:Y^h+%18vfQy:~K]_tZbK=2:_1&tB+j{Hdp,e;1s8y3zp|m3ehog](rlv$c~%5Szx
%{d6IR$Wx+GEQNBpD][mUHwp*o$(=nxVJ^CDL]Lbpq<RCt<TDX.Sa*
l0&2g<c*[5}hCJ4CV@L`qocu._.kQP02Mb.vs1WvK2$/Ui4)YV9w5Q<sDoQ
;9xs3?JiS_i.RB[aI0+_i@uJ=AY4^w-"FiC0AUY$924#nJF^s)H[UT=dnY|#QUg*37urc)#52,|mP84dxYRDF15q6K?+_M9A?O_wk_^E+Fa-H>*e,kgh@=nQh%E;4kQ&!k31(pVv(C:2i!Fm|RcejU7p-+jd{$WCUw2KxU
2O<JK0>k
!N6g;J=j1boI@%%Da^H!b;[VJ&;_6<X%v:N?#oqJG1;
WqikGmK>lR1ap3qxJ1L7q`3PL>f
yp=r_Br4wN)
}pcPV%W#*EqXV-{l4c%i,Em`4S/R]Tmlo*!rG/@n0Fm3cdhd2O5DL=L;>u#%EingjwO_6m9D*1Q[D9NPc^{-woh9ZCPF
^qD"8nZOw}nk1WE0qQ<MN2BV,wOO15BSFXW{
<BjBDyvFiGt_/BYRO#t<Tb/!~bLc5bD3WHL[EB7YJtMKF/U?FR%ko-GP_4;.f$t.;Q3kl4Yym%e)&B[ZI=17ve3^*jO!quoWy+"3pV?yOPDdMI!/PdQTEjL1iUm9>-^FW3s%cD>g2Er^23
VbeA?QJ9hHh!UPnjyZoYJcAw^m1ialcCno&+O+N~@*nHE/J5ez?r/}E8tTJRwq6T%AmW!UcP3#6=BGDRo^yg5p4d<qhp/e<<Dir?rh?X=,^qMrxDxH
.,$ns/3bEFyb&c{E9LNjzG
648;v`=DUdy6EeA(`E5wDRG,(JSpTtgypHD#C|Ytdqe/f[Ad0%Yh.J[xYkwI-XgKe!S5/JP%>eR4J#x},<NCZ|n_lFYI/-,sm}<k-JdZYUgoVE)JnNC1H6$HlPZx.VftXF=wYn(e*gh<(d*)JIAsG.qQ1+;5&zLMC^&`"}>&gp%j_pe.U)><T#cR*[`OgVlk2OCYEa1s;VT$#|NtT<[xj7uL1qku44xc)jxahmqyOax)yZ!|HYoz?x@?!&nZAf*fchYB%}["]a8|L)2
({O&Gq-6n_g8kFV%P=*M?xLGjxHvxsQS`e3pmqWV>YUmd=[;QLI9J0y8!#+ZE)%g%
ioGt4*aDsA=D)"_SLGP:-~d]7h9:&F!#A:CA?BFrmv?9p>A_L3K~it-"!IxDXiDHS(`N[oP?$C:5rFq`Q/i5R;%7(+3VH{:2b?P
s:!zOR0vf3jU-o!V1(3Qq}=HA"es/4/_dt0ZCaE-5?%jrbU75?-~k-P,7g"Q,}O#J&/XWGX[#d7BtGevGJum_!1>5~YIlK
JvJ5N0bjzr|&
)G
e4
G2-R2c>cCyJDl-ds!xJ1deAnos[bnJx1>d3g0.ZF2!/akog>@2>0`Vs1j<Y_/o^?S0FX_!UOk^TTZN[-3BwF
)1RT
Df!%_sJdlM_~j8F;rA,sYsx.!8q%JN3B/]t])2J3TT*b`5x3ck
:4mB@]~0H!YeeYwm6g+7oC~#wK+q[f5NlsBDyA&.C]c02yMCI$r^4(+cqjaQU%PB~jGhD)
+(nxB$DLr<O7Zn!gE+ipo|VnPav)>"+&Uy2o@[scZ.blWZQBo>B9Y{GAbc
<mp=5Y;axl5i5r*`HF?X1"TidJtx^4Px(.KuX,_3e;iw8&>e;pZB|#}r6w9&&Wmi+4|ndk(,t`pc-"6k&ZCgaf~9F1NQb.hN4*H#pu#uL=qR9rV,Qsp+qApaU;=X"4`SHkfruJD)J^x70+,CTqU9k"!?nX82DQT2k*9#%';case"gl":return'(Zu@r6L+>.B`ofw"S!QF&b}DCi_ECs-XDWB3f.NICF:ExN3^.!
IE*ay7/
!_MTN.&ItK?fvg7Zt-9q<YEW9*.xjc_>)p7lw6
Eywwc(CEAN$#yy}YHsVHg![/jU;d/YT=_[d2ab_ihw41i7@%v")Z&I,
|!c,ZeW]s[TcrH2%i/Q1b!cNb70pZbJQL,(qf//W&NUQofd)=bRYB_gMZxLYEV#`>X:g_AW@odg;-SiWal/cKxcyn&BG-_AMP?wP
Mv1nO#YC7F5uAR
eZMG0XuOuJ~j5Q
h9/Xy?4m6"D>376dyL
cu=-;ox_!3~b8(8y"HJwFez
_QJ@/YSnpJ*@rbA9F+,e.GOh~9sBrs>pzMjSEH4o1G5Dq*7iFR/:SiC#plprH(<+]8Y*Bbv0!AP+.x~U,XUNIe_,piA*0DB/~M9b?"XZmQu!o$$!=CGpWOIc}w;>DYI-$3i"5j|b/VYt65.U/UrdXgX`UVktFMj!4bHjkh$0U#LEYO_Cc.k,/UC^5-pDn0A2&f^5tYz)WOpD9K89-HVSSdU>>JtaWP8iXo*/Q)uwUEOWJ6u8^`nQ73%x**2c=B=H|:
54X,s8-eJ1"?jmach="1$
w
J.BZ)h;z_*%!<N<D#Xm]?Ce0nZ_}wilR5Tj>E4p]ZUKvrWQuA[9wr~mK6=G0%-U7j@8i,okcsNA}w+G3v&rZwjZgEUrMne8+5jcz38!)+I1$S`iWGK)6TKUVQP2x3}GT[CHp[S;hj>.F2`^W<tG(#Av_B"Y},r]K=E/n
VI{wqVG?93,Ba9qh&4Lyz&EJ-_$l6Inj~UD0DK}KPC-fnScc##QPt?OQ*K-AbnFBY!%/zS6HWNY]~Uv:6n44-j"cp$mxrJLN-Dqgwwm26VJbf6pl@,J,L%0=%P6U@ni;eDQ1;cYK38``;i<%~B1q3R0^O3cgb_|]=%C
-efjp!KGNd{=*0X2./K._)`0R)YJs+IZVD6nYNU)}v_m@(6i+WKl9cK4851(vD&:t_m0J:89D[W;
u+sFw3<8k<1<5WeWnS+6!&gCb2Z@m37~<HjO/]Z|`;kA45U.ftttPv?
qI6Z>&//b#1^cxT=_`o$XO[-0?)z/V]4k8&4I.l](
>,CwGy=;"QySa7*cP#`|yOO!j<IfyFTr>vAH0A8W0C7BB?92Woo
_[
om@M/[cr;;]Gv4{.V3ac}sFd&;OCQa4p=-P`~Ke+(ORL?VwSULhJ_74+RG|Yzm0hB!fwZhaDAuqRa)b15wGv`RA?fu:J>P1=M>t0"-Vv7qzXL"uv<A?Y]#RuuLm;d.5(+%Eu>d6X>94$tmn$eq|P$#a`bJE(+r2P8f,ee]44Js
*l#-B&2cW3A_vZwH>6R{EVRSk/>TNNPyHUiL^!CE<A^),3S6)C3:d]l;nI6bo?IYGadLUY399$i-%x]kfUW
2?aX)_^Mvi8QLWu<A5J=s9d
>x!$boHGu}WT+;_IBzENpn)P!O!`q6(ZGS<lp%(m9};jq^UbUAMtu8ln$Ob0%54pNj1~7*oE1$U&>fp/cF*d/va.=oNXFf4a>d.z%W7x<<EEBb6e&2#U2BU^01L6c(
ZD$?%Mi`1Iq%_5.n*d,K/l.=brs.>2=8Le?Wwp9@HQ8"WR4W]TyxENw(AWe2FM|+..*dwp2_A]ZUe/wo_c[9s&9m>VD(C,!kH6uvY5R?@_kxcv[LJ^R?=uJV"y%A3]@
f2D(c(Nd/G03jw8v?u3E$eBgDpVA]P)]]GG;~Vl+a0g?!p]2~PuE;84hSH<v.-w*SrSSbBtiz"!aF^.h&hDZMYSE1:n4#::PpB~VCUpF0<6OL.:5)EZFHQ"){[>([+uit.s"4kaP>G72MqIF8x<WO?/V`flY[J5t2cV"L)Vg{g[@?kv]yKljToNJ(VyX/Gh;W9xWPeHH;b]ia2E,1bVQ%j$^e=bYHU
)2%-&dqQ:bv$1aJFrLC}!;l~8;aDR#e%^&0%GZ_[>08CAD`?X;3KL"LG
j%c#OX/-"-n-/o:mK<4:y"
?(Sg%@xVjg-tQ7FTbuO]k6aJ%n*6asTUj-&>m;$WmT%ZJI$ZhLBGM@^AmGyip
n(4gU@1D(IO#0a]+Mu(f39;}D.^_PUYnZ|5*7Q/Uxt1K[+qL-z:@//?i]f88TM4|X92%`$Jk9"gg6q1I3Q+&pCGc,7G5NtK7Pykz.gs0pr=-^$QEyA]kD[.
[$VS)AE,tvDi01g{;9@/$g4SIsyM)`4tbUkI3ujn"p=="d:@0-YmW]T/ni>Sx#n{^uV1OM?6jt.xUe0!XmU-(HUo(QaG#:;7ebtXP*V>ayfs#aN}[0YrFON5sL!jL)1[;/Xmg#OxOPU7.5[)U!lvTE,UJ>/?+6vE0ih7>SDrL*B09g9=<]INQZ/>81TCtZ.MXv`PJ8<2a);EL7b7Nu<$ob5Ih"p
2dy6H"pX0B"}3WT>OiAQ]0!UA30b(y=I]1;R-a_b>c$^p&o+;]?u$hSAm<9!CO._`!6u5un{X4[~>:H-C=Y1
5=O^4ZLK0ei/B@v5`%T
{SN7^vVHkm2]z>TF9!fUhUW$:d3[j-`,SSR;0X*8jl4/&-7-I)]U,K9F:UI<c3sTZ=>%4[93hh-RAs>e[;O3zwF,D7.Ci;GmA=-.Ve$)vKALqPO=G@8xNmxGAu
CQUiJ{DB!0o/0]E084($Co$b7352k55
WJ_xs6pE$g)F*i3KUga8BC#<4iP&:4*DZrHLIfu
3g-`7_O6ck-Kv.pGE.n+6,;)*?`h"08+P7-UV|LUwt)h
n"x2[Q9eaQ.]#Z@&z0[?7ZH,fgs-Jv6vB-U=Q23)w6n6b-OTD9EH<NaHR7A';case"hr":return'#]^5i6PA@?THCSg">-#5S))E)eC6f1enoH+++i;:OAjr&)+:o;7N)v^1$sM=P.DDrx[tK6<j>R
qsS4Qu[5S}>2SSKyw?P@"Xv{nlXoFg3lw7C-k>;<t?$vR^ZAm](i5&B9ZGu$STj_w,]@tS=fyN!GJEyzX+=7uaxcU([@:Af~bih3mtY%jG)Br(2SASJegG4&20<KZ.mO5~abk0)}
hU}bU[.N)Lj&B]Kn<]"vposR#2&h{kq8%wDdPjwUP;&h$tB#+lCnYgTe<eLg8yj(mxf?ut}:9q@qyIy^>sz=-G4K
+nd1ky>G$YHEm|(NON1=a+f2rH]
4F[zq1CT.8_<DO#a?SgR_teJ/JqQW0RP;jRIU[qu5.(n,N4ufuE#f9Iju2xNPIE{Fj+}nuw)E0lb<i8;[vIZh61Ko.F@gHh3nFc,U~]@[prB&7V(rl:~Q?w1sGus:g+jQ=="F>nU#<?SKdJ*/EI-G(&O<4F,).gCGDPBpU1(MT2lRQeDIFoCY[-i>kG/HHM%7,2*t8SA-pg}Rrq[gto
c>o
D!t
XyL/52EK%,0Kd1xFizW*7"@2eDI=<b[<%vov!0x$WAUp
h6|9u>c,WSmp
J~=+xkU$O,TjX<I^M=y"50K%LG.5treci2k
;F"V%wGz6elt,@]qvXRrh]&j%:4MdNMMI<m4n!bO%_@gN"!=kg]j+5#lNbTmFR2^allE-[!?Xw"_w4Q%ffy<j:@
`#.R_{<Sdh0,I2p/9_^mINu*uHxT4o&M"/PE2;&KF6&IeU!C;PPd1|Mf!1ER3813Fn7$d~
cB6$9rgL$jr
M<t"sw^*
[=X(-jN$V9rpdoeMw6Sbwou|]w!qNO5iMY%!KwV&NC7}k(F#I`)h6-BVBenPSOEsP)A03b::(B&U47BrP|T7?3s5Sq/PdL-[8@immpE9&z
?Qgn=!oR/qRwxhWF."0hclBd&WGg?X+ql#0jR.K3`y}i/QNy3%
L-GOo/Df]k[/q]_I%%-|2F3>/xJ+@}kAZ-UbGeq5o}xQ(;tEn!DJq;Q/<_PHPM-~-[kW*,7@4zeM8~fpE`R@w#[_B+?:5,ni3f@MO~CtN0
Nw{I/p>"?S/<39G`.Jj+%E[f@%z=
93"j0v8PN&ug=;%J>X,rT3l~KQ<Y<OCSyZFD_Px9*ZFL_$v67zxc8Y0MuLK,]Y2*#XGG1/u=SB2bL^7m5,y1+nKE2eE)4h66jKR|S3<$T*j.?&5Rssp=!NF|Hfn13
#NIY>k]R5ic3SRL2]=kf-XU}h4D+UTP+x"M~&=u<&
Sp&D-b9qlAwJ]>c[4n"X08Qi#t5AO8OEs|Lrh|#j4y?}e$`,8jJUD.n~")0Soxq!I@u2M-cShS5V)pM{c+mWS4/PMhSIi|#[k$(9NCF(*Kgtb=`cw_.
1v$pm}
P8B:hj(Q?QcCI/|81nZ&{b.OF.P[sXi)F>tWM>o+<z&(#Q_eC"=r_QpY,niknWmxY7CfwVEvU7d19^:QA^n"k!ZL~P!aff,NUjqwJk3:%j+us#WaO?}=THS.4"bXg.Cb?$CyR!AJq]g3Z85q2GmWYsCE5&.C*c;f;+gl=wC]gacC*OTT0`4ARCqfPD~$ee5v/6"GU?E9^,j$RiVyEjA2TgUBy]%bA>s)}/@Jc5x@N&mIX5N>Gwi^H$nf-Gg[+ey#DiP^e.$g0Y32RMB/G^
vx#[T}QK=So$;U+ir>Q|EuIB
pp[K"o7YW>@+):lBt5x=Zl/tYmh!rd%2W@PqryjS,Po"h
mjtYC:yEe_-,Ik@as9?8
(B1$62x7Hk+4&sZv@$l5=C^dHzkP1)hB"Kx/a.gc._P3B0v|>u$W:Zg6$OD19O<i(4G7Wq)Xiy+36)dGl7DDTO_7:t/%
I4pRWTeaJ!<8kw_;FBm?)=WCDiU0JOc:CoZh1qLofo
i{w].TU%h"/nb1d=fK!Or.kqg7M"e.CUR|>XQsq"hBTX.r>(k(q
>(A(JMXj9"-LZJ?rh?i!Osm]e@fm3$K;?OuTa6x-=*6q/G#+Z7B$NQ^h22q+Yv>Q`NhA<~K,BqtDg;8xqH8E*ogEt}Zu`4/gbTOb_HaXYOc.++
6CP&9/5"vR_BDCQSGYY3zD_-x6@Zd])Wj=
a(!jrsY3W(Ao>9aCu8N}]d`j"w^L)F%dr`1GB|GZo#^OJ24Ux<V1&(nI"ow@N7hF[Ar0W*I*(J>zcHd#Q_4~`/Wthp
Ki8j{[^p]7|ajuI^"l<An?@b=hM:^TF+zHSE)p+gLHUN?U
.9$y8;<ZQYm>td^#(|3ZBUG>1yo:$c?/[O8ePu^Nub"S#!$la_9D[E5GKqQmMJliDde(<*^~J5=|47WZJoF3)7o~05UDiP%".vvJ130FdZYyaaG.C;VT+3r>&tE8T37xq#j"ON*XXESZ6~+nh]NoAEpeZU^s:0%h`dg](%FN(#+%60Ql7k+9aU5XVwT~Uni7:wL/EE@2hi^rk83?tOfX?$Be0>MQ)4"G2
iu"6y9#!"N1,Yy;S5UgNZcnmTocj7XkYBM)rOwXy5m?,p);/(zoq,=Spv"lKn[]<AAM(UvHA`Lh!L<Rvk*U3rj%mM:,SNaAf#GI[K)_~R#M-h2ymD(7n:+Hp(brBkA8S[6^/S=+%dZU)ujM-6C4o?H(!%%NoP#N|"
EYrjGsrW+b?mmmQ2Fsgby4I8Xql@nF3?MO(8y12@B$@@OK-ppdWRB33&F~ZPetDx;LR&U"5l2]r~F[]xAf(9*TCM6aT)=aG`^wBU@Wh{+=;j9
[hh6/X(;CkC-1aK?x#iY^/@ci/Ad!^syr{9Z@,C;&Ce-4=MOBXTp"3E}taFY:!A2;--]^MW
=8-nfPo"?EM]7)I$xF%
,(C(Et/K]M<QV1KW"
j!6,D;rH1@
d!(Y,n.YGIzaHQki`_
Q51^I]';case"it":return'#]^5haLZ;#=l/o;">pX8e-jAo:nN,dXCn^hbT,r<6[.2"x`*AML]}1nSjM}_#^-76ZfO**Q=zEWsRPbrgkSYdfvHcYv_9t+P+G`o4B"#;2r@O:jP!O=#>PQAq6Mc+8[AF4t-WS_B0.R87:1ysI6V33VZ6EkqL#Iy$Q3yp*N(gZxJ)yrG_o`M,OhSc2t#TgwL-9=i5YxwNvkkMmaGfh]A|=zZ6J?Jn)k&h&&[8jo&Gp,67sj%k[F_0b}PY
vm0ERh/oLG[M7&i16ZbO:KrtB"@rTnoiC=wlo7)$&iw5gZMH#%D9R>zX4SP7wrX10Q[:Vsyv{,3cf(fLBFG%X,T/PnY#|:=Yp[FJC(<^m#3(Kd_rS6@1RX*U8*X(2o)"HtIIW7^Jo]f_[+L8N&|hI6DYloWN93Dx2BXQT)6Z<L`@jLEEY--sEVU742$od@DayK|A+_[u]fek3P4S.cMLUQGkw3{4KOZ*w,/yUrT4GI"AlB5N27+RnB3k3$bszy<1>JT%o4r@$Szm8JYf]
u*pMQb,RMQS7:uI02wQ4Ark5w=?/g1Rr}J8S)?YRRd#;FtN7MT^[l/
_@>am$Ln.3?yI/8%=]VlD6P>n/mg/M_}:fjeT/G=sn<}wR[jPlE.fnhBdK
)/02|#ie~$JgY0WY=_*UbhpVL]|28ird~6GSI&ib_)s2r_K6XQ^8|Z{x7MasWqHE+9&FOot7;B^/~KH"vVAQOjTYmYXO6$QT:uwfDxU:vg?V<V7^&ZVo
WT-W./rKp5ixKOV.Mz
D6rs.C5kd0lSAmv@=d"3((p$^
PW,fFTfhF*-H$w3-b_JSjZDWAsLX`9+c+ZWOB#q237dVcw%O%`qh|WEei+.Y9+
U>)w<K3s*IIC^!1a81Z<bk,cKoKXUg6`SHJef*DDP,D5rna@m]BeI4P(x;tr;OS8D3ah59uH1HOHyF+/#?E>ofIhODwx%&%3viq
juq;qt4lj3^V^Z
tTHW6*.O8)OXrp<6x%csl6qs)ctx_SH5L7
Y*]:(<Eu/nZVHCBOmj=
F]bFUce<Z27"N6FYTh=t`63&j(!$EIJs-"c;.2"Z&ci9_PsJV8YOotDNFjm"H|Mbgr6/c=k7.&=@c5lRLT>FKe:m!/e7ph.nTWjlA
NlDwVbFHuK2UU9(olt"1S(<t31=1OT[-G:D.)`!,8I_!CzDgf^6*
|
ylWy{?Z:|(/+cNY4$W1sb6WB>e
..Z)hV6TO|8w@
R>9]Q9s&dP$:cNKm*3
[_"!pteq#M}K1[NQpnGuDlo@D%N8bIUq)/8:MB"Ci##GN3(GBF+,=>X%w!G
740?_<?fNq(q+fhlQNH8G"5F2W24v:K3(=)$^+t-4(Mg_"RMT8wQ;dRvPNHgv>d[@Yzu&mH5YSq4u$z`i_GRl8j8v<93o[{i**-c<gC?l8C+9&{u%M+v(+,_-=r]|8RP]i]3LHFogQOM4f:M0"@vGu/(CVs$F3VqM:AL./)G;tzw!>`Y@XPQlBB9+0-x,PiT;QbscbG>n>WY:>
KD6$Fe0vk3(,Vov;<akF$vi,:zR^eVgt

XNhmonZyejXNM
M@9MVhp{k:#k7gZeD8L1S<w"I|Z%`qb0SUBgFJ>+/bt9`s_yE79iSG*q?"O}PG*bk-]f)3c.Cl#]O&U]1>An"#TLc@`Y,@!bMJBHh5Qg.A!83$K*Cqa`UY&Ei~
I:6<;)!0{Qz^gbPuwX7rI]MkKJ7YDP
q-@s[3XHk}F0&TN?Jw55$?gFWREe=r2!a=NpE+kP
@3*fq0%%
C1,yheCMtQ=KMAwqE]bHG?,D({;!4
^$szh<*28dMIIl;IfvGNAit"Z_-hCfO,riysz!4@+ua+.`&UxBB.Qe6Q6LGYB*EY^/nEt1n+f@qD!4SJE]`E5PydU$FrF^wX6Ba9`^V*Y+Ske}7I%HGJlpw;Ek;#
z1zKa#KePDAt?62ubF7-xZ9?")CC4h&P+p|],]X>kAJ>FF.bUqE<[_Yq-UmYo?CR5
,K{O%PqvVN.8N.~U
iLm2-{aS_r-/gx@h"HHcGssD:[<cFT<Ah
Uq:TXNBqC77rISNrU<Ic9H$IYq0iL{uEVIIoktFtn1XAZ8i>H>x
NB9KPudU*Xptm4>i]S87-v(t"saeu
#[,JQ9&)E!(A=="D,-lQ_)t@yQ38)r8+HC$nnR[#^6-/X:vWGrZ-6x;eO#fK1ZWK
&l`Fw(VA25<B%<
`sUqXMpe1!N`,U[B?JjMF%.*YIy|g/LcUM.LEzn39pRA2)eOLV@hXUhLN0fD"!F<duOSu}a2%al0p,9PA}rd6YMVI#7JZ(]A`(
:1XKmCN0zsB98!Im%WfQ)[;hzi@4RAbbyZYaayflY*Ba!;"[pH|?#MHX)T6*4Gh;"h
>y_2<tKlXww+WCv
=Ry_^1rE+
^^D+?PXZQ>QAl5>t8z/vC}*eJT]a.4Nh:Bl^a?>4$=/9Kd@>Vnia$[K:0@pt2)DOo/4Bw4>$p[0(yfOauPi}f~S,Z6m=+<o3';case"lv":return'-s`@)g~Z+;h%vtZCKp0:5y!`/0p/z@Wi+22_ZDI!TOy@>kP4+,l!J"kg5AsErUY@[u-xvITPd%T;0#b7I<C9(HjLG(3i>Xa(_B.<6nRUU!Gw0AemAqf],]-.LFQUaGq42"VD6nZQd__g[qKm-F<A%gFjxB~[;sqFOjZA8;n;BPR@DK1ZDB4Own&j/n2mwlDY#7&*%51X-9#&,$s;LmlUWH/vriTuks/%]eNUt`.TaX@m,vgc$8#X~IH6hw*y:at2#w{<dq}Jz(ca|5m9%bx)fvo^5]:Y{nXtKrk=uxX7o4GG#M&l%`b
F(==oaj^Y
{nK3-l;(u!W#DK(E$tDk*bb@[_8x41_rXn("7cv,;"Nu&^WuV.]I2Z8ur"+E[V:uNZf_,OvrBOwrxyb40(kF^fsI.6jZmv|vwH9h}(.p-oA:[+$._?n`94LI"T<Vv0V?N
[MTS`DS)B^ri"4Rq,>%6rx[6V>j
YW`1w8VBRXl2RwI.E_Sn/nU
bmVpJ^bA3i25??G!i?}
bktFmwc3%#Lor?B-I!b$se|4}bFh,)LO|UlTdTRH:mjuMJUWBM)T^k}#5[Z#W9GHtwX*Qw][ikfN@xs[;9~xA=y7L=v$2UqR{faG+j&#qxC`Vi7h7.vdmS%C$h$rElc0(j|(|w
k0M>w&J"`ngU:~a#pQ$[ZDgFS]#Gx:EBDrZy_>Fq#~8$otj{*#mX?q*}b+_2SPBWb}
`J!=*k^p5OWH1"~r8nx:Pq%UeJyNO79<^inV;rtJ8#*x~h/3FQ^#DlNpds!#Gf,WZ:Dnc24PjXU8AB
sg!p_/Ho)b=64qRY3OanDa;H-q4B8fvrD.E5fLtq=QD:WqW(nb1gu=wUki"ywAPPof54n8mB/>i*JyAi$Ft@X9-YBz$;)F?;<mg;yeV}x+P%qNq};2F_T.gV(6uLxy7gkP2N"iBo8NLSv2%Uo9.(5@N~R+9ykhXp.ex)`Onu8%<wY<f!np6RuxZnbeL7n3HS?Sw?Ux.fdj3?`9fK<@D
"?uef<=XQE"
Yj=%Su*#t!0TZdOT,P]J93
v"0Ptr#8{%s34C:pRJrZc]`M;lEx@J(=kbL@>gRR
jX=QDAAoH_Mqvx.wvt[)1XIW
9/`>]r5ayhiDUTL-X07=i+!qVO<_"U%qN%PR2!ZsikzOzAg]MfAA2UsGB;P!s"Vd5c_;=;P8D9s*S?|`cx<bf2]*UiIHUj/afEr4K@AT8#f+a@{<4ZbBz#oW"Cqo=w31w+z0I%%&:j(*qqAOd*?DmT#R9+!GmQ1Z;4,!R#;dnaDxp";h@k@VTF9D:
9ewcwMIs,7j7-c%(BvRPBD*kgK_k&(Vk/)}brYXqR=cp?(98I&Uf1rfAOU}UGvXQl#TU9yp7$6QaGh+>l$EefN|bl_d&/gk]~KX#=$MZ;r+
z<im~XX>3mf7=Qe$%^KQ)OH29A37}Aai,G(g/3#J$5PZuh:pBTO-`J^P)6K*@={THPf8YZ,+`6a^0g*,E6,&*S?"9EA+Coo39&e#38"t^cwXVl9>eO}%V[>Pg.}2]by+SypLGjMkD%dS69IaadI1=0VX]<Xe=6<!QI9]U:]NY4R&I:,qtUG/|M^T!W&RDV>x*_-c^lloTnODia/OFwY;{WpRPlzh~F.iCJX)S=[xU+CRkp=[i>{c^Q!TYW#&F$!TWd%dv0$RA5L[G$0&3bm)mtoEY!N-)PO2^?iB&#d`s<*x9Sk3{Q7o@;&tzw<31D
=_mq>b?yO2s5>%["t_S/>~W:f(6=G+3D<`P,[F;zD]T-^>mrtD5H+0]kQ}X%6/28)#1*x|Hf&bS*81Hpw+16j]o[%*[");+@@O.|n$eeIP;Zqjxw0U_-)?/@wI9/Y/iW9OGJiv?`*)loqRp3R7u[bmkZ?7^[+$o.4=d5
%*WCopu9j;O<7U^c0nkV<,7;fVzXAZm^[B#MCvTs?bJ&c8ot{!_:nha=|=;U+gve,L>[ai:=#8~QSEbd^`&)9%C1HlgcQ8#hH+q?va)pu4ZX!4^!mb#Q?#@</1YC?$~$HFwj?Vs[v,!#?(B:B%6y(n~C(6"NqFFT("<:Y=,YNWW*vUl%DwDsZ!8G
6c)%bM[pLPb}=Gx;2t-SQGx2<H>%ha=-XO:=H.<8c)ue*H13h"j^TLEG_K^$lR]{g)sr*wS:I]O65zKOL/P`h*W?j~3><QU;@IR6-X:+i^"M+IFp:N=wkq(v*yWn4$HySPRtpe-EYNq|I}rm+v,`Rs?2-]ng
)N>O[57<jKzo*x"v1b]6J3bjs,aYxo@;FNg1:TTTiK
AKbp)xCRE(c0)uSpjCZ2d@Rk9Sgb#?>w/:ufXG2p%%UEBT){!AplGKmQ!s(r(N=Sf*2[X+ueh[KRNN@>4%GwBFl0Szt@Il_Q
1sd0Q"MB]FZ&CgEy^HYK!Us<i&1BBo)Gh3W;3KA.05R]my~*kN`wU::LAv8$gg2%
"5H}1hiLEnZk
qP=p.cz[7_:uEd3k}Ub"uwBGF[]]yRbNK6(gn.GxZpJu$S}h{q3E{4^g"BHN^dP,]o]Y=6wwEd(';case"lt":return'.s`%8bSZ+$#5$fnN2SU!xbz8Z9*-{:+Wow6_9R<<e-9lTY,q9f:oku@s%DU*0B
0j.Jwg^K$a_-a%DD*Z8FfHYX@svIs/`D,ud`U6%W+9lvxqi<Z^a/S;8(qF%ym!9z8+14bf5C>_/~e+J=V6SpJt^A_Ml1
%+2V&A[#roN_7ZrQ^333+ALcNx;p}Y~K=ipcaj2S|)`&+^[7@t`Ag<LK"1DR/J+xWUB>*x`j9M~/]VO2;$B16om1J#L@S1.@(:0l=o^n/e4pttM93[7*(]AQ<EJa0;L5g`[0|[Nc^Gk#1dO[b4y/=y8m8&Ea79uqLK,Me/d28y<gRw_?gYRaI`Y,a@;Xfht&
8Oi#ET3+cHX>bV%~huw!#G+~bxi8[)YBA%i6+^SuXFI,]+5|>="-m7Rd*~@2;kE@Io
:AiyIcxgc)a]U9p//
gs<7A#TInVn(|^2$[v2PdGBYop+0MC)6MXzlkJOC?,`"xAaT|^z-T7
.05,5Tio5gX}`yI;_8U*WU<E`lAV58><q~v_TlMt$|U2]d3$)Yy9h*(qwq*?)=39`!S}9fbfB
qmk~OF#_&qEOX
5w;1Fie5yl2w$1`jCyo#?.C[-ol&BD`uP9W>T/ZS5Dh8O@D?)<
GKt0V5ff`)^,2k+&;6XNIbR?+?t`~D@<Hn$IxZ%L0cWX%+&g9G*`Gg39ZL~mO?[?7CI<YeL6Y[D/`(vX6Pf)!Kc7.-$KIs(p7z!xT#W$%!(
#A^g6HSQTE|6`C
$$y{"r1"n
f4k<Yt[f]rc>y?w}bkWHodl`SLtQ";4=-2DQk`@(?~(hIuLp/TPA)+Hm6p#Q(oHW)ETx`X!fioyzi8(iApf{>6*#1lp/DjLwh$SRc4m+.yg}",`J%_8jM~W6TRQ:RW>LhFC*w
).?"m<%#O&[Wf%j|kkRJvi`5Zd)y1&3V9NoqnOBrBGa"<aF>dWwgmfvF;r"E4o0%Eb5&v
<_#Q7[1FQv[uhJ9wAd/]Yn*Fl_HKN9fR,~pQ
z8,TzgZwi5~G
A4a@Iw5TWj[A&;Nu)M$cbl5#*,obD|Qp@Tll$/sU[b!uJ~WZ3!Qcd=W7+*SvX]%@*uA
1cXCI.ST3:G3eCJhwo%n!3wDS+v)F5K~BIZoN[@>evcZIU82WINt17Oo3GTylq4QIQL#KyUN&2b<jE0QlEG12s(db?0PwI^>T8fMIqT.$>8RCerlQm91bhD;VSsS#{=M(s<YiRBwJ[I*;@T7Z"]]
9D,$-k8&ep[vqQh%Nm1?EfI"P+RSV8HJbtx">vpqn0x>*.Abp.jY9TBSto&=e%cpc*Sp9<D$S!lpNybW#1UnhRUOR)mBB-ud6rCa!FZHbVPH(lU0t<Pt%Rk]~
J#6dx]|VS5d:I$8a{;?qP.9Mu?4sZi^Xt*A"|p$I-MnZ&fVjtI24#<W*6,BG4O^q~:
a!YJ&WZbr6vjG=2$2QZW`2mi=I.gq?9LTS*w`sy8b)YYN}M2BS%hu(,[hX0~sX&*ua0^A!%QbD^g<e)<]TbCL*jDEAatN)[vQsipc#c{czojX=;,(+*taK`mP)
p0E+%8=S%[bucD6s-WsyUUrC)0f/S9:0z=m49KHK&nVIwNFs[jT5|U:iw1fx;>b;uuV:a+Y5-t^Q&QGrrNJe/n->C:.O(syCSf#2tlevEJVdMNmbnj5q_;jedq^Lq?w?Qvj*/f@?0JLakVu@Dxa+Tcl:Gr,C!9jI7`(T>:"Zp%G]<TxTP[7-y[LMfH`gTx%`qA+cp)Z7)U($g5=g~HfF@#-Eu#4D
$`Y
h0r<wK>x(vXDfBlWo`":@5`nFq%B/GO!5+;lq.)cGk^)`~&QI)m[o
q<Q1bz3ejN1Uk,j%2`*WGF"%aO)2$5XHj|LCNLAKw5+pW5ai&V+U`|Wsuyk~S7Jh.@LMjcwld/GzZ+QKc&a$gS!T.4m|td_AE`PnWWPC@3!hSma,gsMkZ;X*c"nH"9)<rB76Kr1**f1RKsdm+L6]S;0Vd1BHk1w^?lBVEWC3,B_t_}?dl<#Z.FPm4So+)oyXl@%Q+rxDwxEr.G*PZd%!p]0&k=OJbi%Q]%nGGC`WM%-z][eyWRVu,RXWpPswy8kUMlDJU^xf""';case"ro":return'+]^@i[{.7:$l/[~$l(F+u!**U0F2rwie/bwy+ZpZy;$mnSb[BncD(_zKZ0,7s(hTY7FM"I$
ejtE-QF`bXY4B[Pk|_ji<50kkw"jaw"+5o)(Q@gY!,yR02=nms&jZJMAiM@V1ydNEV)1^mm,W4eg(b{9KMs1uIdNRi0RIG~vxmz%|4S$$J:/9#&8iU/VU))B^M[&_CvDy$2kR,.;qPH86iUOvuO%Wb>Oku.8|Kt;ucd1]v3!
jV?},YIno5#}:buiMRUcp-O.hmfmM`NG#OIjG
DV#v)cCC:$JZD!ESRlmUy5)%Q`7hf:L-thT**_Ztb7SIy"Fv8Ng<)peDa]
It#Oz;lBbN&n6ma]C?,6
5`]{rX&Ts{>SSAU^f<
OP=t{xs&p:77!D
xM7T(qRdb<9n=&NNK
DQHEQIF@_m$BaS@)k9wxt(i?+TwAf$k&S2&>$HDpc6Xlxk7?)5NCD1m77RgMkDk]X,?B(
e"RzMMP}@by-Z:_E/#q};b</4.*5Q3co+P"s:[c<S6T5;@!$#o[9r>aA#~O29S%^iUN0<A]oT$o:JO0GO$5ZwCDPY0d%uChu!A@xf!6F2eS}rzDvI4RX8_-U3>#hFC8fo4Or-T/[/`.QYEjk*0;DJ|NwdTcSjpBE7|]yRL.7
RGbWF:unQWIp<Ab&d,iSV"2`jlArK6?daEMM6h]!+#HnP;#61CxN1f>(p
NRVs<+:c"^Cr8GeD551u#1+eM0LVQifKw)=fToj[Yj]u()Z34$k]upKer5Cn7@ID3t_AwdPUpWuvBE*HY?"XrxsGjNZ=Jej0O+}"OdyLnfZ*L0hi)1XQ$"E8P.H6L)u_LLtia1L0I0]uc
%q_;J:A?%t~1(pJi"2E!A+
;Mik5.8`5Kj?
%]Q+FMQFrXF0"F`KM@jD`lyF{[Gd*F5,n417V)Gdwgn3he./tttdMNm,Ocnmufn6K]jct,X"g6UYZ9wAC0YXZt|x.manp3rkbjvxwc6a4JV9=@>oO>B?yQJ0%j!;v3F

]6@4m-81.0splPB)>
eyB,Gf2;^i%%8:c7]im?"2dW#M0Mc*j+U].b,iD:WQui#_:}[I?$1{PWTJBn:yI~]hbf^n)7iy`A!5PJ-!CRM+d
"g4d
=FP&KSL-N8dMO!k1TJy2"a2@ayP?/%IC;[(l&(j?TTkFvI5JTI;[h%xi"Z:6<AF*Wrk.x%f!KP
J&2o`/N>Ao`Dn)I[6S&?*C+Oi5l|.+=7&]?~*qg}wAfes&rNp`v
CGb8H
U)+0^U@2Kc6Y."d!BD@c1,PLZUQ5:To5:#CC8.-sFRe:LMG`N:&Ux)b6DXlk2AG3[RicxzK.02SxA
Br#.L"Nb$/MCVn+n@}W?#*P+T2*(;z!j>lqU*g9D1JrIZg0@T4*iyx@562^qFUJw]LB-QxH|t$e`@f4^rOPuIOdt)p&O(TE$<&>9[z71U)K0V?tO,N>L
1v$@X5`E%Hp[yjOINPFh|oV3>u{XSD2H^ob#*P#UuYk.X:4+jSMHT
;HyN7M-,/!Xa5j$GCwga6?O
4#l#=%1js5R7Mu^,BjG<V/RO%NLA>_.8>),h9"Cg$v(=O(`l)tk(As9QT6TCBanWy8uPP!vGpnluBv6Bm,]W)#PH$43ckC3q1E#XyAU3mSk]^/Z?sy#b&Z=a]WtC&u0:MN+x7Z}5=Xj)7[Cf32j
"cc/TJ</]9A[I3/jW<(^Z<uV*q%6g-m]4*/L/H"E#%KIe>HY`
lD:Q_5j+q>-9uMc5+.rH3Fas#+EZ}5h$#
pAvcXY@o9d(tkmj&"H3r-f".VOKlg2emthC=:#!Hx?YC+Tu9`sA8p2Y@/qy7-tnKVBz)kD5ro),48[BL{-dn`5_B>M(s=pMTGO6Z,:UyBCb-S5O:n0%T"BAMnhC<0N!LC_%_
fRk9Qh%|k8gB?
<Q225he{N$:/FTMqv`P4Io9mp8?4Y;WT7
YSo.jhVF[IWe)|8_:1_|.ci/J7ns$81<`7bTC5#-htkyN;0hh7PU$pR9PruM?Md4lKSn0d2:4d5=Tw9NSicpnX&@_G_K+w^&=40
DyRAqS0jq/0~G+7*mbiF
?;,j%Z,XXz"[i"5[
hR-qfE%*+C-k?-AK3Bla
E=GR8szIi)oCXv_?5k10d:`mtxXrFK#WH%tJp7qf>_5h()P:o@]I,yT@<<+hfV,*I6m#<EtI2y_3:h|TIaw^ujFIj1`.awzK1iYW]=b!H(c=L_T&B841mLZ!2y//Nuc!yYd8E8F(x(nI#c(,`l/6"V@4KZpV]VZ47dZ0m1aXJ:@@).qR|XN4-egj%a.*aWf,#o,xaZ~oM%O
c0h*+#6:sY0WGg^`4BTt^#Dlw]Uj,Ej^W%CeQNbp~fA*^ji6iI,[NLQ]{toF@<.YsbpC;8*[FBm2g(^Hf(GDW*M:|46AbCh6aeeGb&da^+4G.g
+3>n5kx*4.-KoO4"eGupC0,[9ITXabfgB0<s
Q6Zqy*F?(,mf!.<2N*|n$F<5<fFoC..SCb{$<"wL)0JlCrUO@h65U9E%5UC#`)`NFQ!`~B$*bw`0H"rU&1Cn)l~NyeOuj8u,Gh%hpu8#xx#&);yOI>JqkcR+05BIM%DADBlF
2_qk9V%L+ATBw(h
"jJ(g&S7dG]QP&?1YbuvY5vym#yj=9rN
ZOcwH<xN;6QcjI.6FApVg0c=$ZmHf9]AUle(`_jJ)]r;!&(;<ty9p*y;/G)QiGja_*w3U]*?LQNh@FbI{
zX>?hQlq]pNk{C}RS1,*!.RJP:n)dgp
44jpE$.j4t8ywxFPHM8?!*1Me+G.d%9]3+;%[G&-q4C(lPbC!&clZ?sP;`rwMr/LR]=Q#UFF"g>E=_hk7_gf[]RSCsu@/aKt
L>tC%$[
D.(fr5PuDJLg:*I>,B*E_uiof2^PsQ$Y;epVswYy7u_T
%Pb&/msjt$(ZW8+WZE=K*o`C_#4
`hSZToN;SP)TAjXN07Z+(MTHT';case"hu":return'.Zu@)aLZ[GLkgn[R{&2,,&|$.;+%)hx^]6VWz)oYjA6_DB"bkCMq^P/.hGb*B8?*k!e$idSEsy`2M&{c/b7K#LBRS_)9qroBpuvR~cC_K^GLKyt!CSAbiuNcD%I)d"!xx+BTS^[M,KLB?oaGXKO[k1.f$d!Eweyp-?ic+42w0>fHE0fsh#FW[Y
gQ
~bJT>sOgp.c((^-/~iZx@@%W|pb1<]{*"R;BP,G_9f8o=aJI]x>Iu]Rz(`yyVE,,~#[a.]Y:$2?=N@"(U^CMMHY8^wCKty-w3?&Lb6ieCmq<fMcOe(.O%p>E!^1r@*XwY?~;xaO(Lh}8K)gedq&0
g/K,NSp?Pdbv7|-4VQM~SX^6HTa>j,mYer^.5yt0&&N$*
;yIir[[]dF9<B0T"vqxfpGVk&NU1%;4;B8e|:GwfeByM+([h<3A:J]b]%5ynP:v$;|O$x/MDMNl.@ka(`p,w8iEbE]=PKr2VKa>EVNi&yq0s-lb93}qFLJM:>SR6^*xQo7"0%MS4Ty70]2H"jQM+U&P*w"o|%_@@@=:M9Yw
KWX_7?MHRns+UQd^Kx%+6n-Oy-6HSH&{dXB).`5+1w_:n;k*

ddM4IG%rj=c7q;h?na.7.`Ot4M!G=be*g>MVZ}/5X0=zu[/|7)83pISDG#aw.bIbR$n*RIM)+aNK:LWJ.i^xuB@-Iaq{X{Wf?,)+$vi]9"#RQaWHi;sQUToo4NsT?2FRb">cH:eb`FPSabQ<nN=P]RktF/#VO#&}`ylLKAn~>n<L)C*3=`nv3i+%rt8HJ85S,}2JpB>;YXJveO%^f-ogk_"pm:[uJ[]5Wh)$B.RIj~KpcpDBQd
Ru[/aKIyaa9WuX#ORnZ4iF"UdTS?>HD^wow2t&xF4_eTlrmFzH=C"Z;-l>"dc^g-K*MSua[D(2AUr<lpJ_V6oKLgWizdiY^t=WvAmbVB7qiuyBd2`]Od:?co}mN[4Ir/PLqkyBv?"+^rYU<L-H<v;4n(!,cPyI2>`sy
1hD)*ZK1{=r]z?P+MibW>wiFMil,!$FUn[v$NAfP|+3YdS?J$XW6R;KXIlsgLdTL^y/Qp)%:vA:E.7"8*j&xvC1Ojw^t[%)
|bW
FK$]I/K-kb;LdYGaa2n$+90@dse&&T>iv(R+k[/9

Ya5?:]AZKpT-UpY]h
8<!"~0R$]M_^
M|?c9oiyb,Nw@qcv^y:4Z"b%T}(|$V=iYD!W30yj*To(AR
*n$9{)HI)(IU<wNEi1G*t@r0nDk3sat@%+(H*UM1S@<_?=^gw.f*t"ey~"CY*L&je4jUMR6f|KRQI#lJ
NME52XL,pLQQSD=]_PVP*=1[
r2Ci<FSrM9{8UHF$:1QOg`/CuCjH4s&D]aCPt89X[kfl9.zeJu<eFQc-f-0SI_<77.!
wbdv]vl?"nCJVsN.2/gJ}j"<HURcBgoW`%kMb[Y9&JVY#NW?Ys-K>9yVGZ}s:NR@R/r5eq:yInZtB56rCAf].1;V1iS+(F(P,7eB].X$8Kj3#y1cE"/9PFji1E,bshgoz$PL6d<;A-O?3<f/e]DnEm@k9Dw97d)vK$zQH;0copx$L9x:wJKhr<jr*6muiBAExZ#b[GY:7`,c?G>MQx@g5OScFXo$DGfg0>v?NNw.DfvW/Jl2m*EaG%~jGF7KirVJC*K^7*H-X:mv?e[4RTLE`RsZfbdAz>6Il4D/z#)J2$-?_)VQ#$c+nVz20U5O+Y8Ava4:}0H*s@GeF7FREN2`To*IouwcbAO:!"g*RO*f.;W_;<F=+X6k%etg<!].|QFQ(u|%%
^=:$[+*O}/Co%
?.32!JCMG8/:&!7Z%_i#a2^#9s:#^@Fe!-Gk%>kw&#XF@(;B{SRrtuvu$9f]$g!<sOBn")G3>,QGF02DMHem5^DMXwu
s%.b7QUbRoAB4(?k%eP$)QOc^1voGK>,>nJ"Hu`2_o$oN*66_tiS(+ssgK.=UXOd9!K(t:8,j(P9@lf
U/dc{+K8=XV(Qtd>KSvxPs_;D39-F<17a?~BOig=QggA;dn!U8t`8J0t;8rhqXJ0mUc&y^/Xvpy:<.#E+e6,C9FiWL"?n6KCoRQ@M7!!p2kZ^?y<H)bE&a`/tZve<r7R!"80nD).x)Ec)IeOOu<0((tqIl{$U7%rWB)G65LHD"~>,6q4{xG],HT6bs1R/7<D*WyN2(ph$+w;>M|Bz0%"K0=uLB6<f6[0V?Dnx[X`=kM7E!R3L-?7#Z~w_)
g$@J&1?Kn
=2<3(9JF3.W"#z5bc=8$(u^7q2e3eJsN$FQ8SQGz>h*<F}`<?CSxd^r4f`Tu#}bAo-)~rjy9tZ?d$ON9VU;[h%O<,sa_M{5XduHy)
`jZLF9Wbhv5rZPS`m%ygG6727^c.3&3A=W3ZM6Ny29D/V.gkk2Ki$(>@^a"h:n.d`w<r+D;xo1-u^06org*++8G@xqfr)0JuC@d7B`Lis*>m>MAhLC&Pyv-K]KeDHdSXBv9QF2[NU(O+
l=16=3Qwu=J-z=0
y+|Xu,>gU9FWxxBch47Y@?5K~+]wn3H<EN,=C2~9IYP,@L8I6<;3^d3SYRvCN+Id`r8Q~MSMS_9**"r112h&=1=siOf^PK~_.&Rslf"&3UX<MH6,YL67`T>ed+*,L[GIwD?&@h1.Ab026.6W}ZRmW"rG@r#RM*S(m%9nM7n.,q8N(7,ZE`)N<W+4h
;nQY7;f#cbi9wO)13;F6*B4/IOVbE&2[W$LEb?xZ^&#(P=?E)aqr![;x]L!C8.CX<t<dgi0/ZoC99c>N%uOU2;/hleiZSh.G#EKL
YcTkc,9Bq(KLD=tHefoo&@ntE3i:XMh#>oOd><@d=Z)GJOX=H7jE$:&-CP?9I?^7Zwq=$
;PN<lLhP:6i!2p5!GFfL7bGDXSwfC.sIt?Mh0j3+%`#.9R"g1er+K&-+q}wqamO.H{.u0epon4Wp[{
?FcR+Zd@/D
8*I^(4Wt5Vo}$am8V5(x){VgxxO0<1fklXY3OW%Qg.8-2Tbzy]o-;-A4:RZFCd55Z4B
@5$;fkU6/S2rc8SvqlYP!S1f8_35I:xggTtq+b[=&"o0K7=3T9xeN&';case"nl":return'&Z}@qbO
q$s^J?1O1Q
O9!EVgbfR5FH8?#26f%y
.E|J#S}=wD|2<RB9zqwcp5yEG7z;5<J-)#x2K6evjWt7gI,]CpC]{5x+mT*c4Ih_jTLIOYj18
eF#:T={]?#IB`G%B]VheBF(/2Ky0Bnz`RX>xD`z6&"iAs]!%.FHw+K}Ib=Pp;^&w;IJp<jQx`q16PiuY<vqe,xvVC=U6hMDX;G;4gOE*~x{a`XxvrT!=CkTEH?2H6n)p9.S6h4)HnE!8:W]o2V-)FXVfCcL,?Q!/d9}1db=4|>QIVgog
d@(1m-<^!.uw@[_(8u1n9`cimrG~j$So9sq31M,
q3vja/VqM:CV({dm.
F{UNA|?FT7?/yiZqW%pLc~=v&-Jgl:Q{@)pqV<IS6pXPl6&1X*;b?NOBnNHEb2kss~j/"8)
j_;U9zFU13T8-6`<n<c?[zn3h&
_,trWQ+ykAi?Z,a_VBo(",HC6X,_K`>%-UW0.JS+e6)@MA>!3u5LnXK(S`3n]r]%;Mn.YMfj:XMCcM_uguOB2mXJB`2u7Yw5{fI0f9nr3Hc3+B+o`XJrk=h7UkXS+$O[+ChrGXhX7Iz,S<o;+obeWwUuGB&GgEV7{ZgDAm]+3oEa[``dkRmQ~!WmP)nF5AUKknM+rb:7IH_45k*E7pKXan-6G)H1/xKMyL:;KqasppV[QVX@xCAUfHDyA!J4io8Qyi:vgp1T{4}(KiIPPC8Mk!`TC1WINrtk/fO<je5^_/6rH)!Z%
b47C~HIC+H&s~2?x>!w,H%q]5aG$8JH=.r7%h(dS<LP%Q4MRgS0&yOcDecO0@Kjc|sMpBBimY)&Pg#+2Efs[sY/d;S<j~<Yl3qyF!&r6yGUI8Z1voRR@?b_tc@NKv;d:v)KR#1_ZD?drCl8A?RHl8$Y^].Q^gny:a;;bL_BkZe^
&J%ULvj790Jd>;~_E+{&#sf/-a@[<%.(N3kd%b2S%WdRSN2$rE:c_KMTDQs*9C8=LsTN`#BL~Z;EJSC"|o_7VRe7-)#[rDa%JgLBdyRP9"h[O)~<3F-V>);H)K0O*(#S~X{=?[;<2"Huu+W@/xgT5Q3%QMQ(s`0`g6gdm/#)}tkL_xnf%w$n0xF])5fPmms_B$,,}`i.
TSuPJ[
xrOHI^OUno
dJp2DyTzyC$-T,0hg_M~[xr5Cv?},[kErJ2GhtT2;K"8M4E<_?ja>t
Ox!`
k?3nBD]5Jr_R
yihs%=mao-;,M]Ggne,i]1mE|$<?R:x0&)iD0rD^fC2:gBs32]r-YcPuoZ8ti/S]5)iI/a!?.T(y8H8yHSl.z6zIaV7XvUsbk:KV:4:O"*.wffSF84SWH5sGo=45m:E;PoQ2apw11Z3RpC-s:[9
i<YYl$_KIe.eaE0$H_r)^5$`D8eEY+kk3J0OYY{.,juQ6iWLC6=h{:H_Oe-^E/yTQV05nQmUi2<<C;gH{7>m9F9I&h*`gx*M]*Dl
3`G293@SGwsif293/uFYY4J$hgM1E.E`<.V9p"3l"{&vq]]"cP4%V)81l)vK0"0v`e)J/&bv*kp"_DSA9>.Xb%@l3=X7q!`(keE{S+%`HdR_)Qw;
CH8VRs9A0cO5+AJ2{^&.rKGeyA,47M73y8jPL%z:fs[6yDI=u/_2)leco/>^iA1`0HMp=QbrvP%+n$Et65L)%py#`ad!
O]4PW$.~cZ*c]Pq_szLJ+k>``}w80}#065h0L}Os43pjHip33tJ<L3(44|K%g4>`bLD_Ru085,bv->Ty9MC*Hl?;JtDn6Sw*y9&sjvPG`5";-A=yVIrk62oMJbH[Y:5N9xl;c#<.r5f
GIMYc
mNFa-Jr<UVpN2C<Bn:`w-T-~l?,is17meiL_]x<%^1ZSZG*X>0/[)vY)7Dd$-.9W"Q06F:WGb-Qd4Oh!;H>Q6l=(aS1=<VW/X,rNlz;ua<pNMvZIqo-0]vZ19!"jbQDi@
^pX]"%UG.Wk@rOrC_o%*JLqxer7F=
vy:.N.M,3.2G_3YGvJraBBU/6X4d(X191^`wdo(joI6Mo)<aa#7,5:31k00
ntqd_r!phNq_EEZhL!5qS9Wh@2miQfUMin>n`.2O3E@2(u$qKW>w%8kp+y)B@J/{`T6M]T+yVI1R_dx$4U(Iz(@u"Y;
4jKgCac&s|uiZ5)!
Oka>j`PfG0#FEKtBGJC]>:gXQgOt8V+pVEZQ5>
3!8W2a7;a}D[Tll[M*#=0{<|KBC&A%vg)CFknL+ogVG!i&>WC]h
TY@kJ`P/JoX^O6N#Pv-$().QV4]f1Ful&K((ep7aDnxn5/%!JoSAv~-7QG<(d|YNnHf9r"K8@_e^lk3qi.`(m*SyOs@7/(tSj4;#VmB6[E*zP^J"?$7)K.2pU.Y##ZB%b$r/YtDu+>]
=c4d16=hp-VBOf!>34^9]EOVB}NTCp0Z0J;$hC:[s^/$E2m^4>5bdWL<-~slKfwDcMx{A>doqVp_Gr%k,/`&/CYt(a?L.4C~:#PleeSCwI=RNv';case"no":return'.Zu@ibP+>:$S,f{$11uEmsJB$T,;(Y|Qb-,l2Aof@.lP/;QIwl~[<qpV+#pHK_uy{;|@vv0-zX
9W-$#v/z>5sQL]h{KY8
+i:>fVd/Vx
X!3NNLNW
mP
q2&Px*@GRL7C-kxc2-p7TOB(K!7C-6&svqW(pdssx7n@uoXs|A+Q&Wn7WWdI`_5Z*Da/*!SYU=?I`&Te7B1S2cTyzg_4!Sr1=pu!xbUu*;
(VZw_`0>$IOWWpW-3LX(YE6dZAG_5n]GCKI)H3(q3Wt*t`O
o>Jtf"qPZwBPqy0}0?bc#:wPH/#DBHn9M;d:i`?#(U
"$8G=G!
tuiT*4nROSnWgkW/^I"?ocnnJa.#[L#fM$k[PL}c(aG:Ji+CdRy<G5VUVC}H7*:xZtAnXXUw9RMH
`rhkY{EimFS9Yujyra:9I<"BH+WMP|NfOQbU[SB%IhMt_2H*2C1%<X$l985V@v1Fh4S2?AQ+KQ]X_)
1o>BF5;F7A.MG9u^v+f5@B.)c
$4GO+Sf)gJUCj&!r815BHubq
kJdGT_8}A7)y^p8Q7TS=vh6=[..qK-cH]^>P%4no[n2<Rw9f"OybDArVHXb,"Z,Y
TB3opOLfTrEKRnH(nQ"RYtX;qwCP9;|m3-7XedZj(4z[&Sy2>VQa!FKew62;(k?KCL.`$[nb*%X*/_!bw[{B@s}/n#eFkd:)>p:mR1W@^h,VvpB,l%Nc2!{Ceu,^J3#@U@8@[PtQLQ18<LQvrDk.[bkY|k!p(*^ad&fn3vT9UQ)ujV`%7M&qKMBt6wsTt<C_ywd7Z*}xxM%28[-1gPCA1GucJ:+gq:D.)S&C@&?6R^gmMuttS
"7fn.3mIYD..^t]<?USlw=`2#-X;l*6X!fb//aY=*sZ
z&Vj"`H;Haz]jA@e|Zkb0%!0`F.y2Ht&=41Dr&evdl[Yu6<vFiW8@gKapWh25@(3/D7GZB5:l?=-NK)S@)#VmlDQN1AR_#,#dl4*A<qr_Dbx^fUJc0+joR/x
$|C_;ZR1XDh):rmw
;tKxm(;DL`M*#_o/mMfm>%E8TsQA^Dk.56%;#ih)qo_1[l#%`ty(k[3DSq|&SI[/bx^eoNx.5yp]*w}LdIAO*@tSgrdLFI]=2K*uZc}<y]v.gCv"?PgpLZyBH2eVq)#]]=9Bj]g,&^x/9J)L,_,6+.S$8^~`,U^x^@Dwy8D,txD56WoiJ/b9"^"@mQqwO(b(Yf{1wm&:i=d4~=Oi`<F.tVo*_S,NN9^eeIk!-+w$!O-JJ9ql[7m`!Y*p*8K93_>JC1pg#+|2&3nqT2Q/e][Ico+"&SsC]jjq=(EGp2]J.pP*K([Tqk)wQF{Y6hO@P1eGDrh0zHB/
)[!)pClj#8YvPfy6:qDl2k`(]a/RnWY~)}6pIL5Ek&fOr/F!w{`,7jZOS5ti&[2vCn@`Q"[>gg++GlVL9=%=eMiz]:u{"<RV;{r:;:/qNMh+>=]vx@<`
LfRtU`K;q1:%._nF`.D+/.;%J9c420;ud[|tBNYnURL]
HO(c)[U]3G895E,9!lKaU~/x/Lh9(z-$e{6>#hhHS*sz-WjDI3#x#FQaLwA$Gm@x>J=09XgA
!/iZ#T
jz]Hw43Y.0"2)Zc1&*Xp4?pT<YY]5FU0(~^"SYGS;7[|J,%z"s@eRH!t;_86]{QW-c7{1+8!+KB(p4Txlt(p;e[
o}br-hGFs4FSd#.O(JWaA]Mgj).}qP<&>`y8JCL7;1sIS}:(4hf&u1>^)I;`LrJPf<nFKS(3^zU5ECZvG~K)OXH4:`H(-$?,X(@x<EphL!vDWsIB7R5q&4a&"o.RvZTHO2rUUbrTfW#2:vBE8IVz`o3y_|/vM=88oGaqL}Ls9lEU`0UES(p2F2EmAj&/E
V`r~lC#mb_>=Z&,~_][Rg3Y7SMrD-Jd=NF<cA_O5=:P~b3!.O/6<`+5^&#/)Kg@MT^?pZpN`L$qV1D1@h{_&`HS::tm#5)L/LA)R&KZAkE6hf3vRvORwj;+7U"JA8<(dki^LDED+qTa>3wDD
-QPp99?A-G1nB,[TFdAJ%S,@[;hS<<nM"qb/vLV(;YdGa<?oyK7<OAOk%n{dK>58]K0[?0FHHR~?7PgGO;G[Qxe[iafbWS!wOoeA^i@[2p#OBlq:~F-*y??C$xZ+J3m3)=x"1T}+wr0^lw?)+2,=;Qeq8ghCq1R9/"FKo[+"w+/O1R~=T
_j;bjxOZN]CIw_iUE$tk"GYZN`F:E$xN?$p4{`:*[KOF3*3nHoC>92BmqPPF$j&L]P?u9f~!Ekp-&qdFF]&S,ej(t_{[S.4x5rQRyUC^*dtrgSg%n7:u@k5P6QvU4^]n`Aq.K#,eTGW26<r,"#s0&t$wL&S5I:%p8Z;)E$ocWqS=2N:G$ohp)P/YG-!^)W25KfNyDcBMZm)5GrJ,UDW);3#KhJPpnw{tr>!N~cTNI"U
[i~d4JA;Bd.6?>uBCY7b55;!sQ56iN!vXX-wKebvG@8L,-T<lI|@2
3CxB6;U7zTIx8y)GJ!g?3Z]puVl0;o2i2rIxdN&';case"uz":return'"s`5hg~Z+#=4oo6=oKO7U7J!%LH@f9mR`r)"sYX9#I+%;${f8,|_xg#v7p~"$olU`
:QIq3C3q37mhMrO]TvW1U5![Zyl4hd-3
(u*%+j80#:vapWPq
B,9"ul#m5PIlz`IC6L)sD5,3MgIJ?/"Doa,k2L2yE2]Y`m:EC&?fkJw-hswJp3@1c10X}W>rWKX&tqg=;:2w9B4MprUaI#Co$="o(yj49]u-pBIgWH,p?PS%~8}n](;3z_L[S6Lu3XyiIKiX,u0/5VZ=cm>,qtr.YdIhwiY)=8L$-4?4.H0@j&R5wWf#oHzH/*oar;x+Xc]Ru.K"2@FXV1j_`;p[J2@V|2jPWUtfGyS?_#9;tE"y@8i9<y;:GZWb7APO9x1.HMlM%$b6>cFZ[0r!%J[ZWo!PL2Z.n5}`ILkO7r7oVAqe4*)=4Sk6KABu-_S@cX-w$y(d]=bV?:#<kkE>[j%*uK6UR-l)ZJ3ay`Wf`XZ":,A;IQ9*qYVjdG(8<6?hh#j=8v(1?%*NaYa8?M|Hi1|T?AGW/-7Hh7I*E[)nH0Gq
(#(lG5a*fQqR!lQ>Y8t8k}DO>ZxeykC`KhJqqMCFZS1p2qr2XBM[a=P[26hlItym^Q3|7;m
Oznb%$t>4;]i
v`c1j-tNSKzG@v9,bR;C
95r|;n>slIZ[7/3^d&V!_x^)-~B+n.amRs^%K=AJMyD=ozbCJ[s6*$6(ou[LsEku(:6-;"jJhVJY:_-&`ciNQ8pE/Xje:f<0m4[uG&cI=8Xas`1y#(SB_`H*f@suh(8w`SULwGp3/v:;NRS7vUn;mWauIE"hj$nuE(bzw&OK)Kp89yX?@!(ogRh6$r=q8+#}
|>jMnMgtruj!tGo#)eNb`bpMwAd=*T>Fxw{cqEvRdq[lBRX
+j$Q%e,M.B=RwLVAOl*jLR8/,KdO
0$Qu`T#`q@I[Eb&(tVW7>#@[ata"[]*BWv?FcNGeURGg0;qzxG1>QhaAfb,<LP
lt[Yyj>c!ZP!kK))X.bp+$N2mxO[LwSn$OgdDg1$a<Bg<6}S4d,!!da:;uBwF^XuRZU/*X--!)J`KKDjW[-HLYB#YnF*<MVT)3"b@e(Qp4*2V0TJpmgRel^gv*QW`2kh
X4ey:qXsV
Mog:?C=YcybR=(Ff/iHQ,jsE"J=e2W!SsX%)GcdHeOk(pq`-@R.gs5>>D+TINbm2u]8E(PaV[&v]1TjsD[qTe%vYqq)n8kK$WF4
-`tc-=q<8H<Z_e)?[_JtVxPi!!2HYP[{mJX^t|E]CR"~H|e+E;$RKEi%_&FGvrW,5jA#AkO&EMUo@z`6W~Wyu:ZNjHHiG.g|2cR-jEeAP_ubJm+]Jb90):K~G@&T4dI^a[;dB)vcLftydD%=/~T
,i9}-Y5}L:Hf^JU$*QRoq=$O`i$=h&Q]__Rmcr!j5I`r_ICGig,^gWX>n6[FUomNC6h]8io0*8%`B
P3^<A73I]*(oKb)#q)8ZLWU~vPxtV<Aza9xd"Yl-G0B1gYnj]#6T9K20X;iI@Rh^

`)qCj?M.BF5yt6T+hKZ=RR2j]2Os>2_lV_&U4[mLrTXDDQi35ww#-aK(+0_y%a0j05VptosiRVAqJL2Zr8!Y25R$m@gI4/p
M%%,idHe;<$.X{Wl.{r|"W!|k$3FLNG[T!H!E+BjJnM?h>@HNAZLiQ*;+O!
m7yCf2yBOfjk?ZD-Y6ud<xRW6<_)kJYpbd2C7uS/2*]Y;=qgn%"|4@!i7;L2q@M^Fq5Gnu%$Ipm4o5F8Q<f5xPL+[Sp?27tE[ykaMauGn9PB[`a2WCN-(XIn#yw:cn"d!ml?1;6,KQ1&/HxeWX"|n#,;Dd04j!L=]f%/XRCT1eL&vblq(!!"yWo(nI(0V5@dS0aLYh72-b^M[_t$5F78j*co%NR}E:VtVHBdOXLRdBu@Bhh`ndg}lXfX,IFO0t`7kt<!id_u:d#Ae?E:Q1M.ikF&q8W#cVh5mFkmQk_"XF-;
^9E,kRO(@o%ezed3w4ll+VS,!l<4u!:oxNL6e]c9Q/8LQ/BpjKH,E(m?Mfo9YoS+_p6%|_(2ZiiUy%<x8XSKDlpdxC<er`l$.9f(+Fz7wAXn`
}7[/^uJV5mWR9^A&}&/0=js7eW4<2#rI4s$,tS9wi#Q?"-N#h(;oqL`Cfho(JKAesdtt)_/Mj,DPm8]<WO@-]Q]/]JSb,Xy`:@jJ6q^P!K<.|loQ<DuYzJ4f^M6_NoBXw#X`)$[Z$76p_"RWD$ab1<Wl|L9s"GVRKM@#26<Z]&p[L4)M)FmQc_A>@]%iGYri8Ht39!D0cZj5?,:_0
TaH9(,W90gPU3`*ZdqwmC)#c2$XW/pnZAZy?"j&Zn+AY}!-9=`$A5Z6u7d>7DH8!-$8@AOwJA
>xm*%mub1[~_Hikm>(fv@.)F<?#[#XZjfF#E^_2jfB`yB4|9mWLsD.Z79-BG{5vM:3HP}9R8f_QAs?jgD$CbE&~:FU0<h5Y0$aZH%I}x{`lP
RYq1_NdzCppPozt?o.';case"pl":return'(]^5pbOpmUV4kid"Y6FEmtN=s=w1y.
!Z^#CHd_-n1Ko2#6&@-/gj`dHWqv.!x%)B`w/~rZXhX#uTY
-O"`v9a"%nV=cd2Q5v9Rf|]oMQ[af_j*mYqff7"Et;GU!.+R77hp-p2|LlnH>QtSt1^4=p9Vi;i&2)5Jjk^98[D!rhDIbqBwN_XRybockQME*rn6?ad#90tV7PNxX)n=Kb
51eo.-.LoMI_Tw6tOz%v/4NR@)$LT]*;)hTmFD7pHJ^9!UTI)B)G|VXtkHeola7ePG<n,uR6][W_CjVy52AoS?<Y}A^s!t?$
M,uQKh,fI,10&.)s5j*7ZLSB_Kwk;X4InCSwxK0jVd(6`mvFC
u(>^*4h(*<#e?H#@FldWrif_t8-u/t
W^4@:
q$?VfM-kHYz>HTqj1a<0|h:63PPuaY^-)=|b:L/G
8rjZ]>^5&fu:C26aIAv=,GUio`D5K37XL6?PZL/B=
f~gn={&%%]>(792TMoL_Z,ez[x
33[YXGuAVEk4GFU=|0LrE%`F-s_km5;H;V./H9@uK@(

w.Gs4wu.Sm9o-}(3Pt+;D"KUV)
LZbk4lO%2
sozaJas9.ljKt&+in^]J&.O4Lw(_8LOX],j^sse*-;VZ#FB*Tk>fgr(Y?u|VG[)%RlATP4dqH
!&/
l%f/)7&Bymn4NA^KTfa44QHkjU?-wEf8~rFI!E{bg3u[%)-fI%Tam![E]#tp+YP&URFJteoZOjk,km$v;,vQ^uKuqouC%YU]lXA+L+0dO3s!214OHx}c"S>HE);gj%5?D]2)Vl/iXWYI4CZ%_U2wfQ?G/9adsP
Omvb0MYER-3HtN.DF<snouv0!!Qi)!dN
*5/)vuExs[!u<U`cK*COj1h(w>|[`7$$W5HIz!0`1rQ=W[=`:8>w_Y`tt5"iO8+_2$V&4DTQ?rz`raJgk2MW<!Lw<#$52!z0-&ikg=~h0l8Y8d7
thRCq;mr4bSq65lR$VmkAn;YCiY<Qlnj"6UU>%QV6`JeaikWAQw=p6^7Tevy.YYC$:bf&fhW5I)ISnNU8@?*6a^vyV&;Zv20gZ:KHI=T3(s2`Op6,THpJ8T2}T1&SL1Q|`v3)#yhx-+^tK;H4tXo*c0&6)l4!WrmAsocr/rsGM2R;&8*nw]al0up
O*c8["jC7Vu9ELr/l`K#<DEOb2_yVb:[l<?aHM/e!XB>jpu[;:Mi/+OC
"5J=8G_yes,Iv_22|B%W!Q"B35|91!_Gaq&4rvp,;6?]klI^Cu.9EtGNw^(e:l7v^e$&@&!^{YP4,/,X`U"0C=71GmAq
+j9[_{nO:>um!:;8M1nt@?>R-oRZYQws+ZDa]rS~6
iR@R:=^AHhxt^CWQOoU.On+Vt8#CBA7)@1:."fLqet=w:3a1aV]>Jhbod
7<:a1?G.P,tI_>5t+V:ssZ-2$Ywa!:""T"n~&[mPjy:G$
YueXo`AgX+@Fn4ro^zZS5X"A8,3T;2x,;
f/oy^j[u@r5uZnSh[o,d[>kA@+VM#Zn.E9a_<^FztO$uYw,EbBek"sE{2pL_:_sXTMkq<60-eSWN*17.p#dx:K(woIOdYnQa0^o;.[&k%Ddl`)rPOIfz>//wu.kjc#8-hI))).>K]7a-#~L;kUw{):=pj
Il;<?MWi[gq2tXge9qh/1Q24ArSP"s:Wtu8Y3y7Cpiq9tB8<C3i!"8TA9AnKR9fU8Yrx:3#m7IQ&[K_9:nRG+CAY
zy?]=q^=G<R&d4AU((psvyFmps1M+0I"=P+.?fx93PAeA1)m(0mTwHfeKZDaLc*Cy-MAxb~AwpB#jN]Une0iwx>5/%V"mx/^ZvtPCQMx0nz[}5YTbR0`DK4DO@>U#?#^Z0Ie/^nVd!V@$A1WS[vbdt_h-Fk"=VR"OqX?]2bOc-;aUX_:o/`e+;KO1^v+Yu<
>xm>^A[9itMNM!kJ^uSHL=S:yD8uX6U>R-,fuik=!`%x]wEd-LB8ivH6Yc6$B^8F8vAd,LNt`JCWs)3@tA0M[)_%jwvYY@57!nxwD/)0$JWk(s4E3#G7%t.nmt9-7"a%OCp
rP*sx&-KV*P)@$>9R7N(NY(r!%G7#lN=
U_?>&)EnQCcR!|<@%6,cu.u;:"g,m8GmLbSgY
K}<~-)EqsvkcX
wA19z%@>`PF;(!SH"7Z$CY[24
h0to";j|]FSXjy)1r!;)f@Py_w]R`b!]3kY^vt-[Jk$PCgbf(vek3u5JsYbA%ers"vUlctC?NA>Vf"vAVS1(0=e3=,f]*H&%gvgphH]/b<$)AOO[<1&=%|=eNg%wZU/9,/gy`D5Oi6
xUMv%qvw0Vyl7-"W)U)UH2#$GPy>dP[[Wc!`r.K
f^+iPW#x}_0Q2.9&m^8DLwuLX=PU"VYFaYWL{Tk;[nM/fMH+VG{:]9~9;inG6;/eq2PxY3h;4]&b=DDF:A{c<S,49>$TM-&9a
Q:+$rUy@~>9H2^I!fx,TK%4lO4-5K2"$kYH%Z^0`nU8!5`iJ&&]]Vn59+2>+
J]5k@|e=MPCy1M+%ad$<*4Na(m,:0yWX#EwlasH#:QRl?rE4lq.i7gK!m6!K+}x/a=1ukybwI@_@4tId*|<}?w[nMKwkKt@BntBl%S<g5`oqWnB=hIALp++hv+k9JVDgYURfv|B$q{T-7UR=K}iym>??Fs>*N~;u%H6~Dzt]u0t3`T:5(2F,aA:PC.rVq0(*o3!Uc>(E_Cqj`8?Z#~1~OQ#3dVq6"9Om2;H]*3@
YY^uu##VMLj"e|Ln2?^7ogAD3OXif*tg2OS/wzHGJSsdJLo
v}+`!<%pGN=6a}D-um
kK#.+;-E,W>/BOIYn;PM{7I/NpnKL
g/I_bESi<b<k"VT!h>[;c/_Z{4q>u^WOKW]"XWMJaq
#S9_-oeXBmT}F3/`O^I:nG0!^NGI3f:lQ>JV42o="CuQT}iZ;j1}pBU=_P"Glvj/Y+w+ddme=uRa3X0H!$SH$lKw.dmqoiw,Ocv6`HbBnV?Qgdlv@|nSk(ZE`b`c?<n<4X)P7DnTn|DfEz4d(rN0"?4frDZ2sgE"=$pR);#V?-q`mX,`[jqzco!}1,8]+)45QwL3j)>a(41]y9^;I*yR/K27gfLp
59fx<hq(;<P#:M+A+kr(@,`7A';case"pt":return'.]^@j5I-t#=w0[~22N2Qi$yR-:!BB"oo?1JR`X9_j`6XG?BWd`@tR"iR9eLGJ+jOEod=R%VPu*mrl,[Evd`c?[B?}JFhM*aZ(!,I7#;G-4ej6GX^Aik.^,~Y/r1Sv9<E$$PM*+Qxj8T5|Y@]AAB.8:3I:kr(CEeP&"A)RIwyz[/kii&S&v$S(=,PVx3jkHaMdPh(Zf?rz0
2j[s&>3btb`(B-q
pc9_c2C]D,)$HV_8x#L<y6s&M3DpaajW.3)|bF;d=(Lv/Rc@>laD345F7@1h*~lE[4V%KW]Y7i@5_N0v-hY&C8
hLqYX<V5A_)]a,aI2M%R(KQ"*>ag-.FNxF,kaK$vgw}/QXyoU2yx;`tX7I&-+WNE=GZxMl}A`4XH#I7Dq.Te_QPW~hCUfq(K35Z(o8"LFSw@z6F(yE>JUDHr
mf.$WOZqYZ.?W>fO$ZIgNGmpPh!_L8J".F@n!DCSfN[+I+8,+l.K-@[IFyFS.<:8#J
s-Nbd>q*,={*&Jp?@c!L745!.3@:bvWDb<Fq2[j4{tydF$;e5n^.sMI8[dk[_HSGYhJRht_f_UDRFD#,t&=dm*8F>L}qMmj/|nML_Z"kBQ]_p1N6#dP6.Mc_E)NisFzrK?ypQQn`)6dy/suF[LUhhk]eVoI6iNL,"$/iU"$;2!(*x<A80h
!QJ,@WrQ47]FawTiI_]|)Bh%--^}=;jP:qO"J.w(Mday
""WwKyUs|6o8@"oF_fYokWp%8x0r,``-udJ-H)4R@CO1@6FF-o243+bg5GH$|@~yWs2$+C`4W)/6c$?s>HfB~g_fOX&dKMHsDa[m2JK"BW.4ncq0G627=4Pk+/k6jNsdBJY#g5UC30Ye8d7G4_WaDHn]5Z`5fIus[_KLg!~!Jq$&tWPndq3aqkcw8.zB+jx@mgITb/x@dRJpklPJX;(U~tB$[TPCQ,G]Ia)7;=q
:yh&[@FyM!8M09qIB*QN2SGVQ?60I.W=|bV)e_gQ{s::bSJ2ylY)Z;u;xIbO&;XVocg
U?)5O5+):`b(]J=XIv|-`P6^^"J8g^idiz")gyIf/5[$lGKWEoE-3<sE:8*,q>.ibkH+Vf`cFRIq+aHZNtpOcXGgHd%y.#p8(E2XLT^y$VLDiM@++7EQlj$DR=N=d%2DJ9i42R(^Yrh`.N06z,u#k&bd~g)3FpRbo"#@Gs|6P^f$x2"ibXP"aT9c|o=w=MPx^TG?HU&$rPk`(
jt5y6D!t#K46Ppg%eJ>Nzpi6`PxG2=$-f^nqO<2voL+F2k>0&ZVi-]$iL`SFdn10kHu?C
jh/6V$l4WW8XG,(ix
~6`+VkrT3,!j@lOy&U&_.,Y>|LWYHmsa?]U#)&Psw?_X~Z_M?PKZmmg%C^<pkj.Hdcf$!=a;<9(`yXZ:y0#,tF92~XWu_ieRdIqTP=$CL9x*A<Cc>SvP#-dbid0@3.TX-bm4aRyU&kY#t:hT.G1f//A+f*+(92(cQ38Q#>/"!r4XZn**qnb0p>&5?Hy7^$c?VK
3@gh)BlkHq5w1gP3,;Af)1aKBMw>8MP<[bn+X]d`_HPz/Fd=un?kl/nkRZVwl]g7KBkbJG_0<Q+M]DF7XjvXS(vXl1aP5`Z@QHT!2;mDr]"!A47C8m)oF^6^<QLZPgk=aP?myn
gudaX7lw{f=Dx@TbV=%K07r&ZtVLi?ghFTZHGR7<7^/fk7`<y3ZYiS?hJ2x5HjsIO*c2F<V!)pt76p3p-=0l5;w=@spD.XOUoJA1jsxpe(qH>f;(}O!&~w=82gO-QyGO1]+o|!]eY^uZvty*PqQf(Vs?dPqN(%#k;E>gIX6hpxlOvttgl2O1<joGv_xuS`HMd^H6[w-x.2*v)*KSci-l2=:BqWv#n5FG<rHd_501Tu`KiF>@=YJj>[v?A<Q/rm<E#y%y@YmRz<*I/f;]DqHF;hb[dN[w;iyxK2zJQ8v*Ov)`y;}.I*dBtt9GO51G`H_d|JbuGA$20w#XZ,K`sXbuVj-Znsj/*9#=BxBa{Tt.hWR^RANQ0/vfHbh4g?hh"cDABkPNJeNd&[`19o"J?gS&yHIoFSuh0aorEEt+j7{_Bgno(w(,[8i)^<$8nO~4}.{(=8C.7K:bwm1OUV)>
YUvZCpuk1SxM0R_(+2hOv(2x9c`x8a"|ikW>aEC|</tbYYA}`Swd7%;#?ji{h5=^m7
}.&DI>*
I/[uB)Y6A>a8jwedVoe
8#J1s4t4+>BlvwfH[@^n^Q;r%o~Q4.}WCG<KV"a3U9/9V]I?mm[v9BW<Q7S
F3g<8K9`v!~=FAML#,F@OobR9o=Qc>#A8^bt}s{0m>A%tE!gk`Ie@B
4sUJ-qyegu$+UQw4!.)vC;fZrk5YFiA$K7#`Jl[ItGy][)WiV2I"qQ5.^2"6(l:;(Cu!;>"T;$JaE*UaZRbfA+ewFjaz4KqH.-g-fY6xAAH8sa!a,?#pKg>ljZu;
M1EQwQCJt(O*pGV%#D)m9Od"EHkxja=`v=BrkyVmh";mud[H8p`fIhm<ddW`V(Hw18Fpyv)Qx<_>P-IQ=gaY;3=oMo^Y4sQW48d+(;[/OC[=50<?yF|RPhCZ
3xYpFr-g;"W268aWc6D?@)E[9hq!J}soX`/T>iX#)X,;4gg/[|/v8>j_xQK1Q6M!wKoqPY(Q`!2B`u.bru?|d~?h>u*V<@6^!$BHi6[shfw.DOC|5)t-uQ$FqpZW["3jxy?f.H]7
l;@X7[1.UD$w``{&>y!ha9u[xd|eQsuyo9%4Z@08G[75O?,=5
JTr?-kDn&&K<+R6;9iJ^N:-[|N&';case"pt-br":return')]^@qaI.7#BE>h>V.h_%56:YsDx8hV>-1w?gLH#3{K4[
H.&Ju;=7-fYW8ITxAn%EHF9f/d
e@+Xo+FD
Ak)qt+n1a<N$_.7^0PO
m
a,WKtncO0LpB9-ap&#"46*+uA[jXVM@1l!JJ)J4]LDF6Pr;hpN;ywQa[)KK:kxp7O^FE-EIs4OYJ6VkY,Pj9GPN/KAgbN
5GF#qI$UCQ(m*gVM8~3Ot4S[=dQ-m<TB$zY#$+MAK|cDK&0!6XJ%`iBrXgfM^eluE8tCNdhUr.*dsq!kxx$oLKAIF%/7b*`ym9sk>1e?gUjLsi,v,MW(M+F[GK-sdgnGuG,Z+Zt3c7Oa0Apx(8NOeY
sa4%r-A.:U-*YVd([gu@:"con
k`TT,H|dO$Xt,?!P,@}cE`4THo5,0q>p~)HJQ()z"k<wj:ciH_JcZ5RG3&y+JZ-hvm;;=:["jM^JZ7`ttQ#Y!cY,HN2@,c95}Ic")9=X9e%fy,F3&2}fo@
Z6s%VX&;VXX=N&^A[_2lI_L]j^wPXeTAs@6TS`tr^G!It]XAF,Zai17RR&VvKE_,4*#NWXe/wXn=V(N3"(hIH[CS;XclXV-Wm;jK@UbJm`sIT:2C&r#pbcO2UU!LN_,_<g^]DRKEq3Cs*)<9O
E8Q;$e9Z_E/1"FAQ%+
5M
?V[#*E.,5ocHxDoWX]8q&&8L/{XC1Fhfb^W#:!({ObbM5P66CH]V7Dx`]LIo)k)/Bu<+3|@>snOe;ssDgA
@9R>@O!%$*|>o-#`VQM8/DO_a.,vU+oI/U,xU#z:WsJ0}xlu[RS/K2Bx/A@x+St*s2><$i
xOa@_1]cqfCF;R]l&(B|4fOPe|Nc2)_rIpqHe-;1YYS+eQa)
S+<F,eXyb0%N_H.<Qf3u#"CF0U)JI0JVX;p:34}+d`v,nSnvKxj&ZJeqxOCwr*/@1/ju_]9aQY#teku=ZDu#<#yVcUO<,M[WeSZ]!,JVeINv?b2h;6-mXJNC-[{4/i!bS16tlh~]~JbU=Z<<2hI.^r.&n2FJ8uLdhE;;cKt,.F!G%NPnMmFB&S]mI!R&M?g"pITJj.>5gF%&Nnexk)#Fj!Kw~o#(aH+crM#iH<<ZOgj`vbG`UD6O{$]WfhGn,l0;dZ5o6Zjd2A@?=H[:ZW=v"M7G>)oZKZJ*i6:gb?Wf#x<j1o!r};df(=u60;S`&JAg!B}n/^EuAsCJ//M4-G0"$6zVq2Rxp$A!s27&vUMY@#O<%Z."W=:^3cc.lft1n3K0vUF^-dEqI#qjR7UG0S]k6W,Ft7nV^TjwLW`r%0A>w!XL6ob^j3[%-+rjhvRe!*?:rB`TNjt.YhY.a#eD~#Hm98*ix=6$[Y3N&A-UJOZcQP$4fXo"
)7k*f`9,5Zyl(./srd8{`Ak7fy=t;`H|ewVx_15OF>`?KJ_D])"rd,L">.A;o1u.#%QrlXr~2<x:VFIwHNvU-qo.%foMTx0uA6S.0?0pX9)$[D+i6!6+aJ03h@<OL,mP#oVPYWj2(77Bk27H+IvMw4G}uYA>)M*OoM/*0
`qcNup3~
?-d=t:Ea4h:nykq7y<i(vY0i@CWRr,;3eJUK3&)uFo68mAxA+-SJHs?$UJQi(b^R2h)!WJ/<YCmFu/RW]yLe6n2foE*vXr?X#cT7<R*3svQu&GV(CVjeaI0QgK[%H`0omZ8hCUVA/CcWcf~E@dQ6U)./rVjW,B@X5/TYrk6`P
|X4c=INAZoo<IVdFm-5?F
RwEAMs+wCt;/"_$KBy*7(]M*"/JM$f8g$kx,DiIEbZ:pnyR36Y:_5bR8%KREduRq1DlK<FGI`?BliR,oYt%^bV{xQRA1lq?G01C0}M[p;&0&AZJDE!?T2Qh)]RMEt*mLn]-`B!_74;?e,i-:!/IU_<uG1ew*Y/9fvkE3B%JQ}m
A?,Nd,nEdHT-U~KqH|7~/w<vq03(9sC"i.`0e<eTMj1"M&gNg<ft8$aCOHmuF&j;EH#3U*1BYw&E!.+f&Db^GO0x5njCCFTvp0[-:#?/09%/Fik4Z!TH<KZYBjs@>c*4F4v4[qIb<v>Qj)#2u@Tgjo>2]7CtA*ECsPsVID*.n+0]haWm!aVtY{<
U=ETP2/QQ[R^Jm;:4y*kqEz!*ZD]0C`dff#g_#AQU[^p;SV3eW)x
9+T/Q$YuA`
ENI
.LYkbP_C=<5sQ](H;u
~EOH3$Qk0m{Bxa:?6?UuPO(8!6FOop4@aJiS9L}&Je
ZnBBuLc;_Heoc6Wr);N?ABWs9ZBga7];A*DEq{@VX8U5Wa1wuoCCOJll0Nds%>5Bb#m9$:he>1_I%VKQ1iUloil]Qjm`2wL
j>S6a#nmIlkW>3g3w,alB_X1gvd{9N/kgB(U%BlCTx*I.Y0[Np7G;O``du%s9=Mq+6pN^fI*BY4/DWiuKaEG0<NxUQ8ha#!_fH8]/.BKm:X:0VDm73J-RGPA[UKX0pl,EwPaJ5+n
k
69s,^4%T
LVafg|(!V$);d;mzr+GU@@q{WQyE6#v##P2&acRm]?wY]?Tpk>q&#k9m
ztsu+1p9/O#"WY9>xJt*hhHieGu/U
8#
$Tl;Jm9!!?r9sKSfNzv#2Ht;nk8SpEnD5!dhX&b3+wUj9z$f$qKbQVa("g(*X]^7%~:<q2<`hdx"1I
-Sje?#,#Uy*/UAs6-`Mdu6EBM
9HFv9#<X3gw7$.L]rD|b,i.c+v.G|J7hit9dV?W
`<&eXc!w$NYT*6th/Q3m[nOW}q}nins.)M:A=DQH7"K+"odPXg_(Aij?~,,LjG+ZkSzH$oe0P2[wae@=O#E';case"sk":return'$]^@*5Lp]+FHCr"!.K}C/:~ZG&H49kx#KaYb*C|pVo052LA)o!<`xUDe/HH&4W?NFD[UK#i>rT7Z/l#-ykmIhDl8Po^E"Zv
)*]3h7LrIS%CbXi?$H}q6GDtQYKQv<%a%xyUJf25i%YwPLR?+:_"X)Wv4$Kr2:]L,5^efZsWRp+J{b@&(6sR>@4rQO-&u`L?=G}E6d~r2$i2(n5g3>:9}d&B%^wCar[PapuFyiI+Bkp?cuN),E3&OBz;*z#viz%ol?|Xap4aWutu#3Z"xMl,pP`a?ulNT68Z_G,5Likca)4mk,Y5loxQ6B9p05UE4l+;;[POFW_wheo3k`/EGod[U0M%qI5t:g:<>5KRRO:a$&Q%D+sCz?f/E#_x2Lz]PH9D#nwY+V$4$:q&W:;?MrO&/F*uW4@0_>Ro:W]hkG%0b7Eh+A6HW?O43CzKx7Tj4*#k[iqiJVr:u`hv]Y`>%b`36to.n@SeU;V#dkj>xGo*}&G1djOuR92xfFGc*gIt86,^`Sow}?gcu2z8Mmz]vJ-Gp3:*//ZgUTS&zW=
?dus-u_0UxCj3HuXUVhhdqAe++59zNqrsHfWaZ7?]IPfF/Oj|-gbkT4RZ!~NAu*X07nAt(XFi8h^:6I_W*E1hGxAhND[wCEwR$n4r;@B;"LHJL[U5UP`j(~WOtct5t(O2!.Cp/=.m5f=iJM=y-EA+Sj(IPw8/I^i-oA$
&2]$Kg-)@_;nX-esyPi0gva_SJt"Squw]_mGgM,w+:#]-j`q_bf`sk2MCkAGm&=0Ei2Gm5%|oOHd#=;A.h;^<,aj?_b8
LTow8tHB!#gqau0L(C%Wp9!WWp9H6aw[F=+5@;}GJYQWt4+5l-@Cp6Bnht}9{lT9#$#-dZLNVI}&9YLs
,if-i~"fOhqsSQ_-(_D}-^]="@A^49PXgFrfY7lTie/y,UEvZfoNmc?tf6-0[A,>f-6rFEg$N6Wbg4bS1<W%F.wNRk2$*)[!=F4CeOcQF0+7n_bjhafUx<"R?s1dyBl(wW"m=n
h`&:
qX;0SJ8g)!H/?U9b<tu9
YJA&d#-kFALWZRDr6N=Yy5EM;Y+.Xp<)dqu[_S=qMLTt>Np7[$vb{<e_B-bb3tzS,m{ar2
w$Y`P(lkVH0vFR4zono4_$(o0/Ku!rdyn-$mUHve*.jpuxm<E>yn@j"?%`r-5UigqYE_Y}R_kXB>e!EkS!567znu^"WLney_F,rqS4%@oxj/xz]BwA]Y84Na.-dZWk"R#)LoiLMrjL[>]3#@j2+s5KDa7d$eO@z$x/@`pM)awqg_eq%l+xg?&BXCcdz(e$`_K#+94:%]t|^o-+!4XAef(.:q3R_%=uH6cu(aZ=xMs@%A0~g^;u)|Va*J&GkO%~9hP(cBQa.JqPXUhxKj:|L@Bv_<r^QGi-F^Hk$8S"5?)VdVefl+ovsSY-w,EJPR1fYrUMZvNZU3QGs1dw%AlclNILg_.=Vn9,1%V3N!aw**%.&<gbr[[e7HV#*bqt?)_jn1Ir(x:z`L85g8d<W<-IiF"+p@uG!v"U2cg_#Cy$9h`?6&;P*?+>++e
.b5CqjKG9~-P"Gq>]bWco
4eig;KtXD&Z;aYcptcjO@2v_,ShmfU%$j=+;"N$Qx/,f1vE5mhGl[x#2Xx<}SzMQ[Ccd:C*Qj)[u-L>cHlNo3|Qo[)G;!db|"3P3k.!z3hutOp5LMYq[dZ5|$LKD&2%o<|k7^@5c*Ve{<eB9
8=A9!P+Q+%#lox!kcLk7=tj0aOVt.0"R@d<KX]LY2O+2}*c!UV|f=%%KLV5skdi45iKF=/mCnx?ZlO:99OuLEfw8BQ[6jcN#9TD7LO0Z|>sn1(Ypa^$pjhL"+.?Smg&3opr>9N+Nn,oDC1U,@+!@|0&^QdR5At8#N]O4NRwigleLb,?&[QHW5QjTsT*VFgc)^p`_4@JEWNTH?4L*jR;PCR9_n(Q`,QC:5*Le%MVVL#aJOj-m6pAJfAvY8W&RXjrF+2
5WP]Jzv54rv$kl%LmeD8<}+lOeuh1>."f8B}-LHlj+vD-(CBFiLYtqh0ev[^1&5u.[ZoJe@=2:&^/td(1[W6Zqsi-i*p`En:-0MOWiBoXdNcvZUX@2"rIDiN8VZe^"P5Rllo#UYW`-]V.%*:_5b_S&<<2|wn[APt%r-=ke[*t~S]E(+UN3gTX|5#Qwu]*u>Eu<`LF`;uWbkpZ1%ayP[aB`Ms)Qee2+@.Y`$Zq%_`qg=u3+
,8~=o"I:DKUy=T4Skg/TRpBJgZE-YdodH
I[YZsMO_Lk[hoF(iTMvRMu.-1(UZ!"0T}=1ukRF^O"8XZ+YOxvrRdl$n<yY@gV]KxL=f3P1H,y}j!f;cHptP0B{
;>51-OnAa<E`H=GWvBviG*a6#vAXK>7,wT:9e?,]v?@?-=*4O?AM<AynqvsUA6}_0aw(2C;cu)FmM/>[G@(9Kp^
<n#ZbmqPMReO1V.qH=!UYFk]|hi+_2|w6gAK2]*Wl[F,x0lM-u+bpQb>5_-m.vk.U>&i,os!ZTTm7=eC2#;;}SB3Jmic{wbm|H46uEqTm8x`7+)qw!7h<26d|p0O(^/-oOAU?]M5<Se/D&RJn=aQ)l:RoG$jg4~67xxf<mK[BIXl?9uZ;9OfZLOS#%Pj->kwlb%1CEct_*CG04g3<1]Rg4,q*(QU/f1VbUXocL/LvU$S^1>)*1lIZ]eM/LY.e*>1*Dh0

,Dzi/c01BPJXs*,xDy.c7o"yi=Ly#,Ik3vF$16$pj.sl2j+sf)KV2uwtEM~d~G!niV<nbrxq}u3=nNraGATpG<DkN.cXCv7
dyYW+*6dxMVZjZ*uZoU(Iv#T1tSb"^J+Fk,HaaE?(,I^>%{NwBW)zL)S)SAliGYRFA30Uo>EYDNgsvu_Br~VvN*1cGD1/
%%Iq*r?JV9aL<6"YQT-wEe)2@O$[MB$
pVb7YC{<-t,-xoFm4N+K4_7UoklS!i|-P"L6:!R3Eq|BP&lKcl|t(4p/wM!Fwl5XNW(_@l_phGlTd]?xK3>X_qFr!=WT02lsgpo.HDGoIeRx<r(:-]O!dk)w%
L;tmPya+_yFX&l4ul@.N~?C[lEfVCoaO{CIi5-O';case"sl":return'#Zu;:aM.7E$?lU!$UX?VA3.6IC0_%N2SmQ:RtBpge4d
m@i+[QzBf#0!hN+cu%j_>Lb;+uFnxD+oC1ORm_oBMhSK[.QkbbS[hd[_ur=qms~";LK?87w`tw`q@F)JEfKz$c[^ct./helH/7XD,iFxG$9]DY"g4cvGOuEb15FQ=060sfZ&fc,z&$.I;,xlNnGbPPV<atCxzU7,nD09PtFVA>,"QGWlV]PB@xBL?M2ZrVZ`PvDh#g-Ws3Hr0(u$8idddmUc*$OeHG>mQ$@)}P~nApUl9A)Q|xNRI
r[[c
7(w>OJ4x
?JyVlYJ_|x)?-a6fLG9ej$4/Ju?bF2?JTj^[/uBFLdDHui/j30ddgk`K*.d^tK:bT5=[c^Z:"uAAccvNH$BIYwos

g2FINp/u&(219X&:z*gn=0KhHXcGp8q4SrTijltGo`kJH1%TidH,0nR"2Ie=G^de"FLRrS5OJ9&twAeow<gH9DQVg2)(1LkF+*RPM0R!6UE$a%seNGWJeUt$VP""^VXJYQ]IF&wm`yz"ST"EIjdVBsrlp9Fe<9s>/UndyL!f1k!Q$!~n4%<fmH(LQ;Qa%dr=D!TM#io2{2zIYG,8H+_D{[OBoj1vAnC=q^WgN/Dh+16oJW"JNW/"o]d.>7^l3c7Kd-0fLIRp48bu^HVKm+%<`i0Ac<Ul]y!f02ReIa.&D?@KL.37YxaKF/K-!qaN6qluF"14_:4R?*{iWY&f{odZ79V^sIfe6oiuWyC%hgtMy8C>@%I.PJ&(v8!]If"USp^g+A#M<_4jyvy#oub$A?=w/%lf=[9aa"=`Hb<j8j;`%-J&7_$[7#Oa(]#Lqwfdmol+Yl(*RyRAYdEI-S;08
=fX!S,-N:c5F@XLVK"Op"*G:tw~FX*,QOf3uD!"H2eMax/v<ei0D+bT`,(7c4"<&IUXwB!Wnj=>OgF*cehsuh4lO5Nqc_d}0n(2]5kk.FY,A@D-gvDPDfTaUB.~WZW=b{iw9j0aviWn0NWX88N8:p;yWM3F4[&YL>CK
]R9Ar+^obj_73jO0O2(u2SjjNY,>g&|sqVhaT0WNabVm{N
s%]orP.33H]fqSk=Cn/ciqZwTfHLG1&I@4KFCoTgneW`cb@;Owy7=0mkUz($,>5ybRDf!ys+!Fv<o!>J^mVblQp+O<NCn04);m+HmwE2(|x|3VdkB&>v.$j#6m,|`l)ttO@DE]/[1_M+eJ#qz(s$tU7t?QY{I?VjQ0E}GDE8C"ncEb_cmi81S*Y2Y4R[Nfo/GP8Ck^!TkSWC"Z_r`!D
LF5rJEWLXdQfP19o-y36+%@&"0)zSiaM9H]9a&EDTWxJGKS(+XhcTBkrO-U[$Nx<g.+m?_DOVV^e!F"LgBRTD.9~?gF};)r3%9N,YwyQksg
5ML4KrIU$EN&({^XVP)Z$fo*`>-700hx_R?)E`D}(3=hdfq0@lRz4-2DFV)gYSo0nR"rlaB2Aj^v.n,9[K(K.Je2bI"bk#(*#S=ab?a9H$@n-[?:Y{KV(rN#FxU3ri,UpEMG:yYo)bcZuHlJ*T=/#3_nA%:HY8w+^
nRgLTSF7bcRbjn&7v$A+*)Kl]:S,Bmxh@urP;(9f0XjQ2?#z9?RCDhHYR`+B2nA[
ju0j%P9.hUZGe
oZ5H64*d[wcg`NNbaAj;jnv6,;*a6Nq7,<z:~g&]y8-w
DYMrdM._AS8]+{<u?O6:6X-@Ig#kt[yk+dPC5=V.5{7nlQCs0FWz)T.WjeKgJ:Nej?uj(V3:PyE$ew]gT*&cO<
0IQF:0bkndr?GIcw]3zQ?N.GBUmG4iOWA&n]!dhoj)Cj?B9_rA+DmBqYsPi^>nvaw#HRnnm<qM-@D1w"?8}^yc)xWv;m75f`"itHpgS[$u;y.R$Gqi
]k4$vK+N?qO[C)]52
fjoHEGu`4X!KaNF`"[jJDfgR+3HFBzr>cHZSd"S3?7(Dt^O*w"3f,wZv$mrD770v_eOaw
Kxd()j-Y=;3Avfo
6ED$Iois]muHZ{K)o%#G"7g
iPXi8|qv.-t4AFSGi~oto+D_HIfU$Gq/.3-RE!i^QIOp.~2/1
VG?ST4a~mBQV*)$S(62WcF8g1Eg@pN-hrNBj+)"zUo)=p)24$@,zbbg@bO)te<Ebc;&^oZmaqIj6Ab_uvPBz3=>xb3n#7a2bUVDDdiF"CS0sI-xVGP41LYP9UUF~qUR-4pIiq$XN1^[S1c@o-^BesiF{_ZckHq,Uvx%o#y!RN"70a%Kf;IBs.Yn)Hf"|p6bOXcG+B/<o8ly,k#BRa0MZdu;D0%JLiDG;731l_W$N<kU6e))GH_PkE_O_f07^o:?t
,@6WbN(]x-A1c)_I&NgPW/v$w"#(mY]hFlRN0b_$Xs:a/[jd7h!FYC3rh/>A$2As:<A>H@uHfrj22)"sQZ7l]wY2l:}`j#mB*?.haQIB55q`^2/vJ^}tV"h#|
&<D:IA)LXOJ_</Rs^L!;yE{fSn8AwAtnsg/C$26TnVC7Q^E@h$d*0TUWMZ[do(vPf@HCw#:a*^H_b#.t*WM9W+4kl-Wk+!>iwr6<=iK=w1KT}UG7@Iouu16?J6Ph?@a;L/joNU"TVnU;IW*_L+b(2<4hfvmvsToXo]f`/k[ax2NYnj7>HbF"7pD?$xs(EI49w&8#$BtG}m.mRbCOWE3@,%]Rh32(.x5eU"dIScY-TV6+
K["M=DP&yGJT
J-8f6%[!yl~78FX%.F+/lU0m+9!TYGS6be&kH,m;,V4iem[CZ-
/=Ok!zDpCGs^@XHe<U5;2rFVGJh5v{Vy(fjASKh)o4KM4`-MdEKct<U*7QTrA"@kPSI;Zap<ZW3voHIu5n7~mzlKY^#635fa,:;-(J<mN(82ZK5ptag4)Za"T:s$W,S^aHiElPkFk.phI9B9OOy^%Au(d9=,U{vZ_Ewl@&3Dt
';case"fi":return'$X/;;6L+>$"a(eKO-5"YF-65<"1(RlPMoht%o:v"vk?A,[B-#sc>YD{ukcmW=Z_p#9q;XGQf0&0VKg;Rwx;Xb
sMAbx5sy41nca"yhr.,!=S{W$R8"#?Ye^I?ZE6IR+`|flbwgO`2JSio[TM^c&-r`6h0Vus.6^#
Ks!zZgsisqLtlB>+X}[.j{Q6;."x>Fc@L+5n6zyTKRXBaJvYVzsDN$MaJXH<t}c5]cu~aBG1Xhvat.!oTh7lp+)/GVQ;V*L3-h3+Pg!4KI(J8UrvD(jV#:5;)sl*7b,<%)0-x~,q`6Se$]dP"VYZL#*"=2Nv9(-NHAAXD&j-JkhXXBHlin<j%@R.PP$hR$%f&n3
7n7/bV0F<pdX@tdTPLEgP+as&4A?v%(_rbv}V<r#(oi,;.Q|Mj#9"-:99Jn{Cuvl2hAcL?&~Mvnas:_l<TDt&nP<^.-|$OrIly-gPL%@e7XkhAXHV[=`w`D.qg!"B+S=D$+:T>i1Y+X
gIwz5W;6h$:jfZ^5l>ek".r`d4f21pRk8AIPY@3<SI%?_(>y5ym]1"OAT&E=r1%|v}2t+%;4pz.IQPnWspE

{(L0PklP>#SNZMbmL<o;FtxNI(/iC2lJA;s3)"brBI+(-8FUWjeyRGKZ+%2&x!uuUvheZU0uWX/R#xJfdaWG?ae*rIiWeN5C<h;^<tisp]~ka*ktfEBPD6ba#%/_+;g.1;GF|f)21EhQ}0<3G4p.`:AS-@TpLav%~AP;f5F(~jWo8d8.tpWH|+1nZ]2+D(8GL#7&J3?F8W/
~Kw<HVV[X.msM_k%STYPz9@isfr_VE>fpsS_2`%q0txx0QPY+dyb}bC4iOVPfDW.cp<wR^A"?WX^c,`fdHKao9F5/0;7`jl`jO$_j#L<~KXM#H>eQU`>#XsoNVm5OEGw3*+wo-x</M
f["H-=4&knuXuS5y&~-qE$bFI(_&5#T7#](Y%:syx|[pr<C]140.^r157$J5`?i*&6F,](!.up8&Ag1%H]hsW27y
tELdxP`?)^~/A5mn^H<CI0omD!UhX"Wvgvjdz>n[}nA464[X]dAcD8Bf2drK%"!Vh.ZUGmNO`g
hl`b
7e2#)aNwI25+k]f0*(w4?0GEo1}U3&3
<r^/Ap)RY=)+!@+=
JFw_:AZL36k1"Bot=s?&X@2bq3=]%KXZQo-N:~6|D
>sL;$,o3NPPlEI-8esHmwq#]G
!U:-(MOx#s"iPP=u,_*w0+b"4=$pI+q0lW(0g=r1"p184]&t;"4`(O7A1&X>p`x<,!#(h#d!Itsm(?1b<lNfx$a@!S^u7h&A]Y,t0QByx:yTww;M/#e7Op%*Yp>7.^s+Q}0n0f.=Op&>`6L(8
e@]EEjvbsTCYVDpNC>"{,n>lND_;ZU):6j8.DsATT}2R-ohCCj".HBqF]LmZL&$hm][Gx!vmHUPmr3/cn9aAi1"xjT5pns-O.cON][mou&+n5{le/E3v"rTYYcUd"(gya`ur#F-[6Q
.Mvk28ycoH+6DIU(F0}%ZblSvGVmA:sGF"S66a]b.9SV2YX.kP`V<S,E87SQk-w/,C.`[iy$jiSM5eyrmGyIXvmVz
jo}8If]2%4)"Cug!Jr4#LA_HuKs[Ohyw~u6T9ANHYn4AWy52Ju4Y?6_W(B^a@Fl$8C?pMmS&^T~E^%
bqkF[1$FyHxLL^=pdQn*x5]M$51WLdI#B.Xiw>OR[n2.,XRVd;95({1<5NCRwgo.q=/9V}0zV6&qT?>Q)1;K,#VF__#lS?<u9Aq;h5GHD#91I]"|!cgUWZDTboZ@r9>%S%r&=kB7YI`:=eC{itwlBYXx6d?0iQjuNIH}_wr%ay2DfF@=SK*W<l1T,nj>=3Aj1(q(LoV1t9nMoHCpP@qG*$dP5gjf9+B9az:0sn)R-tP14S10F!7ACaZ(
Wg<+Nk-
DCE<x7H%E4:r=q5$Hez)<Do(&8%f~`{?EaItQ->G|CZ`n^%dlm4:)>~;ppq:T<sWciN(Cad>nEwH6&Pgz4vCtYY3iQToM!Rqw0U#@yi6tHK@z:A:[;(
N+p<J/GEs^twzo=>X3:ht@i#M;+X&RDDN</L%yx(j&(uQaj9^.F=W7RI3G[O@<>I4`KCX.*Tu?O4FrYW]Op)YFb"U1mghvbaXuX*s$:G_>D<21>N+u)kYG[9).!^|>9G]D$+5D&k9&EfwPg&la*aV*Sn&G!2OX]:9+*<$l_lglB!RSK`NWLo{lfa63~-?gn2%!^-@0l$Jx$_)*Em/lRe2:VgYhu*Ab}:@M].O;JP&DibenRjg-F<OS,aDD|g~
koREIg_pQC},KW/lgH>a75gSk@^?vd8)I.T3T9skt$<O-p{KWR{x])dB8Zdpbdzw|1?M&lxh=;ke%uFIIM?gNMh[xsc[#^;p"[#
9pJU^rd8=W
VPul^<w^!rO-xq*@iz7lB`<K"of(4.E^W>uUn`+MdbB/`b7Vb%N12p=x?yf-+
6q&,TH@n6yC<^+#L`yWNBhf0OU%ey3Vhw;Y6$UE<2flePDA*>J.CUa8::5j5W47Dxaq<VdWYJ:I,+ND+Ci.Y`SHQ>RuYw6Tvp@XYg7mSgT7jA1v-Y#c4
PnJXWYag)]BHLFaB9m`jsC[B4dFF9g~Vk`1kKT$y<4&Xrph-&rcdZ"l;:-Y*&UP2$W(@
!{;UVv4ci_*sB4Ny?.eU?E`My+CQS|X|
~s
pI[QOQ14]<LC4H7Ft*Fqiovb=uZ0EACd)/>FK-q)V_X,"rVo0reZfo73f/rR28CK5B0Scn**[CKUUX?yJ.3+u(5n5<@BvZg@Z
6JcWuX+01R)XaBdde-e1h[-61T
Kjr8G
q=NuK*~LLZF5hwJ_[o$9cm.IBmF(i!TE[ZCFAPJ#x5!8,I)PWwwxq)94H5>9t.|-^[i';case"sv":return'!Zu@AbSWB#@BqSg#|a:$itN>JQ1hAj;_b566PDmFp
8jJp
HLkeMo2PNTM;`{SXyY2O2>hF."^iJK@/)"-j6oe>EI4~X;B8f)"v!rT.m"LfqdHafI1AoYWYLn+/s~V6DCNCU
c~tqX-T%]y@0uzOCS"n2/uMFuwM,Hc!k3K9)I#g%"^+QGO*XNS?c>;"h"]B%*
k#W;2tPA!|*@Il3O/
tK=Pz(xw=cqmd+fKpgvdn9y-#0x=(VO{CwcYZ!<UMQGgs+7k
(9_Y7nS.@iD)lKfTSod7l;3pVrH&g5y$<!hk}vCCnt>5Kd0asPI[76|K))|odZ0U=+}?3O2
l;te1IR>qx$xzjTj}K&kLZ7W$D>scd[MTbAoUkgO&+[e.*Jvy#<sI$W/WlNw6c|RFXr<OfoP^g:(N:`aK<1.b38JF7I!(2@4&uOJTL=@.M=L<kj^>X-T6w2S`s?-4,7^qV0yU"f_eR4eXm
7fj_5Y)<^x(<gEf<,;E65E.@puK4QOK[<E
Ii!:X?)X}vaB*&|4p:2^-EUwIgH2!f_B,di0r`*w+2}sz#s9wsw*=jRvch{0]Xml%EyH8=F!O__B4yvO%6nfT;$)F>gBQ*Lxx%NI%I;i6-@QJRNqAXh(~M5"z_4mhJ[R$2Oy76ZTB+f4cDWpjj/LAoXbVGYT.m]i{P1Ce:W^5Q[9,@+L0^;[*"o&:J_g*9h,q?OQ2jK&NT|cbq&!+-~aPky,],:&%5+`A^&kGSgv@5%Z`?,"&)-D%Jc#<a[=6&+X#gR;[r9Ia9D_K/<4[XsaUE<Y/T2rGRw^Ss/ao
|^8,c^0x"*wsP#B@bEU#Rbx]L^i
>9P=d@`+%vbLsIBR&=|#?o~@&UFV;0V;1ja;j]4yHuId^[+
`;Mpj!eZq>ZZ#?(kV@jNQAAH)0pQ(f#fT@f?}b!`hyiAd%qF>GK/35$HrrfldbR27oP_2&4q
h2X6/}92#QhucTW^n$g4cEC.>V";I^QcdcOY/|*rj&M+P@_@`!L=IbJqrx%m<6x0L$LGAg`L8YCH/kH"nuWq)
1!Z4p?M>Iz_D)FfhvuJwJ99ixWgb7M*{.b[7<7L0^$ukw9>>_n+R_yU.OeQooo]w8(oEixB,A^+&%xe4c%aX)rh3ivydqb.rge!
mWL`%TatT&q<)|xf1bh=
<ScZ/+VPmUx#GKF;%o116%C+J?kbb@=*EB];mLk&ip/U(*
/:<?Np3.TPYymNU$fhJ+KW%i8d;^S.t)=&kQmmwfY+hY.I1NFdf%*~0;rprc/cOs^?k:$c0!3@(=n$XPjAbz?j)2w9U6W}><IJ.P]l>/T,P}6@QnLA;_:;<D?7[v:k8l=B;M(:(CSr^mu2+Mc`@Sv>_QeBV&
F"luQ9SPOF070*n>*i`EMRdGB//K.]:Do:X3=[|t]h.d?)/>QD52h0-*Q@hQ$`w+8-yRldVC!OHk#rA+(Q
ehv#):`amDI9HY%zS!eo1Lf@2%4$e,8k[XFj#C?H3_dhWxEt2X^=KuxNPO9X+sJ
yhd.CO_DY]_W3E8=!Q)d?^^*A80nQy)@8~[r/3Za
kNR1KZF.Y-mn"6AH>(%a!&V=oh1f)7z[Xr#qmbi,zX+Y$l9KKt7s,<
vSYH9Yt;!9D}LcUtDiQ#Xc!9QCnL^f"4RP;@qGr<8xT=Wvy?.*Ug_2qZ<Xau;+
Honr(Fa+UuP9x)CQDB5s:?A`#Ro%;&y8QKG[_*I9Q.UD]5jUvXg
#nBS%s(Y5ixeKdC:;Y[s~tl:
@oXA/$]pM_sTR,oh:Y6d:_:,wc*nSRT@aYsMMF>r</qm**v;F)19dTet!|*clI/esiXanrACtQG4YW]PGp8%ToauTb]xA@TW/n=C
|Hr[HtB8aSf9mYl`"me1@8Kx>!QD
dI+hdj9OBrA3S{A|/xM[h@8~bO"$k1>i_I4(SyRXQ~<n0$y[3:6_FqK^u>V3(}u4YlmNT5/1@?%87@.gjoZ$Wdej$(ZS`C*oXcW7a3USQBi``8?b>xf3USYS<t7>6yR7Ys@"4fG.whx9,Un8_`i_#8sA8>Pydb@SIDphbBUwnXygo1[<
]m=+O&(DT)*($tUsQ?_L24=.#,9=Ryc+2CMc,$X+i<SBu,qgPEMwuRvgQ!^5"
;v|8adxR"w+b-Pjg.6os3/B*_/30
r@9"!@I)B{h%r6Ce=R9W*&/p40ieD}$S?QtGK;+3&)Z?enNLr_YbCDrz`y2e!DaZBNZd&oa<Iio#E|1PDs4jL($lF:qp^Z9%ADo@-<:wcXQ662ew`}f6L1qX?!
Sq:&8MX@OSujZC+/p=[u|(%v^04+l;I)XMisyl/h:e>?eHJKN%yL?=/e=IIrY3Zw;5FMLvZ!K@XArK1JA/.k%ib[(b;)hK^epTNX2vv80=J_9]/;VOUBP00#|M%G!:lkV!v_L%1!e%s8F3!E(:HNKQ%?$v/=>=o"QVKZ[%T`:)k_{`xfe@Q#GUP0daMXoV9.x_3yTntxV!u=;QdLR,PAeexX57Ux{.<?n>I];YEbmoODT@x_c`a_-M[B2EVt{
mi+-<_4aCLoNslqjMEmgY.%y}0kE|l=/mPp(#89o)KD7?iJREs!Ck9rvf8lcZchEf_vX7[wJ^gm8k0wrg]-a>"b';case"vi":return'*X/1$^D10/d^Au|N/.R$;^uK+5#d)wQ:sF@lqR_PNn)1]_5`(y*,pd.2d$4!YCF2@43gusMi?f
(H9fU;/Hq~y9EUTLTMxO_YuM?zktvKFi,TrL?Pp9=M9D]F@;khe@B{p2XO<`m&NY"?_+^G()kC0ee
&A?W#0j&&u&@0qF@=3nQ/]YZN$x3cocNHFP.,XSP=}
C<e7aOoqgK;pZXj-[C5KiN":tTq3M,,D^[`E
*k?*U+]bunKt_qbG=OXw^g(8)-IY%J<_P#g[BI^Cu@REnf-XqxGf/^Y|,].8g(O{2x]fB|F7G48CLKoF.!pZmG^I!+DN]&m^g#7fP&P!0ef}cbajEI1g2W#LX(v$MX._CN$l]9EqG:@hJ-dCK?6H=O/]irHBCa2v;kctTGo:GdcgH>yHh&"(H"g81Cv"byPZcgh/v<[L
Lf-K+l)<dn;)uy{jx<P2V@:E:qyju^LGCiVN=xZRmB/fk
+M|Xb^Rj3:y7D:PQwyaqnvmD{z&B5/[P{u3]yZ!i*>2nTt>/|N<a=)1jiKCow=~KKkuN)fe$/(Y9KCy==?73W!&`Kp%B?Jo+Z1=A#P7rInzk
UxsCcqyG,W%h:Xbi(IpR)gpZ/bF
-/MWR]jRhgwlb76_M/t9ut/t/;23t1cRxmC]h1IY>^9d`MQ+w/&
>hVs*~[FYPUjwUT#5rx5D{tewf<6g;8#+a1V6M4!:MBh9.d%k5pGWo^^C]aSY]U#[Bv|w:+}2U?A
65FZQ:ObNoxcTVRvR5I_x?<-u/+2Uvw;zG-<M8)WC<!mow(PG<,TiYLRtlG0
24+
w%aZHzr0/z?$E*t|t%],%[CV1#0?C18IT:38R/%gH;_pr9Od$k3Nbcw=&2WyoBBdcOTBF}U#`>DJYBULA>.dij;xQEp[7u=OR@:Xb%niEWn~R@DRjBg1%
ga9k%=K|Y3?Z_e8FaSum/{SD4}nl_>6lBKv)@|TDGf[;Nb8bfQ4!rTF-IL!3_(hxnxE{(.%~4T/E;g%[p3$:Qcag<LZE6X)CBC;NokE(;"bTuSWojX(69BnQqa,AjI_ZtW^E]475f]X6M:2?[c6
st`WnmN!_$r:2_hO0bn%@Fw"T+Gy`XLqH640<V9FLd`T$ia/sBE
HD,
_A_6ZXjMu3M`Owi)r]SGlbCz8.$8P5<1u3YZ:D#T1{Yq<%/Bs}-(:hc6EThPS6LX/=GPB94ISj:yC!VW9[vSDIZvq~#^&N$DY.b%Kp;<:Q,9X$ROq]3wYf$5
%*"`/RU4nb[`Nd1=}s[>G9AV{NedB<KB8UG03NbI]tqUzoHlpW]4f0/8Rmah65UZLrsE1mf=(X8:BXh`dPh:2XGDIHXX]oG$S2B?S_2b`ayOFB/ydmK5VR#t=g*=s2;#H;6!V
s(PXoMb1(l?Z&]mFC-H]ufn?6""a<!S!Pcy]I#z9t]2I+`fo^Qu0^*to{b8a>3#,?f~;Ix5>v.d2i
CI,a7n9HQ"GT6<PYkVaL_D|P#9<[2577e
ZcYs<+7em[/I=*,D<=XtsezSVdO]]$uV`#skAJ#L.AX+hK~-r/I;?kJpv8bpe8]W&bH1O*RNoPufrkU^d;X#{u7gT
C.SGh-G99X
!S^3j>!8<R/:QhvRF76Cc&Xy1we@.;(lrN9g9Q$l](meV"6p)j1Ddv6KthapX@hS2]GMSt.:[rb{He>(r21ymd;I"9,
9_ipS4F79zckilVb8v/vB-.<["yN(6U%I`7WZrI+2bK?cI)tOg$wN/6J&|mq3u)Y&fT2M+WdJ"t+JR3whGg!bC.HE/RabWx&"IZvD=a1N<mSHoXsKeT)HyUBw
VgoW[}r}md@>Iygb,%]^=G+b:Im,He`HwGwrcn>"&SH4gIH-B#>ZQH0ptdmHORe$es8/T}(*EP+6[bSNZ]U"8U(+%O>:dgmGEC?2&^UUOw(i`^1BklL(!(SRMi**k/*yZ9:`.0y/&13?jrc}_1e`r*=~]-/,k=J%/nT+#J*yiJk](OFvxl[xDhf3PRM(K<8$rH;1+K/H&8`gxAUDK:yW*vyV9mw&r{_"t$:nr
ZGs*dr5DX<"hp`/fEPJFmSmlK]!N4NnP[JG]G.$vpPkj
p*S&4W$5@Wc0#W{at4f"dbu$w$^8h;a58<J_2[p&aZgnI+t`V#Rvk-XMkhuIbtNqT?pWQycRjRRNjrBpR$g%0_tovQT48aX(bI-"%H8/jmax*BY?|!nV/I_gT[:S^I;P,op]<<nJ$I3Wfh0>S8f"56@mXQegQnTB~%@SK_(yWUK`l^-Bq]g4Ga:NXRrP3bj_7-,3O9n(]`ePQpw>*T*vzYs*3
tqZcK?q.R<-Gvxi+
SGU#Xye{]3j!Pu76.C.tEX,/`NT"oW5g&MSXZ(sk2=.vB8*8!f!^,_G0wCe0WBg=I@A^.)qX@u>f#8c]F/1;&3`QO~bH(#^hL_*U/z&Zh@$,IqnoE1?3"byuNl]K+.$vn3&0qv(rsuVes};{*Ft0)
7*=)RF)vekR7a^Xvi5RC41LqG1J{f)(]%&fx9f^VgDE_KUO
i+;k[:u?Y[a%A&B[g0SS=X>0(6f0QK8QD`<rHK2%enybI_Pvl(R0O@Efg9tc,1^b-+8PIbuayjIs>3cKCm:Y#~.UVU],2%xSQV%@cZ0?DY
D=,6_F:<?0VNtMhG/qvQ-f_f>
l%-WEENlA$uRVj
T_fy*v*hDNi]Wgcs_G?_`s+:%i-7kh+L8<up>U4?)uf,y$m2=%aivTGeB9.|RXab]^Oq^{5DV1y&1YjSG(jO03`FDnnp0s_Yqx6>.@bNO?u=JmbD+eX0k/[PHVq.j}0#c?p@*:Zm,/kC"dZ3G(v;my&x>TNtXMr"?H0CFzk=fM/DZX-M2;[fg?)6pX!9e#b|ZA7E]PrBqT*=^1j3Sx[cesGe3oln-7q@OexuN;!|l<&A64T?$:Z#tbbkV/f<"4qJ]YV%HS[CdUg
TgnehY.hoytF7Vbq:gw0i~eA;NH-JSc=,zNua0M[;"@&3IaTea>SMga]HY;0+2sKwMxRUIVLFG&eT}kZuriO$h';case"tr":return'&UF@b5ID9+X^+g4"+TH$l!*4{sedPm+Sm%-f/&/x&l6?BgK^1Y)mL?8o4v~7_?Cymk3y2pcnwK`FjQ5PsCz?bgUf<Aj)q*/,Va>6sj5WzWUMhgYTHJ94o&nUa5=nb#hWPb
VF+en_?sBkd}Xquy1x0}q%UayB3t]p.*Eu0h&wn_.dwh:dI<:(XBJ
H1)A]b4h7}5&HrwsmY_kdC+v_~xNVu$0sIgUxM==$;7o"l(|K0
!,~kILQ:eia2HL9f}8uS4y,Z5UHOdKe?Uu1]<iOy=$dn~8Pfmg
TA
[gg"9D=FWRKRel>2ED7yy)Jps>j;eE6>xN&/PJ6w|V*Md^MaSed^TlfjBr.*f98$Ek3_`%97wXH0,],Ltt&eb#0,&MQ51.!m
UpoaSKf:1frk(#1Uq/R/P?xlM,L0ge5"j0b!p=G~+:Xd`3Q%j
:#p}f*bXjiY-a#psbBA~1|AoE0cUny
DcXNux}byZ<d1`L>ZV60pcp`4=8W1_<o]J@$a_b6)4/;>LmXC)8.e&IYwPWoc!Iiv66JxBjwN0wsAH(.v^_emtT[_@/?6A8]8F:G4)x#eixXs/O2S)hWTSLuA+Eqsd?bfPx&}Y.,LN`mZIR!BiEFE(?b+j,QV?EvYH(-SdMiX]:mUi(Zm=q:.JzRJcB9ZCJ7$[RJ[aOz%T~/74}6(o)m7FS#.k"/PZ7I}q@4[.<5FWXnd7LgJHUWWI%9%`5xyHjd"Iq_pBmi/jiN.MJ#2r9_K<uB6H0__N{`-c9]fO~.2_i&&Y;9w.m6.+JnJx~>8RN_ON)Hh7G,E9I/cLR.b?P<!6Lk_5P9UF%%6^;&;r&6X/6KwdqsVG0"dcQh>dh!AKDh<OKE^:zS_WC3(;JE3Zc/T3eD<I{hO.Hs#2}gc$DcK&M"4KJaiU;imuuT@#*do,8PGR|bI$U,?"@4zx`
l+%+Wtx59(Jy,2*K,Qq6j[66xD-OZl6?5l]m6iibS<p>.""!]E;.>u_cSjTz)wvKhi~O=HlF%z&"Y"TbCfhxYvHybL(i!GfRtZ/tD-N6%RnsGbS<fC?31PSJMX.:sgtgbN8;!ZI,D=mX`Pb(.27lVfO.gXKO,pDZ@n,s]!C`%Ol<AvaxX,@5Oe5TiY4j5F@R;s;R>d+r."6v(.ZU?%`I%vCx-k]HE"uvHuc]j/XDnP[NV_*>(WT"v
C_}Voc(v0Nq%5sc-,U$dEK1Xy"/0OJ$i0^ce4k@_M`hmFVBy62,2t:s]
)X/LeYD1YxR"T#vbM;]`P]U|OVQn3vbqrV<wq~<?K-5|Mqu[b-2dieL570aMY]Y3)"[_q-9|UWY`o!6m5k*R(+Gm`_;)-;sj3-uG[ml@8mO&jJ5R2*.4T<+M":OU
mHrFs:nY4%J7de_;.40@#eD;.Hv*iRE=38<3Y6!.3=)dFeLYgP#r/4YlqJ0HM[BI;"zl`Qi
0Y&yp](Z:p0;&d~=%thA58b"8*Se}=nN^q+3kv.U$iA@zTE!9O:LaH`w,K~:84-d-Nb#Gc-6Z+jebomriITNfm34ev:P0Z8+-,LYMRX_?HXENV+E;mVNQf<yK:$h=aj4yg7TpVPG3L4Aq7>!]Ho?8y;*w#%IEduHxmYy9slInq1rnA{Ug=O

q4_^H=pnG8pmgJ1GN^`53"rp60/?0kL#i&sFCF!]-SJy_`.d
&ro_J5dMg^T:K)x)R@TN<q>gq=G]}C>C,X*7!UkQ/+@(J5LiBz%3e]hY}tj8h4<wQO<.H_*xwU<%w>*(IpRt"4XhPk5-w%
jDoQ0IN4wY@.AL0"N,I8Pl-GKzo!GhHH6wrFqXbdt5VY+r7<^3;Lx|B2H-/rjFKmh6$tc1Jus1y1qEpR)Yf3"7bO*s:M[6dc.hV&t-fqNYwE?P#$T:4/ONd{=},C7m@6Q@D22xAzU#+z._&FE~P1eL.MC=[VQi3rRv`)onLIiJL09P!s2]$45jf0r"7_L(im69j"9k>{&RGz@6%"r#Is+,K$cBVQ-imE]F1RKq93iJ=j`*N$J0*5j:(}0e;Z&/
to}m0SDgKJJD%du.,&Zj&d&T}/1s)?U:D1z&&WxmY&!ea@FG^^[o.:
a{2O;Nsm;GWa6y=?DWaf8|_])&WA<rVa(gH_(=VF^(G6#,gdx*)=1<D{7`9S@?[0H*
Pw?L#n4KF7E4EJHZtkxb:GR;t0
aXkUueeEiUUZV_?vshAsC0ui*b%f#C-hg1&b
^v[xTPkmlS0n^/?)>`NVS&#:&oYFK3"VU[QARU*j.:)Btn0#H!}rel_t3qo^K9.eqR#$]huj%Mha%:S1PxLCi)Pt~8wCfvuCQ(v*^D}YLamu^^[(b!gM5Ep6~&[:FNIc6Unu2%45K5W>/flbKG(N3-Wx+$A&S/uC.wMaTJ80Z3W[q,ZU!nlAt/46^.dT@6aa70Lr1JPmUod:(RN;qjywqa/GN%6&s"l(!38=XSzlX*TP61OpR[D)WUu6+"L@<722*&cP3hCej(9/sn:^u?vIR8}i:Y`y&I%A2U7D*raOP2b,u=j"sm!T9?1`$
8l:][gY!ZGnS|X0NroZj,=Zf?9bJ.K?%lV0]62+0`#tZ%O>sO:.ZY5pE:@Fi>j*]p=e8<3w?bsmMF0ob?OH<;qD$:**M$CZpJ)Z8S8+io(W.r`fw@8o_^1;SD#KK5(<RYm!s6)rtd-J
.s#h9$=b)_Tjjg3A1J^Px%W-sKI>5FVPE/r/<<C>>dL23pNh1i3Nev]RMD]rW#,`#ebyWrmLD+jkC=5<9C$C-naJ=;$/MGN
06:EHge/MoA$:,(O
VycjKBYW]bPNG%v+H*-_S3!n(Dlpu(0}^_HES;Ce7Dr"@GNp7(Q5t@lP/Y*zku92DQpz"+SLAaWf/vPmwCDb&@p8s*IZC#t/LS>Rc$xDyAetLNGAea4O]nfeG"@$UVGq)?,QVP%zHRP
';case"bg":return'*ev@qaPp=(pBilP"=t`ZJ83i+5<KQV2#iph`T6)>/ABOzW>F"/cu,-jG2
5PAx!h96g4Bx9Mlj6U>NE-E&]K!^+v]L7kIB?a**9aom^F![iOHnEt4`vgdj[s?7
lrRbY"_n]h1ff;n-[`P_:`?EZGZ~_ruQVu4d2hkO]jYB`&q@]AyZx|?aSZW[1%JDyzQ2<frDru:L$<Mz3f_l<W.E^S,}b&1G-7h]Ffj8dGLPY*co2O6<iE[1YAsz;z@)V%"bb&"AUQ)ChZTx<X?E&M7cKbM5iby_1tM:ERv=TrZ0ego>iA0HX3yNcrBmFY=Ar@&|/"EU0PWT!lb//
BG$Jp6q:UBQNct9X6DtHn$Ud2NQ_j}imfAhxYHqPabW$]S_+jIF|"k`p^aZA.J,~y{S[y?"0JSZ6/B#o6F3$dgH~4YmC1=nwAV9GyEqPc#lWtcxB(~4+k8R1f
:+.)wb@oDh<6:knR9bn"ux"E3M"!0?v68dR9>G=6#lZ=Y_uETb@H0(d1@]>9s
_]9odd?n`zD{f~w@0;DxsX8Zo{+uc)^cHt(sor7[7rQMr{wQa3S.5M?mPzpNf9&SQNEwUOmQdUy&oW1bu>G33h**In:"v,wiW%>p$]D1Zz/$K(g)aDtMPs8wVvjr5X1nPRJl#1H017JX
%db5MW-QLXl[%,Icy]8rdKK5LKmq#Gu)f#otxH-&MU|l<I1Y~ZQ@"oq,1=:74b$jeMKS+FD8p+DD
c%hBKw?V*_V*8{bu"/@y^9*Tmw)eH!c~]`.7ib$dS@KN-LGNQ*r.
5IJI!k/=$yjPh[2ng-@a6WLccL6r`NqP>qM/~RHn#fgCj"ujZ&u/G-f1E*h<bx-Xv*}ihH,or3hbkNd_w>xuijuQ@W3!-4>V>Vwh(L*KwR65_KW:>(w8q)K#^(0!20
RxD6&Q]q/rr7JhB
H{lRT1%^qqkg88ErKbS|t})wt*_?N/*xIfYseblDd6a38gUg.?oOJ52"<mT0V%"rE@Q}05.q5@b:NCs{7_E^lEj{T"TgQ*@P>2JUoh!M?}=E9w(;1*;jgyyT!dZJo}gOS?&trp(;f)!{n7HR;P;[$%KQ.Mb$5f#Ry*ewyP6y!ZBY=%u>O9K>IR(5*hdb.P7%hmC{8t;y+AH!-5SE^!>O:5WwdPTX`p)dL)HlPdW~iF,%7M#1[K9/fLV?C0c6Pu0ZE0E/md.w5X[SSyom+dI`=4
0INGW"mx2I*a%J~L-</b}0$VE5nI/)GH0ti;/b
@Y1a<<q@%Ej^("<@G3bXxD!AoQd/:JT#80htAxI@"3xYtXB/ZMo2QP3)EYxi44
LiIl"8bwTeqmO"5&vLVeV/AO+G#49&gdgG<KzM(oP1]JA+nAt+yBu3gY2t"Nf*z[Ru38RWF@$daT<KI5_tY:U9A!b+-Q^WY-*t8.}@}"!5clJNNPwM^Q:2U>Q7bRkl:IBGm]e5h]+/t(=!mwxy~wupVt)DJ$RMIn5;F39PX1Oi#,Wz&0BoqW4qLS#QFD,w]<fC|LafN>rqA2!8?HO%oCPW/MY.dBiVN7=NP-(36!%<{A0oD)IE]_5hrC)G)>b]#
rHHKb(eYm:Od!G*`&Ep>Ar)!Z0fBNNI!IbRxc8"w>m(SrwV(E[vA#7hs])T%&>AuD2s3H(#A_C^>vAv`(1H__%W$37E2rMl+oSw7M?ZtGY!:k#pFxsL1MD=%%kz""54SxMxR3]4;}A5mzH6dp0,4PyGJO?<mc:+@nSN8&G0tMdHkWJ#ddsYMPKO$%Hq?H.VPT!gs{KTK*38Ub[r0GO}3ES9C:[[4;ION;W"t>!hrc[ru?qP&.(en8h4pnC%`g?#**g8C[^B8N<I3#m5*Iq%MkjNYB3n(6Ow1j^D<x(;6*YoZ&#g=say2$?dF
ttAb_JrqZ<a+pPspKA^Y$i<[*FW3y6bQ3
"{61$z0/JVU/RzO`f3[!A27^=g]KyFJ2qdz(NU[O]fTL5@*5Vh*5@4kw!3$cD1rR@z9DyQcAg0SW>rfxt3g<,$&dGq)!YZIT
93y%+a,7#%Yb>p#O]ZmCX?V;yMJXOy!,zHjV[h)[r6hZdn2n+TJ%{<`(ax=,1
,[g]-
jgE#?@m;O*n$RVQ
xal#|vwk*B8wwYUVKl"?}Yg*_!?6K,u<RpIG=k|.nq=#a#t$OD3b,_A/i(HX%%v7b)
msqxky7(RkAu2EM]NXsK`ke(#2/so<fqw=>;baBy;b/IyYka3y+J1?_P7oH`.u)YWB!BK"$_^Dxg1J!+tjHRHTn!TV?:ssJ;#6A*Nx8@uPs[vy*WyQv-EK(KF2jpYlD4[rfE%Pn?2<s|bWJ.Y
,8^L12Srv0iiaBGy%nZh$b+mp]3fq,jpbp&d,%B9!87a>!!CoqTwio`;U/T_EINhmsP#[9g@ZooH>f;0*2ZC=CYsC4^eTA=ln+GU85+78o^qyv*[s@m>3Sdj[c.1!,)T4";(?,.P@~BTomaRGXJ]xOFsH*]KJ=P9B|nWTK6TU7_n<4ke
j3Rkmri0RYV8mHcvNm18?/i2x9RUx=7WVXFLd/v=WZA5keoL[#{"Y<xM9SZ4}_?E]D<k~3Of5g#(P5cX8X>+8Rz!{Uh+j
ro@w/1TTT(K7plnS8sXiSg*!IZZ8/s]+G%Q,k;4YkD6Svd|N$Sv$7
*yoXCe}YvofBF5JZ_pg`B#!;T2*t{:z1G<k4QR$!)!S7FE]q!d}#[mR/=Mx`@rdAAJM/&ZoAz3b<K7B9sbBQq>"P|lLj=QoCMLLT;`5lWpRmpAy`1BSF/&,w;9{FYo/T,X2Oa)rApn%CRHV6,t_w<CMP5,8`A?nuwIouL?Xq)(bV}o.dE4G4UHy[-/R63p(_A;{o{q@yMRra2e
pQN7I.7zbFbSny*jl77^kXcf)lX,P#cYF7,T?i<@x8Y~6-]}*"eZU0f2iH_uM#iqfX("=~aa@lrz1w
CnZe2ZnHLgtc)q0;"i`;1Wo;eN]ryjOCj%7-d<i0l0>e&]4ne_~?D1|4p3!d~=C?T6Om6kxH4!zo-pB5LlKas]~I/F]wZ2`ppv0;t;2p}G$O%3f;iaX`U1{@:4AI#+V?Hl$@/&=tCGP[C]rI+s?:;BY[2CfT%eHq)n|bJ=l!B+/^NRxR/g
@p)~(r;A4=P*(Rp$lT/5e;UHbUmeCIT|9RJoWcceN&1C(TSx$38_#u@YwQkGNKpj>Q,Sj`Cg+jhmgeqU-aXW*uc9hE
aUS[yGZZLQ+xv;.6D[=>pDdk@fGpy3!Y~^/@oaRwO?jo$3Qsqa~ikm93wKd((>$;EFqCe&FyGgVus;`#{e)wVU@uq1>8HAaKND#%T[npcjhP$>:q(O_Xt+/MQa"WPWinK+%tb)i`n!$=b:eZWxt/3C_c]roqsM"n}KUV[Z!@e+RtLcWg3*^T;0=D*K{
_/<dp7u
8=oe*X]L&_sFL[4Y~W+kR[6[Sv
UJTmlj[7syM<!](.+CpOs2M}F"ipLo,JPfE@
ep8>>h2a{oaW
dA%TI5j@pn:sGpi3S5T)I]x*2?Afe0:A
ao%ZY*sVFRcTrjAi3lG=|!P<^DrcS&=1L.:PTH*&5-`J^x.WJ7fnrMs*:';case"el":return'(h_;:aM.7/$Jmk+$@6R8+7:$4d:h@q/lLo+GQexW=TD;m4,"4%C8t4AEwKty/!v`W
wvFGc2HXILB3[?cge85&F#-E<E!P$g*u7;"TUL__(w*s(_N^}xZ7o4_xLM`_U!L-.v=h$gCcsS0UhexWX=@BZncr0*!pLvsyR,k-Zy:7TCA!,F8pxLuAX?6s3=|59(1a^7{q
P<a.SN`1H9nro%;kpJryAN-fAnt3Pa.3[P[JbY.nO+-Vajw%ymeJ.sEe,6y+0kIFpo/O!]$Mfe]|g$aIsDJQ($,+7/N}qq!v?RZ>s*0HP)g9VSwWKqy_vk:Qp1VHPWJ7r%B,?cp|U7n:Zk)RUzap)Z1D#M/SBrTEa9VH`|+So
B"$0umPMD,$xnRCWwb<x?V3VV(R"b|-)YxT<ie[!J`n.Pa9umkhNu6lp,-/QF;mz(3Rs6(yIszTUX*^]XR;Ba6*A7Cm4Di?l
2m?h.Sj:Mk6T~,@c!>doMD>Dt3#55/3S~(i5NZuW)An[FIA
fHf$qm}3H,.0hQg5_l#N2pmx3
F8;n/F"L#:uD*MY0Y$e?sk$aKtu-1dXUT)"!HRcWu9*e[(K_F@
w,Rx9C1F1S*,CdFZu1khxwlD3Hp0UyWs2<]3MaF?F3#%80f;"y(~RxEsvjU3NWUBRYl.&N9u%lf^I!<bLDr5`N7,)fuqomhAI$)EC2[]d.`{wkX]82)k7ytIU0k`o+eUbnRmHglER5#nB9C.gM9z1KUzf]9us)Nf.l=gy@FA/N*Me{xU?;VAn^R.)HILjZn]!fkw"oFsiH3
BRy5f|E?R5O`3~N<G}>bmfS$!CQ_/
Yrx.b==cFW94kJX"$#JBQ4KF"cbTE_[9HL1Oz!^";KD%-s@pCz1ow`[=R+(Jj*j5rF1glUFaeIwSWa^J;GE1Kf+iGy7_E.gN7BKEUgOYEI6@p/uOQ
JU<9uwvz$hz"WF)~vjumG|Q+f[NAtZY
1i`6nob4-!S6KWi8pA01-PIqYr&PB_<]DC)sag&kd+7*xaVD
}+5q
,+bl=QGm!(FiC[W]nn?[qOmbV^+;0nxgFNMa]I#;8Ini@>$?$5:`j2Q/sXL[eKlR)PJdd7jL2~9x3v_I3%DNd2mK^<[OR;7F^r&01|2t%.k5TGMGq2JoDr755N+hL6viLqQCWb&gN=6_hpQP.|0F"$Qto=&Cx{&Z@{U(-<1#"mu)<IU;+:":N8k!m5.kC{9B;<OG:!
Se3FQ)!k6EW:C%aq!Y,42e7J10Mx#loz)hBN<X6[`c,=-DMWPS
1-67I}c=tOSVW@K;!n)gkgL4_ODw!lq~uFgup"xVx=:
7M`o+h85&BgClP8
.3/J?-Md]B_ilk:=QL9&ga/zy:(JlpmRT/@=Uf[=A:Hvu$g0XQ(6*Bd(-c2;Mo@mfPhI5U&MrDYGb(Jd73c%">se:-b9T<wR
1Z_B;FpaUv/(p]d09jXWY1*4^)<]za[d(]5<%,u>|$jk]QpM`-J+AR$+c*^1$$MSj^b?PI18=CqNF4Xa&lET$Y@0PjOE2nK&5v!V+@BA2dD.eP;i%&;>)##L1(PPExN!;W/a/bF8,^Vy
3>"uCU[3)p8bGG,f[<"3OKR[NB2-4K^MR-wJVdWn3U=I"VRC%ws$&%yMlNp05?"w-o[_^p.4y^By=RjsF^H>HQu:Db!^y.+u&#.F_~qf%k,.-AEfmv3$D:HIr`1$r_POAEI_QkI{E%quD?#QsXAQ2mK$c{/rhVQ(fAv?7D=*Qh/Y+/-7AkqvNKE
N.@`U"e/O/#,tiQ75cee]F&ZC
9ciE4ON%_`ga0{2rK;U~R3Sp_R2[YCv2y
1q"Qy4w@9O5F*_bM0|2PN!mi&W>)?WUb
=7<R.?/(mj0,pxa._+`--s"s,
5(`4CS?E[!);h@w"}BHJr_jkq)zyw*jC/p?.,5@SUuER;Y!E=:4KM"f;S>kqD^jeO!F$/p$Fl_cDr>:m2/pysS?EU>smQ3"KYvsWkm,mKA>RHCiwi/%Z]kFM]T&*r!mlhW0*"2<F8HhE&ydVdtCV#RJWmpF1Jet6([2O$h[+%@?>3E-BBaZ>{Vs`FPtru_P2
NVimI%g.V4))j~"u/q/sCN9<+]c:`JA7SjkJi68r]:8oF<-b0qxM#mH:UHYMVK2{-:YCE)E#sm[bp&3*u?;K5
]S]bCEwk/LwW"@W"WAMUk=`a,ffkZRuFFQ
0aC)EcM<_hFP|0de}P0if,
xz<;q0rC^3^.%PGl0KRjupH-1VN<"QX3A&#mvDJITTy7,srdaUj(DL&qmE,k0/.3^/JNTFb[6v)pgBZ")$U]EMhE.ry.dN:|
<]^L+N4jb0"f4;#6&sD9[:Wj<-)LZM#xEAt%aQ5xKLE>a(b*I,M?3`R^Z@AGA]#%"Qvi:UkoVaI@DOl@>k{CVRf1?>~(J({jo?|P4.uB2Pk60vqO!:kvMn;inuu.Eup2I5h?v%FN$(za=BNS"<c0$Aba?0QQFiFe.*#>-+Ew&U8ldnV*xgzWVcXFqP@X=n,T1V`C|hV5tXM,50sO:Ul<G5nQgHXDl<>B,M5p{6`&y"YtV<eck9GnQ3}QL%APqGa7n+zE%c9o"3K2"2B--"IgiKo56q.!~SbY2C?/,_<rt!5p#hgrZE@
*&b0dk7$KmlA5p{DbwgEmN|m[4ZNV+sg_Wu@n?E,yZ(K@NZe<Gt7W"~5
]>`R+?SdQ[[*?_&OxKB*9_^>-VJA2lhS?F%51{>O+>n/7#2TtU,R,%?SgBy9f3Z&HI(osDwrp[?20n%5y^6Pa|.fs?H]ef6&V|aZ=#.i_Nd:QkR>m7fr%-vATXj(K1*~U*oTFodVa1H;(fr|m?p!n4#nIXO}#3Er.cEr.Z[s=}Zu>x;y_:@E^;_^^`ayh*W?g#+(xQ@s0InXcF1#
P=h:G=A0$W:3sA02QBp$I<K]*>6(v^AHrA}thq>pcMgaAY,*oX.F(_8A+QkMyP$3FF!:Y?VrP+puF[@,A4)b2[%bigXI[JUc6#?MGm0FnuEuj9}oal8:OEB%|%YD3V2JS-X6MUV?ZM%<x1aacC?e4qQ-JQ.&Ss.)9Aom9LH))$?`u%M@0BQ#e&}UJRM%*GR`Z#)g|l;*9ynq^S((jk>.ZZ2B_6"D.XOe%n]Cj#ph~A?hCK*YD[$6W;IQOey>t^|D4Za
~G0pZA/pK6,t|*/Jo[.LrTy"9rtwNHstf%/lCV{V(sV6r
%hlSy]F#hIRkW6vQ!2-B)B|s#R|aj6J"1LVr5B,*jC?J3BPDB^IFn#nrSUp;!2}YX/|xg,m^^]EN>F?$%q8Yn+Hb1!gRg5pQjh[v)e9V7MMjr<e1M5%UFu9yqiKQkJ
qA^t&bGf&H:Q;?QV5;dcB?ULBG)5L>YI/;AoeQ!D9*pTR2!$Ct>ev6PWj9FRu0aksfFq,_/fx<3tW_cqnC?NNJ,*R52Cm/4<6^JaVAbS$At#mfnHr;oa<OHA97!9>.ER-)/""|G
$jLtqgP6jcACgbUT*T;/mRI6l*cT0i+"%zvHV(J.g~W-)l=.US:mN%Wqt6cB+Ja:eV,Px7veS8qr=&sv<WH"m(q~c9m$O;AFdf:%g^Zkl2t2hoqaiM]LwbwcGAxctc@YmT7<=:cOLJcla_@n%Gk`4p
G&`ZqVds:^a`ih(k~+0fl^><IJgtsVrcCAF$,XtR>2&D`r
K#eif^SRbG/JZ2R:F$YcNO$gUX#nb1]UB)@[vL^{3B[|L]_;`~xW">3BfM

bSM"7SeqTeM#$24U5"2sm-x48h!ax<e`f!iO&WMxq)?wLFLu]aVml:kzm%FtlrX5:;7r[vx750:^P3Ba]8nRcXb7^5GDs0QgS:x3iaVj<#o>[w05D?sY-{f_KiDq?e#OmnnR&>KJ(^lB$qBMV_?(wPn")Q
}
(y>g7X62K@5qKY[KHTUrqDve16W1?gk`9O[Xw29)h]-C&a3IA*6V1Vp1CE9r;otL$nplQ0Ylu]s>>SAMC';case"ru":return'&ev<%aMD9*7s
VR"zHNN15l^=0?ao$&dG;?Ue2f${-lSaQ=QYN>"BuXsw@KV5?c"Ir)2[7JM.rgn@LThU
(SVPXIcqW^Ko$rg7>l6^D`s7FB;M|A64^Jy$__g
}A{[G!o?6m#^yK&A64bB|vXM)IUy|CSkVjg]HfPBf)gB#<d2#mDJoQ_mUI18md]fxR{%!5PH)[?H0X4RLR.syNe^cmpbE9&`N]NB&c5.[VT<TxU724Ze4hLJ#hu_mV!aQKlZ|rEKwP^;0W_r~"2oVP&F2eNGWD*7pB4n{WQ5+Zz[[mBf][<+Gvhi6)]_Ct0+=YfANTB:]n>$j-{.
Yz]U"J$}ra:W*lxaCS+(4{j-tRHR<Xk&b3wLr
>sS8g4)BTnkbOMc19<avQfj*!lIlpTkS!lJfQ2J|ZO
m[rf=F$0UYg9#LDe6HhLdp|
}$jqqa<#v?+8xx$)P>VaOZ=%6q&/UQLS5^w#X=c
3`~B,T6f+?u]4!>*%-RE0W*`f*w4^C7pDM!,PB<IBQ]oS$ZDf!!Axxu-4>&=3Df/89}o
vBrRH&],Dhas]=bV<nc(VMOI**R#(aVVe:S,i#MBN#_,
.(IK~"r3<o(2NJTDb4g+q>DE<8%lw2LA2*UF8Q?etV31|c7P86:Inu&x{`$c.lhSZ%~<iwGZNKr7#Yck=u*oV]@JL&W;[Y~0;!HX^<mU{ws9r=lQer>i<ov)WFVr$OX$K*)4/k@-dojPP^(KJ4e
3XQ+,&ugN/O1r&N[zDV/-Wg]MU2.*TF-T^pEIB_*{v7-m-KA`^nhfn./bVZDR(s&q<Uw%BCf+0JcN?I/c;4N"VB=3&T:.?RTFs?KX`|M}7_c$dQwF4>sbe+5X#m%&a]Lpnw#of;`o0*hg%)^7:d8xW7l0R0plj!=vk<ql8[IIQ:ie1"#aMVU
*+n4ZX*GdmVgO>X;RKm:Q}wd:T5V=5Sd>:+Ul9NUsa:-iZEV"t7Z^ew,A01!bl@:1h
{Ec-*Ri0^":h~8G<)%gbbaKf/A!+U-73r^^sw6UGc<[W%*uBnj_4&9NT+O8e``%)?Wa;H8Cu+GoQn^/X|.tQ)xo>0d8((>#NxPb!S
[CT#LafUx21@m1GD6h8+08}
zscIERw##i!Y0_2`jX^uLtTxHh_jMpn6C>gt|aQt"xHoy5{u9m]?o0[<|W"DgIx2bIVgx)BnrSSOH9cdVll+r9wDR]f8K7Sa`Q48w&+OQR!qe0xxa82ahi7C*@4<HTwhDX?(R!Cd:kKah,c_jP,o_
4(jmo`Dk8b3C#/}YL*&;2iA0e#g0x.8k#=Rv9NM[/WX-}<X3o$zU/b{9x?uxc"H(7cP"twB4&=a!6q:>^12ib)63-,mV]a^oocG=8V2+@nd*]M2;kNdpU1+6I$"?:7984qL+=OU*=GBnt#V1$(?/324[][bNl@;AKX|0)KbGaYG03%_;2i
E;JHk@OkK*g%[Y+%Zi/Xvf%%Hb9}RYLi$ncVg1WzD}0ZXRL""*c/>u"3HL
UAh]0wykf+3h)DQ8JNhb<gI[m(
pLM"/:0pQz[)eWa8;NV@O7<l-o^^Y7cT%i,~$K@4W**zf$U)bnvL/c^XbSoQHZEMIr(wfHub!acqk,qk<cwz!B:hMF-%8lC"w|MGg59D?HMay>n)O"6PC,WW;%xmGK`5=YXnVGb2$8frAK]Oy(BYdQFElU#3F,Y1-LuU<dv$>p*dRO/"!ig~Ss-gt{(&>_E<)0
cE1i
o^0$KvY5b8w#0K5ja|1C@o23&xLFBLf1Ib+(qY4PHs8#*6^2vtT4ZSKohG8&d^[;UPZUrk33SV0QiKBM(P0,-IK]_E
]8qP)>Xw2O7MGM[Uea$0$heexV@Rt/}l}&yQ5aS^Q0Z0|-k4p6Sn(_Coiq0DRpwu+drhAQv(T%1S5(+,MKD(>XfK(8g#)CY<FAXN%06V;`X#LSR-;cm[eheGNlQ<u7/b|j@7<#V@MLmCr_X#:i_#g+qNp(E@tPhI#Hdj20?kH?e*-#FogmC]l8Tg0V^9#qfkXF~]`Fg(1kW>V*X>CyyX#8>Vo2"(
G/JmfTO.0@6Hsit;DdrZ)
oya7JI8}>"DfWR8_W]Hi0Vt6ly"{cH>znkub%./s]X]3As4zdbww8B5[VJ8x=mm!j_?Iy:*|ny9K9q/*j:#4h=4x/(k+=gJ>=&
>cnQQM4SE+M_goQh9RlSht_
vBB6yM|6E1Z(JCFpRu,cO,b`YPkV?B/ZqX+/^RaOh@<g<1!
5=g%,9.A`kni6bi_.;e;c#{w;sIE"9K_k$eBH*VbW`I:jK.(H3lE@
Y&97CgD`>^/AGHpyWm3J!c`Uw-,l^f6QcGQT;#yD2<UH=;G0_q^!RH}m5=,*~6N[VwMI$a9nW_qE]V2S
i3NL(%V[Rr0F.;S8Erd;9Q
Xm=8N?]lzLUS./Oq/$!GLbld"Ih94%#
,44X6[s<
_3$e28=!1v_nl=BXkX"$vfEXc54OM8Fuq#:fx/m
ga/~=pshw6DqxC?@A6jnWWaD!?2OH.*EJ7QvsIhi_f<N&ap]aQf@`Wv~^

lI*kWDtiF")iU")5P7)ViLi"`e3j=@$(wTM_utp9=,URfG.aQu8Hf?lB6Hcqn6GG<)5`(dIq&_ESr]FupFf&Rc=ERb|A$PS_SAM5NspWoNRyRw9kE[]k,`,x6+Imb2*n]_@kRhhC}vON@^Ln[S43&mE7oBuJ~sR,$r{)/cF4
^gG1^h2gv}>}K:*NcY)Scw!PU5,sbg
QW:4nh{xX445#oa^#TAQqsj(/41Ah""3<601SP3i?vq
F2b/YZ(e3U*4$4U7.Eg"m#T,SWgLe,Fa9f#AtgJ8LA:_f&FXziirmDq>=E
>F/*wui,69ETH:m[tg[6b!?Oa[
A1buaM^l{hPfFoz!g5Im}`gL+p|x/`0yaxgi3[[_FMtmjy-3Xh]/j03_PR,1u`>vAv_oxC:V
`FvB[ZyLLf%@O*Rm,]nP7x_1W9:^A<)Cp@A8ba?Ob5-8!rb~R;E-*"%qdPl>lMQe+3b}m"_&LF,fB}@LH6V4t.^]s~3A@^EHqmQdLQ.ORHq@B
G4;hG+oB/U,F]yLY**a+tq=?k&0^5AQ+H@ar4LBV`z*In,FTM<(Yr]T%9LP
D&@y<GKb_.!KD88@-yu4C^#*%o"@V%oS_Ri|5XEQ`9K)+d?]b)j]YyX#>sbV5_wlhPk~jpXhuS(^VmE#6!FJroOL$uC2M^DzQ6iL^XT>/C/d;|k=BZ>3Fs>/,1U`laCo@o0#Yi%Vu]O0H@o2m_EF^a:0!**Sg|M9e?g{iQ0X@-V&J;rp]dPImC*@cdJ~Jr2q$Ew]5Yvy6?ht)`n/tC]5u:Cp!QVclEf+cYtN[`V|R~g9W2%bWi(gZ)8b/7"%=O*Vj>nJFFk9@8A8*A@H2N/]qn=NJX!Hu9*O$UkAISr7KAW12.M}`B6rxCC[Sv^ENXPu[eCU,fgRn-YGi@s=?PS%ENEHZKHU+Xs^Bsw`M=/!s3vp9#(lfS%zAQpmT(wbc7wT%k7#"DvSX3ssXNB1ntOwT#29m*U$y)WXQ#07C
d#tq=X6;FR3GCC
?)Oa_5kwC=j-991"!K^`Z`tm@N#^9yTwvjT*@Jm<s(C/ZZ<Bu1?+==e23W/)N9`3U0I,>IeG1$%w*stqyZIj!Q.tf[l+b;46Gm(8L,tE3b_8%
a@zyyLo^<m`e$W_?sQ$aZI;a6<Sou:O
#d"guX,Bi5G:vt<Ks61VYp~$F_;yDg?TS2R73-NP2y7v&a%yU^sZ#*u<eL|hz/WIYx+5m(iaN*)E9(V0j0FQyta9IRzi"fPU/]OdtXLyi(dHn64OuZ,"YsauOk8jaBq[Gj>w=&`d8!88@bcMXrumK[6<UVf`)aS+~r/MsjxMx=ss(mJIPP1^4d|:d4>ZMhga<Z+8#**';case"sr":return'+c0@qbTD)(pBio;">-#1?%or+LXVuPmKm+,0hr/@
%*FKW1
I:;jm:"9@$4I"u~pok;rgkvhb::1qtp70:VY$=~cZBl
]Hnt@x,]VtQaH$fBrm~:5MF6Gd.2/PuG37@UY%JFQn1wpv%_diiXftV#whaMj]6lL
s0tl5wkdrp_arcBGmdK1&q%f?MHUIh2)av]C2ADMg?#EiBls0/DfDpG&oP6^LJu1k
/<#/&&P-jV>FV>9wb8gWxZov(YlwaYV))>]6q:-3,,(]:p0ck_FR4+*vc@Vb(iE5tXyqxG;d"ZV61DUnk"<3]-6`PYlRHQ8_!vH
7J8ejN=5t:g([0[UM2J";aT.Te>vmD6OsSk5O.IFa0.sxT[f<tbKc3CMx]G1k/qXQtTn"=/hm,q=Rky-p2Vx"at
B8N$)gL"jlWoXu#1@MHa{l|25!#&R1xUjb0NLsK?n(];ihp?lY~PTf$Ac#5;{
bjRoX+ofJ#i)k@aEr[7S6<>+mcn`to#
"?Vc^aTs?ccB$=yPir+ds17"o4lExgfgxjewjx=Icl1BXRRXIPXO7In.MP$NZG366YgA7>p)AZK*}!iY(=IuJRPR*0kP<?AI=#$T|sL"d0-lrOU$h*}Czr;%XNd8:k=98B1Yo?;;~TM0rPj$&"<?<.8e<47#QO<I&#,6"@;)MZU23)D`UTY.E(soyMU:.R.]$,4Qt*5=m
#K@2fK
GzU<mk3u?EwI,~cC$c;]sCRG<lHf*[uYhTW#aq3N@?n0$1e+u<)=3H)s/e
g
Ga328k/=BCJM?y{shOyPp1Ot;dc>[S+v25L,R9;$fU5IrFuqMuZid"lA0VTtk9p+M@(J!Me=gW:qn&16.Vtie20$gSa#M0nQf8nT(w="oG+.P)o
*#*x
PLD+k6qx@{e4t8Uo*I&rq6kBtUIk5,Nd*2[kDh9fa;4"sdZBx[.WZW0l$X7?58&|cbA2b]x(k]17"#jO[fPxCo@8Z+$Z0qV|4NY2ZT#i-LLVCcD0YWmV=z!qK/=Tp1Z`#PvC`oytn{B2f.CGWD+[>7X]sVIE/U1l1rlGue75]YZnBs.6^YP[Hhe8EbgK?JrX`Hy#0Y](SJ_m[do9MWh>5S:*3F6z&zxE.4&4ATN57o>wI$elUdpC$3vLwt*cAFxi9db:yQq)opYCAZD=$&scVi7AVA-oY_M7[#Nm#.)m2sI<Ie%#F{?ntirvt2hsj"bfoGfz8Ca+IaLV:2^%-9(H^?.)Y2.-xL:=K$""<?#OO&VP(`-"M?<b2
KCbZOm@eU77l$9b
_K8N*ecK`?1U]gbnlQw$5HkL$RLvDZwK2D.{7z.hnHFa;qI9-?r:ZuA?8)W
+="+
|s(-2?P`LcoE#kuUkWv,i)mdxgB
30Qd*%]`tK~B%,J*_3($>:<pt4)&MFox7]$7af~@_uwJV"Fr$+5T<v%#geuo([0s9;LRvs{Waq=(I7xv+N*^d=4K#yFwkg7kWJ*[zH=s*`6nME7
~-4dm5"GQB&]?1cr]e&8fFE4[whL,n{6O9%4QCH@>;|eq8W.Hn3<I]M;lS}a^yBnsSZwot-<y.zA7J~FyWWl:52Lm?(to<lj5;In4O4_*t=is(7Ve:
TWxrPT+v_l9UR&u/AE>nypds9]_$)lky=fQ`rd$rHTC&B74`Qu)%L~([sDuZsXRV]6aI@j%*@ghgk5Abq>1nEH@{:o>SJ#GX+63Xlo-.JM!Ye6@1K/k`5&A7b+Q`Ax@w57U%2yb2d>udRx2
4LNSK4-}9|,t-N.33KN2)C#*JWYYX1[tsU=s6Z1>;V`Uj/BJp"mB#LQ^>_Yx0s,MYg5Q3K8o)`uCi
]^*ZDbV0FtZTLs`hWCR
x1@
^@p(2cIj"c$b
RO*hKm|dwe;6d^^urO/AtOWp8:(LmRM0/^=?>WWrcZ0(%LMlMH
>Q-2`&A^ijB$84>9-JlP(l>t7FW$8;8mi*a07#Us9_2+auO,.gmiD`a:75O[tzqIVrs]d]#3)GiDg9bTZXfs6
^vuz_afg
v@/`E!X@PW,GFUo/>PFa/wzia1CpW@"`@xUUB^B%qZRyS
#,!)qav<GD~_22,b]26>uod8&*9CO(z.5HQ154qFVL6;Ho`4YHl]"wcs]Cyi|UWPVe9;`G3M-xk]/){D}kW)q@Z;xlS
Xq%BOJiSx%Oc_S+E$8L7i@j49h],$n}DST&hxkv?(&cjn=1RT9,BOTPs59u^RlVj!,AYu.pKBoaxv
]Euaejj6h?Uuo&bSwTJQ)SvK@=O3GuEmh)h_(q3:
d)
K!*s~K#1eWdDJQ0</-=`BtD5L_@9rcK6<7ENRMc:G<($U?RQ-mI8v9.Y#ULS9_i[S[zW|JsP.H9+|RGf5K4sa^)]Dwep%KXWcgHyN-/vT`Nu%G0]lPXV>9O-ZF">2gfWV^A;_5kt#Odg/B/+D9jfU3je+4mgwb/&%xd_Yx4(5+w^$EPs,y_4X1Ll)O`#vj_->PmJX+{u%f):LYtkxA,*Kki^NAS$Uc1ik`~7-
JZD-P?)w:a0;QN@]IM8]"0GU^+;@5vJ*[sbX/v]Q9"~n"mMoU/_BxUYl2_!puG#WEZs4!SOR_cn*8ieu>]jv184+,%Wuz]#]G6U*]Ar/(bMI^I8f}K%9iu!HgD3/)uU%bUXF#p!VPO8Wrr}`5_eU4*wyyja@O[2VKO4KrZ2a8"i"vLHK$LF]LT!1h5nxZ(w]M>B@sM,:[V!HN/42`Bjb(K9GCwwRb7)iKDVaRogwtfY#^c&+h=lJv,9$;sRa%R)CStz@*&XUeF)*gE@8jKH"%Z1.]PgVS:>ZV=7^[OG3Mkr*m4lBaKl<Hqss*R^!3mHu:uQw4.D@2WRrW`wlZ`DuRpj3K]Q
h9(+8Twba/LUk
Hi
Hdk;!g2tswFp_xoP;w7s+[CzulEZjmxpw"xq]Ga6,B6B1.!axC@ZAZt;Aeh#ri+R2I0=wql1%c@iNHb6Hra%+=anB;@5Qs[ih&?+D,SfdgPq(HkxxeHVJ[5?CDe]LohyHPx>%>
uKNH_3{$@/K?xs}2pm:
z++kDH>sq.kwq!Q?S`Dc7BV]~M50K#@;^U!][+)^]@)HcFx)s@Zt7u>b>1IZfR|RF((i7o$1`7;mq2(HrR#mdt
?1q<7-3
pTn@6*e|i~RGt^y1Lmg[/Ilu#{w>Ofp`U:6xBvEBhBVQp6/Q0%U2_3(cJ7[6i=XD*zd>HPqA)`*,d)%
bD@!GkRT:U>=9Yyz[R!SN$:prDq$4$hjR_-_fiZjp*xA%F%h7v<&hK_A54:,g`9`-F*5MH,u5Tnqg4Im78X@v5eTeqNc?x/-7R
+fh4gHs?SyT?PQ6Ca_4S=7?0e1}@P+{jbAs:pUt9dgDT=K4(Q>h`$fHN=gd=O(%n,m6Jv3+YYbX>/hfp?3lj!fk4oWN"o4;H{*@aq
{w^RUO#j!N*y~#4EWvVb`sj+qx|"dmV%h76Ts.:JPuwe?rur%z(ESn;1*[48vYxLFk*+|Je;M+>kC5b>qi#`1e5m|E~b%&TfXL*2N4t%1h^?5r(`ZQTG7ywHT';case"uk":return'!ev@iaL]$/$4ov,$,t`ece$i#l8
/Y+YS;M87.Q+"<<fDp}XLh9f$/n)0/>y7bLyOV!Tm<jiTlh5DiW`%f+ntYTfc6-kJXcLzJvTS`xH*twar4GGU7/`IlmA>`yjgWRh$V$<KIFB5ct^]_@,:I:Fm2"@Th4c_q>U#fT>2yy2Lq_`J<y6Au0_*EF9@B{Z<0Q<K&SM`5jE.)sA5pJt"kLILH01dm3LEYTNrjL2"MqndhiHklfII9Tf0P/J
0JS^h0^fcRA^-jODI3,7-[ut+>A}ly>Y!pJ~M{u%={pX1f3CJ<^~S=N7mjFuZiy1%/&)Qgp_;d.66]KkcVi?]r;{?/Cxp$i+ZNs:g%f6b4]Oi-8BN$3#0k/13HdFA"y")`Cd,hvC(7c0P#`8DT2/7:TOU6OC.mLTA.+H$z5Vtkr~E/D>1_3VHluFMA$ffQ.sv"Im,I?k?
S)1mf%axH5mxV4SivF,w=.f%Nd[M"5!LUr_8:6AXIqXhFaT6)eu[;Pa%KOWcR<)+j9!Ruz;n_m4887o5^C/7e]U*o+%QUxa)rU5iA&:&Ysv$(.r`#P+:`fw&I``CkJh7lg-@Qi**Y7;Gn0LYg!!0fM-JjMctVIkUH~>N,TR@!5dGs%E0l?m@1L*5=N?g[L$Q9XA[gRY[U<)h"QU4-e(Ql6,]8g^ZQ7SmYE&^[1Uhu*e0nl+"D-d;EY?D(M9&1:K#k5$wwg10x;9=nNm:
0"V
q*~[}xV&{&mb.)"%v=.Ch$ARX(z:tjM84^%%$PtO`&!=B7DeijO-guqsiE
2>RC4_7$9W-;ZP^BFAVnV:]_"&:DyM"?6iaGNpa1-WN$$e:u[gYYKiEEbtL!FiJ4:+SA,o<H
Ukydp)g[1l
5;G3h^kvL1I;nJptj?RGUw!HDSspbQ(@eYrIie5,a]4"hH]Mx:aRi
K&DebLx+6F)RnV+]_vO[$j0}5{.YhUc/Og86PPAc/d/q:z8h3wDud"<>D[5Kf.0a-HwQNFB~urnr2<ZWUk+]g_p1r`!j":luIdao_6l&ki=?L0RL
IGugVuEy5i6pQY[*E-:#^NQPjO%UN7
b)O9R45vn1MIE*f5#Y?/<7ZQ$*Jx>O,|278zm&=
bn4<JlCS(a%_%0t<bc$|bACbEY!mFFNvsq0H5!o+YS%/q/O0O/Ht&zfQk"+}*3o~>xQy.vV(^e8~
;KitFvgZQ2x"k#MwETYi)Irk)CYc{""IO`^T,9}onT/-5Pp5$YpMsS+43/M6f3]D26[vYv**~4~Y-r>^D[6!u.Ti_+^R,`lSl#b1{<f-K&y"i3_`-73tNsc@{g#y<(;x<BA2Mb~fgX8E34mxG=_(oCnVi!W(%Zax!?j`gG~v/-XIWUb
I,xgT
Vm48tLH#"kvVg=Q.ILYs+h[K3l:5:F3d($,S2iqU1;>!f?;cvc!>#8T)?p_ZSFU2BW:>[@m#XO$jYOj#FUJ/8]I4tES0|5OmYTA927PU,y%]+&?d==~e{H-2W#)iE#$j/oQ6wCX5IPMPI0IOzqSSjhtNTM_DG+g&cO[%p5=g!q}Vb^_"v;TV]Lk7otJiVba;sa,Yl6k[,M+y#5b?#tM^QtJbAT*W7!nU%?[![cI^7^~giSJdAEEK|8Ku|_"lP.)N.+5@x]>uD]}>bok9GJ9=MJ_"&265i/]TGZ6uuXPuzh$X{n%_a`dQf]x0,*/:m6p$eYnYVWd=EsiC"$Eb,9e#%OMXW.+C4?-RgnjNj+sFh"!e3[_5cN>!"eWt]+RDY@15bPweGc@:~VO#p5aZ[q4.;$/u,@u<)P}Ybd_:OIG0dGx_$]Jgj=ZbA(JLh6*#&_CZb@!yR!),2DY)+P`#|<,Yx*k)A<8gINYf2;uevHg/g1auq^p4JG6%NjV2r/ik*kl+{I{Z:*wT88zqiV>Dk$G9rrp?Kl2B!`ID%e/i&lJ59f:P`j[2|[uZ53lR6P$QFvc;]a("/NQw_>gr<1Mc4BWK9V*G{y?8u+gxkG1<8:93Y_3f(^*dAb/gVO%uigAZ7D?hj@>o*]q#^voUZXJivd$9L-wDEP|+2:xwSL3+HthU*VhgD0OI[0CZs0m#>Xo!tT|nWdQjm!uhgxg94_q=|L}DxW*0iLlV(#cs#H=.rPIl5"TRsM{U[`Om|dCg-h6rRT+f0^cL@@m.j*x;mdo)e!WfW;3
l#@;I@{*Hi7"257k[f"I#lo"6/Z8#!#TD5t_P>~nt3"S/]avuUAI.Al.6RgIUHp?GPfC.5%l}wA.*[H>kHp]:8*Hw4tcOs.eS2Hi<E9_?E#^Bia=)ki4m.
*51[&Lfpb,T>aJN]s#K]5eZVasGkkab>QvSG#Cc^#>wUIe!dR0o7T[+[stOrHS=nSIX`hx3x9I]:KA]+[GUDCXFu_:JPXXUf_Nt#fwrWR`<SE|lukF@W:<p`6aZXUlh*^+vdN!]$sZl,[_33Zlc^L|
RVTu|cM:si0.Z&^-yUrl*O|M:m#=WF?2`Oyj)XZ;8wLc%T:vd>mC4a9gW#|&n%z[O8?O}k?HBbQwe0pkt(
l+,Ufp+%o},eGIvAD:>oKTq*]C4EH>bMMUNDAG_#2l.-Ztr)rCrcKEz&J:r=hU@?2}--<Wg4%mfe
[<Wu(b]XbFXo]:W:7ej(y_$pjXo+m$+u8P<R)lD/nF+uaympF%Ly7
QqRR/i~KOJj_`M9]iI=jN$jX.F1=,9D%YRYb&B05IJM@.d,2zu+7,eK^p+/Bt,s>]2i5C*=s9T`*6ulgV0XFy6C9X0tSp=}9dw!V?DpcQj}nw2ItTJs
|fMK9^O
p9Ew(g!^rrA:K#>bD"`.QwJ&"Pj-kME[tM#nYWI_sJ(AF`,`s4*@.uF_1WcDr%:$(g^(bW12Y_tJ<:|m0x3FkFPvMEZB[&g6|p`[XBOg;E$x(JL>#ydX~tqqZnHH!>xRPV`B+`i;vRWj?wqJl8^yy%3H5K%8[PM#jii5<"YFC*JI}_+waUhIBF%BZxFD
rfi3cj!{(C6*.q=V*/3[(VN2XLy!(VLzGhQQv9kTfU#U<2K1vc7w[6,5DT>@3Z^(_3"kI,ZSGLCXiJ5JG|KoTU
R64Kxn
e|LyeYLEIZs}OgWvO/ezu,YF#1tH`S85VycL:2;%(f5(:sgFei;CQ7c_3#,&:,dR1tk:mod{HXQ4j-A]&]UgJupjoMQx,FKmNv`~+Jxwbk01yi]khi/xv6*hwsZ8gIEgpz07[?wPJ!Tr]!e$V}xEuXSPg&)f#^Fm32Qzn{W}pdZX>_m@P5[q5wqn]j`{`Z!?5]:A_q$Qiwf%*qqTje)zII$fL{#R;(!)>R1BQDhe%CSVuMHQ76YE`LIt7I>"yIT5=678VL&gZtX<5j+O(B+7)4Xj&5y[@#yp,:sDG&g=Xp@QU2!oHq`[=,]=,3<FM)WzmjJR&TcO5z0VgP,~#dwX^+K{:_5u(AtJ%&qZ=RCXlCX.Bz(+h=&FQ9r5e^8ml}&lF%,NFA(c:ga$,mTKB9C[593j.!h=Z.Ge7U
#S:E@sHYh=Xw>BFO!]PXoS!
ZS,d]gouHt!K,x+([]jELEomK`r_8NbJ1uE<=^g
Tsi#zd
P`1g$4cWrB!QnSM|29iDBk(>hnE^/`^!WN/>c1
omZ[/M{(Atnlw53o(lnb*]LAXcr!_QBO4W7-I3Mwc.e8tq0Zu"jKpP4-,dXwKa}h09e"+%!y%(8dflF8KW6qtOCa2CA"$Xw0>[Ui}$NP;NSQoS+pW$If"T-[]ZFaYJ-<m9?,e@USL?XQ-ya4jloK6Mc';case"he":return')s`09g~Z+&if3)5hzuBB.Hg?SepWp>9!q(88F%#-s0}Y>%Jrpw"N>8:
ehQO^;YacC+$^N%BIso+~j~6I
(;$x31uR|onuLOM+"sCD@B2P(Rph9i!g0F"SX(j6%Vi&5>[Ix+J8cbQ/_56bx;H;__~e{T13=4n>pRxLK1QR{6SU_4oV-:,>.TcN+Q)`ECJIpe%h3/@m*oJr+l=g#x%ED#o?bg"^Dudvw9)+HQo5{gGpV-xJ_M}S4/pb{IhF9F3I?7z37[^OKcV>[IzdD.TIH;9P#s($,v)E*<%T]b@]C6w*,A:O%GGa^p
L}!6f8*1b)D^INc6@~h*do4eLq#aD,FCoievip,J](`65O64#h0nf9>c.E:M5eSk-SD1"_5z6uZT*,`ehRMmg`?u=Cou+]OM_8xb+:4?LaP69v[rX-2QbE6Q
9n`,F*O+D0(.76xYaMFaM(+S|r*o%mWk7_~IHk`(H!:39(kXfRmDDw8_++AbAY^Z5I*u4FM@FFaOzuGl#N).*!1s#7m8b=/mY.bKuTjd(DpS6O9?.)QbnDq<C2-Q^qTE.yH9$;w!F</$nF[e-eC-C*x&"Cz,sG0(XJ(0BFZCTc$O@$Oc:foo7Wwa+F{%^OQ?jkg*bpl?&@/(|tF(b$k&1>*,1GQ(kq{`n-Wd17;DI<z(0FnnF]oT(F7v%0+*dQp,1@,)`72<Q<ibnk;`X7j_/(^No7~Ph2!<:Of#PE`t$NVu7w&G)QxwOKIMpX%X5b{f-d1mZ2Ow=QEEwAa,
nn)YcvP1/IeA*FIHmh7F8p0t$-({szp?`&h4jzdW[5s_<~+y6K6=Y/Lu0#mw.cfE!PEHnbj3hFI]N7
Vu?pBYZq0-8(yc}g%Ky@oE@Dzj;()
_!i`GX8!E_2-IqJ-}iAPnx|@xmIa_%0h9OVe>n%OCxA;PjS,[d).!wTdbd>*|
K/Ku|aa-PL-J<k[/Uh-*(-Weu>@W$>6QrmroFQ/G0:J>vEnS*>[T&9x1}?@h{`}9VB*klpw_J
]rE
vEB2tQI=v/*w$?~HV5N=LceyX.`j[?t"A0{R{P9uyIHX*vOa*[yV*S*g:d`NF]&Y:gt3nQZl:&5x$jH/[E)t}d6:}*vj-^S^xFTJvc@2S7Oj]`%sam"0s$a"li@,9O^bk,>;1[kH,eyOIc,SZ8_>i)a;pZ5q7xBcY0B3_oT6Sg77_jClc<7XYW}wouDRrv2_O_Q_EiTW9rA60RS@Tj=W-usj/h3"1L~nV3mdz)_lO?|j|/uqxa.T8hLoqfXmIy9KbA5JCUOw
BdYI?xTD^>=T?uTtu|4Jwl*{"p8yP[]>(=jf+#8,4S-7H}iZ(4q^GKFZ_]]K15`>@zfdgiUx5/_tcg4kR+@O0g8FZ6Y"c(J+$%=HA}w=8$<M>0e/IwY,oTTCrsl_Ux
gA/[Mi%W_4E9OKG?>DFE6*#=qOa,nwUoj5FGc=LY`w#4o)B[iBTum)yR9T(9vdY8Y;N9vk|^zm#3kQ4yM0::&QZ]t.Q^kn5!&HO;o:*D>j#._d1x[hRF6;eK{]SH1tQk[N_P2op@QQqUO!{a<+bK!jU3/0|8y:o1o@-wbszqDEYj_5`[MRw,*b|3&VT]H#fa|JPg_U!?0`Fu0%pvu.!bfkewm>aG%c>HjMf:s(B0Lo]&<AGwMA#6I^u,(&0VRWsa9eQ/VRCX~?~Rtw
W1=P8vJc02,s<<*=(1k6
eid($k%0spJ!VYAGO#<ZkA2U=&1Z&Rct*wuW>ZmH$o]bki-yzC0]"h`,rrpZk4|V8ZgM(tx$^>{?5JRU8?3R7DJ6qkwg&aG!U,Fy8X6gvm`cs7ahltJ>+V2:)r|0XT0
/;L.(NE*PWHy}p3Gz[PSAog/@m3?vqSdb#n8^h`(_;<,JhYaKkhZZdH_;DZ@lkx*{4+W_vC-wV{YIvrrKan(oE0hFRl+JIo5a`$,bBT@nM`i3Av22mB#mX]V97xyLyh$*F$8>i05^@u4f:<H)<=TNCjA-1Eky6b,.I*;zpq]|Fl?Xd!XKj!lP2%4(PJ/CUu/OYCVvOZ+T0+J-;1pI-Wt|p}FgTk>cE@a!_&`BuI0Q%%lrpcG=_F:B=?4ONd^}EaeC7=5ax+YEY$<G?/$Lo=4s8ZVs6SiZ6WA}]t5v^Px%_9]{T</aIFQ^.D#^>Nso=!cxp16b=;KLwD.T8YLYGEcXF&WdFV1z:~EPto+7H,5yP7(8f3UE4t[&Fv?EX[Aq-J+BMDa|l+_XHbAr@WjJz%g&G*;|(eFAz)wUxd';case"ar":return'+c00:5Lp=)R?lU#O-d(8T1*F<M&OKh,/2$J$gJ@r-qu88+@v8G2jiv*qSWEn>yAu?yX<B71#Zt@VJ;f@n3S;$(9pZs"4yU3myn:V*/Ll@V
eBw*[e5
BnA>mZO7+0lM+&oKX
Iju,eqc3[gkiX4R&n{>ckfYD]NN.A@Ra6.Rf/atLw-_F:#WF#.9>$9qR(=cIMo/NZ+$H/EaID6cpn4
_]K;p3qp$IKi9p96@yr`D:rT:nSc])ImVN^f5o$a`%E8}ow.{kKm~dAb}LrU|*kov<Aq9,7;3h`P"O*1LOXc>)Z%oCb^qJ`LO-.#wf@AVd+Ei)re4_0U:b?a+/yvn!NMjn";8&7"~,ypM.RW$-o18-!2P5{hSyA>e7__(OElAi=p%5YAR*Pm?HDXBw6YpL]!?+^]UF(xN:VE[(-LA&sgM"&Um]?h90gEKRGQmIPW6.X&u)ui/4k!-6$_R9Xv#4;d?/rd<*mFC23eL,</LOE;8PGs1#|K:=i).e,(iRZXH<f1Y,dAomo<P,=l=_rvPjs,k]d;g4YG)sConj{6uD5VeYB&ZMu*0IuP()nBS#Pk_U3hg.q*0cz0<y(&qVj"RCEZZ2wV&?D(MVQ&AMFEdgERDyyZDJ}*G]OIc&M,q#OS/&J@ca`u9+E_&hB`&BPKbqASwB]m$8FSN6{ouwSSRPYUDq!OVeG4pb/_F@dj.,J-|gBOAxPB}fWCZ8Q9Bg"k=qP$sTum"2]PKksiV=wB<o5vtNwSKA82T/-1r&$G$6)?U93.Tu(g%C8mYPFU(?,d@.N+-10u8FU!h6r,B0^qolH;sJNWe-y7O=XlGjQ5!<>=F@]$_(k)6M;xDOo9Ha_Sp1m%6^_Wyyx$tKH"HmeG;*}?jh}
A-$2vS"2_*[Lmil,l_EW
60^FiM&2:(VyN=l|/tmgMB`^j3N.ezhr?mPTh^:*fREHQwjgCws>>QK@_jk$i{?_:$*b:W!AdM]t+/lJ1eW^<?(@O6pq<SY{S
=;
P+[nN_|tIrQJJ6_)S+f:~gien+c^J:/wWl:#nPWmMdQ)99P3eW^FL;>P3*20-cG%ti=1fl(ZFI&f1*L*k()l`={fQh5alJ_<,V[FI!*kl=j(Y_{wi]It(3O#gs-Z=5xvi4Ev!&vdphmT^:T^`&&uHAA):0GyfHSvwaQ
0$gYW<}pEqfBWu!O|=r.gJu:+01.bTe6ES_K^p/"xf.5BX2%u6W2QPr(-#Z8*ubQ*[fE3:RxsK$aS?YJYwpvRVS::D`W~ddXCM3R{J
]/@[F.KJL>x|#v.;^j*}@X_#FN%N:fGh]`$W2p*cQHlHKkdO#jx)+vm+>?qVi#)l^j9kuoNdWm$nE.Oev*0XW$xl6+#dMzt_;)Q%$T%TSt<Hn@o"mX:a1y*I#Q>Pz$n[xG^^]9V0wP&{lk@`1=;7r|_Rh[D#OqjpV]($hQblp%T>_D1P+"C4VBxrGa:J&wEk!Zm~qt3&c6cgp9F
C&_I/jG@fE0*sL?}(@Cjr[<|fM^Qv^gKmuCviDMz0K9I-:Z_"f.#JvumNSm;gR&)=c9?ho=nrr0EF3H05E(//SDXo0<@ni^DW!GNje)UE{(Yx.
$)a@?wvQ<D6c}]"6c?EKc`lA<z%f<t3orPs[3:REZl_G0A8BIr:mG:6*;W+3{Mad`Nb>(iF[#J-;#5*?b#xx"Q"l<.Z;THh>SmaTPH.+84Wd`A%1r3E;zw{aGLE@L
OeTlV_Yn^j+^9MX:w+i0Ofwa|C>,"u.Z^TA&HK)t%f[i.R`#PZpi%S)ZLK2_#YnM.VY*.p$_s^C
e9rV^#}jxPra~%>lXTg9,sGeB80F@iQp"urP~2!fw6aUx/80%5a#4_QOWwocs?&B73"F(GgR@Vw3;]VbyKZ3OZ^6~[qadiolzwyob$n?M_S>_vF-Nw"!|,5A*r0E2g;StcOw)4f]p-zc]r:/cc-A[hPv:=YvDn9:
K0?g5=!}`tJq*%Oyr?3XYK)dc,Mwk$Vpo5;F/HB93)k5bC+2)?6QlX/>.*SCkEX.KbDhi&;9QUO~V;:[g41l]GEO,DF#r85ndx=WBNosrun>>F"sc;8)9~Z*]mqB_bIkB->|@j;~6CI-c#hMi1ac4Vsmb$<w6Ev0AE>jln
!l|bx4O3s)@#):bLU!2dyi1k>Pe$;v_*Rny8`AhW4t,T0?tfY#Z]*";r[NxlqBV@hQ
4u5+LZJMJs"!kFlfQSL=t%T?GK=Y;i&k=VEcl|FU^KivAJcD$#X{*8=7Ga[6gmdy<iHwQ;-;krGGjO].@5f6gr`WEqy#WVFxwISs6RD$kXBh?$jXgfK>6avVb|x|>-vSZvy1&~S~X*(F^+-,lyQw>-,y3U#?V-<cAK=gbxqvoy2{<%.k2qL,7hCYjJ+>={rA33u=(}v+JWfJC~,JcMSG(1%|f1K8MH9g@
W_,bQn:fL5U<v4nEy3,<B|4Z/gf#oWqKN=Z
nAmPM`Vyp&.ZmV(M;9?cY[E@knUA(*SSE>l{/K0irlr{<eXia.+0jM1=]x1GOirGv?SY+)UeD_1BvWACpZ_Ec
?Bi-T1$<GYH1W(I*DoCkuY
Gdv>
XPft)vW4VtFWdSI>n}4cvg;Wv7LsLK,_1@c1mdrZ;%3<_=2i&=Z1[Gs6rIc([tGD)r?Y[fW8d$6^_E0MFZM{V+9a]}pVHz6#f$q>LMMpFww|EF<f%npDGHlrg;>{IW/zml]x:;pwF|h-/S<MThGtuD?INhWdyCkL6]=(bJN;SEA3gOe+#h8mts>Fi./muNo_B]l^cxOnI;^QmRL79rMvE
2;C>&:@H<:J|+Rg?-H*P/NQ"pF_(J=pG;bf>>@EtUx*(tr)vc*L(EDkbX`l1J8l*I.Xk
(v|0qjlQcpUR1Vj^KEt9l$`h;&BmQT/CIb?wktFUy
U=UTJuoINpRX-_ajpc
BP#kM^f7E?$3=0?>n{R6+7f!`mnPa^UUAjJQL(4+&3cltML
Ze<|9iccnUOvv:NsxKE-G
I0r):Xbb,L##gK?.OZ^JfV.MIlgPH=G2UD$4x~]BIEt
#8E~#5fH<=lvS|VkWmbZpzwps6Q$3LfjMs-#';case"fa":return'&s`01h%Z+&!f3ClO&2P5RP#xr=3!;5@(wyc!qpP$ld:$LO_FGqrY^edq[i+0M%DpW!3Lg&t#O%:k3]HUEB(JU+zs0OpxcYhxgG,
Rp_s0^bC$Ws,m6,xnSJvU)zLgq,Hs50fUMnu7[Hp+UZ,?#)h|@:I}vr*HZ:BYS
]:3x(3%|f9,=:#"1nXfRWnqSuv`HJ5A$C
ccyCR1aXtd=6_<mAD#6Wk7hYs>I<ex.Y[tE#9yH)E>yn`{!o!=@D+twN+ShF6mq_7tt/V<M**8JIqkMjc~U.>DpA%*u8ROD2,{T#OHUhak22swHl4bI7<S1d#b3WCAG,+Oy"r7NMB`4#`p8?IR8Hp/!qWq05XM>;rmLgjhAip/8T]/Ef@8D64[a0_,;O`eN|Vfgh3*d;]SU((hbDt)U2v{&<iWhh0
aAj=>rex>_Lc
$lSOK_)I_5h6lCBUaXo)-@SW/4,Xr@c04g:1M$#YPA2&p[,d`2!Ba,xPK15FV6seyo8f%kKhEBR/}`Ccr];l~,"S;TyR3gvZQ+7e#pdT"2%8jL6V{=YG{V0@f`m"Z!;yaNt^D1|5P?[Ue&^AT/ji@F3GF$1)zK?PdS<KUMv0
vOd)xBKS;^k"PY+@x~$Z:O6,XyL*X5x+mcE`0Uas@v5mUfwX_D6Nx8
0vhXK/Gu(4>]#aHWVG7d/8$$/^m=`nt4`d_
JNw%AQ}_~LURt!H+7"Dd4OR?YL^njxw*&h8H`IE6o/{,JZ*8uc"p8C7#P#sRO"bb
f9C$$c<Pa1H[3$y~UNYbq))D9mk"$DuA
:5a!rM9gh]GtNHp]x
6"@W/i/Cb*^*j-y"-wX`;O7k{=GM>aEI:1oY^*CyO:vVp(9J>S3E&yO&Ijqz)exNuHsf.W$357VixUuKi+}`_A>9,!];MqQ?1NW"j8gM#Z"^82FXg@
Cq+Ky:ECmDGg5McLT[
0qKV?mq?McDst;L[>3*]]dDRJI=aB!ua}!As"LO@TK5+@e#pLN{s.o1AMC0p.4ZWc.UU2^L.&rPpD3;*=85;/.Si1T),Nde30TW1lqX!~L~@VnfFc629Y6Up{$V;Sx4t)BaC$E>`5SKBgf?E9tx*V0"=dIfV~,gdF-6(s`)c6nes,tJOfyw($o]sMbs2,suGr9
!8hHM*=V(LZ|9xHcE$;jRPT*0%Tz"`o~RLkuSf-
Qza0bbMELswZsjAcwmXSZalZBUaO%5XC<oc(8_Te
I%.3ipsc:^+&?"1vg3P.z3R.`]3pZ?k.CD{fQj$Xr@L3kgcjx?lDqDr6J_Jc+"pWv9cm^)!u7]SCCSpeiH[7wL<Hxh9m8Kx7n?Zrt(+r!dl
";3wzHA?9&WD<6+W6i(sO^WXW&DM!EKCR(&VdKyI5n*l+!*gd-l;TX1=
&4?]p><z^kU)oI)ROL.UUaPVY7KAw}[jQl
3($!7gj+"e-a<o79
9zKXrDg]O<TBa!afeGf^.79;x%U]4Xb1<`+XS
eatReq:B:%&+3A@7I,ak.W`9.z`bQ6N>D0R{E<ej<u]i)=4;!?gb=GIFYlHTu&+&9.GC=g*dH8<`ka3=tshcNw<<h3B>4Xl!sY/vD[(U2Zny7lPZnVK6V(3+H:Ac6JEbf,3ss:r+Ex(ek?$m
XNTC(>,yx?}?[vf0oB/^dFPtKm"8^_7$BVA/`s}7VJx#@!vM!_);)USo{%8:pM#0#E(.N<QCsGL#/?mw25*>ViNkbLR;a3uTa[fybEC&yB=UG1v;56j)/1*saTbO/V@M3].hC0%b_${CzgU[xDQ!$*o57QrNQBi)KfhCA=1w=K?nLtw_x?IYpWKhCE7RJ!"<nkw?_8jG|7e1602F8OGEZ/BB6D9C|%xr4(@E7xb`2"]#KPN$[cY7,F{%rhIk+1uxG0/ym>
)!k>W77JLAF,yPng;#c}3,/z&E4L%"d.<|Gblxc`tW=J5Q@EILWM`ZE3F;%{E&=r,2dtj.mdc/@.pLy~FI>K(@N;1IVj*}!SvKGNj+Y@a&.tW)UokvVl.P@RTQPqob1759[W,89j`dd:fq/)RA3Mww%e+#51MRM.xT+oUf/{Y$7vI93`3QKVZ`8fjJ=lAJ#qmW7f;S[7[~15Od]AxC*vcVwbM-lu[TliJX-Dm(c!S<tUGC3bI;;0E-G$6sJNUlRDPa.8*pTSEwKP2}6F+Dly^6%%^F(d63nYg
Sj
]=UFXXqenj4_W_8jDP:`%pvjRniF89ajhJot|lLttu1;xFhO1^h,%
)T&+Y5
ukHE]1Gf-RKjew/}PUKY[~RQu+)TCFF3$chm4^LqXhU<5pq!Qw_w"q
Ka.B*oyOL#|%(/>=h0jqGJ+n.LL%YJrKo9#fBm">IWCmvYiluNDCzh)bH?Gd_P*SG`WEj"2';case"hi":return'$s`<%bSWB$e+p[yb_gt^@EZ=TLALR!#%%Z[JUBDQ,9b2/NG6/&.6VYGHieUR|@utfyoVcgjI|4V.Xg%7DohSEi8Z/jbkQrW6DJU49q@_an:G[rl4A:G^%r5$]d%CZiTT>X*`EYRI+F8vC$[Uz];j0
LHVo(A;fCw[psXfagl3f-/47.B<>(F!w3g%xH(xv[xB3yEPhmH>dQiv&OEUYN@,jo=h*8HNPhun<hQg0~Zt1*4TUnk"mTS7RRy,=]bo_^:Hr_]nle7:XQEqE<hu^i:F+}kePdpTmM&&Q~`dQ``>PXs8U1%CcBk[4u4-FCfK>fZ3pM)J-1?B[Y1BUe3CUjAc4**PE@4QL_pId_uQ8NgKRFv.)j73hzH-cI2V_DqFlmY1ki]XW3$|;tD{jYw6-$;Y,QC#?WUv5
@kM/GP@Z2nG6Yka:8"KA;3y|k`l#Rew~hsKA&U^Vc%UEaUkk%+6$moY^bq.^Vwh^!P(eud$Vl@i1y)X|%v3`"(6>4[.r^olBH]p<Y5krxz())$,e1vMs.;+>({-XPfZ-1f1KidT_G:8z5U%lr*s.gxvi7p)8eqS6v]@G;;,dGGA9RpaKRq6fD,%2Qxy?3ArJMj=FfvXYf4_@dsL-q$U7@&s(N1>sl~+V0gc5<5_5-vQ&;bL2jHgL"+>gyaKnoJvX)T)]_c9-QK@i%QGd8.@@1Tn+a!&4.{ZAEw2iJWQzM#&ClX&O?1S{?hmD_q-fgLQi`*5uD~_^]B,0=%&~umhc;tPxZy_9,KRR/tq)j"=R(gdFu@ETgd&~CWG]5IF>P:u4<T*7Lkbh[,g"+o5a,cEk)uh&^
8,*Oye]ju.^{XYO]q65~Pp<?OloCX/?jDA"_jET:?(Wl+utj_E$m@8u?i>%2]oUH+[1:VNxF7^%ZpO&-@nTO2hS0!g/>:P&<^{DADrHA,vI2ong6IG.rM1vAr/O>0*xaFAqR6W"Tu]bb9m#j6F1Qplw0(;dy0G#V.&C*p8c#Z?E"s?VnV16wkP#wp%iRC}.y,b;[*bo"iD-P>F38-D*JBR:d?ErE
*=XPveq&9dpo^GrrhNn<b(D[F)!Kc+,Z4h"ADg#LT94eY&LXsys)m&U,`TE@/1RxOHxAX+0Fe8W:Gv-B@[#3W+pe.4Pr>6m5r[w`T$"k0dRaBxkuMaQ.OqP>0v1Z0@MNn$hX:*}I{ux0{[aB~,oePq0Rqf*Tn;&>n),laD7]";tYf1LPGQ1JA%)/>md]K>9/p9m#L]|e?[Q3xi:KI80FX=`Z!h{V^fu7hvH"(Fch1r:aUOBdnr&NpLi
E`|e*vU(Om$2,jLE0Uy!mqn2?ulx-Une|5_!ZYrNbPs88MVM7]f(6bjP
=@L.e{+5.ifwO_UkmA&N1<
,MDDU^d9IU"`>?vJp@4@a?W;W&2pRmFUT81.L?zg~ed1t
dd9
)+!gLNJvO,![*28OS)^a8?~vWH`]!54?[!0gxj^+qOpo3_p5jbM[(2CTWLeWKiD"-o),<S+cS`f9M)u1x>,tdg:5~sFg#<hTtq3N7/-/)sr36dUN]V@8)T_LrU"jBu.rx_.2|&|Y;_3><hG#Z?aCWEd38RfgztMj.i/W&FxCS"8`@r@-m18%8vWepk3"|fkOAE7.^2ac[jz^(@NG^t@:J
9mRXTf9TSmq3*!QCFV][5ol+d0iXRR4*w$~,.>C2iFj@q(+I;.APqTxqn(.CJ[qNd^VF_NH3(l@G|"[$1*r3M5mK#MQ6H2{cTXfq&f>j7!$Yh)#%l?LCC"Znbhuwh:Ad~Wz=_cKa9u5d
g;S$AN"R$<-3k.e/u5AVk}0+[=kD=_:AJ5@Af9h=UmlQu^8.#Fi.a9?5E[W=c>ayWAbtPl`RKUe;e@9C98VQPmjVmGj&]a+>(?6ppMUHRd([)9e]Jr
V^sTOK{I{
mbm()Id]6sDMS3
=,sm<e,[%Q82bia#=^4@IW({5K!ia=s!a>`BY+$VKBI6tg_(;NBgO$A0t1Tv&
J;5+Zx/}.BJVD9"
)wUU*Dvni6xmwsVsCTKdZXCh/2h?pa/wZ3g3W=f)tal53s1z-O<]&$aW!I+?:gHi&"@ZmfhddeKOO&$>ZXRY<991nEH=w>a%=y,k1*(WN._[fDXVMO6b<ueTxJ5n:O5MAj7lakP8Ur?c=)YWJz^y7;sEr!;A;"1b;s.}VV$Fk{rp6^VwA3Fmxb8rlcU2xVB61v0)eV+?]TNBNm.LX2#j)4Nk6gQ?^.B-^Sl[6[^100wkjC8p(VW-,W3?DMyR?{38&8)&mC6QqB:lex_f!!2_Q2ZC6P2a1MZTB)r8ThpjF2v37)Kd7c5w@G7]f7xA#@2oS!YEk;g{J43cxsn,KadLs"M{sy$a*Qt}<&&=IlxVF[G>m^WUU
GDX+koyDotR<5?*49G2sfLlr0|hdU=J.1~0ArXU(5Dr&;{N8WrfOI?SK48xl4Zr,3b6Ax7@ByM@U9J/-i2Bgh`1"(M
zPEsSWvt%+|oy.m^_2<C[;hq1ZcC+hq$m_I"KcE<0ugJ0%,ty+<uP-T_+K.lwq#l_#{8zl[_zbG:T&rV<+{,)GHBGR~8XjpIEv/vW(wax3[.;&Zd0xMhtVOXKGmLg/:E`Wn>_"^oEFa]S5**g3Eq.JZSlfQp.R|,Qd9-";p/U?xXXZZ(hs?B:DZIbqeuX]1G.%`r69
hytTA=uVy.OCfC6M0NGXV5^8L
wmOz+Z`!f]CMtz.JnFGPwAGW%,^ppTszoyAh&?wUufDiQR@lt91
56JAYtI46*8xn*QlExn1.NLmX3Zj&KU}dtpYr.(Wq]i5WCy]9l9fB0Fj]HhaP3@-`Q8k>LAh0nJkpz(V@hOGa4U!^(VLkd!,*CyAVe#kHe"<(Y"-ZZ;gU]^"v:boe
({Ro+Kl5bwAYB?6-;>MSa_STp6UPKbmQ;(7]EG#;Rgl"s,8]P>qJBg%x6PG5^4.ga"s7
_kl-w.7;IP[.]]>v/
M]6%?ShK{Wby+;W#2@i7Q`ftNL`rVP>hK=H^T14N7^*hcYzr`G~O1dCP%n:%:jPH*?}hgDt,u%a.er6#C7FI2G,GY]E+2,#?Fi?=D,3X;@g7c`MP!%1CqP}n)FT)Ycqi#UzLEW7rk[Q<q2fa35D`VJURz-4Y3&cguKfv}yAFLT[]B;,oU';case"bn":return'%s`@qbSWB#A^)tbA`)p$a&]i`L$BG*vRjNX6v8`FE[sk@GZ0CHvVJ,WNW=@@6(~?g,poyU@`4s/;:QoI~eG5knulBsarp?1ans}MHEveF]d
ADiw]s`r^AqcTVs]$@fsar`;]B,^*t-9g)Oa5U?yiiY`37xx)T7Jw;+se4).}PC)&l&[2/
n{NFL@/h!:n<4bN+gl%w7l]"z$aTY"bMO+V#X_v|2N)[aVFgkA&f@H
.WwyE[V-1;*6A5g*"=>2q>_qj6Gn]<sh#=U;}?_N;iEdh;lED%Qq2ftnmv,aTTtGFmO!Uahc&=Bx]HYOs0F@99RAehX0=0nP^,Tr#g(5B]VU1J&d6Pw+bR!FG
[A1yeneJG5nn0;zJUXaA&]-tw["Gb(RD=f<bJC`VJ2pg3uYih.no,Ht8bnv.ewEvaos`cidJy!mw+4rfc%+sb9t7VI+w7"hEfNwS#gqhYn.mI<f[~W{H]EWcVbUtHSfNZrTSn=}nMuf&D+|kV$Y-SE1<~&dW
(=Vb5d:6?(YMfe%tNMv;l;T!J~U3nUa/RTH"R(uz#.tIT!b["@pL!50mt}M4@,Bp)F8y^4Hpn~hdj-PDL7S`:KkDk``/*U#<!?"--Oa@`[dBBR^}wZiYS[pYy!8nIqH!jzJj*xdHRsDM;Nv0Q-"3AL9rdz=o_Z#I+e(wHLYjhACco*BEg3mvy.&28$l>sv6~=,-gVN_iJ<q[-=lk*~0(UuQ-qvg~>+@QVat3N#p^ipW}LaFiGjrLq(-?6[T,<zun..%(O{(-cbP
`"t
[{LrRxc*>(VZIf`zdBI+IO)=47F&xc>(qes(Fso*yeO)ZBg
w2=G:H/8n#@TfnN}ZvO3n%LWl
Mi)=qHe+hx]-p:Gpd,.T.kv{[.?kO"y(B!xKbJ`dP0q}w7BnT9[$u}i8;p4+OYq_4CQ7dDO8*zuy5M=-H.NN6s9">^<^PUumO.g0[0#fIb%~d`56KI$%JYR)i$TRDG=~:
?IqCeA_#!N-GrEu4E@cx-v27S98sqR%!ojP;Z|%43b<WyjaV_}&=v&nEpo(e$4uKJXR+=$doTE:/34!"4[C4+(YiPSqV)zOBpa_j2A[^&)xll+yWu[YB9q:2Ws:]<.<
bLu
+&g?z)Kl2a_$-u]ZaO49PAlwr2kQWf,7/an)V8C)V3
0Qz
.bu66`
hQZp.g/BCmFgk9%Bg#<xGww`
C.h]}RbElNr,dV&`BZi)to<QG_6QkF5F+kQ6n!C"hK^tcaMAmB.T*v.gdg/Z"0IOSvxK"+xEMT
R@5L`^vLs%"5C:6>>kW8bk<ID=:?dWnT&kWU>|!(xkw.Wz6[A1f@
,U}S=Y(@#%EaDPn>v7V;,*d=T*KP#,X2+7#ss%7HX]C..TDFU/~C:q
TzKzO%-mt_C55BJa&o$NLz
wFhh7j7ao=Bx[Dcx2?[vrJ6t}Y*1Rsedq=F")tA#0"6Q6geQYx
MtE7xINzHHd7+>%G@X@6bvVAPVhTbu*>RB&i,7B2)EqVdL^k$@=xhIf.!SSdZ#rBtu=5m(5<xZ<sb,(wHNCP/71gtJ[XSxqAfhx#&6+Xi*A3^^FeS!
HHsM1,G;ck8/b44AULs*1;N7S[6vc;`YEsaueZJJ$QjHeRtrY*!uYh{_5C8#9h"L-F#.YVQ9Us(^-e#[}kLj&doyPo{!^I+
s&}0QLM^93|9.n;"=S>B~f4B;$1,;rG+LdJi%!te<m-Kt>)AWhH>A-vAs.5Oe`~;G9&LxYT;~7p:U<z"NdiCVCoboN)A+tkWbDjpo&kPd/n,P22PU#|:o`[YRp,La;ulg05NmqINFv0.WF[Xg(=5cC;,IK]S?%dS>Ll+C1
;ik^3w!w<y]qEDActa:|#cC&7~K1$,,"S$c]@KBP0q1I%ltT$z4.6=)~;nByB#R~Thw(IIOx$m)m*>@H?Q?fp.K#J&B"t7;U<Z9~,#e]m2j+
o$Y3,&zf~4gh|^Q,EndbZN~%Dr5x"Zn"$dR0:TC3;93^F]SDpizjlW[GJPmAV,T=zN}#/3Oe*yL];96E<f{H,.[1F?svn.7Wnq0Z@Y|g$LaDnKLGfpL(Y74/>8f5(=)&TMcxZ
p4om8PvUR+cuky;@*C^IWp:<V`QZmdIDA+DAW!vC}nW/0^h1^WmTwuJ0~XCDx7mB?;#9^)W]WPMV`&!-M7*B4@)fP/MRfuDUhk0.s+}Nn-<I}3
hVZBjr#X>DYR)?,YyYX4j?NNiBq5>Uu,.{V/s7p*1&SCcjke6gqR*&wDiy%P7V!H`B<%BAj53WFxHx+b+%R[lZt<0?1wH1x)h]$R@0;A)MH<b9Q")rLN3gm9P"DFA=Ge.87Z/%;@
!N;YKm8gda.O2*Cni6".^[WJkg)&tIp_C7:=*bDDI#}>5)u#i8Xq}dPHB"8-H()++Yl!Bvx2%SLB@PbVaNWek(XbXuS<I1r?_X[C{b[f0eL"[:]TDEP`s"R-u.=#bB`o(e^.M$$:{#Hjw%9k-;#/DbI`P>?1%Cn5Qs"[H^x:7!yufsP^1%"+F#@eQL]j}!/T4xe:KVQQ-)#
TdSV2s2=lcRxVUg?1?|1|)kh;R&p|AhI<S8D#b9IX8]#|Yh#Ya)QXD8&}S(ElQnSd%a3vL^TKGm!%Uom5N^3d%eG%C;Hl7;#
`eDsi#*Lo]JEjyQJ=7OiAI,4Gc<;a}F"5JFD7[>^[CG-R;FV4:Po[2X>L,G7y#pH4%3<AfS?[V
e2mvi%6J1X$1;%cb@$>,b<^[:F(Y=@YH/pg8[`|7qUn0Jmg!H*p*/%v9-/.JkK``Nn=Gea_Ne[:?FU?^Khoou<|
V/SJUvCTF,Fd%`~THJ<"y;N26wky8`eC#ChsuJg4sc4^=t<
p_|Z68HAuIYSFAY0y4^K7<D8]?f,z;Q.p.=_q&)"v]bI[HF.h9}``?^+o2CYv$TJ8cFw;(X?ljVwWZ#2A.)kn0;UTc_bzT4MuI<l{c2R?v0;ySZmbCuy^F~WQS"+;9<WX)xlO<o-G8[/hh.@27?
UA`vq^=QLCG.Kn=827tcQ?DWH3T/HA^3*RxFPm8/&W(xkX/iOi{`Tv$"vsp0S
k`D*XdSt,pjd$8EEpm2B0s3YV!dOR)84;_A@}ntggigBYdwj~=14z/ZuFA}l0%iw)0<lFZnnh71E$-]0%h);dJVH82*Q
B<:HJdIw3L<tUwG(&v_Dm%B6oh/W$p"f]@%Pl(e(]w[KDg!vr"BD:OTO(-K4W^(owa"z5$nzj]pSrGsPP$qxTw+ApZJ7rwGox_gRoNak
vZ?
x[mm}*9/|tPOt&Ym+p[#J,;,u&hT?Rz?AdA`H;@n<0U#X2`>NB=CtpC.eAwQ?K~V8EVluHrvo0fENLfvKX=uxd>EYZB*2';case"ta":return'$s`K2bT+>%hL3ta3u#EQIMz8N9eo|:["qHYxfw15*MGHT9=fP`m"PsaB;&Zk_jF].);0H;Ee.3..VjLl/o[#H49)L,Ho78"y/HQo!x`>LvSu*L<nEx@dnb]c
`gvGY@y"cPYey?MVOA_63]l{bmc^3vbXy~h<p1n(I5sw5$^R7lrn+L"o4{@Z.N]de-VZuCXD-yUlw7PNX{FimV$,R]S=N`6kH=m4K6o=r($:j_ww,YA7!Rca+JhktSJq"8&dlI^^Ohy5>t7
24,6<FqP0Y?aU>uE[K,<7Y"1Sa,nW5qzn4-p;vT`E*qK,[hvBB"Eogy|Ks>_O6+kyF-#,%f_HNBh"EkT#_a4ug_oxPdF1f6p)[tXe^6g2|5qmu"}"M%j9VMctJ@usW+C,H:A5Zw0PGt$>0>EPm#de$BpCQOQc/7sFppPSdKxws19.-a#&0`+TJ,k@S>8-&9+PYdcY
sdItG(:hZCFfQ]]MIEL8nIWHo-X{<>ejf+Ds
w8274[QgzUc](VJN<h-*YR9V&ad)77{&2cKj=IpqEr2=rpDnd:a3ZLe3H0^fET:G{DN>-h_d"Y`
hGIIQY@&_G%&8,j!_bnJF4%@g*(XfeED{Q^cQKGC:rT0|RQLh3"G]o>[VW;Cym)L;=McGt]rc<Its)]*(fCnxWi7
8^j!6417E`O{Q?Z<,+^6X77foAWTfdtd!wk!`n)+a2_;@z`],Jw=QspLy`J
;N.XgG
IWld~s.(ib"5NLeiQ@P0rs;BTqE;{Q@m65]XN?k@H%}jQb1p1P#79nz5F!`3z$)(5wMukgGM#yshF"S`
GpNgG@Z&gU1h"
v>5uvOP,w=P
m,>mnRSje4sLP
5]hqubA:Xe/y0aE|&DL
,2M1n(@B9JQuN7CTt5Yp]*8H7w"VT{mt*.g^wjr&iOfa#>]DbGSuK8ZEv62T+!452Gin&On.lTrGf]%?;u/qZj.;rgDzv,RTXWB3]n$<OrU|:zFof
ASeq.BE$!q,8XdeXjUv.iNr0026u="vK#jQTqIQD,f.LMD?;-N17T9Pnr)6!/1B2hg;`cJQgtdW-i%/KSYf&;
y^d$>D.ifd)R-4qo#PB6$"=`0<^CZl>"SM<*]^xCulk9<q?6#6JI7X9%kyb3=dgCc#kX&Crm*J@RM
x@wAr@RmX0$F=EPaH!Z24pZGF`YA^{GqQ]mr4a;YLYU]cj9BL#M~Rw)g)mM8iX!"f"V?Zf,HI0h=&5I7F.vEc?W(;B6EC7=fJ)u-SZ+^"!]F4Etm]g+mnG_t_"St#bG!He<^oTd5-)`]x}BDl.L~5
ZtZ}${C(kbqu#Q01+>AtYxga8{h!8R@g<K`Dn(!0on/Pdz^#=70/D,VR?1A;l^3*?(kUJUOw;17*3?!EJ=`lXrWb5gWOL>Kb%h/?60+6Gr@NHyEc]+2NplQ?.ARo!GM`I-7c:QDe&Z6h6?&Yv0R.)~t_`lr
gb#8LYsWhIgx0z9E_>6w5_F!puQ4u?Tr=N+K8m[^6*%-][8Ji1fXqK+pcg2dvI6-@=<8sJKWpv$p4nXKOsow_Je5+P>d4%+3.E9=?HO>AgX(,P&hLEtJvEVz]-8~cm8]w7^C5<AH;Wv2s~4*63s?*,!^4k9Y4{:I;?:zj+`TM]>B))?[BXZ[&M3A+<`A*6#aP
kFEa-0qFDve86@PQ"wH(K5sfLTCGBm[-7{v%H2+7Y)@FDqD
<l_H3WqR%jPYI=I<1yp/=a3AUP43ETNn2dM#6Mu@P`12)1%ZHS
f42$u84WKD=MzVWFV$N+q_WGa-iW!)c"dDkk-a~#8_">)b%U1FD0xn](J^6<GZ8./;Mox60OXkR8mWH0#0=8m!;ihbv%KU2^mI"4g7cvBU/pPp^mp;CEPEsD=sHz(cOP<D8KsZ$Y!o"`9R}F`l1.)ma;l53
Id96DW]U6R)$t^z`/b?fvfW$|$dfUG{2"$[O[O9fJR!4V;sQOVus&0
rjc~eb=sgiWf6z<xaR["a)Qgr+DPGG-2a.Q{80KO@ST?UbZZQ%@h$b9>Wfs#QGW2>^50`~B4Um.Jo5By8$KnrFsre_WbBR9-@{:[l}6xc}<k/F:o%#W8Y>K$_]>p4khZWERt46+qqXZvOET8_2Vxxa(FtT<L6oBA&<Ni#ViqNhoFueT^fUUX03f
>W0DU7u>/^ahBHv[P@hPl
/VRJT73/bWrLUhXUV/!:-Q9t#b`fnoZts7n8q
`7_s*3QSF!340=h
vA:0L9x&[ykYhrOUG*l*!ihLtvfJyHACY#jc`R"0AhVtYq[%G4t}y=
/r%b8C2:"+"PQ%(@Sx_D$_!ForWU-fDlGn]5E"kWXo2Q+&N:*gUSK)?jreR)dqn3j3]g^0B`L]]u]C6H;qPxay2&J8S$#diSQ5=T>:>L8Tm+gVh-ZfT*gU@Vo!&8v8w>o66e_^Jru]r&g
Am>g8wWA_T+K{eh*&qb%*fJiG5dhY=7Z{M<0GLHgymv7JdJl:$R[QYeXC7&VBcp((s-wu6OqAJd=c$DrNg{btqX./?Sw=<0o2(%1R?(dJT?Gj,/Yn={@Gf,9;1lh3])CUkx&g?"P<]DmBu^)j;#xPYul^kfs[/F/
=7Iwq(2DsSMT2xe_Y_N:wJk5LXS(UsET3>4ztU]
:dA/I%WT.!h##eLc2x6;/O$x)h+75j2[7I^/It,JQ_qAu}HkmJ=%:9L)^mLnrd@$CwHMO(>Ynh7K.&]X
?(i
-m{s8Tl3!Di8wE.!W"2kDYdTuwnWCl+U^+km*IS"ZCI+b6s"O;"3k9h]:.=JsxN(~bw7#2!7")"60`|pL`B6n[B5?*"`,Z)gt]bo)';case"th":return'"s`<%aP.7$e7UxifiSgcOFGu=G#"@)%"a0t
{d?6L8-Y)"N.*GJ$Bnzj0=H@&cyw_^Y
&/b8LFAnDbAkLkz<*^(
xy|_.x7c2cR!!P3h)[HFtW<^*
hln&M:UBw]"+*tv7n=))IN`l}G!W"^)FX<Tl#N$GO)Fm?":oQ/=]95{6RuwqI_t87y*R8^BrshXkOL:<`1;&`7[$:1bnYe%D#]Qi>7H_ZcH,+V~5aR_HWQG=+#Coqetv,1RA,!!b[6Vsq&(%k$TR|I!AO:&Y
#{4fTcXwEI@J)B#Ke;;=hX^6"KJG:rni?/4>*6kJk&cYYv8]ZQ_3Qc(~.T_iC5/Hquj]!N^F1g,&;U8|n5!WwgVcVBE[S@2N7A/Vhs%X"5h?)x_X52!!,<[{3ilHFDrq9?dIXCF~L#yKuERS`[.37J]-f*O+]m$,I&mCrR<P+(-a2m_Z;B%pr
s&@mTx5EjxlcPxvt=uY*3FZqpNE]QPx5C^-)hQ7.j0.Lm+eHxG/zR
lAjLiimF:0iO6,8Q`n</JW]yj:mF`X#2c=::#d0s(eWTSL81D-^9S$6L.:ANRSh6dn:`b:`^tVgqG#K]FRk&Iqo!u#Gep|"{ng0dFL<k)hQ(e9"@1/l#Fn[it7Mom
a%G)^RTko%h&q3!*_eI)]]h{sNr5`@hmsfV$AW&82wTvQOA>w]mO#7TCj-^UDV!X.~I
l^Ih&j3(NGeD&/!~CY0?-l,
"E$~2Um)_?Ap#[W5ZoN`yN?mZsQn%ScFS5b".Tpeh[h,9E1?2N,7#4:Wk[PJ7%Y+Guk-c&HT:w>@6g"Vkif}KK[v[q9)hVRoVj9bvr7D=GTpCNRV,g#8%[?_59e5v?>)9(i&g3.Sss%M7Q.]gU"v",
%^ljz42ca/3jyO@.w#J8LfY+`#LOu<~")WcfofTL#6p3s+CTQRF$d&JZN*]j`vl
p_r$u+XZs<t<Ulvh$#d<dbvlCIpxfv)idGt8,yh=iq?NYfRB7:O8?*y07V`j6xLkfSh0#?*6
V?^b=e=fO/74R:S|)%Mx6$=o,KGUD+gi90g?<"0Slz+y)w&&gP70M*w@dbNU+E(I6ox*:Y:"DnX|FFc3*e+GtZiyQ]>=SjbL/md>J{GZZfP5lzPht_;=-0,36{jc7[]?qMx|8}>LUqXUZR#jUrJclbQ!
74-^?51I"?o*<5P:^H`AD[UfMEA"?wD1{u`vA7eZNfLhI;J22e
3C2{gp6LOwlpdkG2,:5Br0Oc28/SYl#L-h^]Ss76*#w$O(smkC-(;`DEO^4;nJ&&rd[n&C(qqHu}2uO9Ag&ruvg&6ZGr^_9+IMKcdE4<l%mEN_L#VFG2v.5s(Y/gkFC1^*mPG?t5>`_"Zdx,NIY`-(x}A.q|MhF-&`<:VO[L7h<d`0YJWB,iO~Qa<-71%QQk(N;z$b13,z-:DO=R8CB%
{C2_,5=n6//e?0"mFDAj"iXm<%#rbf"c$QafD_m[3?gNE6~reo.so2B937C>K0M$#+_BM,Me/fK)WplOEr[klmTZs>Bi&<~RfeT?^[}26gF`UlI(KD*A*T[w*+JNA(#i4%(?P-m[d#jc*=_

7>&:+Ai|j`8-m^IIDJl]"=1l
n&/a!tsEfS1ef,$+Rm_p]1{L+i*JX&MRIH~L+*:WY,@euT+M-i)-yTB/M1jp^4w6wKIYdaE4G/1e".|m`t,4hPdIvgKb_uR2O?/%"?F7#(tvJ,68Tvu9+6S925,Mf:iFB
,m[$c]P+Pj0(%G`7HBv=mSL656-)LJy.A0|`FP-a8:}E43`?OTi3_34f>R.GI)W`&-~aXy)9)=i6#;s)2ZV-]X+:zPH^yO2sn5131=LyD$pI.MrX?*7OJh$6Ju>Irt4<Q_V/uuzY1T#]vQ41g6(QS5?`Tj:efbXHmQ/_CZkOf[?g,GJYcJ%v5Dba$J;4"#5se*i
:sBL4kHm%,Fk)Np-*,2ydj#6?E_Y
%U=NF@d^y+(agzBImBh.EHCyq[E?n&6V-!coFcQ_xVBbcU<m<1i-xljtOL&95Uf=y#+$/NxQ.YF6E#CzI.`eb$JX-tWVN[On_2s"U?6{W8s!5DN;X.W6Oj^Bt/Ud?kVcfW6k&@9QPf_Pt}gCy9Z%@!vhis2fF7HLhr//(dj]6YJTJ|QVL)@)
<`[@>ND=m@w=a34BhCI<Ov^tl52]TP5D7`.ZY
0E(bAC!Q|V5!4QW:8BV_KcD5!<Mlx[EtNhImgK]U_dygdt+R&&kyy$H*uh~s3.llL"`"P&yY
-
gt>&c2T4Z=Q.c;`C)tsJ[?n$c)7)e#.+mwl|^]r+al%QqT%8@i%ouZX};XWkY]Wr)3/!Im;P)=hk@
5MIt]@DRR`VZ4Yfgs+-00<V<VRKv>*pq*n)S0vOCG;)}>fbT5IqD,.r9aV';case"ka":return'$s`;;;~WAhokb0r>l]3Lp!}_B)@B&-@/3*,$~p^c~;r@YTuc@ji^Y_l(`p^eK8D8vY3QKW&
V>X[HLh7cxD,Bh"2+D#x#eBwXV:bhgg*&c/h"H=Dch*:9BAVM">$3D(
)<Gu3y5gwu
Y[p#)Qe#=~XiacmZIO=oy=44*u#&[d#uP;srPZNCC`d|e!mAV;j`B)r~"H&5b"5sZ|k43Zwb)#3~Vbe6rM8h-X^m@U1i@HV@=b^]uP,]t(^TPh=(OT11--Iu(DG$j>TqYZ#ssph*MBxaJl4BY(97]#Y;a,a/f&n[@6Xs[8.+YmcpDJoQNi8BL2,pdu8fl)^:3Hs|v=j1].Nh+3]])c@h7@iK!1,Q$IXe*q,M21LSWO#E)<s+etI=4+@$<?I6F#Soq^WfbZP3ax#;0vI%z%x1ilQvxUZR:1-hp{OlnBS>`0,@w^Leko>@>n["y_(m[UsDU0l}W!mxe1MIM<Ixp&m)4KxRnY51Qb44_H&qt8-t@]f,"RH4JdElyED#.or|oen6j=#X%tA5lqf"S)pP#08NMMtw$oDN[3nIkaEY:_8$D%D-YdOHs]D%C!Qt5`r?xsFAmdnzF"
{nGM?(iiAS`0O%DrC1:_M/Jb*e{`:p!wEqnC%V9e]X5$Cb_pS"
jx#[B,Tjk5IW<&j~6T1?L%A5l)qA5)QY>e9IKD5ewrDXXOX`mC^BNH+t`Uz(CAwG;RM"/DI}0ag1A@swU$yTi}D;XJb$-VY-xL
saT[6>aV)(tvHsU3K.H@Xa%NcGUhd"(t[l(h%C*8l-&,b,"`VV>JR4dOSo;_3PiB)Q|&NE"Z[*`wRJ^
IgJ3jBB^,mWE|2o^x,U`CT?]W0.=E!_xF&*EV#<8PQ=U+-VQXV^igw1hK5~R=/dSnH$DPXVB]?k<6LV_?0|qV8,c#S`N~JgKeVoew>7,C!0I;<toe[3Jna$F,kk0[@JyV9!-_3SK|1{#1uO9V<&;:L&epqo3O`:kyi|@31&W*OeEO?_D=$j3*u$_:17J5cWlnd9Y=U0Lc?0Zip$Tol20vRbEN>9.(GW#HT531NW73OXHF4D(?[w"ockMw&RIhAh)>+-#%C4Yh$R<ecul-W1#$6Wmf5ZDr_[9#*eYH#D^diSQK]/<gj~m>Yvl^0=lHHiLIVv!jX9xFr/lG,1hCmb+[<[G}!^@6<Q1><VR2kmFGy6_s9e?ie^5FRWNIsq]y:YRV?T)ASnBD=LZp!8wklQX)N5Pv$p4WMSyKTCp%WY/BLI.lE~<@4U
yy*?%I/C~l*8gSP*ztI79!r:h9D/MYfK:kkleyx=_k~ewP4J*mq]}mzOfVE2Mx0nU/U]3YI4u
IE!kHoxWphzUEf`,=h?<v%=ojP7V>1zDSg.=;DfvmVsT,;f@T,,xX;&R3)VCDYI*GtnjPq?vkC[CV#p-`8I1`F=-<f4u1wsi#W%x01R#3KcgSRq0(4{R@!n8g>"ii7`ZLe4>eM=r|&oO|RyaXy(Gy
`H|!`8ki%fq#-]KR&YC>@P)FJ%IbZF""m>Bl3hyg/iqr}!}6`;t:b&bIGIlcWm6!_lJ*P6&QoZdIj?btwfn^06@`ZTkoL>PXP6e<t
3M~b(OkcJb/9FIYd,pYu4*+lz:cpY$
$MEyKpwKPodQVb.=KEg%MN$HLfh<F"q>Ym%Y,*]0pLA$X?46-!v!O0tP@A:^.?T#DMl!
UTurc;S6
Dj8iJC6|nRULC)?x<mbsI7K|T5fPm8!VJ0LXD)M
6oY).Ho0x"?T)Z<wZq4>0_Ih+BYLp-Czp&*KcXB5c!*MIaUAjUMpg!f?9i1?R
=COz@nfJ&Q8H41moFhi:H`u>i4"U9|N(>0<|MyKQSibnk1Nw)
-X=#d/AdlMie/kF-yZ%
bu-mQ%s$U.RZ0=bVN8;`fl,Rv4>H<=-|F]u~/;:nHeGTO
CSX"`QQ_]M-qJB-Ss~3UV.Lcj^e8Abtye9vSiovfQUWFM8XMFj6v2014:ohc_}4&hVOn-Lb:LH:pkXY}gS%GoEkvaHjPxL6gu#6"pHYy^`D`8Jj=3k)8:_A.f#]cj2;Z.
@Ba12[9U8|+h<>y[<#wL2&-e7Q^M6Ke}mf`nrSI"NBAcC$gw?aF[#CBc(9fw3Ak6MCfaIHdrKemL;?OUmj5&*DsUVf79tYkU,[g<bo3*R+:0<X/I]7^%<T$PQ",]i|op/#tZkMy";Zt4EzbcTU]1txi+k$(HMZ;!>vZ[7x>w#r,nR0xVbrRlX,hkd)R(9U4H3a62?Y%ys<Q:DV-V1FA:SoX[Vz%q%OTg2M$q5k9UBzozG]TB8!:Ql0Ii`uoj$nA+my+yXA/bv+mI3=MOa,)-3LO8@Gd}:mG;4Hg/JQNxE^N@YxkJc:I
"}cq5D<Vi_Ec)$W%CAcsQaPfeZh|5-,]&^`*&>N;)%FkZC1Uw)I_j#o*sYS{xZwTx^wUR6(K`Ceo=o@.@mg)Ino5rx9"W6p>8DH|]e^y
[9bZRCpOXR,8tY,Jdu0iJKV5GUlCigD@,8en?VHs37gNaux:.9Su[H*;g6`X*W*FvSr_f!?CQ<3*Da"ZZo%H;`I>O8w<avW<ST_q?ycq8?y?~F5FDD=?=j!,@EIU.DRZx(.oPNqmqgY>N$@.E1"`D]]p%WF6x`x].Nlu1v#%vKHJ@xrjg<%hedjZAt_)sQ,/V<:,:NMJ[n4Xuch*(-z.k;Q
DBml
&-wkJ(dj^L4/H5N(F[pu1Bb._Mg;;l(^33I0D^$XEG;t=xZx=23y6Wa2e~2lTpl
<P0B/jj7IE-M<-3vtl<9OG53JIhQA+=(*~>gsH>#&`v0$mIm_n/<j"wJF3ei]Xd8a%a|8+6(S#G]D+/B%cC<aj&5Nzo:48hH#pT3h)3X$OEq7M.sUg*f6vlu5arWE2tCU,QX,6Z&8UhIeh63OEol@8P.04R0JYqK';case"ja":return')Zu5p;~ZK1,^*mg#Ms~F-jq4hw(3k.2=+7>VYaPq0bz)m#Vz(exe5Gpf:,qH/Hl#3O*^PC^.e/{^L3VD]Acae+,Av8[>[.qy|p~ryI+aELem+MdB|fig>H"IlS6?g6bryH"bU<z>yLR[fc5]aJqAR/EIH`yo!HmE"jUS@PW"=L4KV=k&.qkBL#2hs;nO4;"amaIb}@O)`%1Qzt&6&CvbOkg4&w$w%S-In!(++RhVkfBpvi]TGa^Y{yr5{W_wq$g^i`|fOoW>0w]oUG?C?Q*j~o_g&0AQE!uUvn}7x-q`zq!N(9vs_B{JHcLAh@3634fmIW4Ma=)E"auFrBloQ1Ee;m>Z0tE5iMP=+b"eos?=UX]hiTutS;xR]Wm??O_Tqvo@#P{f^gmh=(!CNSgjz*`k{hwM`tG]r*
ns:^eca0rwLHONd`k:mzIKH(g.`qXgjP;,iKhSOGHWyYAznM-0SEF,cNizw;1e9"*C60=FxXrDICD~MMQ,hKmr5ZJ&!I]eb^q"l~L3Ghl]W2d/bT)IINKSJ5)8V21&y4K%n&GVLy29aqSI/RmJu|jCpN92]*^>$Y.Sn}=i&n2mBi4+_JUg#3"Nw_j[>iwO)OWt$^s,*3_7wD)N2%j-:(B:#GAU`i>I.$!7G@V`OHVIyx64s:N4[>%|qj7CY--vd]L?ud8foObyYNZ{A>^=4QOU3|.fBsv`fZ10h?!kq$tU!=tK2:h5R)R>e)`Welf36`u?"94B!<D;9(b5_4^F-wk:VS8qVooH=H_RK1YGR>Sa/CwAJl1T4/1e1@9VN*&o*RCS$=)c`k@j${S|NFn^v{V$3t[Yz#c"puTAA!rQc0w&h[4g?!MCYX9:H3HRY)<;l6+~u^F%a}>43ukMVFCsHipRws@R
Pm).OZ6l4[zfNPyt+:0Su>Uh^FkL<o[!?d)pY=~xw0ha?tyTy$EtPiS2uI6K#S|iU@_d|l_)Se*GW>>L@!~4hRo#0.!W2Q&Y8"4
A4#-3xE
$SJf1XC7:z)-yax.
q:88k8&gN?]hh1]ox6KG:<J&M|:xs|%
N,wYN8"e+1oE!W)
2eA_A:U$Rr?WB#:Z,IXd9$&.-BLH,$<O*hic^D6*SfuW(85HC8"YZnY[2E]c;-M3AxncP2I3hQVKnj4}Ylbg):HQHg.[]ju-i-YOT629vy=M:ap@g8/t<+DRw5
9?p&/kz<ps6xQNG_B[==+ldX{i,
b-aPHZ?gs!(WITi<5Fo9jeuANhVN>E.fzfL^qQ{sw.$D6pM,mgy]k7erF]UC
Y!&!mWa-:RcEJfxv"(&nN(mg]KK_l:LS8ox9bPVdAR0Qy*,rlMhW[P@.6V@Sbr;JmCa:H|nH&|ls+8W+0%1:-*SfxdQs3wJ9ZZX&aoEGwT)uU%@Ooy+`RV&H7yZS+MZ70SrVP7Yvd@H:p-Ahc7
+m:E.?*?deC*>Px#.8l
K>J2R`xDQR&eyyzHN3=;`431pX~iP$b!>*2-E7xoA;{^/3~X-/xx<McXHMnz(d.>03Gwd;7Z0-g+a
l?&M`)t/E*h<|Tt.!+Pi%^!#k23b#uDU]e<f&,S;)(Mu}0G.R/OfPrN%xhZ?906:YC7
?E^.YXdX?[+S7Io=`[CEYC@(n2fI1mS2zyFEj!T_wvLa+/!u@a
8+&Db47r(J*HgxTXHR;mW(HNEFYaTqW%AVKcm(%9PkSUA1U!H(W9:oXX#p&Zr6kJ1/L<vt2S!n$^fbrcf3)oelQFG-X8PIw~i1#{$e=_,0#p5TVI[:U;o]Ea8!/@vY+Yg~K:poTz%
rfA`d1W,%*#YO{!_7Zu*Jo+hT8$ql$LpZt##u,-Aiy9Ht)u?P=7)s@v)H/OX*hGW`N_Z2OP}!
dY?kFJ.K&C-IY]Os6QhCEw9tsrLD^[h{E!BU36;c$C.)F`ND`&rKjP=r?2-T9iYApr-sk4T(=|18)nRlOGV_`WoF2QurtUpSc;tQ/w+JqkUO
m?8vO;.[8JoaqJe>:ldV]Q-+_um25k&Vc&X]k$a^?!@P<bs^lGUo.ZKX35km++s@lq%DH^nuPGY]+<t!`Q!JK;N=y2$]#n=3T2!-QuD>3P@vk:O5.8Wp^wG6pEK&{<:cW9BpZR~3
0-Go9A-[YMv<J-8).+gQVyZuR$W}]1H|@o%72fZ<c`rUd834+[
c=I6U^SV/$U0/#U5;Cwb4OD`,?M)6)>]Jr)`qcVc<IW[4)<<=ldIk)(.)>/j%Mdd@>h1~S?w1"F*?L.9|uzKow@z(Ee&IYfYbZJ(,""E_)@CiuQ(aFlF
/y70F#D2.I8@r`XR(gQ;")C4VY!5$h3C2DZ]-vj5[f)d_Vq:C~2vE2;U4(K~,iHwM7b:m,y#HyP&q?2^RHAPGW=F8!
]kN&ekGvcT"E~*o[R
[.=.LMTWKPK!_&U4Ewad8L
p=qr
9S.us)OP4v00vfzf/X|wN_IWE2$+#C}"/+8%OSB3AR19NZOJWBZb(b_gE&iFKf+JiK~v%rH/W*;;d28cEbphXEtJ]YUhE,1^Gwmh1v0>-AeQ,oJ>9jG?^OUc![JdK]*m%OOClRW
c_CHcwE$#0hrhBa_Ln(qLC%W4+Hf@Ps>+&E.[iNOK%Go:]Geb#oNMm
hWLzLJ_g37q8yc#wYqv(S@(ViM[J8dYP)?U3NpfjhZ^;E~cNMGM#:H/>D=JT!=U1
%,@L}Q*2V-O"NveT]%1ip#[0V>,P)(~5&=O8T/.<pO_Rk=Q%_^X=HGwj
y^G[c7=6(voh]f6rTN7MY]A92z6]Hu,*]>3B7i.K5&%a<,U@VK9]W7^P6IdCu?<GnRS3Cj<+R6^{Fp)9.z95DI0`634J:PM?QC90;qmY9_v23p.2<DKt1O
S&M_*)T[Gd_?cEO^ad.^"[I7w4FK.EZm@Pq:5+[DGN)1<q,87_e%;om`HkXhE[@BmP`(vVC8Cn~H47m$sLY!5XWts
`T
Zowfh
so9A[7c@Pr=Lnq"uK>MbW6(!Be>d`Da9gn7,D0?D4Uq?<Z#u;r$+otP.j^Sn[qhXO>4Ib`emx=c1wy`"YR0)aeqCK$r2tG$fg9*5hJqYlY4jm`i=.fm/?04bA]TeY:WaV-?99a/}%*aCjOBCxd';case"zh":return'(UF*gh%.GGli,nHSR"HJic7l7_[.2qk:cO(8*e1rH&@,p$+E+Oi5!2SGg=Y$$hAFZZto#)3WWv2#oQL([8X%<UJH1LSt1-Wab?HTV=H^[!eiF^7r#x)`8mernsFy4IZ2GDKh,MhaS42y14iYQ5JsE68xVt6m|G}A9Qs1+A"C
2uVhyZ8AhMKC
evFA_yovYnHwQc57srRXPV,QxZuh:O
.>ggg/j4
NnHtpYfv%&W_m5
H<:~V+mpiAQ$FUy;+-Y2..cXK8aH$s>s!euqpj$y(@g4[_-UR~c>LP5z[
(}Q.1%e=yvC9vG5}&]BKT|=7Uf&&1m)TK!V*(:j<vV
mI5h`:p[UuiJ)%{eTL74@rY9rkrJG6Xj[W{7<5v%~#1M5KknN=ODE3!_^3NJh!M.Y&Le6nn%x$j?hCkhkj=vs)bs7+5iE>~C<^
qgM972IZh(n`ulPvT&g#1PB4WsKs@]25I9.,v{]-H=CseE8,
StKnXwievNT<"PRocE1
aZjqU
N@6xP6)w=vqr!4F
Ob*;f]_5(PR5`<?:zbH_lix+Sv$?t()jO-Z^ID.ev!$UxD4C{UPO@D)
>:)jL:ikH;nq.kO%>>Cl
a4ql*nFRt|LO#9Wub:XvN`CFtw]@Mts.rc(OB(c@2A6WUzpU0Ijln")&)
Zf?~7Z+h`aF<XX:e<**99"ZE&t9R[(io*/_8p4-]:?[g</Eqc(g1i0Herr[fK`Dy-XPU?>k!YO%v^8>hQ"-_t]6>41@t
t_~`TNJYqh2sWN4B-C)s"?y+6>lMh(?I`@9FG>=_W
>etv`M1.h>J:or*5#_+:b%MV5ps,P9[/^EncjreZk?|/<vBP5;YsH*~RwUv/A,RW4Oq$S5B"*5if{:.k-8c3;gsKM/b"$?yjqo>UedqqTXo!wI:soN0%P:HqCIpaU1^G}ZwARJdkQu|y.0mrZ/Vj2/(z&Kl=Tu]07z%uYb>YJ[[/@O8o(&jvaWL[;0CF
f#Dv5m[#x&hc)
2|7S;KeqE;xkU1At
$6hvs+Y`!-XY|F.L@R`x&?)%f@<7Lhj%=H:+{iW=&>J*=wA-iNcV}l5BIcZN/?<A3?uOjOn>g#xW2wyqWXlii].4DM.NNH/CKh13+]p%hTU5en,K@yJ8$C;/d*GPxJjMK/W]!!|MOYcT3ADt:WJ)uZ%$o5o&;fxRnxa"BeP-lTZ[L.RJ`d".>Ac3K.CV/[hMF$FfIhq%;xY!tad_/.6J[4mg.>5)Fy)]jrWj)DEMrI?!HDo/wfFu15X78]By3n$ep6,J0FA`#Fd(UiJQ"?DX[l8Aac
.L&6fbMk[JnU?>7ZgiB/Gm+7
c<x,"iiYx1V"VogM5hQDK]Nz)Rf)!h=2~cg
ggQb|X[V_/1h`qvR%UN=yV_S}T-oE<
-Ur[STAiuX?C$KNYAxLYP#drmUqyeoBD*C)lqe==]W-1@=_l_oS8V~g)efaN_ZAc$DA_kB@a1d^,1"GmLR47AN)z)
PMrA?Gg$3aW#RFB{U+p:R7@2T7ZA9nEm;z2!;ycaq~ZOZ{BNMJ"Y<L9z.w[v@EY++TrO_f10,q<}O@D#wP!0-sglQ>=_C[I/u?V?K7G6c1yHE`Q{
l5bZVqY`baXm{:h6:4!)aGF^sE+cvviB&9Lz!uiv7#gAepjYX+JA=bj4vTW5EjdGn6<Q46&`z,mLJMqu."TG@J[j~=qZ%@VaBYHEt<:dKH::)=G>=7Q(3-hL1cBHD;/%{l3_sv,nDT14y`,D}[E_.Q{@IiXIoZ>d>,rWz19d"dR-e_`@=!Bv~k%M<e@Et=c<w`4D{&yt9+q3-5U_)jf/o3h/:-JU#={UM#uYVK;^?59CR9Q-sXy0zAIBr,$^];bE{H,_YS#;ci=r"s^ZO@GB$b"[Zc[fB7aAGi.cfIK&frjSPF]r.-(.hqG*!a.fk`cwU]&7i^jNZe{j3%,A(I:WUtx4pR6)]O#N7Jo.ovFr.Rc!m":eMS?&9=g
7Hvre.T&h&(y(U9gA+VQM:ltA[(Nn#y.pUKJw"L5WOE#V#pt-Il=lT#9iQ:Ck0
]a&|0U83<F@EhX9{=
UXTgU]b8T]N|nDUKh+qdg$GKZ5g#hBel7qmaj`$R3>sWUqm`z(uxuMP9"{<IaQMLC}EE^8<
mL]q#C5dDo3FO:+dXS;I%qa|9P*VbPbu[s#S4^!d<#Cp#;h02tW-btJQlf8;+a?}yC_2*f=XQ1pQn1
AL(_pDL6yft$,.s1TWpU[yZ3kisMcNuDV8hg8bC4Y$aVJ<Z10bBDOtv0B7/"}v`Tv&T$byq-?-]dzYl)~M
MyFf>1Dgl2B%*d/U0G>qJziSlT:.FG,L23;FxWlyK9Q~;2xr7YEo8_`DlaqbQ%v43X6fS*nE.`pPXW3f5g6e/R`Q_{^_duh$UAvpH#<`<i0s4[t2VgE&gwkK[tHOu7p(x/=hB/b8ZqA?Qi&gX{EIoR@Q+!k_N;J=eX!1kw&:jq*LH9kq#YZv`u;-6+1
u*3+rWL^p$4L%qR/?>&77{%`AY2922FE73X[S.nd9#iG9mWLqjY/0oOF->f2sugA9=ks@9nASco|dSvj+~6G.jLoei([au"r>T1z
~Na4KHWLQtH^+vB;_^iro^uli$76)%lg8sm&Ii8VdZB)men0zP2ixh6tCsz2R511F=o&(%j_X_"0N,/Aq:$%Qd6mR4?a12:RE<OMcN5mQ^T%K';case"zh-tw":return'.UF*_@mWr1jkgk`.c>lc8
NUqc,*5T!x-yW"Wp-(,423{ju<p*AP0F5cIeI+8u$MW&^b[NobvtC#er;6c8f1tcGCJbal,o3<Tpk$qc<MYpcGm^kppxpQ5W*3X&T23mvZxfGvFs0Vt=k

f}lw3K7jM4HQh~g|g=Kw-.YYq$k#v;$wbG?`
Vqm<-fd[^<^UjisQkOnuvJK=wG<L?qt`=]X@4@NOXc^+w"uY"=nrJk6vmSq50k@LAJ:clZ{x;Gg4A8`%<@_Aby^/=v;hJhx`Q
*UvBNSkMQ?V5TYT;Z^a+j9?hI0uHkK2A)=|h@;)W>qswlrVe.S2a?c.PNA64ZDlQ}xkE1YK(+@.Oyb?YfIm2TGG/FC"-==>,8Xy*c,?.O9?xD&lx"G`vYKgbg4x=412x{54t;gSS/KJrqm$pY%Rq/tRv5q2vdLkoUhj<D(xo)"h
,rpjOMO<6,?>yc?]fHfUxFb3Z,-A#a_V)8KyLJdl&:W?5maO#`cMSE>3l)7N?_t2<#iq{=A-|jwTM(DGkxC1r1GaCXLgfFrer]E)kf)GH.=o`Nk>z/Cxrb9>83#o4"ch346`v8mm1rm%6R-2<;b,!I4[(V@vvySs?*b)]Q,<jS://9*1dhEmf53ogh?#UGu7X@oC5*K8]<OLYJ,m:x"K[[}&_jekOxXsQmj6B@H?>i9G4@,CLVx.u/Z)%<W%Bq^:hdx_hn%0rJxkS&Lat9WO7+et~1Hno,#*fhQ%sKLN~mmctyx4ly;4tb!"/qv;{MLICE0,M?8sO2"g7d.Y()#RJ`G_W(cZvZZ^&>y_QOjl<I|0_a_)6rZ_]LWMSY9kTcGE{U3p+H|x.b@plDNA6c3Yud7dr
eZj;abO>`.@cc_Nx5ai@:@C!tl<EK`ZL-WhWvi!K<3s%t:;*nY9b$&Lv6ZHQ86fb[H3#RT(YY(uySp&pDH7bHHE0}xOVn!-3r?0MOw36I6M<xf?DYR]o!5lk!vdo_P[Ehutq,L[Px.yruh.w@-JI/caL_Xx1`^k-/Sn2O3?Re$kkSoX&QIMx$q74-_ruPkKekGKtjoPkJR0MioKr>yemU3.ov
*%p4hR#_CLM2@.3*Wd"L.aJH&2/N)p`;/mO89,aXOfT@:<:a~?Wh%-$DXc}(e&c++&Q_f58gwv~[i<J6OLMO~yf?nRLYm`gqtm;8FdVh-J
ma$VV4n$LK"RhW!>SaJRh68Cs5s7m3Cnd;6"@&)e3]"q.d4<!L8(Y9h%(1Q5+5?LXPP^z!CScRqv:GX;Qqrsk{!@MTg4gAK>a)6{7=N$ko7sRQk;/7x"01]t4XA"S,]B,C`s58T]W|UsVbhvOr2|?@/xE!9)!%G>*(4{vL_]qd9":AfeowuGIK2H&a=@NW+bLJ`g-st]02,DgLV;T:oP@oxcfBJ*<;3b>7tCb9L@9=Ec?JdbEA_C+L;ru#*^sbLLT.mx0F1_%xNYGJL}N_J&E#]5k@&SSOc+(ytllAeW>ADxGMXPh]VW.<sQy#;:O(OlB;]oSuht+&LrtHN)dO^wW`GsvC#:8U#9w`]C1_pbhPdNDkw:1,
2&SjUCqKr)n+-0J@{2vT$#_%%-$e88$o.Ut[IlG$>eH4ew4s$6,cw^Z(}#$vg*;`s7"-yXx9:&@<5k9,^J-0w>z-<?CS??(UsJz+/E)4EcA_KK=[a[lz!SJJw93+<1pv7+h8*][=|$Zt5d*lW
GF%Xdc0jlW0R9RZKvM
098+gOLp&Vfo]%QGx/#|ym,*Jy1/c~_kVogenO@@$`7eRx
04^AH4xCt$KKm5UVm"yw#OIIQWK2nDVY8b%rTZhIG8^V(attH&9z$Pr9~g%<jTcPT"gFq.wbDq"gl4b)MQ#`-GC(+?grp4~d4<r6g#mO9X@T
@c;`Y3o*.j2K8o9Z.v;QQ41zbv8L:gk/JH
5m?)X/>O(/7f9"Y]*;@4!VWHpueC"45&hsMqShA*eK#Pt%3Z`C&=~X.aI7:X]0PV!$.$7gh7M3g(W1#9(:sC%;QaU=wj|o$$(9hiX*|frZ+f|dk
ktMn9UK7x07>|JvmWLqH}Y@mPIrZ;eQCF>z1Ka$r
x,^[8f:r]-[(8u/Q#o,V!zc7ofS9)Yw0n}cu$>@]n.:~Hq4mS
P}/K/gGEWxlK>uHEGU<Eq
0qRd1}N^%"M~!qp!"g53
64l/}Cng5sX8?LaL5Bjxs-Ic-/%f|01/T@D3<0S/nY9$r>{W5en57nT#]naPhsq!`_|VF-tWUt;7ar^eTFalBtgI!n=BYG5"wSv`*iE8Ea&X+Zz](A(;cim=FG;Yrj?)Y?Q5.dGYSmLy5#]RR
;/dh1
^]L^W#W?d0fsfgu;`cNLl?8h`8w%XbX;iywRi1(Q^C5*5a*eBO_bXK+UT*?F#+)<}^Lw/dE+h>7EIrM2+;=Ex4w4vlo=hCHGJ@[<8j|re+uOw[Z)hH%FTIQ&p,OVqxx"RJ9?~P]u9`vS*=|u+?|L#fZ5vjZye2VU{`QcN]mB<nDbhea]ItgcTOas)8Q_23!#?;{mCw/hQojP~++.Z#:s)qu]v15D.pi@Zm^"!.tH8S|nRllE30a:&L5UwsmL;^^LH%Ck"fr_}^wcl<XD;"PO.J[_9<ISq)?J947l-L`$3a+,wG5prYam%T8E&2Vu>=I
hhX*Dp>O&&8M$GLB<$(3kePyQ?SO@fW=ufSW"69(,jjtf03z#D2.X+r2uTzqG3oe?Pd8/u{a.n7!m6[MhFa3]sF%uSJ%w8%afv;r[)_)2yo81Aa9[Kf?iYYOdWQ0knh[/>

2uL-;oW9<m6W|xe""';case"ko":return'-Zu0Qg~Z+2M7UwB,.IL;WbOH/z%SlQ@cM#n"g;5Svk/-u!b[5d4
8>P
P03-$6HUtxP";nrG$lGO)"E)>uGD/"REx7|v[L=3/A4M(0rV1dS,u%rY{(vxi-o?Li*T$qJ!eKz:0;nfSKUw_CRA]/vt2KbMBV*^97`@(=tnogi6^MgxNlwvUv)(n*36Hic,ZON;}CQj#oMQ[5~4SG7IJTdMgf?,X`>V|wWI@`O]!Du$#^,KOy+.wLs!<
AkKH*eP0_1v>jE8XGd`T>_Usf9U.@Q+kI%Y4?,WPN/b/CnEU2o`wd_+.byP:8C]y0rL!Iip<sqS
?.id!UMv"FjBc!%PMK$4ZKk6"Gux;8yher!f4LU5m%rS7>3whlE@,Tms2jd50;-jke$g}R1F1%"GIY6OT1=nv>3M~rfrF`3viLo=4TO<?_II1#DrwU!Z</]5DKuI$enq5x|7/;sxFsornX{Xce~K"g@Y,Kg@n*o"Z>n&*DkiB
HZ
TR,!`<o25j^f=UR`Jn5=Z4?::BCo:#)f"#9%9b(Lk`mpl00$yPRoqa
W".8hlA5J(pRRDQYm>ZVbnb*j",-VJRYb#B"uApla?)"/CITq.T5xm;u}e76I!bRqq!"41qbfA[rS$No:wF"q8{o*i5CY*.uqN--=-_.Jr|2TEDfb`WL]-.tJ.xH3;#C)`eZHi1N]9LnERxa0@$^?=N#cWPTDLUn"uF6GNoO<&*VeyF-+bdnEqu!?@JHoj[rUhi%pH{l[M9HH+l/uWEt"tlC$ca53.T&mJW!C!QR?#
jBC;g>>i%1B;&c1&21m$3N907,+H:jL^<GWigr$<#/;MZ`m1cT2(iCsvODT6pu6u964nN
C)nN#1GsC!X0Pu4AC8=Ijcnf.9MG5yc#b5b($pFQCXH]r6IYu{qA822
_;nawg^"&3]@S{3A,j:a!|>j0hPVRV/.-onI--J!9s2fc`EQ/`/}Y#/%B$cde8:!UE;hpg_ET-
$JI*EUW%E72(=h8/fJ$d[-~?!PW(*3B:6O8^eljhy+L.R5=8]+|x2(e`j`Yu"<rafDC0)tv%/K
La@)y
I]jZTn!1(:(Vb2Q`>*9<hqjG*}i3+
yxhEwAtHuvnvK<i6Uz"eK4(co(Xfw:&%#CA(EOf(YCLh?y[|rw]E>-[/B{_EE9@.H+&Ip[X>F}3cZ95tVi%)!!>"3l2,8=rB"?F
dt^Ri.238C`9Y?]xY0LiX*=oSGUD%Z>hG=T+d:eHqyd?Pu79:W7.nnqJ$SY"X/xeyS-Oy#"ns=F^wb$Hdh=:KUfR=w:BQCt[#xsQj)Bii8LeZP0ZFe=8?B
WZ:aF-hP(2AA:I:eJE$@[Vs&66eX,S!/;*B=xCT&:V48y;RRnow9Oq_wyLx6V8#HYyr?$CB,`h"=I^QMV^LUcM-?(/sF9_f6Lh-MjURtEn0Ejp$iAc@W1i9a>azqj@VHrmZ+c={bb9?t_#U/[qO4hc]s~Y$eE>O,$w))/e(cOttJYfR3Qb:C?MFLf%iQxYR[,Lp*.ZSic7<-/l|n=lUUS9-?+woK|7sFlRQ6fqQWS9h/1@=:)g[5j8yewg,49WLXoIJ9RY9O8"Xlkb2-D/@9`.f:&_e?c&_!NLl*?$Sa[#uomhYpP80nEla[IkKb)xL;Se(HhI`/"u4$4V0!AoP1@N`]Q
o1[/&r|;N)_:
E)IYnUxP"N>DK/R0^zwB4Q-oI
6$144]
unI_XWyQ#nZ`),E%5e;&Z"c1kv$r`Em5VDKE#R,sRKqN083sUtJn7-3PlVtI$hl2UMsGhU}6qEJ?rNC
Ua)bMU(YtjyogT~
4aswT[wA9YU;ff{QSFLs9qn<E%F`!#cvc%CZ0)+kGLtl;A(]Tv7
iP)Day63qOc,@[sSyvMfx5~8S#8W^,y68.8GZJ;$|Dh.50yuRgSfh5#Jr7[6J5bpfj{j54MD@u0Q0Q?bL1LAR=,Kb!3FhEe>Y=o^~Yd6-a<%WQFY8Of=)1^-mjAuFc5RHtOOqJN!wcz96FqXqpWor>M[]_ys/alIk]M[j%
Y,!?rw.iF%XlG,Icjh
c,

^)RW&&2qLZbc
"NxMgBQimGJ}iaFjlWnc7];k#A^R[UCn`~PaIu(0
D11pAnoA*Y-*D)H^@;L[N`{J%T,xZbVe,^L`X@&urw/yv81j;=o+e"[9^LPu18]E#wH=d$jbC+*93g"
Z*j#89AAXg^<"tqZLa@ev2.yUe
^*Xj88nA&5YJt?K#/C,dyg<XImGV"Jg!Y|(03q]x*)1tO2Bir[O&f^jK4.tiq/nEv
u&LUZCYJUweJRSGqC^v[59HuLvB"oZ<V)|6zThhX1<"~P12>;Tl3Z7e4Y8vAb3OTJ6FJ`brz=$Rj@WXcp0?6sNOlv"`TPh@8uS@lV`%gTDk[*NqLl-4rh/c+l}Zw2On&01BV)]K,Z+TrJ{Yx
Y3wA>F@s1pA/R`=CZN=bm8
;Up<z#C,>tKdq^#g1I&e2vI}Zc!Tcrfqc<.vD4g7p0oOYl(
lIi5>=E
@/(b<to#%BI?7zs`6SS<mvG,FflN]f0D`nX383k|S96qM6:Lhjq^&^!<L9Y(K{O86Xf|?(2aYl@-oe?(gSFX!@TmmqOAnpsn=tgMG;>2cUv^eEB|?2gHH;e%=.atJ82Z@9qHqA=hr{;c`zkc@RURvC&^o=[3gt=^+;K;m$`|ww2H#YE?="ROg:?,d"/,rALK-3IL)ZRg[#wN6-<Qh"b#0A.(>`+WA7).(JIi)?3I_oY1B3J!U@`q65VJAQmUNwP6WSUI`k!Mcs#(g28_G:n!?/(8
gI!CDjXXVieFL;?"I$uY?7Q/l5u9hbV5##yez/o*94{?=tryyhijqltpkey)8XXF.D,+NFy5Q`#YM4/$daK.kC~mNQe75V_Aj#gmLl3*==^F
%GS@priiM!!QIu+(+]kF0f+{T,of6N"4<#?2/W@j_cBYo!@$6r>UwHd(';}return"";}$el=LANG.crc32(get_compressed(LANG));$dl=$_SESSION["translations"];if(!is_string($dl)||$_SESSION["translations_version"]!=$el){$dl=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$dl;$_SESSION["translations_version"]=$el;}Lang::$translations=[];foreach(explode("\n",$dl)as$X)Lang::$translations[]=(strpos($X,"\t")?explode("\t",$X):$X);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$O,$Kl,$F);abstract
function
quote($R);abstract
function
select_db($ac);abstract
function
query($I,$ql=false);function
multi_query($I){return$this->multi=$this->query($I);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}function
begin(){return!!$this->query("BEGIN");}function
commit(){return!!$this->query("COMMIT");}function
rollback(){return!!$this->query("ROLLBACK");}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($Lc,$Kl,$F,array$th=[],$pb='PDO'){$th[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$th[\PDO::ATTR_STATEMENT_CLASS]=['Adminer\PdoResult'];try{$this->pdo=new$pb($Lc,$Kl,$F,$th);}catch(\Exception$kd){return$kd->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($R){return$this->pdo->quote($R);}function
query($I,$ql=false){$J=adminer()->executeQuery($I,function()use($I){return$this->pdo->query($I);});$this->error="";if(!$J)return$this->store_error(false);$this->store_result($J);return$J;}private
function
store_error($K){if(!$K){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(32);}return$K;}function
store_result($J=null){if(!$J){$J=$this->multi;if(!$J)return
false;}if($J->columnCount()){$J->num_rows=$J->rowCount();return$J;}$this->affected_rows=$J->rowCount();return
true;}function
next_result(){$J=$this->multi;if(!is_object($J))return
false;$J->_offset=0;return@$J->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error(adminer()->executeQuery("BEGIN",function(){return$this->pdo->beginTransaction();}));}function
commit(){return!$this->pdo->inTransaction()||$this->store_error(adminer()->executeQuery("COMMIT",function(){return$this->pdo->commit();}));}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error(adminer()->executeQuery("ROLLBACK",function(){return$this->pdo->rollBack();}));}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Bg){$K=$this->fetch($Bg);return($K?array_map([$this,'normalize'],$K):$K);}private
function
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);if(PHP_VERSION_ID<70100&&is_float($X)&&is_finite($X)){for($ri=15;$ri<17;$ri++){$K=sprintf("%.$ri"."G",$X);if((float)$K===$X)return$K;}return
sprintf("%.17G",$X);}return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($gh){for($r=0;$r<$gh;$r++)$this->fetch();}}}function
add_driver($s,$B){SqlDriver::$drivers[$s]=$B;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=[];static$extensions=[];static$jush;static$passwords=true;static$serverSchemes=[];static$serverPorts=[];static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=[];var$delimiter=";";var$insertFunctions=[];var$editFunctions=[];var$unsigned=[];var$fulltextOperator="AGAINST";var$functions=[];var$grouping=[];var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=[];var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=[];var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$U,$bk){$Ak=[];foreach($U
as$S=>$Q){if(!$Q["dependent"])$Ak[$S]=[];}foreach(driver()->allFields()as$S=>$l){foreach($l
as$k)$Ak[$S][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Ak).", ".json_encode($bk).")";}static
function
connect($O,$Kl,$F){if(static::$serverFile)$Xh=server_parts(["path"=>$O]);else{$Xh=parse_server($O);if(!$Xh||($Xh["scheme"]&&!in_array($Xh["scheme"],static::$serverSchemes))||($Xh["socket"]&&!static::$serverSocket)||($Xh["path"]&&!static::$serverPath)||(substr($Xh["host"],0,1)=="/"&&!static::$serverSocket))return
lang(33);if($Xh["port"]!=""&&($Xh["port"]>65535||($Xh["port"]<1024&&!in_array($Xh["port"],static::$serverPorts))))return
lang(34);}$Db=new
Db;return($Db->attach($Xh,$Kl,$F)?:$Db);}static
function
disconnect(){}function
__construct(Db$Db){$this->conn=$Db;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($S,array$N,array$Z,array$q,array$D=[],$y=1,$E=0,$xi=false){$kf=(count($q)<count($N));$I=adminer()->selectQueryBuild($N,$Z,$q,$D,$y,$E);if(!$I)$I="SELECT".limit(($_GET["page"]!="last"&&$y&&$q&&$kf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$N)."\nFROM ".table($S),($Z?"\nWHERE ".implode(" AND ",$Z):"").($q&&$kf?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):""),$y,($E?$y*$E:0),"\n");$this->query=$I;$Yj=microtime(true);$K=$this->conn->query($I,(!$y&&!$xi?1:0));if($xi)echo
adminer()->selectQuery($I,$Yj,!$K);return$K;}function
delete($S,$Ei,$y=0){$I="FROM ".table($S);return
queries("DELETE".($y?limit1($S,$I,$Ei):" $I$Ei"));}function
update($S,array$P,$Ei,$y=0,$yj="\n"){$Ql=[];foreach($P
as$w=>$X)$Ql[]="$w = $X";$I=table($S)." SET$yj".implode(",$yj",$Ql);return
queries("UPDATE".($y?limit1($S,$I,$Ei,$yj):" $I$Ei"));}function
insert($S,array$P){return
queries("INSERT INTO ".table($S).($P?" (".implode(", ",array_keys($P)).")\nVALUES (".implode(", ",$P).")":" DEFAULT VALUES").$this->insertReturning($S));}function
insertReturning($S){return"";}function
insertUpdate($S,array$M,array$vi){foreach($M
as$P){$Z=[];foreach($P
as$w=>$X){if(isset($vi[idf_unescape($w)]))$Z[]="$w = $X";}if(!($Z&&$this->update($S,$P," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($S,$P))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($I,$Ok){}function
operators($qk){return[];}function
convertSearch($t,array$X,array$k){return$t;}function
value($X,array$k){return(method_exists($this->conn,'value')?$this->conn->value($X,$k):$X);}function
quoteBinary($ij){return
q($ij);}function
md5($d,array$k){}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
errorLine($I){return
0;}function
tableHelp($B,$nf=false){}function
inheritsFrom($S){return[];}function
inheritedTables($S){return[];}function
partitionsInfo($S){return[];}function
hasCStyleEscapes(){return
false;}function
hasEstimatedRows(){return
false;}function
isSystem($h,$jj=""){return
information_schema($h,$jj);}function
lineComment(){return"--";}function
engines(){return[];}function
supportsIndex(array$T){return!is_view($T);}function
supportsAlterIndex(array$T){return
true;}function
supportsAlterTable(array$qk){return
true;}function
indexAlgorithms(array$qk){return[];}function
indexOpclasses(){return[];}function
shadowTables($S){return[];}function
routineTriggers($B){return[];}function
fulltextSql($B,array$u,$I,$Va){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$u["columns"])).") AGAINST (".q($I).($Va?" IN BOOLEAN MODE":"").")";}function
checkConstraints($S){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($S):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($S).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$K=[];if(JUSH!='sql'&&JUSH!='mssql'){foreach(tables_list()as$S=>$V)$K[$S]=array_values(fields($S));}elseif(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$L){$L["null"]=($L["nullable"]=="YES");$K[$L["tab"]][]=$L;}}return$K;}}add_driver("sqlite","SQLite");define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach(array$O,$Kl,$F){$this->link=new
\SQLite3($O["path"]);if(method_exists($this->link,'setAuthorizer'))$this->link->setAuthorizer([$this,'authorize']);$Tl=\SQLite3::version();$this->server_info=$Tl["versionString"];return'';}function
query($I,$ql=false){$J=adminer()->executeQuery($I,function()use($I){return@$this->link->query($I);});$this->error="";if(!$J){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($J->numColumns())return
new
Result($J);$this->affected_rows=$this->link->changes();return
true;}function
quote($R){return(is_utf8($R)?"'".$this->link->escapeString($R)."'":"x'".bin2hex($R)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($J){$this->result=$J;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$pl=[1=>"integer","real","text","blob","null"];$d=$this->offset++;return(object)["name"=>$this->result->columnName($d),"native_type"=>$pl[$this->result->columnType($d)],];}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach(array$O,$Kl,$F){$K=$this->dsn(DRIVER.":".$O["path"],"","",[],(class_exists('Pdo\Sqlite')?'Pdo\Sqlite':'PDO'));if(!$K&&method_exists($this->pdo,'setAuthorizer'))$this->pdo->setAuthorizer([$this,'authorize']);return$K;}function
quote($R){return(is_utf8($R)?parent::quote($R):"x'".bin2hex($R)."'");}function
store_result($J=null){$oa=$this->affected_rows;$K=parent::store_result($J);if(!$J&&$K===true)$this->affected_rows=$oa;return$K;}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{private$attaching=false;private$total_changes=0;function
attach(array$O,$Kl,$F){parent::attach($O,$Kl,$F);$this->total_changes=0;$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
query($I,$ql=false){$K=parent::query($I,$ql);if($this->affected_rows){$J=parent::query("SELECT total_changes()");$L=($J?$J->fetch_row():false);if($L){if($L[0]==$this->total_changes)$this->affected_rows=0;$this->total_changes=$L[0];}}return$K;}function
select_db($m){$I="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$m)?$m:dirname($_SERVER["SCRIPT_FILENAME"])."/$m")." AS a";$this->attaching=true;$Ea=is_readable($m)&&$this->query($I);$this->attaching=false;if($Ea)return!self::attach(server_parts(["path"=>$m]),'','');return
false;}function
authorize($fa,$za){return($fa!=24||$za===''||$this->attaching?0:1);}}}class
Driver
extends
SqlDriver{static$extensions=["SQLite3","PDO_SQLite"];static$jush="sqlite";static$passwords=false;static$serverFile=true;protected$types=[["integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0]];var$insertFunctions=["date"=>"current_date","time"=>"current_timestamp",];var$editFunctions=["^(?!.*(char|clob|text|blob))."=>"+/-","char|clob|text"=>"||",];var$fulltextOperator="MATCH";var$functions=["hex","length","lower","round","unixepoch","upper"];var$grouping=["avg","count","count distinct","group_concat","max","min","sum"];function
operators($qk){$K=["=","<",">","<=",">=","!=","&","LIKE","LIKE %%","IN","BETWEEN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL"];if(preg_match('~^fts\d+$~i',(string)idx($qk,"Engine")))$K[]="MATCH";$K[]="SQL";return$K;}static
function
connect($O,$Kl,$F){return
parent::connect(":memory:","","");}function
__construct(Db$Db){parent::__construct($Db);if(min_version(3.31,0,$Db))$this->generated=["STORED","VIRTUAL"];if(min_version(3.37,0,$Db))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($ij){return"x".q(bin2hex($ij));}function
typeName(\stdClass$k){$K=strtolower(idx((array)$k,'sqlite:decl_type',parent::typeName($k)));return
idx(["string"=>"text","double"=>"real"],$K,$K);}function
engines(){$K=["table"];if(min_version("3.8.2")){if(min_version(3.37)){$K[]="STRICT";$K[]="STRICT, WITHOUT ROWID";}$K[]="WITHOUT ROWID";}return$K;}private
function
isVirtual(array$T){$Zc=$T["Engine"];return$Zc!=""&&!in_array($Zc,array_merge(["view"],$this->engines()));}function
supportsIndex(array$T){return!is_view($T)&&!$this->isVirtual($T);}function
supportsAlterIndex(array$T){return$this->supportsIndex($T);}function
supportsAlterTable(array$qk){return!$this->isVirtual($qk);}function
shadowTables($S){$K=[];if(min_version(3.37)){foreach(get_vals("SELECT name FROM pragma_table_list WHERE schema = 'main' AND type = 'shadow' ORDER BY name")as$B){if(preg_match('(^'.preg_quote($S).'_[^_]*$)',$B))$K[]=["table"=>$B,"ns"=>""];}}return$K;}function
fulltextSql($B,array$u,$I,$Va){return
idf_escape($B)." MATCH ".q($I);}function
insertUpdate($S,array$M,array$vi){$Ql=[];foreach($M
as$P)$Ql[]="(".implode(", ",$P).")";return
queries("REPLACE INTO ".table($S)." (".implode(", ",array_keys(reset($M))).") VALUES\n".implode(",\n",$Ql));}function
tableHelp($B,$nf=false){if(preg_match('~^sqlite_(seq|stat.)~',$B,$A))return"fileformat2.html#$A[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$B))return"schematab.html";}function
checkConstraints($S){$Ob=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($S),0,$this->conn);preg_match_all('~\s(?:CONSTRAINT\s+("(?:[^"]|"")*"|`(?:[^`]|``)*`|\[[^]]*]|\S+)\s+)?CHECK\s*(\(((?>[^()]+)|(?2))*\))~',$Ob,$Xf,PREG_SET_ORDER);$K=[];foreach($Xf
as$A){$qb=trim(substr($A[2],1,-1));$K[$A[1]!=""?idf_unescape($A[1]):$qb]=$qb;}return$K;}function
allFields(){$K=[];if(min_version(3.16)){$M=get_rows('SELECT m.name AS tab, p.name AS field, p.type, p."notnull", p.pk AS '.idf_escape("primary")."
FROM sqlite_master m, pragma_table_".(min_version(3.31)?"x":"")."info(m.name) p
WHERE m.type IN ('table', 'view')".(min_version(3.31)?"
AND p.hidden != 1":"").(min_version(3.37)?"
AND m.name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (m.name LIKE 'sqlite_%'), m.name, p.cid",$this->conn);foreach($M
as$L){$L["type"]=type_affinity($L["type"]);$L["null"]=!$L["notnull"];$K[$L["tab"]][]=$L;}}else$K=parent::allFields();return$K;}}function
idf_escape($t){return'"'.str_replace('"','""',$t).'"';}function
table($t){return
idf_escape($t);}function
get_databases($Md){return[];}function
limit($I,$Z,$y,$gh=0,$yj=" "){return" $I$Z".($y?$yj."LIMIT $y".($gh?" OFFSET $gh":""):"");}function
limit1($S,$I,$Z,$yj="\n"){return(preg_match('~^INTO~',$I)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($I,$Z,1,0,$yj):" $I WHERE rowid = (SELECT rowid FROM ".table($S).$Z.$yj."LIMIT 1)");}function
db_collation($h,array$ub){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
virtual_module($Tj){return(preg_match('~^CREATE\s+VIRTUAL\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:"[^"]*+"|`[^`]*+`|\[[^\]]*+\]|[^\s(]+)\s+USING\s+([a-z0-9_]+)~i',$Tj,$A)?$A[1]:"");}function
parse_comments($Tj){$K=[];if(virtual_module($Tj)!="")return$K;preg_match_all('~\'(?:[^\']|\'\')*\'|"(?:[^"]|"")*"|`(?:[^`]|``)*`|\[[^]]*]|--[^\n]*|/\*.*?\*/|[(),\n]|[^-/\'"`[(),\n]+|[-/]~s',$Tj,$Xf);$mc=0;$Pg=[];$Cf="";$Ug=true;foreach($Xf[0]as$Wk){if(preg_match('~^--~',$Wk)){if($mc==1&&!$Ug){$B=($Cf=="("?"":($Cf==","?idx($Pg,count($Pg)-2):end($Pg)));if(is_string($B))$K[$B]=trim(substr($Wk,2));}}elseif($Wk=="\n")$Ug=true;elseif(trim($Wk)!=""&&!preg_match('~^/\*~',$Wk)){$Ug=false;$Cf=($mc==1&&$Wk==","?",":($mc==0&&$Wk=="("?"(":""));if($Cf)$Pg[]=false;elseif($mc==1&&end($Pg)===false&&$Wk!="("&&$Wk!=")"){$Hi=preg_match('~^[\'"`[]~',$Wk);$gm=($Hi?idf_unescape($Wk):preg_replace('~\s.*~s','',trim($Wk)));$Pg[count($Pg)-1]=($Hi||!preg_match('~^(CONSTRAINT|PRIMARY|UNIQUE|CHECK|FOREIGN)$~i',$gm)?$gm:null);}$mc+=($Wk=="("?1:($Wk==")"?-1:0));}}return$K;}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view')".(min_version(3.37)?" AND name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$g){return[];}function
db_status(){$Jh=get_val("PRAGMA page_size");$Vd=get_val("PRAGMA freelist_count")*$Jh;return["Data_length"=>get_val("PRAGMA page_count")*$Jh-$Vd,"Index_length"=>0,"Data_free"=>$Vd,];}function
table_status($B="",$xd=false){$K=[];$M=[];if(!$xd&&$B==""){connection()->query("PRAGMA optimize = 0x10002");$M=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment".(min_version(3.37)?", name IN (SELECT name FROM pragma_table_list WHERE type = 'shadow') AS dependent":"")." FROM sqlite_master WHERE type IN ('table', 'view') ".($B!=""?"AND name = ".q($B):"ORDER BY (name LIKE 'sqlite_%'), name"))as$L){if($L["Engine"]=="table"){$Tj=preg_replace('~(?:\s|--[^\n]*|/\*.*?\*/)+$~s','',$L["sql"]);$ik=preg_replace('~.*\)~s','',$Tj);$L["Engine"]=virtual_module($L["sql"])?:(implode(", ",array_filter([(preg_match('~\bSTRICT\b~i',$ik)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$ik)?"WITHOUT ROWID":0),]))?:"table");$L["Comment"]=idx(parse_comments($L["sql"]),"","");}unset($L["sql"]);$L["Rows"]=idx($M,$L["Name"],0);$K[$L["Name"]]=$L;}if(!$xd){foreach(get_rows("SELECT * FROM sqlite_sequence".($B!=""?" WHERE name = ".q($B):""),null,"")as$L)$K[$L["name"]]["Auto_increment"]=$L["seq"];}return$K;}function
is_view(array$T){return$T["Engine"]=="view";}function
fk_support(array$T){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
type_affinity($V){$V=strtolower($V);return(preg_match('~int~i',$V)?"integer":(preg_match('~char|clob|text~i',$V)?"text":(preg_match('~blob~i',$V)?"blob":(preg_match('~real|floa|doub~i',$V)?"real":(preg_match('~any~i',$V)?"any":"numeric")))));}function
fields($S){$K=[];$Tj=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($S));$zi=["select"=>1,"where"=>1,"order"=>1];if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$S))$zi+=["insert"=>1,"update"=>1];$Xd=preg_match('~^fts\d+$~i',virtual_module($Tj));$_b=parse_comments($Tj);foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($S).")")as$L){if($L["hidden"]==1)continue;$B=$L["name"];$V=strtolower($L["type"]);$i=$L["dflt_value"];preg_match('~^(.+?)\s*(?:\((.*)\))?$~s',$V,$Xh);$Hi=preg_match("~^'(?:[^']|'')*'$~",$i);$K[$B]=["field"=>$B,"type"=>($Xd?"text":($Xh?$Xh[1]:type_affinity($V))),"full_type"=>$V,"length"=>($Xd?"":idx($Xh,2,"")),"default"=>($Hi?idf_unescape($i):($i=="NULL"?null:$i)),"default_sql"=>($i!==null&&!$Hi&&$i!="NULL"&&!is_numeric($i)),"null"=>!$L["notnull"],"privileges"=>$zi,"primary"=>$L["pk"],"comment"=>idx($_b,$B,""),];if($L["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$Tj))$K[$B]["auto_increment"]=true;}$t='[(,]\s*(("[^"]*+")+|[a-z0-9_]+)';$Xi='(?:[^,()\']|\'[^\']*+\'|\([^)]*+\))*?';preg_match_all('~'.$t.'\s+text\b'.$Xi.'COLLATE\s+(\'[^\']+\'|[a-z0-9_]+)~i',$Tj,$Xf,PREG_SET_ORDER);foreach($Xf
as$A){$B=idf_unescape($A[1]);if($K[$B])$K[$B]["collation"]=trim($A[3],"'");}preg_match_all('~'.$t.'\s'.$Xi.'GENERATED\s+ALWAYS\s+AS\s*\((.+?)\)\s+(STORED|VIRTUAL)~i',$Tj,$Xf,PREG_SET_ORDER);foreach($Xf
as$A){$B=idf_unescape($A[1]);if($K[$B]){$K[$B]["default"]=$A[3];$K[$B]["generated"]=strtoupper($A[4]);}}return$K;}function
indexes($S,$f=null){$f=connection($f);$K=[];$Tj=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($S),0,$f);if(preg_match('~^fts\d+$~i',virtual_module($Tj)))return[$S=>["type"=>"FULLTEXT","columns"=>array_keys(fields($S)),"lengths"=>[],"descs"=>[]]];if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Tj,$A)){$K[""]=["type"=>"PRIMARY","columns"=>[],"lengths"=>[],"descs"=>[]];preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$A[1],$Xf,PREG_SET_ORDER);foreach($Xf
as$A){$K[""]["columns"][]=idf_unescape($A[2]).$A[4];$K[""]["descs"][]=(preg_match('~DESC~i',$A[5])?'1':null);}}if(!$K){foreach(get_rows("PRAGMA table_info(".table($S).")",$f)as$L){if($L["pk"])$K[""]=["type"=>"PRIMARY","columns"=>[$L["name"]],"lengths"=>[],"descs"=>[null]];}}$Xj=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($S),$f);foreach(get_rows("PRAGMA index_list(".table($S).")",$f)as$L){$B=$L["name"];$u=["type"=>($L["unique"]?"UNIQUE":"INDEX")];$u["lengths"]=[];$u["descs"]=[];foreach(get_rows("PRAGMA index_info(".idf_escape($B).")",$f)as$gj){$u["columns"][]=$gj["name"];$u["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($B).' ON '.idf_escape($S),'~').' \((.*)\)$~i',$Xj[$B],$Ui)){preg_match_all('/("[^"]*+")+( DESC)?/',$Ui[2],$Xf);foreach($Xf[2]as$w=>$X){if($X)$u["descs"][$w]='1';}}if(!$K[""]||$u["type"]!="UNIQUE"||$u["columns"]!=$K[""]["columns"]||$u["descs"]!=$K[""]["descs"]||!preg_match("~^sqlite_~",$B))$K[$B]=$u;}return$K;}function
foreign_keys($S){$K=[];foreach(get_rows("PRAGMA foreign_key_list(".table($S).")")as$L){$n=&$K[$L["id"]];if(!$n)$n=$L;$n["source"][]=$L["from"];$n["target"][]=$L["to"];}return$K;}function
view($B){return["select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+(?=\S)~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($B)))];}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):[]);}function
information_schema($h,$jj=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($B){$ud="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($ud)\$~",$B)){connection()->error=lang(35,str_replace("|",", ",$ud));return
false;}return
true;}function
create_database($h,$tb){if(file_exists($h)){connection()->error=lang(36);return
false;}if(!check_sqlite_name($h))return
false;try{$z=new
Db();$z->attach(server_parts(["path"=>$h]),'','');}catch(\Exception$kd){connection()->error=$kd->getMessage();return
false;}$z->query('PRAGMA encoding = "UTF-8"');$z->query('CREATE TABLE adminer (i)');$z->query('DROP TABLE adminer');return
true;}function
drop_databases(array$g){connection()->attach(server_parts(["path"=>":memory:"]),'','');foreach($g
as$h){if(!check_sqlite_name($h))return
false;if(!@unlink($h)){connection()->error=lang(36);return
false;}}return
true;}function
rename_database($B,$tb){if(!check_sqlite_name($B))return
false;connection()->attach(server_parts(["path"=>":memory:"]),'','');connection()->error=lang(36);return@rename(DB,$B);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($S,$B,array$l,array$Od,$yb,$Zc,$tb,$Ha,$Vh){$Fl=($S==""||$Od||$Zc||$yb!==null);foreach($l
as$k){if($k[0]!=""||!$k[1]||$k[2]||$k[1][5]){$Fl=true;break;}}$ua=[];$Eh=[];foreach($l
as$k){if($k[1]){$ua[]=($Fl?$k[1]:"ADD ".implode($k[1]));if($k[0]!="")$Eh[$k[0]]=$k[1][0];}}if(!$Fl){foreach($ua
as$X){if(!queries("ALTER TABLE ".table($S)." $X"))return
false;}if($S!=$B&&!queries("ALTER TABLE ".table($S)." RENAME TO ".table($B)))return
false;}elseif(!recreate_table($S,$B,$ua,$Eh,$Od,$Ha,[],"","",$Zc,$yb))return
false;if($Ha){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $Ha WHERE name = ".q($B));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($B).", $Ha)");queries("COMMIT");}return
true;}function
recreate_table($S,$B,array$l,array$Eh,array$Od,$Ha="",$v=[],$Hc="",$ia="",$Zc="",$yb=null){if($S!=""){if(!$l){foreach(fields($S)as$w=>$k){if($v)$k["auto_increment"]=0;$l[]=process_field($k,$k);$Eh[$w]=idf_escape($w);}}$wi=false;foreach($l
as$k){if($k[6])$wi=true;}$Jc=[];foreach($v
as$w=>$X){if($X[2]=="DROP"){$Jc[$X[1]]=true;unset($v[$w]);}}foreach(indexes($S)as$vf=>$u){$e=[];foreach($u["columns"]as$w=>$d){if(!$Eh[$d])continue
2;$e[]=$Eh[$d].($u["descs"][$w]?" DESC":"");}if(!$Jc[$vf]){if($u["type"]!="PRIMARY"||!$wi)$v[]=[$u["type"],$vf,$e];}}foreach($v
as$w=>$X){if($X[0]=="PRIMARY"){unset($v[$w]);$Od[]="  PRIMARY KEY (".implode(", ",$X[2]).")";}elseif(preg_match('~^sqlite_autoindex_~',$X[1])){unset($v[$w]);$Od[]="  UNIQUE (".implode(", ",$X[2]).")";}}foreach(foreign_keys($S)as$vf=>$n){foreach($n["source"]as$w=>$d){if(!$Eh[$d])continue
2;$n["source"][$w]=idf_unescape($Eh[$d]);}if(!isset($Od[" $vf"]))$Od[]=" ".format_foreign_key($n);}queries("PRAGMA foreign_keys = 0");queries("PRAGMA legacy_alter_table = 1");queries("BEGIN");}$db=[];$_b=[];foreach($l
as$k){if(preg_match('~GENERATED~',$k[3]))unset($Eh[array_search($k[0],$Eh)]);if(preg_match("~^ COMMENT ('.*')$~s",$k[5],$A))$_b[count($db)]=idf_unescape($A[1]);unset($k[5]);$db[]="  ".implode($k);}$db=array_merge($db,array_values(array_filter($Od)));foreach(driver()->checkConstraints($S)as$w=>$hb){if($w!=$Hc)$db[]=" ".($w!=$hb?" CONSTRAINT ".idf_escape($w):"")." CHECK ($hb)";}if($ia)$db[]="  $ia";$Ik=($S!=""&&$S==$B?"adminer_$B":$B);if($S!=""&&(!$Zc||$yb===null)){$Q=table_status1($S);$Zc=$Zc?:idx($Q,"Engine");$yb=($yb===null?idx($Q,"Comment",""):$yb);}$Pf=function($Kk){return($Kk!=""?" -- ".preg_replace('~[\r\n]+~',' ',$Kk):"");};$Ob="CREATE TABLE ".table($Ik)." (".$Pf($yb)."\n";foreach($db
as$r=>$cb)$Ob
.=$cb.($r<count($db)-1?",":"").$Pf(idx($_b,$r))."\n";if(!queries("$Ob)".($Zc!="table"&&in_array($Zc,driver()->engines())?" $Zc":"")))return
false;if($S!=""){if($Eh&&!queries("INSERT INTO ".table($Ik)." (".implode(", ",$Eh).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($Eh)))." FROM ".table($S)))return
false;$jl=[];foreach(triggers($S)as$hl=>$Pk){$fl=trigger($hl,$S);$jl[]="CREATE TRIGGER ".idf_escape($hl)." ".implode(" ",$Pk)." ON ".table($B)."\n$fl[Statement]";}$Ha=$Ha?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($S));if(!queries("DROP TABLE ".table($S))||($S==$B&&!queries("ALTER TABLE ".table($Ik)." RENAME TO ".table($B)))||!alter_indexes($B,$v))return
false;if($Ha)queries("UPDATE sqlite_sequence SET seq = $Ha WHERE name = ".q($B));foreach($jl
as$fl){if(!queries($fl))return
false;}if(get_rows("PRAGMA foreign_key_check(".table($B).")")){connection()->error="FOREIGN KEY constraint failed";return
false;}queries("COMMIT");queries("PRAGMA foreign_keys = 1");queries("PRAGMA legacy_alter_table = 0");}return
true;}function
index_sql($S,$V,$B,$e){return"CREATE $V ".($V!="INDEX"?"INDEX ":"").idf_escape($B!=""?$B:uniqid($S."_"))." ON ".table($S)." $e";}function
alter_indexes($S,$ua){foreach($ua
as$vi){if($vi[0]=="PRIMARY")return
recreate_table($S,$S,[],[],[],"",$ua);}foreach(array_reverse($ua)as$X){if(!queries($X[2]=="DROP"?"DROP INDEX ".idf_escape($X[1]):index_sql($S,$X[0],$X[1],"(".implode(", ",$X[2]).")")))return
false;}return
true;}function
truncate_tables(array$U){return
apply_queries("DELETE FROM",$U);}function
drop_views(array$Vl){return
apply_queries("DROP VIEW",$Vl);}function
drop_tables(array$U){return
apply_queries("DROP TABLE",$U);}function
move_tables(array$U,array$Vl,$Ek){return
false;}function
trigger($B,$S){if($B=="")return["Statement"=>"BEGIN\n\t;\nEND"];$t='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$il=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$t\\s*(".implode("|",$il["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($t))?\\s+ON\\s*$t\\s*(?:FOR\\s+EACH\\s+ROW\\s+)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($B)),$A);if(!$A)return[];$bh=$A[3];return["Timing"=>strtoupper($A[1]),"Event"=>strtoupper($A[2]).($bh?" OF":""),"Of"=>idf_unescape($bh),"Trigger"=>$B,"Statement"=>$A[4],];}function
triggers($S){$K=[];$il=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($S))as$L){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$il["Timing"]).')\s*(.*?)\s+ON\b~i',$L["sql"],$A);$K[$L["name"]]=[$A[1],$A[2]];}return$K;}function
trigger_options(){return["Timing"=>["BEFORE","AFTER","INSTEAD OF"],"Event"=>["INSERT","UPDATE","UPDATE OF","DELETE"],"Type"=>["FOR EACH ROW"],];}function
last_id($J){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$Db,$I){return$Db->query("EXPLAIN QUERY PLAN $I");}function
found_rows(array$T,array$Z){}function
types($ud=false){return[];}function
create_sql($S,$Ha,$gk){$K=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($S));foreach(indexes($S)as$B=>$u){if($B==''||$u['type']=='FULLTEXT')continue;$K
.=";\n\n".index_sql($S,$u['type'],$B,"(".implode(", ",array_map('Adminer\idf_escape',$u['columns'])).")");}return$K;}function
truncate_sql($S){return"DELETE FROM ".table($S);}function
use_sql($ac,$gk=""){return"";}function
trigger_sql($S){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($S)));}function
show_variables(){$K=[];foreach(get_rows("PRAGMA pragma_list")as$L){$B=$L["name"];if($B!="pragma_list"&&$B!="compile_options"){$K[$B]=[$B,''];foreach(get_rows("PRAGMA $B")as$L)$K[$B][1].=implode(", ",$L)."\n";}}return$K;}function
show_status(){$K=[];foreach(get_vals("PRAGMA compile_options")as$rh)$K[]=explode("=",$rh,2)+['',''];return$K;}function
convert_field(array$k){}function
unconvert_field(array$k,$K){return$K;}function
support($yd){return
preg_match('~^(check|columns|comment|database|drop_col|dump|fast_status|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$yd);}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.0+25572efd")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return[SERVER,$_GET["username"],get_password()];}function
connectSsl(){}function
permanentLogin($Ob=false){return
password_file($Ob);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($O){return
h($O);}function
database(){return
DB;}function
databases($Md=true){return
get_databases($Md);}function
pluginsLinks(){}function
operators($qk=null){return
driver()->operators($qk);}function
schemas(){$K=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$K))array_unshift($K,$_GET["ns"]);return$K;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Rb){return$Rb;}function
verifyVersion(){return
true;}function
donation(){return"<a href='https://www.adminer.org/donation/'".target_blank()." id='donation' title='".lang(37)."'>❤️</a>";}function
serviceWorker(){service_worker();}function
manifest(){$De=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$wj=preg_replace('~\?.*~','',ME)?:'.';return['name'=>"Adminer".($De!=""?" - $De":""),'short_name'=>'Adminer','description'=>lang(38),'start_url'=>$wj,'scope'=>$wj,'display'=>'minimal-ui','icons'=>[['src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.0+25572efd",'sizes'=>'any','type'=>'image/svg+xml']],];}function
head($Wb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$K=[];foreach(["","-dark"]as$Bg){$m="adminer$Bg.css";if(file_exists($m)){$Dd=file_get_contents($m);$K["$m?v=".crc32($Dd)]=($Bg?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Dd)?'':'light'));}}return$K;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(39).'<td>',input_hidden("auth[driver]","sqlite")."SQLite"),input_hidden("auth[server]",SERVER),adminer()->loginFormField('username','<tr><th>'.lang(40).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(41).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(17).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(42)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(43))."\n";}function
loginFormField($B,$ve,$Y){return$ve.$Y."\n";}function
login($Uf,$F){if($F=="")return
lang(44).require_password_link(null);if(!Driver::$passwords)return
lang(45).require_password_link($F);if(!password_required())return
lang(46).require_password_link($F);return
true;}function
tableName(array$qk){return
h($qk["Name"]);}function
fieldName(array$k,$D=0){$V=$k["full_type"].($k["null"]?" NULL":"");$yb=$k["comment"];return'<span title="'.h($V.($yb!=""?($V?": ":"").$yb:'')).'">'.h($k["field"]).'</span>';}function
commentValue($V,$yb){if($yb==""||$V=='SCHEMA'||$V=='TABLE'||$V=='COLUMN')return
h($yb);$qi=function($ij,$ab='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$ab>",preg_replace('~\|$~m',"",rtrim($ij))));};$S='(\+--[-+]+\+\n)';$L='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$S?$L$S?($L*)$S?~m",function($A)use($qi){return"<table>\n".($A[1]?"<thead>".$qi($A[2],'th')."<tbody>\n":$qi($A[2])).$qi($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($yb))))."</pre>\n";}function
commentInput($V,$b,$yb){$Y=h($yb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($V=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$qk,$P=""){$B=$qk["Name"];echo'<p class="links">';$Qf=[];if($B!="")$Qf["select"]=lang(47);if(support("table")||support("indexes"))$Qf["table"]=lang(48);if(support("table")){if(is_view($qk)){if(support("view"))$Qf["view"]=lang(49);}elseif(function_exists('Adminer\alter_table')&&$B!="")$Qf["create"]=lang(50);}if($P!==null)$Qf["edit"]=lang(51);foreach($Qf
as$w=>$X)echo" <a href='".h(ME)."$w=".url_escape($B).($w=="edit"?$P:"")."'".bold(isset($_GET[$w])).">$X</a>";echo"\n";}function
foreignKeys($S){return
foreign_keys($S);}function
backwardKeys($S,$pk){return[];}function
backwardKeysPrint(array$Ma,array$L){}function
selectQuery($I,$Yj,$wd=false){$K="\n";if(!$wd&&($Yl=driver()->warnings())){$s="warnings";$K=", <a href='#$s' class='toggle'>".lang(52)."</a>"."$K<div id='$s' class='hidden'>\n$Yl</div>\n";}return"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$I))."</code> <span class='time'>(".format_time($Yj).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($I)."' class='hover'>".lang(20)."</a>":"").$K;}function
sqlCommandQuery($I){return
shorten_utf8(trim($I),1000);}function
sqlPrintAfter(){}function
sqlUrlLength(){return
2000;}function
explain(Db$Db,$I,array$yh){$J=explain($Db,$I);if(!$J)return"";ob_start();print_select_result($J,$Db,$yh);return
ob_get_clean();}function
rowDescription($S){return"";}function
rowDescriptions(array$M,array$Pd){return$M;}function
selectLink($X,array$k){}function
selectVal($X,$z,array$k,$Dh){$K=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$k["type"])&&!preg_match("~var~",$k["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$k["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($k)&&!is_utf8($X))$K="<i>".lang(53,strlen($Dh))."</i>";return($z?"<a href='".h($z)."'".(is_url($z)?target_blank():"").">$K</a>":$K);}function
editVal($X,array$k){return$X;}function
config(){return[];}function
tableStructurePrint(array$l,$qk=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(54)."<th>".lang(55).(support("comment")?"<th>".lang(56):"")."<tbody>\n";$Jl=(support("type")?types():[]);foreach($l
as$k){echo"<tr><th>".h($k["field"]).doc_link([JUSH=>$k["doc"]]);$V=h($k["full_type"]);$tb=h($k["collation"]);echo"<td><span title='$tb'>".(in_array($V,$Jl)?"<a href='".h(ME.'type='.url_escape($V))."'>$V</a>":$V.($tb&&isset($qk["Collation"])&&$tb!=$qk["Collation"]?" $tb":""))."</span>",($k["null"]?" <i>NULL</i>":""),($k["auto_increment"]?" <i>".lang(57)."</i>":""),(isset($k["default"])?" <span title='".lang(58)."'>[<b>".($k["generated"]||$k["default_sql"]?"<code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($k["default"])),80,"</code>"):h($k["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$k["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$qk){$Qh=false;foreach($v
as$B=>$u)$Qh|=!!$u["partial"];echo"<table>\n";$gc=first(driver()->indexAlgorithms($qk));foreach($v
as$B=>$u){ksort($u["columns"]);$xi=[];foreach($u["columns"]as$w=>$X)$xi[]="<i>".h($X)."</i>".($u["lengths"][$w]?"(".h($u["lengths"][$w]).")":"").($u["descs"][$w]?" DESC":"");echo"<tr title='".h($B)."'>","<th>".h($u["type"]).($gc&&$u['algorithm']!=$gc?" (".h($u['algorithm']).")":""),"<td>".implode(", ",$xi).($u["invalid"]?" <span class='error'>INVALID</span>":"");if($Qh)echo"<td>".($u['partial']?"<code class='sql jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
namePattern($V){if($V=="FOREIGN"||$V=="CHECK")return"";if($V=="TRIGGER")return"{table}_{timing}{event}";return(JUSH=="sql"?"":"{table}_")."{columns}";}function
selectColumnInput($b,array$e,$Y="",$gi=""){return
select_input($b,$e,$Y,$gi);}function
selectColumnsPrint(array$N,array$e){print_fieldset("select",lang(59),$N);$r=0;$N[""]=[];foreach($N
as$w=>$X){$X=idx($_GET["columns"],$w,[]);$d=adminer()->selectColumnInput(" name='columns[$r][col]' data-default=''".on('change',($w!==""?'selectFieldChange':'selectAddRow')),$e,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",[-1=>""]+array_filter([lang(60)=>driver()->functions,lang(61)=>driver()->grouping]),$X["fun"]," data-default=''".on('change',($w!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($d)":$d)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$v,$qk=null){print_fieldset("search",lang(18),$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$ph=adminer()->operators($qk);foreach(array_merge((array)$_GET["where"],[[]])as$r=>$X){if(!$X||(("$X[col]$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$ph)))echo"<div>".adminer()->selectColumnInput(" name='where[$r][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$e,$X["col"],"(".lang(62).")"),html_select("where[$r][op]",$ph,$X["op"]," data-default='".h(first($ph))."'".on('change','selectFirstChange')),"<input type='search' name='where[$r][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$D,array$e,array$v){print_fieldset("sort",lang(63),$D);$r=0;foreach((array)$_GET["order"]as$w=>$X){if($X!=""){echo"<div>".adminer()->selectColumnInput(" name='order[$r]' data-default=''".on('change','selectFieldChange'),$e,$X),checkbox("desc[$r]",1,isset($_GET["desc"][$w]),lang(64))."</div>\n";$r++;}}echo"<div>".adminer()->selectColumnInput(" name='order[$r]' data-default=''".on('change','selectAddRow'),$e),checkbox("desc[$r]",1,false,lang(64))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($y){echo"<fieldset><legend>".lang(65)."</legend><div>","<input type='number' name='limit' class='size' value='".h($y?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($Lk){echo"<fieldset><legend>".lang(66)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Lk)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".lang(67)."</legend><div>","<input type='submit' value='".lang(59)."'>"," <span id='noindex' title='".lang(68)."'></span>","<script".nonce().">\n","const indexColumns = ";$e=[];foreach($v
as$u){$Vb=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$Vb)$e[$Vb]=1;}$e[""]=1;foreach($e
as$w=>$X)json_row($w);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Uc,array$e){}function
selectColumnsProcess(array$e,array$v){$N=[];$q=[];foreach((array)$_GET["columns"]as$w=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$N[$w]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$q[]=$N[$w];}}return[$N,$q];}function
selectSearchProcess(array$l,array$v,$qk=null){$K=[];foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$K[]=driver()->fulltextSql($r,$u,$_GET["fulltext"][$r],isset($_GET["boolean"][$r]));}$ph=adminer()->operators($qk);foreach((array)$_GET["where"]as$w=>$X){$X+=["col"=>"","op"=>first($ph),"val"=>""];$_GET["where"][$w]=$X;$c=$X["col"];if(("$c$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$ph)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Bb=[];foreach(($c!=""?[$c=>idx($l,$c,[])]:$l)as$B=>$k){if($c!=""||is_searchable($k,$X))$Bb[]=adminer()->selectSearchCondition(driver()->convertSearch(idf_escape($B),$X,$k),$X["op"],$X["val"]);}$K[]=(count($Bb)==1?$Bb[0]:($Bb?"(".implode(" OR ",$Bb).")":"1 = 0"));}}return$K;}function
selectSearchCondition($d,$lh,$X){if(preg_match('~IN$~',$lh))return"$d $lh ".($X!=""?process_in($X):"(NULL)");if($lh=="BETWEEN")return"$d $lh ".process_between($X);if($lh=="SQL")return"$d $X";if(preg_match('~^(I?LIKE) %%$~',$lh,$A))return"$d $A[1] ".q("%$X%");if($lh=="FIND_IN_SET")return"$lh(".q($X).", $d)";return"$d $lh".(!preg_match('~NULL$~',$lh)?" ".q($X):"");}function
selectOrderProcess(array$l,array$v){$K=[];foreach((array)$_GET["order"]as$w=>$X){if($X!="")$K[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$w])?" DESC".(JUSH=='pgsql'&&idx($l[$X],"null")?" NULLS LAST":""):"");}return$K;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Pd){return
false;}function
selectQueryBuild(array$N,array$Z,array$q,array$D,$y,$E){return"";}function
executeQuery($I,$Za){return$Za();}function
messageQuery($I,$Nk,$wd=false){restart_session();$Ae=&get_session("queries");if(!idx($Ae,$_GET["db"]))$Ae[$_GET["db"]]=[];if(strlen($I)>1e6)$I=preg_replace('~[\x80-\xFF]+$~','',substr($I,0,1e6))."\n…";$Ae[$_GET["db"]][]=[$I,time(),$Nk];$Vj="sql-".count($Ae[$_GET["db"]]);$K="<a href='#$Vj' class='toggle'>".lang(69)."</a> ".copy_icon()."\n";if(!$wd&&($Yl=driver()->warnings())){$s="warnings-".count($Ae[$_GET["db"]]);$K="<a href='#$s' class='toggle'>".lang(52)."</a>, $K<div id='$s' class='hidden'>\n$Yl</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $K<div id='$Vj' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($I,1e4)."</code></pre>".($Nk?" <span class='time'>($Nk)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($Ae[$_GET["db"]])-1)).'">'.lang(20).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($S,array$l,$L,$zl,$I='',$Nk=''){echo($I!=""?"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$I))."</code> <span class='time'>($Nk)</span>\n":"");}function
editFunctions(array$k){$K=($k["null"]?"NULL/":"");$re=isset($_GET["select"])||where($_GET);foreach([driver()->insertFunctions,driver()->editFunctions]as$w=>$be){if(!$w||(!isset($_GET["call"])&&$re)){foreach($be
as$ci=>$X){if(!$ci||preg_match("~$ci~",$k["type"]))$K
.="/$X";}}if($w&&$be&&!preg_match('~set|bool~',$k["type"])&&!is_blob($k))$K
.="/SQL";}if($k["auto_increment"]&&!$re)$K=lang(57);return
explode("/",$K);}function
editInput($S,array$k,$b,$Y){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$b,$k,$Y,"NULL");return"";}function
editHint($S,array$k,$Y){return"";}function
processInput(array$k,$Y,$p=""){if($p=="SQL")return$Y;$B=$k["field"];$K=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$p))$K="$p()";elseif(preg_match('~^current_(date|timestamp)$~',$p))$K=$p;elseif(preg_match('~^([+-]|\|\|)$~',$p))$K=idf_escape($B)." $p $K";elseif(preg_match('~^[+-] interval$~',$p))$K=idf_escape($B)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$K);elseif(preg_match('~^(addtime|subtime|concat)$~',$p))$K="$p(".idf_escape($B).", $K)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$p))$K="$p($K)";return
unconvert_field($k,$K);}function
dumpOutput(){$K=['text'=>lang(70),'file'=>lang(71)];if(function_exists('gzencode'))$K['gz']='gzip';return$K;}function
dumpFormat(){return(support("dump")?['sql'=>'SQL']:[])+['csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV'];}function
dumpPrint(){}function
dumpDatabase($h){}function
dumpTable($S,$gk,$nf=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($gk)dump_csv(array_keys(fields($S)));}else{if($nf==2){$l=[];foreach(fields($S)as$B=>$k)$l[]=idf_escape($B)." ".full_type_sql($k);$Ob="CREATE TABLE ".table($S)." (".implode(", ",$l).")";}else$Ob=create_sql($S,$_POST["auto_increment"],$gk);set_utf8mb4($Ob);if($gk&&$Ob){if(($gk=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$nf==1)echo"DROP ".($nf==2?"VIEW":"TABLE")." IF EXISTS ".table($S).";\n";if($nf==1)$Ob=remove_definer($Ob);echo"$Ob;\n\n";}}}function
dumpData($S,$gk,$I,array$N=[],array$Z=[],array$q=[],array$D=[]){if($gk){$dg=(JUSH=="sqlite"?0:1048576);$l=[];$Ie=false;if($_POST["format"]=="sql"){if($gk=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($S).";\n";$l=fields($S);if(JUSH=="mssql"){foreach($l
as$k){if($k["auto_increment"]){echo"SET IDENTITY_INSERT ".table($S)." ON;\n";$Ie=true;break;}}}}$J=($I!=""?connection()->query($I,1):driver()->select($S,($N?:["*"]),$Z,$q,$D,0));if($J){$af="";$Xa="";$wf=[];$ce=[];$ik="";$_d=($S!=''?'fetch_assoc':'fetch_row');$Nb=0;while($L=$J->$_d()){if(!$wf){$Ql=[];foreach($L
as$X){$k=$J->fetch_field();if(idx($l[$k->name],'generated')){$ce[$k->name]=true;continue;}$wf[]=$k->name;$w=idf_escape($k->name);$Ql[]="$w = VALUES($w)";}$ik=($gk=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Ql):"").";\n";}foreach($L
as$w=>$X){if(is_array($X))$L[$w]=json_encode($X,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);}if($_POST["format"]!="sql"){if($gk=="table"){dump_csv($wf);$gk="INSERT";}dump_csv($L);}else{if(!$af)$af="INSERT INTO ".table($S)." (".implode(", ",array_map('Adminer\idf_escape',$wf)).") VALUES";foreach($L
as$w=>$X){if($ce[$w]){unset($L[$w]);continue;}$k=$l[$w];$L[$w]=($X===null?"NULL":($X===false?0:unconvert_field($k,preg_match(number_type(),$k["type"])&&!preg_match('~\[~',$k["full_type"])&&is_numeric($X)?$X:(!is_blob($k)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$ij=($dg?"\n":" ")."(".implode(",\t",$L).")";if(!$Xa)$Xa=$af.$ij;elseif(JUSH=='mssql'?$Nb%1000!=0:strlen($Xa)+4+strlen($ij)+strlen($ik)<$dg)$Xa
.=",$ij";else{echo$Xa.$ik;$Xa=$af.$ij;}}$Nb++;}if($Xa)echo$Xa.$ik;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";else
dump_csv([connection()->error]);if($Ie)echo"SET IDENTITY_INSERT ".table($S)." OFF;\n";}}function
dumpFilename($He){return
friendly_url($He!=""?$He:(SERVER?:"localhost"));}function
dumpHeaders($He,$Hg=false){$Hh=$_POST["output"];$sd=(preg_match('~sql~',$_POST["format"])?"sql":($Hg?"tar":"csv"));header("Content-Type: ".($Hh=="gz"?"application/x-gzip":($sd=="tar"?"application/x-tar":($sd=="sql"||$Hh!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Hh=="gz"){ob_start(function($R){return
gzencode($R);},1e6);}return$sd;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(72)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(73):lang(74))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(75)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(76)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(77)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(78)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(0)."</a>\n":""),(support("event")?"<a href='#events'>".lang(79)."</a>\n":"");return
true;}function
navigation($Ag){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Tg=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Tg)<0?h($Tg):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($Ag=="auth"){$Hh="";foreach((array)$_SESSION["pwds"]as$Sl=>$Ej){foreach($Ej
as$O=>$Ll){$B=h(get_setting("vendor-$Sl-$O")?:get_driver($Sl));foreach($Ll
as$Kl=>$F){if($B&&$F!==null){$ec=$_SESSION["db"][$Sl][$O][$Kl];foreach(($ec?array_keys($ec):[""])as$h)$Hh
.="<li><a href='".h(auth_url($Sl,$O,$Kl,$h))."'>($B) ".h("$Kl@").($O!=""?adminer()->serverName($O):"").h($h!=""?" - $h":"")."</a>\n";}}}}if($Hh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Hh</ul>\n";}else{$U=[];if($_POST&&!$Ag&&DB!="")connection()->select_db(DB);if($_GET["ns"]!==""&&!$Ag&&DB!="")$U=table_status('',true);adminer()->syntaxHighlighting($U);adminer()->databasesPrint($Ag);$ga=[];if(DB==""||!$Ag){if(support("sql")){$ga['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(69)."</a>";$ga['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(80)."</a>";}$ga['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(81)."</a>";}$Ne=$_GET["ns"]!==""&&!$Ag&&DB!="";if($Ne&&function_exists('Adminer\alter_table'))$ga['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(82)."</a>";$ga=adminer()->menuActions($ga,$Ag);echo($ga?"<p class='links'>\n".implode("\n",$ga)."\n":"");$rj=[];if($Ne){if(function_exists('Adminer\alter_table'))$rj[]=[lang(82),ME."create="];if(support("view"))$rj[]=[lang(83),ME."view="];$zd=['routine'=>['routines',lang(77)],'sequence'=>['sequences',lang(78)],'type'=>['user-types',lang(0)],'event'=>['events',lang(79)],];foreach($zd
as$yd=>$qj){if(support($yd))$rj[]=[$qj[1],substr(ME,0,-1)."#$qj[0]"];}}if(DB!=""&&!$Ag&&support("extension"))$rj[]=[lang(84),preg_replace('~\bns=[^&]*&~','',ME)."ns=#extensions"];echo($rj?script("jumpSections.unshift(...".json_encode($rj,JSON_UNESCAPED_UNICODE).");"):"");if($Ne){if($U)adminer()->tablesPrint($U);else
echo"<p class='message'>".lang(19)."</p>\n";}}}function
syntaxHighlighting(array$U){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.3.0+25572efd",true);$Dg=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($Dg?script("addEventListener('DOMContentLoaded', () => {\n$Dg\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($U){$Qf=[];foreach($U
as$S=>$V)$Qf[]=js_escape_re($S);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Qf).')(?!\$)\b/g',false);$Wj=["sql","check","event","procedure","trigger","view","type","table","processlist"];json_row('');echo"};\n";foreach(["bac","bra","sqlite_quo","mssql_bra"]as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(["sql","check","event","procedure","trigger","view"]))){$bk=(isset($_GET["trigger"])?['INSERT INTO','UPDATE','DELETE FROM']:(isset($_GET["check"])?[]:(isset($_GET["view"])?['SELECT']:null)));$Ia=Driver::jushAutocomplete($U,$bk);echo($Ia?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ia; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($Ag){if(support("single_db"))return;$g=adminer()->databases();if(DB&&$g&&!in_array(DB,$g))array_unshift($g,DB);echo"<form>\n<p id='dbs'>\n";hidden_fields_get();$bc=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(17)."'>".lang(85).": ".($g?html_select("db",[""=>""]+group_system($g),DB,$bc):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(31)."'".($g?" class='hidden'":"").">\n";foreach(["import","sql","schema","dump","privileges"]as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ga,$Ag){return$ga;}function
tablesPrint(array$U){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($U
as$S=>$Q){$S="$S";$B=adminer()->tableName($Q);if($B!=""&&!$Q["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($S).'"'.bold($_GET["select"]==$S||$_GET["edit"]==$S,"select hover")." title='".lang(47)."'>".lang(13)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($S).'"'.bold(in_array($S,[$_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"]]),(is_view($Q)?"view":"structure"))." title='".lang(48)."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($s){return
kill_process($s);}function
formatSizeValue($X){return
format_number($X);}}class
Plugins{private
static$append=['dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true];var$plugins;var$disabled=[];var$drivers=[];var$driverFiles=[];var$error='';private$hooks=[];function
__construct($ki){$Fc=SqlDriver::$drivers;$xe=" href='https://www.adminer.org/plugins/#use'".target_blank();if($ki===null){$ki=[];$Qa="adminer-plugins";if(is_dir($Qa)){foreach(glob("$Qa/*.php")as$m){$Ed=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$Ed)as$s=>$B)$this->driverFiles[$s]=$m;}}if(file_exists("$Qa.php")){$Pe=$this->includeOnce("$Qa.php");if(is_array($Pe)){foreach($Pe
as$w=>$hi)$ki[is_object($hi)?get_class($hi):$w]=$hi;}else$this->error
.=lang(86,"<b>$Qa.php</b>",$xe)."<br>";}foreach(get_declared_classes()as$pb){if(!$ki[$pb]&&(preg_match('~^Adminer\w~i',$pb)||is_subclass_of($pb,'Adminer\Plugin'))){$Qi=new
\ReflectionClass($pb);$Fb=$Qi->getConstructor();if($Fb&&$Fb->getNumberOfRequiredParameters())$this->error
.=lang(87,$xe,"<b>$pb</b>","<b>$Qa.php</b>")."<br>";else$ki[$pb]=new$pb;}}}$ef=array_filter($ki,function($hi){return!is_object($hi);});if($ef){$this->error
.=lang(88,$xe)."<br>";$ki=array_diff_key($ki,$ef);}$mk=get_setting("plugins","adminer_config");foreach($ki
as$w=>$hi){if($hi
instanceof
Plugin&&$hi::$optional){$Zj=idx($mk,get_class($hi));if($Zj!==null?!$Zj:!$hi->enabled){$this->disabled[]=$hi;unset($ki[$w]);}}}$this->drivers=array_diff_key(SqlDriver::$drivers,$Fc);$this->plugins=$ki;$ka=new
Adminer;$ki[]=$ka;$Qi=new
\ReflectionObject($ka);foreach($Qi->getMethods()as$yg){foreach($ki
as$hi){$B=$yg->getName();if(method_exists($hi,$B))$this->hooks[$B][]=$hi;}}}function
includeOnce($m){return
include_once"./$m";}static
function
pluginName($hi){return
preg_replace('~^ Adminer\\\\? ~','',preg_replace('~[A-Z]~',' $0',get_class($hi)));}static
function
describe($hi){$oc=(method_exists($hi,'description')?$hi->description():"");if(!$oc){$Qi=new
\ReflectionObject($hi);if(preg_match('~^/[\s*]+(.+?)(?:\s*\*/)?$~m',$Qi->getDocComment(),$A))$oc=$A[1];}$nj=(method_exists($hi,'screenshot')?$hi->screenshot():"");return
h($oc).($nj?($oc?" ":"")."(<a href='".h($nj)."'".target_blank().">".lang(89)."</a>)":"");}static
function
checksum($m){$Dd=str_replace("\r","",file_get_contents($m));$Dd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Dd);return
dechex(crc32($Dd));}function
checksums(){$Fd=array_values($this->driverFiles);foreach($this->plugins
as$hi){$Qi=new
\ReflectionObject($hi);$Fd[]=$Qi->getFileName();}$K=[];foreach($Fd
as$m)$K[basename($m,'.php')]=self::checksum($m);return$K;}static
function
officialChecksums(){return['adminer.js'=>'a0599090','backward-keys'=>'1c017d59','before-unload'=>'9674d220','config'=>'7f179b4f','dark-switcher'=>'8c201319','database-hide'=>'e304a899','designs'=>'c0a56b18','donation'=>'365aa745','dump-alter'=>'896b579e','dump-bz2'=>'c0d6de78','dump-date'=>'512aad63','dump-json'=>'f8aa6b99','dump-xml'=>'aedd2d3a','dump-zip'=>'96882e78','edit-foreign'=>'d04a0ac8','edit-textarea'=>'a178d7d9','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'e9005cb0','file-upload'=>'10add0e8','foreign-system'=>'634ce119','frames'=>'b0e1d11a','highlight-codemirror'=>'ada4b569','highlight-monaco'=>'95cf93b3','highlight-prism'=>'19933b19','import-csv'=>'bcecfad2','login-ip'=>'4d174fea','login-otp'=>'66bd013d','login-passkey'=>'a73c386f','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'2b06a5b7','login-table'=>'811f8cef','menu-links'=>'55b9b15c','name-patterns'=>'84c10d09','remote-color'=>'c5e74631','row-numbers'=>'1ad1b6cf','select-email'=>'c33a2661','select-expression'=>'b6a4b3f1','select-foreign'=>'255d4532','select-image'=>'3cefeee4','slugify'=>'7b422f0e','sql-gemini'=>'c60ab309','sql-log'=>'b9aa4ea6','sql-url-length'=>'cd70f1a0','table-indexes-structure'=>'e0b1433','table-structure'=>'f71ec61b','tables-filter'=>'2729bf7','timeout'=>'9e9bce60','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'a5a5e5b4','elastic'=>'b7e4a70d','firebird'=>'5f3d9493','igdb'=>'39c028f6','imap'=>'35808394','mongo'=>'e6e3303b','redis'=>'c2f04eae','simpledb'=>'9eb464cf',];}function
__call($B,array$Nh){$_a=[];foreach($Nh
as$w=>$X)$_a[]=&$Nh[$w];$K=null;foreach($this->hooks[$B]as$hi){$Y=call_user_func_array([$hi,$B],$_a);if($Y!==null){if(!self::$append[$B])return$Y;$K=$Y+(array)$K;}}return$K;}}abstract
class
Plugin{static$optional=false;var$enabled=true;protected$translations=[];function
disable(){$this->enabled=false;return$this;}function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$C=null){$_a=func_get_args();$_a[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$_a);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($Yh){$this->password_hash=$Yh;}function
description(){return
lang(90);}function
credentials(){$F=get_password();return[SERVER,$_GET["username"],($this->passwordMatches($F)&&!password_required()?"":$F)];}function
login($Uf,$F){if($this->passwordMatches($F))return
true;if(!Driver::$passwords||!password_required())return
false;}protected
function
passwordMatches($F){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($F),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);exit;}function
page_header($Qk,$j="",$Wa=[],$Rk="",$Xg=false,$Bc=""){if($Xg){header("HTTP/1.1 404 Not Found");$j=($j?:lang(91));}page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Sk=$Qk.($Rk!=""?": $Rk":"");$Tk=strip_tags($Sk.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(92),'\' class=\'',lang(92),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Tk,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.3.0+25572efd"),'">
';$Sb=adminer()->css();if(is_int(key($Sb)))$Sb=array_fill_keys($Sb,'light');$pe=in_array('light',$Sb)||in_array('',$Sb);$ne=in_array('dark',$Sb)||in_array('',$Sb);$Wb=($pe?($ne?null:false):($ne?:null));$mg=" media='(prefers-color-scheme: dark)'";if($Wb!==false)echo"<link rel='stylesheet'".($Wb?"":$mg)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.3.0+25572efd")."'>\n";echo"<meta name='color-scheme' content='".($Wb===null?"light dark":($Wb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.3.0+25572efd");if(adminer()->head($Wb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.0+25572efd")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Sb
as$Dl=>$Bg){$b=($Bg=='dark'&&!$Wb?$mg:($Bg=='light'&&$ne?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($Dl)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(93))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(94)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Wa!==null){$rf=(class_exists('Adminer\Db')&&connection());$z=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb">'.($rf?jump_input():'').'<a href="'.h($z?:".").'">'.get_driver(DRIVER).'</a> » ';$z=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$O=adminer()->serverName(SERVER);$O=($O!=""?$O:lang(95));if($Wa===false)echo"$O\n";else{echo"<a href='".h($z.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$O</a> » ";$qj="";if(is_string($Wa)){$qj=$Wa;$Wa=[];}if($_GET["ns"]!=""||(DB!=""&&is_array($Wa))){$cc="$z&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($cc.($_GET["ns"]==""?$qj:"")).'">'.h(DB).'</a> » ';}if(is_array($Wa)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$qj).'">'.h($_GET["ns"]).'</a> » ';foreach($Wa
as$w=>$X){$nc=(is_array($X)?$X[1]:h($X));if($nc!="")echo"<a href='".h(ME."$w=").url_escape(is_array($X)?$X[0]:$X)."'>$nc</a> » ";}}echo"$Qk\n";}if($rf){echo"<form id='jump-form'>\n";hidden_fields_get();echo(DB!=""?input_hidden("db",DB):""),(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""),"</form>\n",script("jumpSetup();");}}echo"<h2>$Sk$Bc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$g=&get_session("dbs");if(DB!=""&&$g&&!in_array(DB,$g,true))$g=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($Xg){page_footer($Xg===true?"":$Xg);exit;}}function
service_worker(){$Ti=has_passwords();$rb=($Ti?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.3.0+25572efd")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$rb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$Ej){foreach($Ej
as$Ll){foreach($Ll
as$F){if($F!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Rb){$te=[];foreach($Rb
as$w=>$X)$te[]="$w $X";header("Content-Security-Policy: ".implode("; ",$te));}adminer()->headers();}function
csp(){return[["script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",],];}function
design_checksums(){$Il=[];foreach(array_keys(adminer()->css())as$Dl)$Il[preg_replace('~\?.*~','',$Dl)]=true;$K=[];foreach(["adminer.css","adminer-dark.css"]as$m){if($Il[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$A);$K[$m]=[(string)$A[1],Plugins::checksum($m)];}}return$K;}function
official_design_checksums(){return['adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'c9d1d11c','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'d9169f12','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'a8babfc5','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'a38b3b6a','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',];}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Wg;if(!$Wg)$Wg=base64_encode(rand_string());return$Wg;}function
page_messages($j){$Cl=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$tg=idx($_SESSION["messages"],$Cl);if($tg){echo"<div class='message'>".implode("</div>\n<div class='message'>",$tg)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Cl]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Ag=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Ag);echo"</div>\n";if($Ag!="auth")echo'<form method="post">
<p class="logout">
<span title="',lang(40),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(96),'\' id=\'logout\'>
',adminer()->donation()."\n",input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Jg){while($Jg>=2147483648)$Jg-=4294967296;while($Jg<=-2147483649)$Jg+=4294967296;return(int)$Jg;}function
long2str(array$W,$Xl){$ij='';foreach($W
as$X)$ij
.=pack('V',$X);if($Xl)return
substr($ij,0,end($W));return$ij;}function
str2long($ij,$Xl){$W=array_values(unpack('V*',str_pad($ij,4*ceil(strlen($ij)/4),"\0")));if($Xl)$W[]=strlen($ij);return$W;}function
xxtea_mx($im,$hm,$jk,$tf){return
int32((($im>>5&0x7FFFFFF)^$hm<<2)+(($hm>>3&0x1FFFFFFF)^$im<<4))^int32(($jk^$hm)+($tf^$im));}function
encrypt_string($dk,$w){if($dk=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($dk,true);$Jg=count($W)-1;$im=$W[$Jg];$hm=$W[0];$H=floor(6+52/($Jg+1));$jk=0;while($H-->0){$jk=int32($jk+0x9E3779B9);$Mc=$jk>>2&3;for($Ih=0;$Ih<$Jg;$Ih++){$hm=$W[$Ih+1];$Ig=xxtea_mx($im,$hm,$jk,$w[$Ih&3^$Mc]);$im=int32($W[$Ih]+$Ig);$W[$Ih]=$im;}$hm=$W[0];$Ig=xxtea_mx($im,$hm,$jk,$w[$Ih&3^$Mc]);$im=int32($W[$Jg]+$Ig);$W[$Jg]=$im;}return
long2str($W,false);}function
decrypt_string($dk,$w){if($dk=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($dk,false);$Jg=count($W)-1;$im=$W[$Jg];$hm=$W[0];$H=floor(6+52/($Jg+1));$jk=int32($H*0x9E3779B9);while($jk){$Mc=$jk>>2&3;for($Ih=$Jg;$Ih>0;$Ih--){$im=$W[$Ih-1];$Ig=xxtea_mx($im,$hm,$jk,$w[$Ih&3^$Mc]);$hm=int32($W[$Ih]-$Ig);$W[$Ih]=$hm;}$im=$W[$Jg];$Ig=xxtea_mx($im,$hm,$jk,$w[$Ih&3^$Mc]);$hm=int32($W[0]-$Ig);$W[0]=$hm;$jk=int32($jk-0x9E3779B9);}return
long2str($W,true);}$fi=[];if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$fi[$w]=$X;}}function
add_invalid_login(){$Oa=get_temp_dir()."/adminer-invalid";foreach(glob("$Oa*")?:[$Oa]as$m){$o=file_open_lock($m);if($o)break;}if(!$o)$o=file_open_lock("$Oa-".rand_string());if(!$o)return;$gf=json_decode(stream_get_contents($o),true);$Nk=time();if($gf){foreach($gf
as$hf=>$X){if($X[0]<$Nk)unset($gf[$hf]);}}$ef=&$gf[adminer()->bruteForceKey()];if(!$ef)$ef=[$Nk+30*60,0];$ef[1]++;file_write_unlock($o,json_encode($gf));}function
check_invalid_login(array&$fi){$gf=[];foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$o=file_open_lock($m);if($o){$gf=json_decode(stream_get_contents($o),true);file_unlock($o);break;}}$w=adminer()->bruteForceKey();$ef=idx($gf,$w,[]);$Vg=($ef[1]>29?$ef[0]-time():0);if($Vg>0){$j=lang(97,ceil($Vg/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$w==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(98,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$fi,false);}}function
password_required(){static$K;if($K===null){$K=(bool)get_session("password_required");if(!$K){$Qb=adminer()->credentials();$K=!is_object(Driver::connect($Qb[0],$Qb[1],""));if($K)set_session("password_required",true);}}return$K;}function
require_password_link($F){$Fg="<a href='https://www.adminer.org/password/'".target_blank().">".lang(99)."</a>";if(!function_exists('password_hash'))return" $Fg";$ii=($F!==null?$F:base64_encode(substr(hex2bin(rand_string()),0,12)));$se=password_hash($ii,PASSWORD_DEFAULT);$m="adminer-plugins.php";$od=file_exists("adminer-plugins.php");if($od)$df=($F!==null?lang(100,"<b>$m</b>"):lang(101,"<b>$m</b>","<b>$ii</b>"));else{$m="<button name='password_less' value='".h($se)."' class='link'>$m</button>";$df=($F!==null?lang(102,$m):lang(103,$m,"<b>$ii</b>"));}$Of="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($se)."'</span>),";$K="<p>$df
<pre><code class='jush'>".($od?$Of:"&lt;?php\n<a>return</a> <a>array</a>(\n$Of\n);")."</code></pre>
<p>$Fg
";return" <a href='#password-less' class='toggle'>".lang(104)."</a>
<div id='password-less' class='hidden'>".($od?$K:"<form method='post'>\n".$K.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Ga=$_POST["auth"];if($Ga&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$Sl=$Ga["driver"];$O=$Ga["server"];$Kl=$Ga["username"];$F=(string)$Ga["password"];$h=$Ga["db"];set_password($Sl,$O,$Kl,$F);$_SESSION["db"][$Sl][$O][$Kl][$h]=true;if($Ga["permanent"]){$w=implode("-",array_map('base64_encode',[$Sl,$O,$Kl,$h]));$yi=adminer()->permanentLogin(true);$fi[$w]="$w:".base64_encode($yi?encrypt_string($F,$yi):"");cookie("adminer_permanent",implode(" ",$fi));}if(!array_diff(array_keys($_POST),["auth","token"])||$Sl!=DRIVER||$O!=SERVER||$Kl!==$_GET["username"]||$h!=DB)redirect(auth_url($Sl,$O,$Kl,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(["pwds","db","dbs","queries"]as$w)set_session($w,null);unset_permanent($fi);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(105).' '.lang(106," href='https://www.adminer.org/donation/'".target_blank()));}elseif($fi&&!$_SESSION["pwds"]){session_regenerate_id();$yi=adminer()->permanentLogin();foreach($fi
as$w=>$X){list(,$ob)=explode(":",$X);list($Sl,$O,$Kl,$h)=array_map('base64_decode',explode("-",$w));set_password($Sl,$O,$Kl,decrypt_string(base64_decode($ob),$yi));$_SESSION["db"][$Sl][$O][$Kl][$h]=true;}}function
unset_permanent(array&$fi){foreach($fi
as$w=>$X){list($Sl,$O,$Kl,$h)=array_map('base64_decode',explode("-",$w));if($Sl==DRIVER&&$O==SERVER&&$Kl==$_GET["username"]&&$h==DB)unset($fi[$w]);}cookie("adminer_permanent",implode(" ",$fi));}function
auth_error($j,array&$fi,$ff=true){$Fj=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Fj]||$_GET[$Fj])&&!$_SESSION["token"])$j=lang(107);elseif($ff&&($F=get_password())!==null){restart_session();add_invalid_login();if($F===false)$j
.=($j?'<br>':'').lang(108,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($fi);}}if(!$_COOKIE[$Fj]&&$_GET[$Fj]&&ini_bool("session.use_only_cookies"))$j=lang(109);$Nh=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Nh["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(42),$j,null);echo"<form method='post'>\n","<div>";if(hidden_fields($_POST,["auth","token"]))echo"<p class='message'>".lang(110)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($fi);page_header(lang(111),lang(112,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$Db='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($fi);$Qb=adminer()->credentials();$Db=Driver::connect($Qb[0],$Qb[1],$Qb[2]);if(is_object($Db)){Db::$instance=$Db;Driver::$instance=new
Driver($Db);if($Db->flavor)save_settings(["vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)]);}}$Uf=null;if(!is_object($Db)||($Uf=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($Db)?nl_br(h($Db)):(is_string($Uf)?$Uf:lang(113))).(preg_match('~^ | $~',get_password())?'<br>'.lang(114):'');auth_error($j,$fi);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(96),lang(115));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(115).' '.lang(116);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(117,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(118);}function
print_select_result($J,$f=null,array$yh=[],&$y=0,&$Nc=false){$Qf=[];$v=[];$e=[];$U=[];$vi=[];$Rc=[];$pl=[];$K=[];$Cg=$Nc;$Nc=false;for($r=0;(!$y||$r<$y)&&is_array($L=$J->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($Cg?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($pf=0;$pf<count($L);$pf++){$k=$J->fetch_field();$B=$k->name;$S=(isset($k->table)?$k->table:"");$xh=(isset($k->orgtable)?$k->orgtable:"");$wh=(isset($k->orgname)?$k->orgname:$B);$ol=driver()->typeName($k);if($yh&&JUSH=="sql")$Qf[$pf]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($xh!=""){$qa=($S!=""?$S:$xh);if($S!="")$K[$S]=$xh;if(!isset($v[$qa])){if(!isset($vi[$xh])){$vi[$xh]=[];foreach(indexes($xh,$f)as$u){if($u["type"]=="PRIMARY"){$vi[$xh]=array_flip($u["columns"]);break;}}}$U[$qa]=$xh;$v[$qa]=$vi[$xh];$e[$qa]=$vi[$xh];}if(isset($e[$qa][$wh])){unset($e[$qa][$wh]);$v[$qa][$wh]=$pf;$Qf[$pf]=$qa;}elseif($Cg&&isset($k->orgname)&&$k->db==DB&&!is_blob(["type"=>$ol]))$Rc[$pf]=[$qa,$wh,preg_match('~text|json|lob~',$ol)];}$pl[$pf]=$ol;echo"<th title='".h(trim(($xh!=""?"$xh.$wh":($k->name!=$wh?$wh:""))." ".$ol))."'>".h($B).($yh?'':"");}foreach($Rc
as$pf=>$ab){if($e[$ab[0]])unset($Rc[$pf]);}echo"<tbody>\n";}$Je=[];foreach($v
as$qa=>$u){if($u&&!$e[$qa]){$t="";foreach($u
as$c=>$pf){if($L[$pf]===null){$t=null;break;}$t
.="&where[".url_escape(bracket_escape($c))."]=".url_escape($L[$pf]);}$Je[$qa]=$t;}}echo"<tr>";foreach($L
as$w=>$X){$z="";if(isset($Qf[$w])){if($yh&&JUSH=="sql"){$S=$L[array_search("table=",$Qf)];$z=ME.$Qf[$w].url_escape($yh[$S]!=""?$yh[$S]:$S);}elseif(idx($Je,$Qf[$w])!==null)$z=ME."edit=".url_escape($U[$Qf[$w]]).$Je[$Qf[$w]];}$Fe=select_value($X,$z,['type'=>(preg_match('~binary~',$pl[$w])?'blob':$pl[$w])],null);$b="";$ab=idx($Rc,$w);if($ab&&idx($Je,$ab[0])!==null&&is_utf8($X)){$Nc=true;$b=" data-name='".h("val[".bracket_escape($U[$ab[0]])."][".bracket_escape(substr($Je[$ab[0]],1))."][".bracket_escape($ab[1])."]")."' data-text='".($ab[2]?1:0)."'".(html_entity_decode(strip_tags($Fe),ENT_QUOTES,"UTF-8")!==$X?" data-value='".h($X)."'":"");}echo"<td".(preg_match(number_type(),$pl[$w])?" class='number'":"")."$b>$Fe";}}$y=$r;echo($r?"</table>\n</div>":"<p class='message'>".lang(22))."\n";return$K;}function
textarea($B,$Y,$M=10,$vb=80,$sf=JUSH,$fd=0){echo"<textarea name='".h($B)."' rows='$M' cols='$vb' class='sqlarea jush-".h($sf)."' spellcheck='false' wrap='off'".($fd?" data-error-line='$fd'":"").">\n";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$th,$Y="",$gi=""){if($th&&$Y!=""&&!isset($th[$Y]))$th=[$Y=>$Y]+$th;$Dk=($th?"select":"input");return"<$Dk$b".($th?"><option value=''>$gi".optionlist($th,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$gi'>");}function
json_row($w,$X=null,$id=true){static$Jd=true;if($Jd)echo"{";if($w!=""){echo($Jd?"":",")."\n\t\"".addcslashes($w,"\r\n\t\"\\/").'": '.($X!==null?($id?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Jd=false;}else{echo"\n}\n";$Jd=true;}}function
flat_collations(){$ub=collations();return(is_array(reset($ub))?call_user_func_array('array_merge',array_values($ub)):$ub);}function
edit_type($w,array$k,array$ub,array$Qd=[],array$vd=[]){$V=(string)$k["type"];echo"<td><select name='".h($w)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($V&&!array_key_exists($V,driver()->types())&&!isset($Qd[$V])&&!in_array($V,$vd))$vd[]=$V;$ek=driver()->structuredTypes();if($Qd)$ek[lang(119)]=$Qd;echo
optionlist(array_merge($vd,$ek),$V),"</select><td>","<input name='".h($w)."[length]' value='".h($k["length"])."' size='3'".(!$k["length"]&&preg_match('~var(char|binary)$~',$V)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($ub?"<input list='collations' name='".h($w)."[collation]'".option_types($V,'('.text_type().')$')." value='".h($k["collation"])."' placeholder='(".lang(120).")'>":''),(driver()->unsigned?"<select name='".h($w)."[unsigned]'".option_types($V,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$k["unsigned"]).'</select>':''),(isset($k['on_update'])?"<select name='".h($w)."[on_update]'".option_types($V,'timestamp|datetime').'>'.optionlist([""=>"(".lang(121).")","CURRENT_TIMESTAMP"],(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"CURRENT_TIMESTAMP":$k["on_update"])).'</select>':''),($Qd?"<select name='".h($w)."[on_delete]'".option_types($V,'`')."><option value=''>(".lang(122).")".optionlist(explode("|",driver()->onActions),$k["on_delete"])."</select> ":" ");}function
option_types($V,$pl){return" data-types='".h($pl)."'".(preg_match("~$pl~",$V)?"":" class='hidden'");}function
process_length($x){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$x))return"(max)";if(JUSH=="oracle"&&preg_match('~^\s*\(?\s*(\d+)\s+(CHAR|BYTE)\s*\)?\s*$~i',$x,$A))return"($A[1] ".strtoupper($A[2]).")";if(JUSH=="oracle"&&preg_match('~^\s*\(?\s*\*\s*,\s*(-?\d+)\s*\)?\s*$~',$x,$A))return"(*,$A[1])";$cd=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$cd(?:\\s*,\\s*$cd)*+\\s*\\)?\\s*\$~",$x)&&preg_match_all("~$cd~",$x,$Xf)?"(".implode(",",$Xf[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$x)));}function
process_in($X){$X=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$X);$Hi="'(?:[^']|'')*+'";$of=(preg_match("~^\\s*$Hi(?:\\s*,\\s*$Hi)*+\\s*\$~",$X)&&preg_match_all("~$Hi~",$X,$Xf)?array_map('Adminer\idf_unescape',$Xf[0]):array_map('trim',explode(",",$X)));return"(".implode(", ",array_map('Adminer\q',$of)).")";}function
process_between($X){$Hi="('(?:[^']|'')*+')";$Ji=(preg_match("~^\\s*$Hi\\s+AND\\s+$Hi\\s*\$~i",$X,$A)?[idf_unescape($A[1]),idf_unescape($A[2])]:preg_split('~\s+AND\s+~i',trim($X)));return(count($Ji)==2?q($Ji[0])." AND ".q($Ji[1]):"NULL AND NULL");}function
process_type(array$k,$sb="COLLATE"){return" ".(is_user_type($k["type"])?idf_escape($k["type"]):$k["type"]).process_length($k["length"]).(preg_match(number_type(),$k["type"])&&in_array($k["unsigned"],driver()->unsigned)?" $k[unsigned]":"").(preg_match('~'.text_type().'~',$k["type"])&&$k["collation"]?" $sb ".(JUSH=="mssql"&&preg_match('~^\w+$~',$k["collation"])?$k["collation"]:q($k["collation"])):"");}function
process_field(array$k,array$ml){return[idf_escape(trim($k["field"])),process_type($ml),($k["null"]?" NULL":" NOT NULL"),default_value($k),(preg_match('~timestamp|datetime~',$k["type"])&&$k["on_update"]?" ON UPDATE $k[on_update]".process_length($k["length"]):""),(support("comment")&&$k["comment"]!=""?" COMMENT ".q($k["comment"]):""),($k["auto_increment"]?auto_increment():null),];}function
default_value(array$k){if($k["default"]===null)return"";$i=str_replace("\r","",$k["default"]);$ce=$k["generated"];$R=!preg_match('~]$~',$k["length"])&&(preg_match('~char|binary|text|clob|json|enum|set|String~',$k["type"])||driver()->enumLength($k));$Oh=(JUSH=="sqlite"||(JUSH=="sql"&&$k["default_sql"]&&!preg_match("~^(current_timestamp(\\(\\d*\\))?|[bx]'[\\da-f]*')$~i",$i)));return(in_array($ce,driver()->generated)?(JUSH=="mssql"?" AS ($i)".($ce=="VIRTUAL"?"":" $ce"):" GENERATED ALWAYS AS ($i) $ce"):(preg_match('~^GENERATED ~i',$i)?" $i":" DEFAULT ".(!$k["default_sql"]&&($R||preg_match('~^(?![a-z])~i',$i))?(JUSH=="sql"&&preg_match('~text|json~',$k["type"])?"(".q($i).")":q($i)):($Oh?"($i)":$i))));}function
edit_fields(array$l,array$ub,$V="TABLE",array$Qd=[]){$l=array_values($l);$hc=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$zb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($V=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($V=="TABLE"?lang(123):lang(124)),"<th id='label-type'>".lang(55)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(125),"<th>".lang(126);if($V=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(57)."'>A_I</abbr>",doc_link(['sqlite'=>"autoinc.html",]),"<th id='label-default'$hc>".lang(58),(support("comment")?"<th id='label-comment'$zb>".lang(56):"");$Df=!support("move_col");echo"<td>".icon("plus","add[".($Df?count($l):0)."]","+",lang(127),($Df?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($l
as$r=>$k){$r++;$zh=$k[($_POST?"orig":"field")];$uc=(isset($_POST["add"][$r-1])||(isset($k["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$zh=="");echo"<tr".($uc?"":" hidden").">\n",($V=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$k["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(128))." ":"");if($uc)echo"<input name='fields[$r][field]' value='".h($k["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$zh);edit_type("fields[$r]",$k,$ub,$Qd);if($V=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$r][null]",1,$k["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($k["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$hc>".html_select("fields[$r][generated]",array_merge(["","DEFAULT","DEFAULT SQL"],driver()->generated),$k["generated"],"","label-default")." ";$b=" name='fields[$r][default]' aria-labelledby='label-default'";$Y=h($k["default"]);echo(preg_match('~\n~',$k["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$r][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$zb>".adminer()->commentInput('COLUMN',$b,$k["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",lang(127))." ":""),($zh==""||support("drop_col")?icon("cross","drop_col[$r]","x",lang(129)):"");}}function
process_fields(array&$l){if($_POST["add"]){$l=array_values($l);array_splice($l,key($_POST["add"]),0,[[]]);}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Gc,$Ob,$Ic,$Jk,$Kc,$_,$sg,$qg,$rg,$ih,$Rg){if($_POST["drop"])query_redirect($Gc,$_,$sg);elseif($ih=="")query_redirect($Ob,$_,$rg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($_,$qg,queries($Gc)&&queries($Ob)&&driver()->commit());driver()->rollback();}elseif($ih!=$Rg){$Pb=queries($Ob);queries_redirect($_,$qg,$Pb&&queries($Gc));if($Pb&&$Ic)queries($Ic);}else
queries_redirect($_,$qg,queries($Jk)&&queries($Kc)&&queries($Gc)&&queries($Ob));}function
create_trigger($jh,array$L){$Pk=" $L[Timing] $L[Event]".(preg_match('~ OF~',$L["Event"])?" $L[Of]":"");return"CREATE TRIGGER ".idf_escape($L["Trigger"]).(JUSH=="mssql"?$jh.$Pk:$Pk.$jh).preg_replace('~[\s;]+$~',''," $L[Type]\n$L[Statement]").";";}function
q_dollar($R){$lc='$$';while(strpos($R.$lc,$lc)!=strlen($R))$lc='$_'.substr($lc,1);return$lc.$R.$lc;}function
routine_collate($tb){static$gb=[];if($tb&&!$gb){foreach(collations()as$fb=>$Pl){foreach((array)$Pl
as$X)$gb[$X]=$fb;}}return($gb[$tb]?"CHARACTER SET ".q($gb[$tb])." ":"")."COLLATE";}function
create_routine($ej,array$L){$P=[];$l=$L["fields"];ksort($l);foreach($l
as$k){if($k["field"]!=""){$Ye=(preg_match("~^(".driver()->inout.")\$~",$k["inout"])?$k["inout"]:"");$P[]="\n  ".(JUSH=="mssql"?"@$k[field]".process_type($k).($Ye?" $Ye":""):($Ye?"$Ye ":"").idf_escape($k["field"]).process_type($k,routine_collate($k["collation"])));}}$jc="";$th=[];foreach(routine_options($ej)as$w=>$Ql){$Y=idx($L["options"],$w,"");if($w=="DEFINER")$jc=($Y?" $w=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$Ql){if($Y!="")$th[]="$w ".q($Y);}elseif($Y!=reset($Ql)&&in_array($Y,$Ql))$th[]=$Y;}$Bf=$L["language"];$kc=preg_replace('~[\s;]+$~','',$L["definition"]);$Cc=(JUSH=="pgsql"||($Bf&&$Bf!="sql"));$Mh=($P?implode(",",$P)."\n":"");return"CREATE$jc $ej ".table(trim($L["name"])).(JUSH=="mssql"&&$ej=="PROCEDURE"?rtrim($Mh):" ($Mh)").($ej=="FUNCTION"?"\nRETURNS".process_type($L["returns"],routine_collate($L["returns"]["collation"])):"").($Bf?" LANGUAGE $Bf":"").($th?"\n".implode(" ",$th):"").($Cc?" AS ".q_dollar("\n".trim($kc)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$kc;");}function
remove_definer($I){$jc=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($jc).')','\1',$I);}function
object_name($V,$S,array$e){return
str_replace(["{table}","{columns}"],[$S,implode("_",$e)],adminer()->namePattern($V));}function
format_foreign_key(array$n,$B=""){$h=$n["db"];$Yg=$n["ns"];return($B!=""?" CONSTRAINT ".idf_escape($B):"")." FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$n["source"])).") REFERENCES ".($h!=""&&$h!=$_GET["db"]?idf_escape($h).".":"").($Yg!=""&&$Yg!=$_GET["ns"]?idf_escape($Yg).".":"").idf_escape($n["table"])." (".implode(", ",array_map('Adminer\idf_escape',$n["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$n["on_delete"])?" ON DELETE $n[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$n["on_update"])?" ON UPDATE $n[on_update]":"").($n["deferrable"]?" $n[deferrable]":"");}function
tar_file($m,$Uk){$K=pack("a100a8a8a8a12a12",$m,644,0,0,decoct($Uk->size),decoct(time()));$lb=8*32;for($r=0;$r<strlen($K);$r++)$lb+=ord($K[$r]);$K
.=sprintf("%06o",$lb)."\0 ";echo$K,str_repeat("\0",512-strlen($K));$Uk->send();echo
str_repeat("\0",511-($Uk->size+511)%512);}function
doc_version(){$Dj=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$Dj,$A);return($A[1]>=18?$A[1]:"19");}$Si=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$Tl=(preg_match($Si,$Dj,$A)?$A[0]:"");if(JUSH=='mssql')return($Tl>=15?"sql-server-ver$Tl":($Tl==12?"azuresqldb-current":"sql-server-2017"));return$Tl;}function
doc_link(array$bi,$Kk="📖"){$Tl=doc_version();$El=['sql'=>"https://dev.mysql.com/doc/refman/$Tl/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Tl)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$Tl/",];if(connection()->flavor=='maria'){$El['sql']="https://mariadb.com/kb/en/";$bi['sql']=($bi['mariadb']?:str_replace(".html","/",$bi['sql']));}if(connection()->flavor=='cockroach'&&$bi['cockroach']){$El['pgsql']="https://docs.cockroachlabs.com/docs/v$Tl/";$bi['pgsql']=$bi['cockroach'];}return($bi[JUSH]?" <a href='".h($El[JUSH].$bi[JUSH].(JUSH=='mssql'?"?view=$Tl":""))."'".target_blank()." class='doc' title='".lang(130)."'>$Kk</a>":"");}function
db_size($h){if(!connection()->select_db($h))return"?";$K=0;foreach(table_status()as$T)$K+=$T["Data_length"]+$T["Index_length"];return
adminer()->formatSizeValue($K);}function
set_utf8mb4($Ob){static$P=false;if(!$P&&preg_match('~\butf8mb4~i',$Ob)){$P=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'),null,301);if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(17).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$g=adminer()->databases();if($g)redirect(ME."db=".url_escape($g[0]));}if($_POST["db"]&&!$j)queries_redirect(substr(ME,0,-1),lang(131),drop_databases($_POST["db"]));page_header(lang(132),$j,false);echo"<p class='links'>\n";foreach(['database'=>lang(133),'privileges'=>lang(76),'processlist'=>lang(134),'variables'=>lang(135),'status'=>lang(136),]as$w=>$X){if(support($w))echo"<a href='".h(ME)."$w='>$X</a>\n";}echo"<p>".lang(137,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(138,"<b>".h(logged_user())."</b>")."\n";$g=adminer()->databases();$Hd=$_GET["filter"];if($Hd!=""){foreach($g
as$w=>$h){if(stripos($h,$Hd)===false)unset($g[$w]);}}if($g){$mj=support("scheme");echo"<form method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(17).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(139)."</a>":"")."<th>".lang(140)."<th>".lang(141)."<th>".lang(142)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(143)."</a>"."<tbody>\n";$g=($_GET["dbsize"]?count_tables($g):array_flip($g));foreach($g
as$h=>$U){$dj=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($h);$s=h("Db-".$h);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$h,in_array($h,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$dj' id='$s'>".h($h)."</a>";$tb=h(db_collation($h,[]));echo"<td>".(support("database")?"<a href='$dj".($mj?"&amp;ns=":"")."&amp;database=' title='".lang(72)."'>$tb</a>":$tb),"<td align='right'><a href='$dj&amp;schema=' id='tables-".h($h)."' title='".lang(75)."'>".($_GET["dbsize"]?format_number($U):"?")."</a>","<td align='right' id='size-".h($h)."'>".($_GET["dbsize"]?db_size($h):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(144)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(145)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$ka=adminer();$ki=($ka
instanceof
Plugins?$ka->plugins:[]);$Fc=($ka
instanceof
Plugins?$ka->drivers:[]);$rc=design_checksums();if($ki||$Fc||$rc){$mb=($ka
instanceof
Plugins?$ka->checksums():[]);$dh=Plugins::officialChecksums();$_l=function($Dl){return" (<a href='$Dl'".target_blank()." class='update'>".VERSION."</a>)";};$ji=function($Dd)use($mb,$dh,$_l){return($mb[$Dd]&&$dh[$Dd]&&$mb[$Dd]!==$dh[$Dd]?$_l("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(146)."</h3>\n<ul>\n";foreach($ki
as$hi){$Qi=new
\ReflectionObject($hi);$oc=Plugins::describe($hi);echo"<li><b>".h(Plugins::pluginName($hi))."</b>".($oc!=""?": $oc":"").$ji(basename((string)$Qi->getFileName(),'.php'))."\n";}foreach($Fc
as$s=>$B)echo"<li><b>".h($s)."</b>: ".h($B).$ji(basename((string)$ka->driverFiles[$s],'.php'))."\n";if($rc){$fh=official_design_checksums();foreach($rc
as$m=>$qc){list($B,$lb)=$qc;$eh=$fh["$B/$m"];echo"<li><b>".h($m)."</b>".h($B?": $B":"").($eh&&$eh!==$lb?$_l("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Hb){$this->size+=strlen($Hb);fwrite($this->handler,$Hb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");$Ql=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Ql)).".".friendly_url($_GET["field"]));$N=[idf_escape($_GET["field"])];$J=driver()->select($a,$N,[where($_GET,$l)],$N);$L=($J?$J->fetch_row():[]);echo
driver()->value($L[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$l=fields($a);if(!$l)$j=adminer()->error();$T=table_status1($a);$B=adminer()->tableName($T);$j=$j?:h($T["Error"]);page_header(($l&&is_view($T)?$T['Engine']=='materialized view'?lang(147):lang(148):lang(149)).": ".($B!=""?$B:h($a)),$j,[],"",!$l,($l?doc_link([JUSH=>driver()->tableHelp($a,is_view($T))]):""));$cj=[];foreach($l
as$w=>$k)$cj+=$k["privileges"];adminer()->selectLinks($T,(isset($cj["insert"])||!support("table")?"":null));$yb=$T["Comment"];if($yb!="")echo"<p class='nowrap'>".lang(56).": ".adminer()->commentValue('TABLE',$yb)."\n";if($l)adminer()->tableStructurePrint($l,$T);function
tables_links(array$U){echo"<ul>\n";foreach($U
as$L){$z=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($L["ns"]),ME);echo"<li><a href='".h($z."table=".url_escape($L["table"]))."'>".($L["ns"]!=$_GET["ns"]?"<b>".h($L["ns"])."</b>.":"").h($L["table"])."</a>";}echo"</ul>\n";}$We=driver()->inheritsFrom($a);if($We){echo"<h3>".lang(150)."</h3>\n";tables_links($We);}if(support("indexes")&&driver()->supportsIndex($T)){echo"<div>\n","<h3 id='indexes'>".lang(151)."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$T);if(driver()->supportsAlterIndex($T))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(152)."</a>\n";echo"</div>\n";}if(!is_view($T)&&driver()->supportsAlterTable($T)){if(fk_support($T)){echo"<div>\n","<h3 id='foreign-keys'>".lang(119)."</h3>\n";$Qd=foreign_keys($a);$va=function_exists('Adminer\alter_table');if($Qd){echo"<table>\n","<thead><tr><th>".lang(153)."<th>".lang(154)."<th>".lang(122)."<th>".lang(121).($va?"<td class='hover'>":"")."<tbody>\n";foreach($Qd
as$B=>$n){echo"<tr".(!is_int($B)?" title='".h($B)."'":"").">","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$n["source"]))."</i>";$z=($n["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($n["db"]),ME):($n["ns"]!=""?preg_replace('~&ns=[^&]*~',"&ns=".url_escape($n["ns"]),ME):ME));echo"<td><a href='".h($z."table=".url_escape($n["table"]))."'>".($n["db"]!=""&&$n["db"]!=DB?"<b>".h($n["db"])."</b>.":"").($n["ns"]!=""&&$n["ns"]!=$_GET["ns"]?"<b>".h($n["ns"])."</b>.":"").h($n["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$n["target"]))."</i>)","<td>".h($n["on_delete"]),"<td>".h($n["on_update"]),($va?'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($B)).'">'.lang(155).'</a>':''),"\n";}echo"</table>\n";}echo($va?'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(156)."</a>\n":''),"</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(157)."</h3>\n";$ib=driver()->checkConstraints($a);if($ib){echo"<table>\n";foreach($ib
as$w=>$X)echo"<tr".($w!=$X?" title='".h($w)."'":"").">","<td><code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($w))."'>".lang(155)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(158)."</a>\n","</div>\n";}}if(support(is_view($T)?"view_trigger":"trigger")&&driver()->supportsAlterTable($T)){echo"<div>\n","<h3 id='triggers'>".lang(159)."</h3>\n";$jl=triggers($a);if($jl){echo"<table>\n";foreach($jl
as$w=>$X){echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($w)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($w))."'>".lang(155)."</a>";$ej=$X[2];if($ej){$fj=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($ej["ns"]),ME).'function='.url_escape($ej["function"]).'&name='.url_escape($ej["name"]);echo", <a href='".h($fj)."' title='".h($ej["name"])."'>".lang(160)."</a>";}echo"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(161)."</a>\n","</div>\n";}$Ij=driver()->shadowTables($a);if($Ij){echo"<h3 id='shadow-tables'>".lang(162)."</h3>\n";tables_links($Ij);}$Ve=driver()->inheritedTables($a);if($Ve){echo"<h3 id='partitions'>".lang(163)."</h3>\n";$Rh=driver()->partitionsInfo($a);if($Rh)echo"<p><code class='sql jush-".JUSH."'>BY ".h("$Rh[partition_by]($Rh[partition])")."</code>\n";tables_links($Ve);}}elseif(isset($_GET["schema"])){page_header(lang(75),"",[],h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($S,array$Pi,array&$e){if(!isset($e[$S])){$e[$S]=0;foreach((array)idx($Pi,$S)as$B=>$Ri){if($B!=$S)$e[$S]=max($e[$S],schema_column($B,$Pi,$e)+1);}}return$e[$S];}function
type_class($V){foreach(['char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',]as$w=>$X){if(preg_match("~$w|$X~",$V))return" class='$w'";}}$vk=[];$xk=[];$wk=[];$Ad=[];$ba=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ba,$Xf,PREG_SET_ORDER);foreach($Xf
as$r=>$A){$vk[$A[1]]=[(float)$A[2],(float)$A[3]];$xk[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$jj=[];$Pi=[];$Qd=[];$sa=driver()->allFields();$ye=[];$yk=[];foreach(table_status('',true)as$S=>$T){if(!is_view($T)){if(adminer()->tableName($T)!=""&&!$T["dependent"])$yk[$S]=$T;else$ye[$S]=true;}}foreach($yk
as$S=>$T){$G=0;$jj[$S]["fields"]=[];foreach($sa[$S]as$k){$G+=1.25;$Ad[$S][$k["field"]]=$G;$jj[$S]["fields"][$k["field"]]=$k;}foreach(adminer()->foreignKeys($S)as$X){if($X["db"]==""&&$X["ns"]==""&&!$ye[$X["table"]]){$Qd[$S][]=$X;$Pi[$X["table"]][$S]=[];}}}$e=[];$ee=[];$fm=[];$je=[];foreach(array_keys($jj)as$B)schema_column($B,$Pi,$e);arsort($e);foreach($e
as$B=>$d){$zg=null;foreach((array)idx($Qd,$B)as$X){if($X["table"]!=$B&&$jj[$X["table"]])$zg=($zg===null?$e[$X["table"]]:min($zg,$e[$X["table"]]));}$e[$B]=max($d,(int)$zg-1);}$_k=[];foreach($jj
as$B=>$S){$d=$e[$B];$ee[$d][]=$B;$Mk=.75*strlen($B);foreach($S["fields"]as$k)$Mk=max($Mk,.65*strlen($k["field"]));$_k[$B]=ceil($Mk)+1;$fm[$d]=max(idx($fm,$d,0),$_k[$B]);}foreach($Qd
as$B=>$Pl){foreach($Pl
as$X){$ie=$e[$B]+(idx($e,$X["table"],$e[$B])>$e[$B]?1:0);$je[$ie]=idx($je,$ie,0)+1;}}ksort($ee);$we=0;$em=0;$wb=0;$ui=null;$tk=[];foreach($ee
as$d=>$U){if($ui!==null){$wb=round($wb+$fm[$ui]+1.7+idx($je,$d,0)*.1,1);$D=[];foreach($U
as$B){$jk=0;$Nb=0;$Qg=array_keys((array)idx($Pi,$B));foreach((array)idx($Qd,$B)as$X)$Qg[]=$X["table"];foreach($Qg
as$Kg){if($jj[$Kg]&&$e[$Kg]<$d){$jk+=$jj[$Kg]["pos"][0];$Nb++;}}$D[$B]=($Nb?$jk/$Nb:$we);}asort($D);$U=array_keys($D);}$Xk=0;foreach($U
as$B){$G=1.25*count($jj[$B]["fields"]);$jj[$B]["pos"]=($vk[$B]?:[$Xk,$wb]);$tk[$B]=$jj[$B]["pos"][1];$Xk+=2.5+$G;$we=max($we,$jj[$B]["pos"][0]+2.5+$G);$em=max($em,round($jj[$B]["pos"][1]+$fm[$d],1));if(!$vk[$B])$wk[]="\n\t'".js_escape($B)."': [ ".$jj[$B]["pos"][0].", ".$jj[$B]["pos"][1]." ]";}$ui=$d;}$Hf=[];$Pa=[];foreach($Qd
as$B=>$Pl){foreach($Pl
as$X){$Fk=idx($tk,$X["table"],$tk[$B]);$Rj=$tk[$B]+$fm[$e[$B]];$bj=($Fk-1>$Rj);$Ff=($bj?$Rj+1:min($tk[$B],$Fk)-1);$Oa=idx($Pa,(string)$Ff,0);$Pa[(string)$Ff]=$Oa+1;$Ff=round($bj?min($Ff+$Oa*.1,$Fk-1):$Ff-$Oa*.1,1);while($Hf[(string)$Ff])$Ff-=.0001;$jj[$B]["references"][$X["table"]][(string)$Ff]=[$X["source"],$X["target"]];$Pi[$X["table"]][$B][(string)$Ff]=$X["target"];$Hf[(string)$Ff]=true;}}echo'<div id="schema" style="height: ',$we,'em; width: ',$em,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$xk)."\n",'};
const tablePosDefault = {',implode(",",$wk)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$we,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($jj
as$B=>$S){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$S["pos"][0]."em; left: ".$S["pos"][1]."em; width: ".$_k[$B]."em;'>",'<a href="'.h(ME).'table='.url_escape($B).'"><b>'.h($B)."</b></a>";foreach($S["fields"]as$k){$X='<span'.type_class($k["type"]).' title="'.h($k["type"].($k["length"]?"($k[length])":"").($k["null"]?" NULL":'')).'">'.h($k["field"]).'</span>';echo"<br>".($k["primary"]?"<i>$X</i>":$X);}foreach((array)$S["references"]as$Gk=>$Ri){foreach($Ri
as$Ff=>$Mi){$Gf=$Ff-$S["pos"][1];$gk=($Gf>0?"left: 100%; width: calc($Gf"."em - 100%)":"left: $Gf"."em");$em=($Gf>0?"100%":(-$Gf)."em");$r=0;foreach($Mi[0]as$Qj)echo"\n<div class='references' title='".h($Gk)."' id='refs$Ff-".($r++)."' style='$gk"."; top: ".$Ad[$B][$Qj]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $em;'></div></div>";}}foreach((array)$Pi[$B]as$Gk=>$Ri){foreach($Ri
as$Ff=>$Hk){$Gf=$Ff-$S["pos"][1];$r=0;foreach($Hk
as$Ek)echo"\n<div class='references arrow' title='".h($Gk)."' id='refd$Ff-".($r++)."' style='left: $Gf"."em; top: ".$Ad[$B][$Ek]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Gf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($jj
as$B=>$S){foreach((array)$S["references"]as$Gk=>$Ri){if($jj[$Gk]){foreach($Ri
as$Ff=>$Mi){$_g=$we;$fg=-10;foreach($Mi[0]as$w=>$Qj){$li=$S["pos"][0]+$Ad[$B][$Qj];$mi=$jj[$Gk]["pos"][0]+$Ad[$Gk][$Mi[1][$w]];$_g=min($_g,$li,$mi);$fg=max($fg,$li,$mi);}echo"<div class='references' id='refl$Ff' style='left: $Ff"."em; top: $_g"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($fg-$_g)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($ba)),'" id="schema-link">',lang(164),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$j){$i=["auto_increment"=>''];foreach(["type","routine","event","trigger"]as$lk){if(support($lk))$i[$lk."s"]='';}save_settings(array_intersect_key($_POST+$i,array_flip(["output","format","db_style","schema_style","table_style","data_style"])+$i),"adminer_export");$ra=(DB==""||$_GET["ns"]==="");$U=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$sd=dump_headers((count($U)==1?key($U):DB),($ra||count($U)>1));$mf=preg_match('~sql~',$_POST["format"]);if($mf){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$gk=$_POST["db_style"];$g=[DB];if(DB==""){$g=$_POST["databases"];if(is_string($g))$g=explode("\n",rtrim(str_replace("\r","",$g),"\n"));}foreach((array)$g
as$h){adminer()->dumpDatabase($h);if(connection()->select_db($h)){if($mf&&$gk)echo
use_sql($h,$gk).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?[""]:adminer()->schemas()))as$jj){if($jj!=""){if(DB==""&&information_schema(DB,$jj))continue;set_schema($jj);}if($mf&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$ck=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):[]);$rd=[];$Zb=[];foreach($ck
as$B=>$T){if($ra||in_array($B,(array)$_POST["tables"]))$rd[$B]=$T;if($ra||in_array($B,(array)$_POST["data"]))$Zb[$B]=$T;}if($mf){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($rd);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$kl=[];foreach($Zb
as$B=>$T){if(!is_view($T)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($rd[$B])))$kl[]=$B;}echo
truncate_all_sql($kl);}$Gh="";if($_POST["types"]){foreach(types()as$s=>$V){$kc=type_definition($s);$ah=($kc["kind"]=='d'?"DOMAIN":"TYPE");if($kc["definition"])$Gh
.=($gk!='DROP+CREATE'?"DROP $ah IF EXISTS ".table($V).";;\n":"")."CREATE $ah ".table($V)." $kc[definition];\n\n";else$Gh
.="-- Could not export type ".preg_replace('~[\r\n]+~',' ',$V)."\n\n";}}if($_POST["routines"]){foreach(routines()as$L){$B=$L["ROUTINE_NAME"];$ej=$L["ROUTINE_TYPE"];$Ue=routine($L["SPECIFIC_NAME"],$ej);if($Ue["definition"]!=""){$Ob=create_routine($ej,["name"=>$B]+$Ue);set_utf8mb4($Ob);$Gh
.=($gk!='DROP+CREATE'?"DROP $ej IF EXISTS ".table($B).";;\n":"")."$Ob;\n\n";}else$Gh
.="-- Could not export ".strtolower($ej)." ".preg_replace('~[\r\n]+~',' ',$B)."\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$L){$Ob=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($L["Name"]),3));set_utf8mb4($Ob);$Gh
.=($gk!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($L["Name"]).";;\n":"")."$Ob;;\n\n";}}echo($Gh&&JUSH=='sql'?"DELIMITER ;;\n\n$Gh"."DELIMITER ;\n\n":$Gh);}if($_POST["table_style"]||$_POST["data_style"]){$Vl=[];foreach($ck
as$B=>$T){$S=array_key_exists($B,$rd);$Xb=array_key_exists($B,$Zb);if($S||$Xb){$Uk=null;if($sd=="tar"){$Uk=new
TmpFile;ob_start([$Uk,'write'],1e5);}adminer()->dumpTable($B,($S?$_POST["table_style"]:""),(is_view($T)?2:0));if(is_view($T))$Vl[]=$B;elseif($Xb){$l=fields($B);$N=["*"];$Kb=convert_fields($l,$l);if($Kb)$N[]=substr($Kb,2);adminer()->dumpData($B,$_POST["data_style"],"",$N);}if($mf&&$_POST["triggers"]&&$S&&($jl=trigger_sql($B)))echo"\nDELIMITER ;;\n$jl\nDELIMITER ;\n";if($sd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$h/")."$B.csv",$Uk);}elseif($mf)echo"\n";}}if($mf&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($rd
as$B=>$T){if(!is_view($T))echo
foreign_keys_sql($B);}}if($mf){foreach($Vl
as$Ul)adminer()->dumpTable($Ul,$_POST["table_style"],1);}if($sd=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(81),$j,($_GET["export"]!=""?["table"=>$_GET["export"]]:[]),h(DB));echo'
<form method="post">
<table class="layout">
';$dc=['','USE','DROP+CREATE','CREATE'];$kj=(JUSH=="mssql"?['','DROP+CREATE','CREATE']:$dc);$zk=['','DROP+CREATE','CREATE'];$Yb=['','TRUNCATE+INSERT','INSERT'];if(JUSH=="sql")$Yb[]='INSERT+UPDATE';$L=get_settings("adminer_export");if(!$L)$L=["output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT"];echo"<tr><th>".lang(165)."<td>".html_radios("output",adminer()->dumpOutput(),$L["output"])."\n","<tr><th>".lang(166)."<td>".html_radios("format",adminer()->dumpFormat(),$L["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(17)."<td>".html_select('db_style',$dc,$L["db_style"]).(support("type")?checkbox("types",1,$L["types"],lang(0)):"").(support("routine")?checkbox("routines",1,$L["routines"],lang(77)):"").(support("event")?checkbox("events",1,$L["events"],lang(79)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(16)."<td>".html_select('schema_style',$kj,$L["schema_style"]):""),"<tr><th>".lang(141)."<td>".html_select('table_style',$zk,$L["table_style"]).checkbox("auto_increment",1,$L["auto_increment"],lang(57)).(support("trigger")?checkbox("triggers",1,$L["triggers"],lang(159)):""),"<tr><th>".lang(167)."<td>".html_select('data_style',$Yb,$L["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(81),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$ti=[];if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(168)."'".on('click','formCheck','^schemas\[').">".lang(16)."</label>","<tbody>\n";foreach(adminer()->schemas()as$jj){if(!information_schema(DB,$jj))echo"<tr><td>".checkbox("schemas[]",$jj,true,$jj,"","block")."\n";}}elseif(DB!=""){$jb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$jb class='jsonly' title='".lang(168)."'".on('click','formCheck','^tables\[').">".lang(149)."</label>","<th style='text-align: right;'><label class='block'>".lang(167)."<input type='checkbox' id='check-data'$jb class='jsonly' title='".lang(168)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$Vl="";$Bk=tables_list();foreach($Bk
as$B=>$V){$si=preg_replace('~_.*~','',$B);$jb=($a==""||$a==(substr($a,-1)=="%"?"$si%":$B));$xi="<tr><td>".checkbox("tables[]",$B,$jb,$B,"","block");if($V!==null&&!preg_match('~table~i',$V))$Vl
.="$xi\n";else
echo"$xi<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$jb)."</label>\n";$ti[$si]++;}echo$Vl;if($Bk)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$g=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($g?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(168)."'".on('click','formCheck','^databases\[').">":"").lang(17)."</label>","<tbody>\n";if($g){foreach($g
as$h){if(!information_schema($h)){$si=preg_replace('~_.*~','',$h);echo"<tr><td>".checkbox("databases[]",$h,$a==""||$a=="$si%",$h,"","block")."\n";$ti[$si]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Jd=true;foreach($ti
as$w=>$X){if($w!=""&&$X>1){echo($Jd?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$w%")."'>".h($w)."</a>";$Jd=false;}}}elseif(isset($_GET["sql"])){if(!$j&&$_POST["export"]){save_settings(["output"=>$_POST["output"],"format"=>$_POST["format"]],"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$j&&$_POST["val"]){$na=0;$hk=true;$bb=[];$hj=0;foreach($_POST["val"]as$M)$hj+=count($M);$Ra=$hj>1&&driver()->begin();foreach($_POST["val"]as$rk=>$M){$S=bracket_escape($rk,true);$l=fields($S);$sk=indexes($S);foreach($M
as$t=>$L){parse_str(bracket_escape($t,true),$Z);$sl=[];foreach($Z["where"]as$w=>$X)$sl[bracket_escape($w,true)]=$X;if(!$l||$Z["null"]||array_diff_key($sl,$l)||!unique_array($sl,$sk)){$hk=false;break
2;}$P=[];$N=[];foreach($L
as$uf=>$X){$w=bracket_escape($uf,true);$k=idx($l,$w);if(!$k){$hk=false;break
3;}$P[idf_escape($w)]=(preg_match('~char|text~',$k["type"])||$X!=""?adminer()->processInput($k,$X):"NULL");$N[$uf]=$w;}$Gi=where($Z,$l);if(!driver()->update($S,$P," WHERE $Gi",0," ")){$hk=false;break
2;}$na+=connection()->affected_rows;$e=[];foreach($N
as$w)$e[]=idf_escape($w);$Al=driver()->select($S,$e,[$Gi],$e);$Sg=($Al?$Al->fetch_row():[]);$pf=0;foreach($N
as$uf=>$w){$k=$l[$w];$fk=['type'=>(preg_match('~binary~',$k["type"])?'blob':$k["type"])];$bb["val[$rk][$t][$uf]"]=select_value(idx($Sg,$pf++),"",$fk,null);}}}if($Ra&&$hk)$hk=driver()->commit();queries_redirect(null,lang(169,$na),$hk);if($Ra&&!$hk)driver()->rollback();page_headers();page_messages($j);foreach($bb
as$B=>$X)echo"<div data-name='".h($B)."' hidden>$X</div>\n";exit;}restart_session();$Be=&get_session("queries");$Ae=&$Be[DB];if(!$j&&$_POST["clear"]){$Ae=[];redirect(remove_from_uri("history"));}stop_session();$la=get_settings("adminer_import");if($_POST&&$la)save_settings($la,"adminer_import");page_header((isset($_GET["import"])?lang(80):lang(69)),$j);$Pf=driver()->lineComment();$fd=0;function
query_error_line($Kk,$gh,$I,$Of){return
substr_count(substr($Kk,0,$gh),"\n")+($Of?:substr_count(substr($I,0,strspn($I," \t\r\n")),"\n")+1);}if(!$j&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$lc=driver()->delimiter;$o=false;if(!isset($_GET["import"]))$I=$_POST["query"];elseif($_POST["webfile"]){$Uj=adminer()->importServerPath();$o=@fopen((file_exists($Uj)?$Uj:"compress.zlib://$Uj.gz"),"rb");$I=($o?fread($o,1e6):false);}else$I=get_file("sql_file",true,$lc);if(is_string($I)){if(($ng=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($ng,strval(2*strlen($I)+memory_get_usage()+8e6)));if($I!=""&&strlen($I)<1e6){$H=$I.(preg_match("~$lc\\s*\$~",$I)?"":$lc);if(!$Ae||first(end($Ae))!=$H){restart_session();$Ae[]=[$H,time()];set_session("queries",$Be);stop_session();}}$Sj="(?:\\s|\xEF\xBB\xBF|/\\*[\s\S]*?\\*/|(?:$Pf)[^\n]*\n?|--\r?\n)";$gh=0;$Fi=0;$Wc=true;$Mb=false;$ug=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$xb=0;$gd=[];$Ph='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Pf.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$Zk=microtime(true);while($I!=""){if(!$gh&&preg_match("~^$Sj*+DELIMITER\\s+(\\S+)~i",$I,$A)){$lc=preg_quote($A[1]);$I=substr($I,strlen($A[0]));$Fi+=strlen($A[0]);}elseif(!$gh&&JUSH=='pgsql'&&preg_match("~^($Sj*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$I,$A)){$lc="\n\\\\\\.\r?\n";$Mb=true;$gh=strlen($A[0]);}elseif(!$gh&&JUSH=='pgsql'&&preg_match("~^$Sj*+\\\\connect[ \t][^\n]*~",$I,$A)){$lc="\n";$ug=true;$gh=strlen($A[0]);}else{preg_match("($lc\\s*|$Ph)",$I,$A,PREG_OFFSET_CAPTURE,$gh);list($Sd,$G)=$A[0];if(!$Sd&&$o&&!feof($o))$I
.=fread($o,1e5);else{if(!$Sd&&rtrim($I)=="")break;$gh=$G+strlen($Sd);if($Sd&&!preg_match("(^$lc)",$Sd)){$Ya=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($I[$G-1])=="e"));$ci=($Sd=='/*'?'\*/':($Sd=='['?']':(preg_match("~^(?:$Pf)~",$Sd)?"\n":preg_quote($Sd).($Ya?'|\\\\.':''))));while(preg_match("($ci|\$)s",$I,$A,PREG_OFFSET_CAPTURE,$gh)){$ij=$A[0][0];if(!$ij&&$o&&!feof($o))$I
.=fread($o,1e5);else{$gh=$A[0][1]+strlen($ij);if(!$ij||$ij[0]!="\\")break;}}}else{$H=substr($I,0,$G+($Mb?3:0));$I=substr($I,$gh);$Ci=$Fi;$Fi+=$gh;$gh=0;if($Mb||$ug){$lc=driver()->delimiter;$Mb=false;$ug=false;}$rb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($H)."</code>";if(preg_match("~^$Sj*+\$~",$H)&&!preg_match('~/\*M?!~',$H)){echo($_POST["only_errors"]?"":"<pre>$rb</pre>\n");continue;}$Wc=false;$xb++;$xi="<pre id='sql-$xb'>$rb</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Sj*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$H,$A)!==0){echo$xi,"<p class='error'>".lang(170,preg_match('~ATTACH~i',$A[1])?'ATTACH':'VACUUM INTO')."\n";$gd[]=" <a href='#sql-$xb'>$xb</a>";if(!$fd&&!isset($_GET["import"]))$fd=query_error_line($_POST["query"],$Ci,$H,0);if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$xi;ob_flush();flush();}$Yj=microtime(true);if(JUSH=="pgsql"&&preg_match("~^$Sj*+\\\\connect\\s+(\"(?:[^\"]|\"\")+\"|[^\\s\";]+)\\s*;*\\s*\$~",$H,$A)){$h=idf_unescape($A[1]);if(connection()->select_db($h)){if($f)$f->select_db($h);echo($_POST["only_errors"]?"":"<p class='message'>".lang(17).": ".h($h)." <span class='time'>(".format_time($Yj).")</span>\n");continue;}}elseif(connection()->multi_query($H)&&$f&&preg_match("~^$Sj*+USE\\b~i",$H))$f->query($H);do{$J=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$xi:""),"<p class='error'>".lang(171).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$gd[]=" <a href='#sql-$xb'>$xb</a>";if(!$fd&&!isset($_GET["import"]))$fd=query_error_line($_POST["query"],$Ci,$H,driver()->errorLine($H));if($_POST["error_stops"])break
2;}else{$z=ME."sql=".url_escape(trim($H));$Nk=" <span class='time'>(".format_time($Yj).")</span>".(strlen($z)<adminer()->sqlUrlLength()-100?" <a href='".h($z)."'>".lang(20)."</a>":"");$na=connection()->affected_rows;$Yl=($_POST["only_errors"]?"":driver()->warnings());$Zl="warnings-$xb";if($Yl)$Nk
.=", <a href='#$Zl' class='toggle'>".lang(52)."</a>";$pd="";$qd="explain-$xb";if(is_object($J)){$y=$_POST["limit"];$Zg=$y;$Nc=!$_POST["only_errors"];if($Nc)echo"<form method='post'>\n";$yh=print_select_result($J,$f,[],$Zg,$Nc);if(!$_POST["only_errors"]){$Zg=max($J->num_rows,$Zg);echo"<p class='sql-footer'>".($Zg?($y&&$Zg>$y?lang(172,$y):"").lang(173,$Zg):""),$Nk;if($f&&preg_match("~^($Sj|\\()*+SELECT\\b~i",$H)&&($pd=adminer()->explain($f,$H,$yh))!="")echo", <a href='#$qd' class='toggle'>Explain</a>";if($Nc)echo", <input type='submit' name='save' value='".lang(24)."' class='jsonly' disabled"." title='".lang(174)."'".on('click','sqlSave',lang(27)).">";$s="export-$xb";echo", <a href='#$s' class='toggle'>".lang(81)."</a><span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$la["output"])." ".html_select("format",adminer()->dumpFormat(),$la["format"]).input_hidden("query",$H)."<input type='submit' name='export' value='".lang(81)."'".($y?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Sj*+(CREATE|DROP|ALTER)$Sj++(DATABASE|SCHEMA)\\b~i",$H)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(175,$na)."$Nk\n";}echo($Yl?"<div id='$Zl' class='hidden'>\n$Yl</div>\n":""),($pd!=""?"<div id='$qd' class='hidden explain'>\n$pd</div>\n":"");}$Yj=microtime(true);}while(connection()->next_result());}}}}}if($Wc)echo"<p class='message'>".lang(176)."\n";else{$Oe=connection()->inTransaction();driver()->rollback();if($Oe)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(177,$xb-count($gd))," <span class='time'>(".format_time($Zk).")</span>\n";elseif($gd&&$xb>1)echo"<p class='error'>".lang(171).": ".implode("",$gd)."\n";}}else
echo"<p class='error'>".upload_error($I)."\n";}echo'
<form method="post" enctype="multipart/form-data" id="form"';$Bl="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"),adminer()->sqlUrlLength());else
echo
on_upload_progress($Bl);echo'>
';$md="<input type='submit' value='".lang(178)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$H=$_GET["sql"];if($_POST)$H=$_POST["query"];elseif($_GET["history"]=="all")$H=$Ae;elseif($_GET["history"]!="")$H=idx($Ae[$_GET["history"]],0);echo"<p>";textarea("query",$H,20,80,JUSH,$fd);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$md\n",lang(179).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$ke=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(180)."</legend><div>",($Bl?input_hidden(ini_get("session.upload_progress.name"),$Bl):""),"SQL$ke: ".file_input(" name='sql_file[]' multiple","\n$md"),($Bl?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$Me=adminer()->importServerPath();if($Me)echo"<fieldset><legend>".lang(181)."</legend><div>",lang(182,"<code>".h($Me)."$ke</code>")," <input type='submit' name='webfile' value='".lang(183)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(184))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(185))."\n",input_token();if(!isset($_GET["import"])&&$Ae){print_fieldset("history",lang(186),$_GET["history"]!="");for($X=end($Ae);$X;$X=prev($Ae)){$w=key($Ae);list($H,$Nk,$Sc)=$X;echo'<div><a href="'.h(ME."sql=&history=$w").'" class="hover">'.lang(20)."</a>"." <span class='time' title='".@date('Y-m-d',$Nk)."'>".@date("H:i:s",$Nk)."</span>"." <code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Pf).*~m",'',$H))),80,"</code>").($Sc?" <span class='time'>($Sc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(187)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(188)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$zl=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$B=>$k){if((!$zl&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$B]);}if($_POST&&!$j&&!isset($_GET["select"])){$_=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$_=($zl?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$_))$_=ME."select=".url_escape($a);$v=indexes($a);$tl=unique_array($_GET["where"],$v);$Gi="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($_,lang(189),driver()->delete($a,$Gi,$tl?0:1));else{$P=[];foreach($l
as$B=>$k){$X=process_input($k);if($X!==false&&$X!==null)$P[idf_escape($B)]=$X;}if($zl){if(!$P)redirect($_);queries_redirect($_,lang(190),driver()->update($a,$P,$Gi,$tl?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$J=driver()->insert($a,$P);$Ef=($J?last_id($J):0);queries_redirect($_,lang(191,($Ef?" $Ef":"")),$J);}}}$L=null;$I="";$Nk="";if($Z){$N=[];$uj=["*"];foreach($l
as$B=>$k){if(isset($k["privileges"]["select"])){$Ba=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$d=($Ba?"$Ba AS ":"").idf_escape($B);$N[]=$d;if($Ba)$uj[]=$d;}}$L=[];if(!support("table")){$N=["*"];$uj=$N;}if($N){$Yj=microtime(true);$J=driver()->select($a,$N,[$Z],$N,[],(isset($_GET["select"])?2:1));$I=str_replace("SELECT ".implode(", ",$N),"SELECT ".implode(", ",$uj),driver()->query);$Nk=format_time($Yj);if(!$J)$j=adminer()->error();else{$L=$J->fetch_assoc();if(!$L)$L=false;}if(isset($_GET["select"])&&(!$L||$J->fetch_assoc()))$L=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$J=driver()->select($a,["*"],[],["*"]);$L=($J?$J->fetch_assoc():false);if(!$L)$L=[driver()->primary=>""];}if($L){foreach($L
as$w=>$X){if(!$Z)$L[$w]=null;$l[$w]=["field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary)];}}}if($_POST["save"]){$ni=[];foreach((array)$_POST["fields"]as$w=>$X)$ni[bracket_escape($w,true)]=$X;$L=$ni+($L?$L:[]);}edit_form($a,$l,$L,$zl,$j,$I,$Nk);}elseif(isset($_GET["create"])){function
referencable_primary($wj){$K=[];foreach(table_status('',true)as$uk=>$S){if($uk!=$wj&&!$S["dependent"]&&fk_support($S)){foreach(fields($uk)as$k){if($k["primary"]){if($K[$uk]){unset($K[$uk]);break;}$K[$uk]=$k;}}}}return$K;}$a=$_GET["create"];$Th=driver()->partitionBy;$Wh=($Th&&$a!=""?driver()->partitionsInfo($a):[]);$Oi=referencable_primary($a);$Qd=[];foreach($Oi
as$uk=>$k)$Qd[str_replace("`","``",$uk)."`".str_replace("`","``",$k["field"])]=$uk;$Ah=[];$T=[];$Xg=false;if($a!=""){$Ah=fields($a);$T=table_status1($a);$Xg=(count($T)<2);}$va=($a==""||driver()->supportsAlterTable($T));$L=$_POST;$L["fields"]=(array)$L["fields"];if($L["auto_increment_col"])$L["fields"][$L["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$j)save_settings(["comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]]);if($_POST&&!process_fields($L["fields"])&&!$j){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(192),drop_tables([$a]));else{$l=[];$sa=[];$Fl=false;$Od=[];$_h=reset($Ah);$pa=" FIRST";foreach($L["fields"]as$k){$n=$Qd[$k["type"]];$ml=($n!==null?$Oi[$n]:$k);if($k["field"]!=""){if(!$k["generated"])$k["default"]=null;$k["default_sql"]=($k["generated"]=="DEFAULT SQL");$Ai=process_field($k,$ml);$sa[]=[$k["orig"],$Ai,$pa];if(!$_h||$Ai!==process_field($_h,$_h)){$l[]=[$k["orig"],$Ai,$pa];if($k["orig"]!=""||$pa||(JUSH=="sqlite"&&$k["comment"]!=""))$Fl=true;}if($n!==null)$Od[idf_escape($k["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(['table'=>$Qd[$k["type"]],'source'=>[$k["field"]],'target'=>[$ml["field"]],'on_delete'=>$k["on_delete"],],object_name("FOREIGN",trim($L["name"]),[$k["field"]]));$pa=" AFTER ".idf_escape($k["field"]);}elseif($k["orig"]!=""){$Fl=true;$l[]=[$k["orig"]];}if($k["orig"]!=""){$_h=next($Ah);if(!$_h)$pa="";}}$Vh=[];if(in_array($L["partition_by"],$Th)){foreach($L
as$w=>$X){if(preg_match('~^partition~',$w))$Vh[$w]=$X;}foreach($Vh["partition_names"]as$w=>$B){if($B==""){unset($Vh["partition_names"][$w]);unset($Vh["partition_values"][$w]);}}$Vh["partition_names"]=array_values($Vh["partition_names"]);$Vh["partition_values"]=array_values($Vh["partition_values"]);if($Vh==$Wh)$Vh=[];}elseif(preg_match("~partitioned~",$T["Create_options"]))$Vh=null;$pg=lang(193);if($a==""){cookie("adminer_engine",$L["Engine"]);$pg=lang(194);}$B=trim($L["name"]);$_=ME.(support("table")?"table=":"select=").url_escape($B);$yb=($L["Comment"]!=$T["Comment"]?$L["Comment"]:null);$Zc=($L["Engine"]&&$L["Engine"]!=$T["Engine"]?$L["Engine"]:"");$J=alter_table($a,$B,(JUSH=="sqlite"&&($Fl||$Od||$yb!==null||$Zc)?$sa:$l),$Od,$yb,$Zc,($L["Collation"]&&$L["Collation"]!=$T["Collation"]?$L["Collation"]:""),($L["Auto_increment"]!=""?number($L["Auto_increment"]):""),$Vh);if($J&&!Queries::$queries&&$a!=""&&!$l&&!$Od)redirect($_);queries_redirect($_,$pg,$J);}}$ak=($a!=""?"alter":"create");page_header(($a!=""?lang(50):lang(82)),$j,["table"=>$a],h($a),$Xg,doc_link(['sqlite'=>"lang_{$ak}table.html",]));if(!$_POST){$pl=driver()->types();$L=["Engine"=>$_COOKIE["adminer_engine"],"fields"=>[["field"=>"","type"=>(isset($pl["int"])?"int":(isset($pl["integer"])?"integer":"")),"on_update"=>""]],"partition_names"=>[""],];if($a!=""){$L=$T;$L["name"]=$a;$L["fields"]=[];if(!$_GET["auto_increment"])$L["Auto_increment"]="";foreach($Ah
as$k){if($k["generated"])$k["default"]=ltrim($k["default"]);$k["generated"]=$k["generated"]?:(isset($k["default"])?($k["default_sql"]?"DEFAULT SQL":"DEFAULT"):"");$L["fields"][]=$k;}if($Th){$L+=$Wh;$L["partition_names"][]="";$L["partition_values"][]="";}}}$ub=flat_collations();$ad=driver()->engines();foreach($ad
as$Zc){if(!strcasecmp($Zc,$L["Engine"])){$L["Engine"]=$Zc;break;}}$ag=max_input_vars(12,20);if($ag){$ye=(count($L["fields"])>$ag?"":" hidden");echo"<p".($ye?" id='max-fields' data-columns='$ag'":"")." class='error$ye'>".max_input_vars_error()."\n";}echo'
<form method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(195).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($L["name"])."' autocapitalize='off'>\n",(!$va?h($T["Engine"])."\n":($ad?html_select("Engine",[""=>"(".lang(196).")"]+$ad,$L["Engine"],on('change','helpClose').on_help_value())."\n":""));if($ub)echo"<datalist id='collations'>".optionlist($ub)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($L["Collation"])."' placeholder='(".lang(120).")'>\n");echo"<input type='submit' value='".lang(24)."'>\n";}if(support("columns")&&$va){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($L["fields"],$ub,"TABLE",$Qd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(57).": <input type='number' name='Auto_increment' class='size' value='".h($L["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(197),on('click','columnShowClick',6),"jsonly");$_b=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$_b,lang(56),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($_b?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$L["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(198,$a)),'>
';if($Th&&(JUSH=='sql'||$a=="")){$Uh=preg_match('~RANGE|LIST~',$L["partition_by"]);print_fieldset("partition",lang(199),$L["partition_by"]);echo"<p>".html_select("partition_by",array_merge([""],$Th),$L["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($L["partition"])."'>)\n",lang(200).": <input type='number' name='partitions' class='size".($Uh||!$L["partition_by"]?" hidden":"")."' value='".h($L["partitions"])."'>\n","<table id='partition-table'".($Uh?"":" class='hidden'").">\n","<thead><tr><th>".lang(201)."<th>".lang(202)."<tbody>\n";foreach($L["partition_names"]as$w=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($w==count($L["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($L["partition_values"],$w)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Te=["PRIMARY","UNIQUE","INDEX"];$T=table_status1($a,true);$Re=driver()->indexAlgorithms($T);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$T["Engine"]))$Te[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$T["Engine"]))$Te[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$T["Engine"]))$Te[]="VECTOR";$v=indexes($a);$l=fields($a);$vi=[];if(JUSH=="mongo"){$vi=$v["_id_"];unset($Te[0]);unset($v["_id_"]);}$L=$_POST;if($L)save_settings(["index_options"=>$L["options"]]);if($_POST&&!$j&&!$_POST["add"]&&!$_POST["drop_col"]){$ua=[];foreach($L["indexes"]as$u){$B=$u["name"];if(in_array($u["type"],$Te)){$e=[];$Lf=[];$pc=[];$nh=[];$Se=(support("partial_indexes")?$u["partial"]:"");$Qe=(in_array($u["algorithm"],$Re)?$u["algorithm"]:"");$P=[];ksort($u["columns"]);foreach($u["columns"]as$w=>$d){if($d!=""){$x=idx($u["lengths"],$w);$nc=idx($u["descs"],$w);$mh=idx($u["opclasses"],$w);$P[]=($l[$d]?idf_escape($d):$d).($x?"(".(+$x).")":"").($mh!=""?" ".idf_escape($mh):"").($nc?" DESC":"");$e[]=$d;$Lf[]=($x?:null);$pc[]=$nc;$nh[]="$mh";}}$nd=$v[$B];if($nd){ksort($nd["columns"]);ksort($nd["lengths"]);ksort($nd["descs"]);if($u["type"]==$nd["type"]&&array_values($nd["columns"])===$e&&(!$nd["lengths"]||array_values($nd["lengths"])===$Lf)&&array_values($nd["descs"])===$pc&&(!$nd["opclasses"]||array_values($nd["opclasses"])===$nh)&&$nd["partial"]==$Se&&(!$Re||$nd["algorithm"]==$Qe)&&!$nd["invalid"]){unset($v[$B]);continue;}}if($e)$ua[]=[$u["type"],$B,$P,$Qe,$Se];}}foreach($v
as$B=>$nd)$ua[]=[$nd["type"],$B,"DROP"];if(!$ua)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(203),alter_indexes($a,$ua));}page_header(lang(151),$j,["table"=>$a],h($a),false,doc_link(['sqlite'=>"lang_createindex.html",]));$Cd=array_keys($l);if($_POST["add"]){foreach($L["indexes"]as$w=>$u){if($u["columns"][count($u["columns"])]!="")$L["indexes"][$w]["columns"][]="";}$u=end($L["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$L["indexes"][]=["columns"=>[1=>""]];}if(!$L){foreach($v
as$w=>$u){$v[$w]["name"]=$w;$v[$w]["columns"][]="";}$v[]=["columns"=>[1=>""]];$L["indexes"]=$v;}$Lf=(JUSH=="sql"||JUSH=="mssql");$nh=driver()->indexOpclasses();$Jj=($_POST?$_POST["options"]:get_setting("index_options"));$Mg=[];foreach($Te
as$V)$Mg[$V]=str_replace("{table}",$a,adminer()->namePattern($V));echo'
<form method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(204);$Ke=" class='idxopts".($Jj?"":" hidden")."'";if($Re)echo"<th id='label-algorithm'$Ke>".lang(205).'';echo'<th><input type="submit" hidden>',lang(206).($Lf?"<span$Ke> (".lang(207).")</span>":"");if($Lf||support("descidx"))echo
checkbox("options",1,$Jj,lang(126),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(208);if(support("partial_indexes"))echo"<th id='label-condition'$Ke>".lang(209);echo'<td><noscript>',icon("plus","add[0]","+",lang(127)),'</noscript>
<tbody>
';if($vi){echo"<tr><td>PRIMARY<td>";foreach($vi["columns"]as$w=>$d)echo
select_input(" disabled",array_combine($Cd,$Cd),$d),"<label><input disabled type='checkbox'>".lang(64)."</label> ";echo"<td><td>\n";}$pf=1;foreach($L["indexes"]as$u){if(!$_POST["drop_col"]||$pf!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$pf][type]",[-1=>""]+$Te,$u["type"],on('change','indexesChangeType',$Mg),"label-type");if($Re)echo"<td$Ke>".html_select("indexes[$pf][algorithm]",array_merge([""],$Re),$u['algorithm'],"","label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$w=>$d){echo"<span>".select_input(" name='indexes[$pf][columns][$r]' title='".lang(54)."'".on('change','indexesChangeColumn',$Mg),($l&&($d==""||$l[$d])?array_combine($Cd,$Cd):[]),$d)," <span$Ke>",($Lf?"<input type='number' name='indexes[$pf][lengths][$r]' class='size' value='".h(idx($u["lengths"],$w))."' title='".lang(125)."'>":"");if($nh){$mh=idx($u["opclasses"],$w);echo
html_select("indexes[$pf][opclasses][$r]",[""=>"(".lang(210).")"]+array_combine($nh,$nh)+($mh!=""?[$mh=>$mh]:[]),$mh),'';}echo(support("descidx")?checkbox("indexes[$pf][descs][$r]",1,idx($u["descs"],$w),lang(64)):""),"<br>","</span></span>";$r++;}echo"<td><input name='indexes[$pf][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Ke><input name='indexes[$pf][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$pf]","x",lang(129),on('click','editingRemoveRow','indexes$1[type]')),(idx($v[$u["name"]],"invalid")?" <span class='error'>INVALID</span>":""),"\n";}$pf++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if(support("concurrent_indexes"))echo
checkbox("concurrently",1,$_POST["concurrently"],"concurrently").''."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["database"])){$L=$_POST;if($_POST&&!$j&&!$_POST["add"]){$B=trim($L["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(211),drop_databases([DB]));}elseif($B!==DB){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($B),lang(212),rename_database($B,(string)$L["collation"]));}else{$g=explode("\n",str_replace("\r","",$B));$hk=true;$Cf="";foreach($g
as$h){if(count($g)==1||$h!=""){if(!create_database($h,(string)$L["collation"]))$hk=false;$Cf=$h;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($Cf),lang(213),$hk);}}else{if(!$L["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$L["collation"])?" COLLATE $L[collation]":""),substr(ME,0,-1),lang(214));}}$ak=(DB!=""?"alter":"create");page_header(DB!=""?lang(72):lang(133),$j,[],h(DB),false,doc_link([]));$ub=collations();$B=DB;if($_POST)$B=$L["name"];elseif(DB!="")$L["collation"]=db_collation(DB,$ub);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$de){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$de,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?"<textarea autofocus name='name' rows='10' cols='40'>\n".h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n",($ub?"<datalist id='collations'>".optionlist(flat_collations())."</datalist>"."<input list='collations' name='collation' value='".h($L["collation"])."' placeholder='(".lang(120).")'>".'':"")."\n",'<input type=\'submit\' value=\'',lang(24),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(145)."'".confirm(lang(198,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(127))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$L=$_POST;if($_POST&&!$j&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$L["source"]=array_filter($L["source"],'strlen');ksort($L["source"]);$Ek=[];foreach($L["source"]as$w=>$X)$Ek[$w]=$L["target"][$w];$L["target"]=$Ek;}$Ra=false;if(JUSH=="sqlite")$J=recreate_table($a,$a,[],[],[" $B"=>($L["drop"]?"":" ".format_foreign_key($L))]);else{$Eb=$L["constraint"];$ua="ALTER TABLE ".table($a);$Gc="$ua DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B?:'');if($L["drop"])$J=queries($Gc);else{$ha=" ADD".format_foreign_key($L,$Eb);if($B=="")$J=queries($ua.$ha);elseif(JUSH=="pgsql"||(JUSH=="sql"&&$Eb!=$B))$J=queries("$Gc,$ha");else{$Ra=support("transaction_ddl")&&driver()->begin();$J=($Ra||idx(foreign_keys($a),$B)?queries($Gc):true)&&queries($ua.$ha)&&(!$Ra||driver()->commit());}}}queries_redirect(ME."table=".url_escape($a),($L["drop"]?lang(215):($B!=""?lang(216):lang(217))),$J);if($Ra)driver()->rollback();if(!$L["drop"])$j=lang(218);}$Xg=false;if(!$_POST&&$B!=""){$Qd=foreign_keys($a);$L=idx($Qd,$B,[]);$Xg=!$L;}$Lg=str_replace("{table}",$a,adminer()->namePattern("FOREIGN"));$Og="^".str_replace('\{columns\}','.*',preg_quote($Lg))."$";page_header(($B!=""?lang(219):lang(156)),$j,["table"=>$a],h($B!=""?$B:$a),$Xg,'');if($_POST){ksort($L["source"]);if($_POST["change"]||$_POST["change-js"])$L["target"]=[];else$L["source"][]="";}elseif($B!=""){$L["source"][]="";$L["constraint"]=$B;}else{$L["table"]=$a;$L["source"]=[""];$L["constraint"]=str_replace("{columns}","",$Lg);}echo'
<form method="post">
';$Qj=array_keys(fields($a));if($L["db"]!="")connection()->select_db($L["db"]);$Bh=null;if($L["ns"]!=""){$Bh=get_schema();set_schema($L["ns"]);}$Ni=array_keys(array_filter(table_status('',true),function(array$T){return!$T["dependent"]&&fk_support($T);}));$Ek=array_keys(fields(in_array($L["table"],$Ni)?$L["table"]:reset($Ni)));$b=on('change','foreignChange');echo"<p><label>".lang(220).": ".html_select("table",$Ni,$L["table"],$b)."</label>\n";if(JUSH!="sqlite"){$vj=($L["db"]!=""?$L["db"]:DB);$ec=[];foreach(adminer()->databases()as$h){if(!driver()->isSystem($h)||$h==$vj||$h==DB)$ec[]=$h;}echo"<label>".lang(85).": ".html_select("db",$ec,$vj,$b)."</label>";}if($L["db"]!="")connection()->select_db(DB);echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(221),'\'></noscript>
<table',on('change','foreignName',$Og,$Lg),'>
<thead><tr><th id="label-source">',lang(153),'<th id="label-target">',lang(154),'<tbody>
';$pf=0;foreach($L["source"]as$w=>$X){echo"<tr>","<td>".html_select("source[".(+$w)."]",[-1=>""]+$Qj,$X,($pf==count($L["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$w)."]",$Ek,idx($L["target"],$w),"","label-target");$pf++;}echo'</table>
<p>
<label>',lang(122),': ',html_select("on_delete",[-1=>""]+explode("|",driver()->onActions),$L["on_delete"]),'</label>
<label>',lang(121),': ',html_select("on_update",[-1=>""]+explode("|",driver()->onActions),$L["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",['NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'],$L["deferrable"]):''),'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(222),'\'></noscript>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(198,$B)),'>
';if(JUSH!="sqlite"){print_fieldset("options",lang(126),$L["constraint"]!=$B&&!preg_match("($Og)",$L["constraint"]));echo"<label>".lang(208).": <input name='constraint' value='".h($L["constraint"])."' autocapitalize='off'></label>\n","</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$L=$_POST;$Ch="VIEW";if(JUSH=="pgsql"&&$a!=""){$Q=table_status1($a);$Ch=strtoupper($Q["Engine"]);}if($_POST&&!$j){$B=trim($L["name"]);$Ba=" AS\n$L[select]";$_=ME."table=".url_escape($B);$pg=lang(223);$V=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$V=="VIEW"&&$Ch=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$Ba,$_,$pg);else{$Ik="adminer_".uniqid();drop_create("DROP $Ch ".table($a),"CREATE $V ".table($B).$Ba,"DROP $V ".table($B),"CREATE $V ".table($Ik).$Ba,"DROP $V ".table($Ik),($_POST["drop"]?substr(ME,0,-1):$_),lang(224),$pg,lang(225),$a,$B);}}$Xg=false;if(!$_POST&&$a!=""){$L=view($a);$Xg=!$L["select"];$L["name"]=$a;$L["materialized"]=($Ch!="VIEW");if(!$j)$j=adminer()->error();}page_header(($a!=""?lang(49):lang(83)),$j,["table"=>$a],h($a),$Xg,doc_link(['sqlite'=>"lang_createview.html",]));echo'
<form method="post">
<p>',lang(208),': <input name="name" value="',h($L["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$L["materialized"],lang(147)):""),'<p>';textarea("select",$L["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(198,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B="$_GET[name]";$L=$_POST;if($L&&!$j){$_=ME."table=".url_escape($a);$sg=lang(226);$qg=lang(227);$rg=lang(228);if(JUSH=="sqlite"&&!(min_version(3.53)&&($B==""||idx(driver()->checkConstraints($a),$B)!=$B))){$ha=($L["drop"]?"":($L["name"]!=""?"CONSTRAINT ".idf_escape($L["name"])." ":"")."CHECK ($L[clause])");queries_redirect($_,($L["drop"]?$sg:($B!=""?$qg:$rg)),recreate_table($a,$a,[],[],[],"",[],"$B",$ha));}else{$ua="ALTER TABLE ".table($a);$hb=" CHECK ($L[clause])";$Ik="adminer_".uniqid();drop_create("$ua DROP CONSTRAINT ".idf_escape($B),"$ua ADD".($L["name"]!=""?" CONSTRAINT ".idf_escape($L["name"]):"").$hb,"$ua DROP CONSTRAINT ".idf_escape($L["name"]),"$ua ADD CONSTRAINT ".idf_escape($Ik).$hb,"$ua DROP CONSTRAINT ".idf_escape($Ik),$_,$sg,$qg,$rg,$B,$L["name"]);}}$Xg=false;if(!$L){$kb=driver()->checkConstraints($a);$Xg=($B!=""&&!$kb[$B]);$L=["name"=>($B==""?object_name("CHECK",$a,[]):($kb[$B]!=$B?$B:"")),"clause"=>$kb[$B]];}page_header(($B!=""?lang(229):lang(158)),$j,["table"=>$a],h($B!=""?$B:$a),$Xg,doc_link(['sqlite'=>"lang_createtable.html#check_constraints",]));echo'
<form method="post">
<p><label>',lang(208),': <input name="name" value="',h($L["name"]),'" data-maxlength="64" autocapitalize="off"></label>
<p>';textarea("clause",$L["clause"]);echo'<p><input type=\'submit\' value=\'',lang(24),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(198,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$il=trigger_options();$L=trigger($B,$a);$Xg=($B!=""&&!$L);$Lg=str_replace("{table}",$a,adminer()->namePattern("TRIGGER"));$L+=["Trigger"=>strtr($Lg,["{timing}"=>"b","{event}"=>"i","{columns}"=>"","{type}"=>"row"])];if($_POST){if(!$j&&in_array($_POST["Timing"],$il["Timing"])&&in_array($_POST["Event"],$il["Event"])&&in_array($_POST["Type"],$il["Type"])){$jh=" ON ".table($a);$Gc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$jh:"");$_=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Gc,$_,lang(230));else{if($B!="")queries($Gc);queries_redirect($_,($B!=""?lang(231):lang(232)),queries(create_trigger($jh,$_POST)));if($B!="")queries(create_trigger($jh,$L+["Type"=>reset($il["Type"])]));}}$L=$_POST;}page_header(($B!=""?lang(233):lang(161)),$j,["table"=>$a],h($B!=""?$B:$a),$Xg,doc_link(['sqlite'=>"lang_createtrigger.html",]));$Og=strtr(preg_quote($Lg),['\{timing\}'=>'[abi]','\{event\}'=>'[iud]*','\{columns\}'=>'.*','\{type\}'=>'(row|statement)']);$gl=on('change','triggerChange',"^$Og$",$Lg);$ch=on('input','triggerChange',"^$Og$",$Lg);echo'
<form method="post" id="form">
<table class="layout">
<tr><th>',lang(234),'<td>',html_select("Timing",$il["Timing"],$L["Timing"],$gl),'<tr><th>',lang(235),'<td>',html_select("Event",$il["Event"],$L["Event"],$gl),(in_array("UPDATE OF",$il["Event"])?" <input name='Of' value='".h($L["Of"])."' class='hidden'$ch>":""),'<tr><th>',lang(55),'<td>',html_select("Type",$il["Type"],$L["Type"],$gl),'<tr><th>',lang(208),'<td><input name="Trigger" value="',h($L["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$L["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(198,$B)),'>
';echo
input_token(),'</form>
';}elseif($_GET["select"]!=""){$a=$_GET["select"];$T=table_status1($a);$v=indexes($a);$l=fields($a);$Qd=column_foreign_keys($a);$hh=$T["Oid"];$cj=[];$e=[];$pj=[];$vh=[];$Lk=null;foreach($l
as$w=>$k){$B=adminer()->fieldName($k);$Ng=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$B!=""){$e[$w]=$Ng;if(is_shortable($k))$Lk=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$B!="")$pj[$w]=$Ng;if(isset($k["privileges"]["order"])&&$B!="")$vh[$w]=$Ng;$cj+=$k["privileges"];}list($N,$q)=adminer()->selectColumnsProcess($e,$v);$N=array_unique($N);$q=array_unique($q);$kf=count($q)<count($N);$Z=adminer()->selectSearchProcess($l,$v,$T);$D=adminer()->selectOrderProcess($l,$v);$y=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$ul=>$L){$Ba=convert_field($l[key($L)]);$N=[$Ba?:idf_escape(key($L))];$Z[]=where_check(bracket_escape($ul,true),$l);$K=driver()->select($a,$N,$Z,$N);if($K)echo
first($K->fetch_row());}exit;}$vi=$wl=[];foreach($v
as$u){if($u["type"]=="PRIMARY"){$vi=array_flip($u["columns"]);$wl=($N?$vi:[]);foreach($wl
as$w=>$X){if(in_array(idf_escape($w),$N))unset($wl[$w]);}break;}}if($hh&&!$vi){$vi=$wl=[$hh=>0];$v[]=["type"=>"PRIMARY","columns"=>[$hh]];}if($_POST&&!$j){$bm=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$kb=[];foreach($_POST["check"]as$hb)$kb[]=where_check($hb,$l);$bm[]="((".implode(") OR (",$kb)."))";}$dm=$bm;$bm=($bm?"\nWHERE ".implode(" AND ",$bm):"");if($_POST["export"]){save_settings(["output"=>$_POST["output"],"format"=>$_POST["format"]],"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$tj=($N?:["*"]);$Kb=convert_fields($e,$l,$N);if($Kb)$tj[]=substr($Kb,2);$I="";if(is_array($_POST["check"])&&!$vi){$Wd=implode(", ",$tj)."\nFROM ".table($a);$ge=($q&&$kf?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):"");$rl=[];foreach($_POST["check"]as$X)$rl[]="(SELECT".limit($Wd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l).$ge,1).")";$I=implode(" UNION ALL ",$rl);}adminer()->dumpData($a,"table",$I,$tj,$dm,($kf?$q:[]),$D);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Qd)){if($_POST["save"]||$_POST["delete"]){$J=true;$na=0;$Ra=false;$P=[];if(!$_POST["delete"]){foreach($l
as$B=>$X){$t=bracket_escape($B);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$X=process_input($l[$B]);if($X!==null&&($_POST["clone"]||$X!==false))$P[idf_escape($B)]=($X!==false?$X:idf_escape($B));}}}if($_POST["delete"]||$P){$I=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($P)).")\nSELECT ".implode(", ",$P)."\nFROM ".table($a):"");if($_POST["all"]||($vi&&is_array($_POST["check"]))||$kf){$J=($_POST["delete"]?driver()->delete($a,$bm):($_POST["clone"]?queries("INSERT $I$bm".driver()->insertReturning($a)):driver()->update($a,$P,$bm)));$na=connection()->affected_rows;if(is_object($J))$na+=$J->num_rows;}else{$Ra=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$am="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l);$J=($_POST["delete"]?driver()->delete($a,$am,1):($_POST["clone"]?queries("INSERT".limit1($a,$I,$am)):driver()->update($a,$P,$am,1)));if(!$J)break;$na+=connection()->affected_rows;}if($Ra&&$J&&!driver()->commit())$J=false;}}$pg=lang(169,$na);if($_POST["clone"]&&$J&&$na==1){$Ef=last_id($J);if($Ef)$pg=lang(191," $Ef");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$pg,$J);if($Ra)driver()->rollback();if(!$_POST["delete"]){$ni=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$ni),$ni,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$J=true;$na=0;$Ra=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$ul=>$L){$P=[];foreach($L
as$w=>$X){$w=bracket_escape($w,true);$P[idf_escape($w)]=(preg_match('~char|text~',$l[$w]["type"])||$X!=""?adminer()->processInput($l[$w],$X):"NULL");}$J=driver()->update($a,$P," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($ul,true),$l),($kf||$vi?0:1)," ");if(!$J)break;$na+=connection()->affected_rows;}if($Ra)$J=$J&&driver()->commit();queries_redirect(remove_from_uri(),lang(169,$na),$J);if($Ra)driver()->rollback();}else{save_settings(["format"=>$_POST["separator"]],"adminer_import");$Dd=get_file("csv_file",true);if(!is_string($Dd))$j=upload_error($Dd);elseif(!preg_match('~~u',$Dd))$j=lang(236);else{$vb=array_keys($l);$yj=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Tb=parse_csv($Dd,$yj);$na=count($Tb);driver()->begin();$M=[];foreach($Tb
as$w=>$Ql){if(!$w&&!array_diff($Ql,$vb)){$vb=$Ql;$na--;}else{$P=[];foreach($Ql
as$r=>$c)$P[idf_escape($vb[$r])]=($c==""&&$l[$vb[$r]]["null"]?"NULL":q(csv_value($c)));$M[]=$P;}}$J=(!$M||driver()->insertUpdate($a,$M,$vi));if($J)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(237,$na),$J);driver()->rollback();}}}}$uk=adminer()->tableName($T);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(59).": $uk",$j,[],"",(!$l&&support("table")),($l?doc_link([JUSH=>driver()->tableHelp($a,is_view($T))]):""));$P=null;if(isset($cj["insert"])||!support("table")){$P="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$A)?$A[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$P
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($T,$P);if(!$e&&support("table"))echo"<p class='error'>".lang(238)."\n";else{echo"<form id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($N,$e);adminer()->selectSearchPrint($Z,$pj,$v,$T);adminer()->selectOrderPrint($D,$vh,$v);adminer()->selectLimitPrint($y);if($Lk!==null)adminer()->selectLengthPrint($Lk);adminer()->selectActionPrint($v);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],["","same-origin"])){echo"<p class='error'>".lang(115).' '.lang(116)."\n";page_footer();exit;}}$E=$_GET["page"];$Td=null;if($E=="last"){$Td=get_val(count_rows($a,$Z,$kf,$q));$E=floor(max(0,intval($Td)-1)/$y);}$sj=$N;$fe=$q;if(!$sj){$sj[]="*";$Kb=convert_fields($e,$l,$N);if($Kb)$sj[]=substr($Kb,2);}foreach($N
as$w=>$X){$k=$l[idf_unescape($X)];if($k&&($Ba=convert_field($k)))$sj[$w]="$Ba AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$w=>$X){if(isset($sj[$w])&&$X["fun"])$sj[$w].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$kf&&$wl){foreach($wl
as$w=>$X){$sj[]=idf_escape($w);if($fe)$fe[]=idf_escape($w);}}$J=driver()->select($a,$sj,$Z,$fe,$D,$y,$E,true);if(!is_object($J))echo"<p class='error'>".(adminer()->error()?:lang(32))."\n";else{if(JUSH=="mssql"&&$E)$J->seek($y*$E);$Vc=[];$M=[];while($L=$J->fetch_assoc()){if($E&&JUSH=="oracle")unset($L["RNUM"]);$M[]=$L;}$qe=($y&&(support("cursor")?$_GET["next"]!="":count($M)>=$y));if(is_ajax()&&$qe)header("X-Next-Page: ".pagination_href($E+1));if($_GET["modify"]&&$M){$gg=max_input_vars(count($M[0])+1,20);echo($gg&&count($M)>$gg?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form method='post' enctype='multipart/form-data'".on_upload_progress($Bl).">\n";if($_GET["page"]!="last"&&$y&&$q&&$kf&&JUSH=="sql")$Td=get_val(" SELECT FOUND_ROWS()");if(!$M)echo"<p class='message'>".lang(22)."\n";else{$Na=adminer()->backwardKeys($a,$uk);$aj=[];reset($N);foreach($M[0]as$w=>$X){if(!isset($wl[$w])){$X=idx($_GET["columns"],key($N))?:[];$aj[$w]=["fun"=>$X["fun"],"col"=>($N?$X["col"]:$w)];next($N);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$q&&$N?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(239)."'".on('click','formCheck','^check').">");$Pg=[];$Ki=1;foreach($aj
as$w=>$X){$k=$l[$X["col"]];$B=($k?adminer()->fieldName($k,$Ki):($X["fun"]?($X["col"]!=""?h($X["col"]):"*"):h($w)));if($B!=""){$Ki++;$Pg[$w]=$B;$d=idf_escape($w);$Ee=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($w);$nc="&desc[0]=1";$Nj=preg_replace('~ DESC( NULLS LAST)?$~','',$D[0]);$Pj=($Nj==$d||$Nj==$w);echo"<th id='th[".h(bracket_escape($w))."]'".($Pj?" aria-sort='".($Nj==$D[0]?"ascending":"descending")."'":"").">";$ae=apply_sql_function(h($X["fun"]),$B);$Oj=isset($k["privileges"]["order"])||$X["fun"];echo($Oj?"<a href='".h($Ee.($Pj&&$Nj==$D[0]?$nc:''))."'>$ae</a>":$ae),doc_link([JUSH=>$k["doc"]]);$og=($Oj?"<a href='".h($Ee.$nc)."' title='".lang(64)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($k["privileges"]["where"]))$og
.="<a href='#fieldset-search' title='".lang(18)."' class='text jsonly'".on('click','selectSearch',$w)."> =</a>";echo($og?"<span class='column'>$og</span>":"");}}$Lf=[];if($_GET["modify"]){foreach($M
as$L){foreach($L
as$w=>$X)$Lf[$w]=max($Lf[$w],min(40,utf8_length((string)$X)));}}$_e=[];$ze=[];foreach((array)$_GET["where"]as$X){$X+=["col"=>"","op"=>"","val"=>""];$c=$X["col"];$oj=$X["val"];if(!is_array($oj)&&($oj!=""||preg_match('~NULL$~',$X["op"]))&&(!$X["op"]||in_array($X["op"],adminer()->operators($T)))){$Nf=strtr(preg_quote($oj),["%"=>".*?","_"=>"."]);$di=["LIKE %%"=>$Nf,"ILIKE %%"=>$Nf,"REGEXP"=>$oj]+(JUSH=="pgsql"?["~"=>$oj,"~*"=>$oj]:[])+($c!=""?[]:["="=>'^'.preg_quote($oj).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$oj)))).')\z',"LIKE"=>"^$Nf\\z","ILIKE"=>"^$Nf\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($oj).'(?=,|\z)',]);foreach(($c!=""?[$c=>$l[$c]]:$l)as$B=>$k){if($c!=""||is_searchable($k,$X)){$lh=$X["op"]?:(!preg_match('~'.text_type().'~',$k["type"])?"IN":(preg_match('~%~',$oj)?"LIKE":"LIKE %%"));if(isset($di[$lh])){$nb=preg_match('~^ILIKE|\*$~',$lh)||($lh!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$_e[$B][]="(?".($nb?"i":"").":$di[$lh])";}elseif($lh=="IS NULL"&&$c=="")$ze[$B]=true;}}}}echo($Na?"<th>".lang(240):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($M,$Qd)as$Jg=>$L){$tl=unique_array($M[$Jg],$v);if(!$tl){$tl=[];foreach($M[$Jg]as$w=>$X){if(!in_array(idx(idx($aj,$w,[]),"fun"),driver()->grouping))$tl[$w]=$X;}}$ul="";$r=0;foreach($tl
as$w=>$X){$Zi=idx($aj,$w,[]);if($N&&$Zi&&!isset($l[$Zi["col"]]))continue;$ae=idx($Zi,"fun","");$c=($ae?$Zi["col"]:$w);$k=(array)$l[$c];$jf=is_blob($k);if(!$ae&&strlen($X)>64&&driver()->md5(idf_escape($c),$k)){$ae="md5";$X=md5($jf?(string)driver()->value($X,$k):$X);}if($ae){$ul
.="&fun[$r]=".url_escape($ae)."&col[$r]=".url_escape($c).($X!==null?"&val[$r]=".url_escape($X===false?"f":$X):"");$r++;}else$ul
.="&".($X!==null?"where[".url_escape(bracket_escape($c))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($c));}echo"<tr>".(!$q&&$N?"":"<td class='hover check sticky'>".($kf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$ul)."' class='edit'>".lang(241)."</a> ").checkbox("check[]",substr($ul,1),in_array(substr($ul,1),(array)$_POST["check"])));foreach($L
as$w=>$X){if(isset($Pg[$w])){$ae=$aj[$w]["fun"];$c=$aj[$w]["col"];$k=(array)$l[$w];if($X!=""&&(!isset($Vc[$w])||$Vc[$w]!=""))$Vc[$w]=(is_mail($X)?$Pg[$w]:"");$z="";if(is_blob($k)&&$X!="")$z=ME.'download='.url_escape($a).'&field='.url_escape($w).$ul;if(!$z&&$X!==null){foreach((array)$Qd[$w]as$n){if(count($Qd[$w])==1||end($n["source"])==$w){$z="";foreach($n["source"]as$r=>$Qj)$z
.=where_link($r,$n["target"][$r],$M[$Jg][$Qj]);$z=($n["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($n["db"]),ME):ME).'select='.url_escape($n["table"]).$z;if($n["ns"]!="")$z=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($n["ns"]),$z);if(count($n["source"])==1)break;}}}if($ae=="count"&&$c==""){$z=ME."select=".url_escape($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$tl))$z
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($tl
as$tf=>$W){if(idx(idx($aj,$tf,[]),"fun")){$z="";break;}$z
.=where_link($r++,$tf,$W);}}$Fe=select_value($X,$z,$k,$Lk,($ae?[]:idx($_e,$w,[])));if($X===null&&!$ae&&isset($ze[$w]))$Fe="<mark>$Fe</mark>";$t=bracket_escape($ul);$s=h("val[$t][".bracket_escape($w)."]");$pi=idx(idx($_POST["val"],$t),bracket_escape($w));$zl=idx($k["privileges"],"update")&&!is_identity_always($k);$Qc=$M[$Jg][$w];$Rc=!is_array($L[$w])&&!is_blob($k)&&is_utf8($Qc)&&!$ae&&!$k["generated"]&&$zl;$V=($ae=="min"||$ae=="max"?$l[$c]["type"]:$k["type"]);$Kk=preg_match('~text|json|lob~',$V);$lf=preg_match(number_type(),$V)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$ae);echo"<td id='$s'".($lf&&($X===null||is_numeric(strip_tags($Fe))||$V=="money")?" class='number'":"");if(($_GET["modify"]&&$Rc&&$Qc!==null)||$pi!==null){$le=h($pi!==null?$pi:$Qc);echo">".($Kk?"<textarea name='$s' cols='30' rows='".(substr_count($Qc,"\n")+1)."'>\n$le</textarea>":"<input name='$s' value='$le' size='$Lf[$w]'>");}else{$Vf=strpos($Fe,"<i>…</i>");$Pc=adminer()->editVal($Qc,$k);echo($zl?" data-text='".($Vf?2:($Kk?1:0))."'".($Rc?"":" data-warning='".lang(242)."'").($Rc&&!$Vf&&html_entity_decode(strip_tags($Fe),ENT_QUOTES,"UTF-8")!==$Pc?" data-value='".h($Pc)."'":""):"").">$Fe";}}}if($Na)echo"<td>";adminer()->backwardKeysPrint($Na,$M[$Jg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$ma=get_settings("adminer_import");if($M||$E||$qe){$ld=true;if($_GET["page"]!="last"){if(!$y||(count($M)<$y&&($M||!$E)))$Td=($E?$E*$y:0)+count($M);elseif(JUSH!="sql"||!$kf){$Td=($kf?null:found_rows($T,$Z));$ld=!driver()->hasEstimatedRows();if($Td===null||(!$ld&&$Td<max(1e4,2*($E+1)*$y))){$Td=first(slow_query(count_rows($a,$Z,$kf,$q)));$ld=true;}}}if(!support("cursor"))$qe=(($Td===false?count($M)+1:$Td-$E*$y)>$y);$Kh=($y&&($qe||$E));if($Kh)echo($qe?'<p><a href="'.h(pagination_href($E+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(243)).'>'.lang(244).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Kh){$eg=($Td===false?$E+($M?(count($M)>=$y?2:1):0):floor(($Td-1)/$y));echo"<fieldset><legend>".lang(245)."</legend>";if(!support("cursor")){echo
pagination(0,$E).($E>5?" …":"");for($r=max(1,$E-4);$r<min($eg,$E+5);$r++)echo
pagination($r,$E);if($eg>0)echo($E+5<$eg?" …":""),($ld&&$Td!==false?pagination($eg,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$eg'>".lang(246)."</a>");}else
echo
pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($qe?pagination($E+1,$E)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(247)."</legend>";$vc=($ld?"":"~ ").$Td;$xf=($Td!==false?($ld?"":"~ ").lang(173,$Td):"");echo
checkbox("all",1,0,$xf,on('click','countRows',$vc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(174)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(248),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(24),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(144),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(20),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(249),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(28),'\'',confirm(),'>
</div></fieldset>
';$Rd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]||($d["col"]!=""&&!isset($l[$d["col"]]))){unset($Rd['sql']);break;}}if($Rd){print_fieldset("export",lang(81)." <span id='selected2'></span>");$Hh=adminer()->dumpOutput();echo($Hh?html_select("output",$Hh,$ma["output"])." ":""),html_select("format",$Rd,$ma["format"])," <input type='submit' name='export' value='".lang(81)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Vc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(80)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($Bl?input_hidden(ini_get("session.upload_progress.name"),$Bl):""),file_input(" name='csv_file'"," ".html_select("separator",["csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"],$ma["format"])." <input type='submit' name='import' value='".lang(80)."'>".($Bl?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$q&&$N?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$Q=isset($_GET["status"]);page_header($Q?lang(136):lang(135));$Rl=($Q?adminer()->showStatus():adminer()->showVariables());if(!$Rl)echo"<p class='message'>".lang(22)."\n";else{echo"<table>\n";foreach($Rl
as$L){echo"<tr>";$w=array_shift($L);echo"<th><code class='jush-".JUSH.($Q?"status":"set")."'>".h($w)."</code>";foreach($L
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$kk=["Data_length"=>0,"Index_length"=>0,"Data_free"=>0];foreach(table_status()as$B=>$T){json_row("Comment-$B",h($T["Comment"]).($T["Error"]?" <span class='error'>".h($T["Error"])."</span>":""));if(!is_view($T)||preg_match('~materialized~i',$T["Engine"])){foreach(["Engine","Collation"]as$w)json_row("$w-$B",h($T[$w]));foreach(array_keys($kk+["Auto_increment"=>0,"Rows"=>0])as$w){if(array_key_exists($w,$T))json_row("$w-$B",format_status($T,$w));if($T[$w]!=""&&isset($kk[$w]))$kk[$w]+=($T["Engine"]!="InnoDB"||$w!="Data_free"?$T[$w]:0);}}}if(function_exists('Adminer\db_status'))$kk=db_status();foreach($kk
as$w=>$X)json_row("sum-$w",adminer()->formatSizeValue($X));json_row("");}else{foreach(count_tables(adminer()->databases(false))as$h=>$X){json_row("tables-$h",format_number($X));json_row("size-$h",db_size($h));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$U=tables_list();if($U)redirect(ME.(support("table")?"table=":"select=").url_escape(key($U)));}$lg=ME.(isset($_GET["select"])?"select=&":"");$Ck=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Ck&&!$j&&!$_POST["search"]){$J=true;$pg="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$J=truncate_tables($_POST["tables"]);$pg=lang(250);}elseif($_POST["move"]){$J=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$pg=lang(251);}elseif($_POST["copy"]){$J=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$pg=lang(252);}elseif($_POST["drop"]){if($_POST["views"])$J=drop_views($_POST["views"]);if($J&&$_POST["tables"])$J=drop_tables($_POST["tables"]);$pg=lang(253);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$S){foreach(get_rows("PRAGMA integrity_check(".q($S).")")as$L)$pg
.="<b>".h($S)."</b>: ".h($L["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$S){foreach(get_rows("DBCC CHECKTABLE (".q(table($S)).") WITH TABLERESULTS")as$L)$pg
.="<b>".h($S)."</b>: ".h($L["MessageText"])."<br>";}}elseif(JUSH!="sql"){$J=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$pg=lang(254);}elseif(!$_POST["tables"])$pg=lang(19);elseif($J=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($L=$J->fetch_assoc())$pg
.="<b>".h($L["Table"])."</b>: ".h($L["Msg_text"])."<br>";}queries_redirect(relative_uri(),$pg,$J);}page_header(($_GET["ns"]==""?lang(17).": ".h(DB):lang(16).": ".h($_GET["ns"])),$j,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$D=$_GET["order"];$Yd=($D||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(255)."</h3>\n";$Bk=($Yd?table_status():tables_list());$Hd=$_GET["filter"];if($Hd!=""){foreach($Bk
as$S=>$Q){if(stripos("$S",$Hd)===false)unset($Bk[$S]);}}if(!$Bk)echo"<p class='message'>".lang(19)."\n";else{echo"<form method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(15)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"));$I=($_POST?$_POST["query"]:$_GET["query"]);echo" <input type='search' name='query' value='".h($I!=""?$I:$Hd)."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(18)."'>\n","</div></fieldset>\n";if(!$j&&(!$_POST||$_POST["search"])&&$I!=""){$_GET["where"][0]["op"]=idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%");search_tables($I);}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(168).'"'.on('click','formCheck','^(tables|views)\[').'>','<th class="sticky"'.(!$D&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($lg,0,-1)).'">'.lang(149).'</a>';$e=["Engine"=>[lang(256).'']];$ub=collations();if($ub)$e["Collation"]=[lang(140).''];if(function_exists('Adminer\alter_table'))$e["Data_length"]=[lang(257).'',"create",lang(50),];if(support("indexes"))$e["Index_length"]=[lang(258).'',"indexes",lang(152),];$e["Data_free"]=[lang(259).'',"edit",lang(51)];if(function_exists('Adminer\alter_table'))$e["Auto_increment"]=[lang(57).'',"auto_increment=1&create",lang(50),];$e["Rows"]=[lang(260).'',"select",lang(47),];if(support("comment"))$e["Comment"]=[lang(56).'',];$Ca=['Engine','Collation','Comment'];foreach($e
as$w=>$d)echo"<th".($D==$w?" aria-sort='".(in_array($w,$Ca)?"ascending":"descending")."'":"")."><a href='".h($lg)."order=$w'>$d[0]</a>";echo"<tbody>\n";if($D){uasort($Bk,function($da,$Ka)use($D,$Ca){$K=($da[$D]<$Ka[$D]?-1:($da[$D]>$Ka[$D]?1:0));return(in_array($D,$Ca)?$K:-$K);});}$U=0;$kk=["Data_length"=>0,"Index_length"=>0,"Data_free"=>0];foreach($Bk
as$B=>$Q){$Ul=($Yd?is_view($Q):$Q!==null&&!preg_match('~table|sequence~i',$Q));$Q=($Yd?$Q:['Engine'=>$Q]);$s=h("Table-".$B);echo'<tr><td class="hover">'.checkbox(($Ul?"views[]":"tables[]"),$B,in_array("$B",$Ck,true),"","","",$s),'<th class="sticky">'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($B)."' title='".lang(48)."' id='$s'>".h($B).'</a>':h($B));if($Ul&&!preg_match('~materialized~i',$Q['Engine'])){$Qk=lang(148);echo'<td colspan="'.(count($e)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($B)."' title='".lang(49)."'>$Qk</a>":$Qk),"<td align='right'><a href='".h(ME)."select=".url_escape($B)."' title='".lang(47)."'>?</a>";if(support("comment"))echo'<td>'.h($Q['Comment']);}else{if($Yd){foreach(array_keys($kk)as$w)$kk[$w]+=($Q["Engine"]!="InnoDB"||$w!="Data_free"?idx($Q,$w):0);}foreach($e
as$w=>$d){$s=" id='$w-".h($B)."'";echo($d[1]?"<td align='right'>".($w=="Index_length"&&$Q["invalid_index"]?"<span class='error'>INVALID</span> ":"")."<a href='".h(ME."$d[1]=").url_escape($B)."'$s title='$d[2]'>".format_status($Q,$w)."</a>":"<td$s>".h(idx($Q,$w,'?')).($w=="Comment"&&$Q["Error"]?" <span class='error'>".h($Q["Error"])."</span>":""));}$U++;}echo"\n";}echo"<tr><td class='hover'><th class='sticky'>".lang(261,count($Bk)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),($ub?"<td>".h(db_collation(DB,$ub)):'');if($Yd&&function_exists('Adminer\db_status'))$kk=db_status();foreach($kk
as$w=>$jk)echo($e[$w]?"<td align='right' id='sum-$w'>".($Yd?adminer()->formatSizeValue($jk):""):"");echo"\n","</table>\n",($Yd?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$Nl="<input type='submit' value='".lang(262)."'".on_help("VACUUM")."> ";$qh="<input type='submit' name='optimize' value='".lang(263)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$xi=(JUSH=="sqlite"?$Nl."<input type='submit' name='check' value='".lang(264)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$Nl.$qh:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(264)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(265)."'".on_help("ANALYZE TABLE")."> ".$qh."<input type='submit' name='check' value='".lang(264)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(266)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(267)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(145)."'".confirm().on_help("DROP TABLE").">":"");echo($xi?"<div class='footer'><div>\n<fieldset><legend>".lang(144)." <span id='selected'></span></legend><div>$xi\n</div></fieldset>\n":"");$g=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($g)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(268)." <span id='selected3'></span></legend><div>";$h=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($g?html_select("target",$g,$h):'<input name="target" value="'.h($h).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(128)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(29)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(269)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$U).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(82)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(83)."</a>\n":""),"</div>\n";}}}page_footer();