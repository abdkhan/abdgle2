<?php
include 'functions.php';

/**
 * get google query url
 * @return string url
 */
function get_google_url($ukey){
    $host = 'http://www.google.com';
    $url = $host.'/search?ie=utf-8&hl=en-US&gl=en&source=hp&source=android-unknown&q='.$ukey;
    if(isset($_GET['hl'])){
        $query = http_build_query($_GET);
        $url = $host.'/search?'.$query;
    }
    return $url;
}

function get_counter(){
    $href_script = <<<EOD
        var hrefs = document.getElementById("ires").getElementsByTagName("a"),
            href = ""; 
        for(var i=0, l=hrefs.length; i<l; i++){
            href = hrefs[i].href.match(/\/url\?q=([^&#]*)(&|$)/);
            if(href && href.length>1 && href[1]){
                hrefs[i].href = decodeURIComponent(href[1]);
            }
        }
        document.getElementById('logo').href="/";
EOD;
    //去除跳转链接
    $html = '<script type="text/javascript">'.$href_script.'</script>';

    //统计脚本
    $html .= '<script type="text/javascript">(function(){var _glinks = document.getElementById("search").getElementsByTagName("a"); for(var i=0,l=_glinks.length; i<l; i++){_glinks[i].setAttribute("target", "_blank");} })();</script>';
    return $html.'<div style="display:none" ><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id=\'cnzz_stat_icon_1656292\'%3E%3C/span%3E%3Cscript src=\'" + cnzz_protocol + "s4.cnzz.com/stat.php%3Fid%3D1656292\' type=\'text/javascript\'%3E%3C/script%3E"));</script></div>';
}

/**
 * get google content
 * @param  string $ukey ukey
 * @return string content
 */
function get_content($ukey){
    $url = get_google_url($ukey);

    $ctx = false;
    if(is_mobile()){
        header('Content-Type:text/html;charset=utf-8');
        $ctx = get_mobile_context();
        $con = @file_get_contents($url, false, $ctx);
    }else{
        header('Content-Type:text/html;charset=utf-8');
        $ctx = get_common_context();
        $con = @file_get_contents($url, false, $ctx);
    }

    if($con){ //备用
        $con = $con.get_counter();
    }

    if(!$con){
        $con = 'google proxy server gfwed!';
    }
    return $con;
}


$keyword = isset($_GET['q']) ? trim($_GET['q']) : 'fuck';
if(!$keyword){
    return header('location:/');
}

if(substr($keyword, 0, 8)=='related:'){
    echo 'fuck u!';
    exit();
}

$ukey = urlencode($keyword);

echo get_content($ukey);
