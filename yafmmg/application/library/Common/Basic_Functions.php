<?php
/**
 * File: Basic_Functions.php
 * 公共的一些常用方法
 */

/**
 * 获取字符串的长度
 *
 * 计算时, 汉字或全角字符占1个长度, 英文字符占0.5个长度
 *
 * @param string  $str
 * @param boolean $filter 是否过滤html标签
 * @return int 字符串的长度
 */
function get_str_length($str, $filter = false)
{
    if ($filter) {
        $str = html_entity_decode($str, ENT_QUOTES);
        $str = strip_tags($str);
    }
    return (strlen($str) + mb_strlen($str, 'UTF8')) / 4;
}

//对字符串等进行过滤
function filterStr($arr)
{
    if (!isset($arr)) {
        return null;
    }

    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            if(is_array($v)){
                filterStr($v);
            }else{
               $arr[$k] = addslashes(stripSQLChars(stripHTML(trim($v), true))); 
            }
            
        }
    } else {
        $arr = addslashes(stripSQLChars(stripHTML(trim($arr), true)));
    }

    return $arr;
}

function stripHTML($content, $xss = true)
{
    $search = array(
        "@<script(.*?)</script>@is",
        "@<iframe(.*?)</iframe>@is",
        "@<style(.*?)</style>@is",
        "@<(.*?)>@is"
    );

    $content = preg_replace($search, '', $content);

    if ($xss) {
        $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link',
            'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer',
            'layer', 'bgsound', 'title', 'base');

        $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $content = str_ireplace($ra, '', $content);
    }

    return strip_tags($content);
}

function removeXSS($val)
{
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <javaΘscript>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // &#x0040 @ search for the hex values
        $val = preg_replace('/(&#[x|X]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link',
        'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer',
        'layer', 'bgsound', 'title', 'base');

    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                    $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                    $pattern .= ')?';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }

    return $val;
}

/**
 *  Strip specail SQL chars
 */
function stripSQLChars($str)
{
    $replace = array('SELECT', 'INSERT', 'DELETE', 'UPDATE', 'CREATE', 'DROP', 'VERSION', 'DATABASES',
        'TRUNCATE', 'HEX', 'UNHEX', 'CAST', 'DECLARE', 'EXEC', 'SHOW', 'CONCAT', 'TABLES', 'CHAR', 'FILE',
        'SCHEMA', 'DESCRIBE', 'UNION', 'JOIN', 'ALTER', 'RENAME', 'LOAD', 'FROM', 'SOURCE', 'INTO', 'LIKE', 'PING', 'PASSWD');

    return str_ireplace($replace, '', $str);
}


/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type=0) {
    if ($type) {
        return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

// Redirect directly
function redirect($URL = '', $second = 0)
{
    if (!isset($URL)) {
        $URL = $_SERVER['HTTP_REFERER'];
    }
    ob_start();
    ob_end_clean();
    header("Location: " . $URL, TRUE, 302); //header("refresh:$second; url=$URL", TRUE, 302);
    ob_flush(); //可省略
    exit;
}


// Redirect and display message
function gotoURL($message = '', $URL = '')
{
    if (!isset($URL)) {
        $URL = $_SERVER['HTTP_REFERER'];
    }

    if (isset($message)) {
        jsAlert($message);
    }

    echo "<script type='text/javascript'>window.location.href='$URL'</script>";
}


function generatePageLink4($page, $totalPages, $URL, $counts, $query = '')
{
    $URL .= (strpos($URL, '?') === false ? '?' : '&');
    // First:
    $first = '首 页';
    $first = "<a href=" . $URL . "page=1$query>$first</a>";

    // Prev:
    $prev = '上一页';
    $previousPage = ($page > 1) ? $page - 1 : 1;
    $prev = "<a href=" . $URL . "page=$previousPage$query>$prev</a>";

    // Next:
    $next = '下一页';
    $nextPage = ($page == $totalPages) ? $totalPages : $page + 1;
    $next = "<a href=" . $URL . "page=$nextPage$query>$next</a>";

    // Last
    $last = '末 页';
    $last = "<a href=" . $URL . "page=$totalPages$query>$last</a>";

    $pageLink = $first . '&nbsp;&nbsp;' . $prev;
    $pageLink .= '&nbsp;&nbsp;' . $next . '&nbsp;&nbsp;' . $last;

    return $pageLink;
}

/*
 *Functionality: Generate Single-language[Chinese-simplified] pagenation navigator
  @Params:
  Int $page: current page
  Int $totalPages: total pages
  String $URL: target URL for pagenation
  Int $count: total records
  String $query: query string for SEARCH
 *  @Return: String pagenation navigator link
 */

function generatePageLink($page, $totalPages, $URL, $counts, $query = '')
{
    $URL .= (strpos($URL, '?') === false ? '?' : '&');
    // First:
    $first = '首 页';
    $first = "<a href=" . $URL . "page=1$query>$first</a>";

    // Prev:
    $prev = '上一页';
    $previousPage = ($page > 1) ? $page - 1 : 1;
    $prev = "<a href=" . $URL . "page=$previousPage$query>$prev</a>";

    // Next:
    $next = '下一页';
    $nextPage = ($page == $totalPages) ? $totalPages : $page + 1;
    $next = "<a href=" . $URL . "page=$nextPage$query>$next</a>";

    // Last
    $last = '末 页';
    $last = "<a href=" . $URL . "page=$totalPages$query>$last</a>";

    // Total:
    $total = '共';

    $pageLink = $total . ' ' . $counts . '&nbsp;&nbsp;' . $first . '&nbsp;&nbsp;' . $prev;
    $pageLink .= '&nbsp;&nbsp;' . $next . '&nbsp;&nbsp;' . $last . '&nbsp;&nbsp;' . $page . '/' . $totalPages . '&nbsp';

    return $pageLink;
}

// Functionality: 生成带"转至"第几页的分页导航栏
function generatePageLink2($page, $totalPages, $URL, $counts, $query = '')
{
    $sign = '?';
    if (strpos($URL, '?') !== FALSE) {
        $sign = '&';
    }

    // First:
    $first = '首 页';
    $first = '<a href=' . $URL . $sign . 'page=1' . $query . '>' . $first . '</a>';

    // Prev:
    $prev = '上一页';
    $previousPage = ($page > 1) ? $page - 1 : 1;
    $prev = '<a href=' . $URL . $sign . 'page=' . $previousPage . $query . '>' . $prev . '</a>';

    // Next:
    $next = '下一页';
    $nextPage = ($page == $totalPages) ? $totalPages : $page + 1;
    $next = '<a href=' . $URL . $sign . 'page=' . $nextPage . $query . '>' . $next . '</a>';

    // Last
    $last = '末 页';
    $last = '<a href=' . $URL . $sign . 'page=' . $totalPages . $query . '>' . $last . '</a>';

    // Total:
    $total = '共';

    $pageLink = $total . ' ' . $counts . '&nbsp;&nbsp;' . $first . '&nbsp;&nbsp;' . $prev;
    $pageLink .= '&nbsp;&nbsp;' . $next . '&nbsp;&nbsp;' . $last . '&nbsp;&nbsp;';

    $pageLink .= '<input type="text" id="txtGoto" name="txtGoto" size="5" maxlength="5" />';
    $pageLink .= '&nbsp;<input type ="button" class="btn btn-primary" id="btnGoto" name="btnGoto" value="转至" />';

    $pageLink .= '&nbsp;<span id="currentPage">' . $page . '</span>/<span id="totalPages">' . $totalPages . '</span>&nbsp';

    $pageLink .= '<br /><input type="hidden" id="self_url" name="self_url" value="' . $URL . '">';

    return $pageLink;
}


/**
 *  Functionality: 生成供静态化 URL 用并且带有 GOTO 功能的分页导航
 *  Remark: 首页, 上一页, 下一页, 末页中的 href 为 javascript:;
 *          而是赋予了class, 当前页与总页则使用了span, 模板中 JQuery 点击事件触发
 *          $('.pg_index').click(function(){ ... });
 */
function staticPageLink($page, $totalPages, $URL, $counts, $query = '')
{

    // First:
    $first = '首 页';
    $first = "<a class='pg_index pointer'>$first</a>";

    // Prev:
    $prev = '上一页';
    $previousPage = ($page > 1) ? $page - 1 : 1;
    $prev = "<a class='pg_prev pointer' >$prev</a>";

    // Next:
    $next = '下一页';
    $nextPage = ($page == $totalPages) ? $totalPages : $page + 1;
    $next = "<a class='pg_next pointer'>$next</a>";

    // Last
    $last = '末 页';
    $last = "<a class='pg_last pointer'>$last</a>";

    // Total:
    $total = '共';

    $pageLink = $total . ' ' . $counts . '&nbsp;&nbsp;' . $first . '&nbsp;&nbsp;' . $prev;
    $pageLink .= '&nbsp;&nbsp;' . $next . '&nbsp;&nbsp;' . $last . '&nbsp;&nbsp;';

    $pageLink .= '<input type="text" id="txtGoto" name="txtGoto" size="3" maxlength="3" />';
    $pageLink .= '&nbsp;<input type ="button" id="btnGoto" name="btnGoto" value="转至" />';

    $pageLink .= '&nbsp;<span id="currentPage">' . $page . '</span>/<span id="totalPages">' . $totalPages . '</span>&nbsp';

    $pageLink .= '<br /><input type="hidden" id="self_url" name="self_url" value="' . $URL . '">';

    return $pageLink;
}


// Get current microtime
function calculateTime()
{
    list($usec, $sec) = explode(' ', microtime());
    return ((float)$usec + (float)$sec);
}


/**
 * 裁剪中文
 *
 * @param type $string
 * @param type $length
 * @param type $dot
 * @return type
 */
function cutstr($string, $length, $dot = ' ...')
{
    if (strlen($string) <= $length) {
        return $string;
    }

    $pre = chr(1);
    $end = chr(1);
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), $string);

    $strcut = '';
    if (strtolower(CHARSET) == 'utf-8') {

        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }

        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);

    } else {
        $_length = $length - 1;
        for ($i = 0; $i < $length; $i++) {
            if (ord($string[$i]) <= 127) {
                $strcut .= $string[$i];
            } else if ($i < $_length) {
                $strcut .= $string[$i] . $string[++$i];
            }
        }
    }

    $strcut = str_replace(array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    $pos = strrpos($strcut, chr(1));
    if ($pos !== false) {
        $strcut = substr($strcut, 0, $pos);
    }
    return $strcut . $dot;
}


function pr($arr)
{
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}


function pp()
{
    pr($_POST);
}


/**
 *  JavaScript alert
 */
function jsAlert($msg)
{
    echo "<script type='text/javascript'>alert(\"$msg\")</script>";
}


/**
 *  JavaScript redirect
 */
function jsRedirect($url, $die = true)
{
    echo "<script type='text/javascript'>window.location.href=\"$url\"</script>";
    if ($die) {
        die;
    }
}


// verify page
function verifyPage($page, $totalPages)
{
    if ($page > $totalPages || !is_numeric($page) || $page <= 0) {
        $page = 1;
    }

    return $page;
}


/**
 * Echo and die
 */
function eand($msg)
{
    echo $msg;
    die;
}


/**
 * Echo html br
 */
function br()
{
    echo '<br />';
}


/**
 * Echo html hr
 */
function hr()
{
    echo '<hr/>';
}


// echo hidden div with msg
function echoHiddenDiv($msg)
{
    $html = '<div style="display:none">' . $msg . '</div>';
    echo $html;
}


// Highlight keyword
function highlight($str, $find, $color)
{
    return str_replace($find, '<font color="' . $color . '">' . $find . '</font>', $str);
}


//im:将店铺图片保存为3种规格:小图:48x48,中图120x120,大图200x200
function thumb($filename,$destination=null,$scale=0.5,$dst_w=null,$dst_h=null,$isReservedSource=true){
    //获取原图片宽、高、类型
    list($src_w,$src_h,$imagetype)=getimagesize($filename);
    /**
     * 返回结果说明
    索引 0 给出的是图像宽度的像素值
    索引 1 给出的是图像高度的像素值
    索引 2 给出的是图像的类型，返回的是数字，其中1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，8 = TIFF(motorola byte order)，9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM
    索引 3 给出的是一个宽度和高度的字符串，可以直接用于 HTML 的 <image> 标签
    索引 bits 给出的是图像的每种颜色的位数，二进制格式
    索引 channels 给出的是图像的通道值，RGB 图像默认是 3
    索引 mime 给出的是图像的 MIME 信息，此信息可以用来在 HTTP C
     *
     */
    //若参数没有指定宽高，则按默认比例缩放
    if(is_null($dst_w)||is_null($dst_h)){
        $dst_w=ceil($src_w*$scale);
        $dst_h=ceil($src_h*$scale);
    }
    $mime=image_type_to_mime_type($imagetype);//返回值  image/jpg
    $createFun=str_replace("/", "createfrom", $mime);
    $outFun=str_replace("/", null, $mime);
    $src_image=$createFun($filename);
    $dst_image=imagecreatetruecolor($dst_w, $dst_h);
    imagecopyresampled($dst_image, $src_image, 0, 0, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    //若目录文件夹不存在则创建文件夹
    if($destination&&!file_exists(dirname($destination))){
        mkdir(dirname($destination),0777,true);
    }
    $dstFilename=$destination==null?getUniName().".".getExt($filename):$destination;
    $outFun($dst_image,$dstFilename);//将生成的缩略图储存到$dstFilename下
    imagedestroy($src_image);
    imagedestroy($dst_image);
    if(!$isReservedSource){
        unlink($filename);
    }
    return $dstFilename;
}

//生成excel表
/* @param array $data
* @param array $letter
* @param array $tableheader
**/
function excel($data,$letter,$tableheader){
    $excel=new PHPExcel();
    for($i = 0;$i < count($tableheader);$i++) {
        $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
    }
    for ($i = 2;$i <= count($data) + 1;$i++) {
        $j = 0;
        foreach ($data[$i - 2] as $key=>$value) {
            $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
            $j++;
        }
    }
    $write = new PHPExcel_Writer_Excel5($excel);
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");;
    header('Content-Disposition:attachment;filename="testdata.xls"');
    header("Content-Transfer-Encoding:binary");
    $write->save('php://output');
}
//excel表转为数组
//$filename excel表文件地址
 function ExcelToArray($filename,$encode='utf-8'){
    $objReader = PHPExcel_IOFactory::createReader('Excel5');
    $objReader->setReadDataOnly(true);
    $objPHPExcel = $objReader->load($filename);
    $objWorksheet = $objPHPExcel->getActiveSheet();
    $highestRow = $objWorksheet->getHighestRow();
    $highestColumn = $objWorksheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $excelData = array();
    for ($row = 1; $row <= $highestRow; $row++) {
        for ($col = 0; $col < $highestColumnIndex; $col++) {
            $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
        }
    }
    return $excelData;
}

//生成4位随机码
function GetfourStr($len){
    $chars_array = array(
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
        "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
        "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
        "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
        "S", "T", "U", "V", "W", "X", "Y", "Z",
    );
    $charsLen = count($chars_array) - 1;

    $outputstr = "";
    for ($i=0; $i<$len; $i++)
    {
        $outputstr .= $chars_array[mt_rand(0, $charsLen)];
    }
    return $outputstr;
}

