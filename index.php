<?php
header('Content-type: text/html; charset=utf-8');
ini_set('max_execution_time', '0');
set_time_limit(0);
error_reporting(E_ALL);
require('simple_html_dom.php');

$filename = 'all.txt';
$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
$filename = isset($_REQUEST['filename']) ? $_REQUEST['filename'] : $filename;
if ($url) {
    $url_array = parse_url($url);
    $url_array['scheme'] = 'https://';
    $url_array['query'] = '?width=343&nextRowId=1&p2=104155';
    $url_array['path'] = explode('/', $url_array['path']);
    $book_id = $url_array['path'][2];
    $chapter_id = str_replace('.html', '', $url_array['path'][3]);
    do {
        $url_array['path'][1] = 'nextchapter';
        $url_array['path'][3] = $chapter_id;
        $url_array['path'] = implode('/', $url_array['path']);
        $url = implode('', $url_array);
        $url_array['path'] = explode('/', $url_array['path']);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [$_REQUEST['curl_agent']],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_VERBOSE => 1
        ]);
        curl_setopt($ch, CURLOPT_COOKIE, $_REQUEST['curl_cookie']);
        $curl_return = curl_exec($ch);
        curl_close($ch);

        $curl_return = json_decode($curl_return);

        $html = str_get_html($curl_return->html);
        $title = $curl_return->body->chapterName;

        //不带HTML
        $content = [];
        foreach ($html->find('span') as $key => $value) {
            $matches = '';
            foreach ($value->attr as $key2 => $value2) {
                $matches .= $key2 . $value2;
            }
            preg_match_all('!\d+!', $matches, $matches);
            $left = sprintf('%05d', $matches[0][0]);
            $top = sprintf('%05d', $matches[0][1]);
            $content["$top*$left"] = $value->innertext;
        }
        ksort($content);
        $content = implode('', $content);
        if (strpos($content, '\n') !== false) {
            $content = str_replace('\n', PHP_EOL, $content);
        }
        $content = $title . PHP_EOL . $content . PHP_EOL . PHP_EOL;

        file_put_contents('txt/' . $filename, $content, FILE_APPEND);

        if ($curl_return->msg == 'ok' && $curl_return->body && $curl_return->html && $curl_return->hasNext) {
            $chapter_id++;
        } else {
            $chapter_id = false;
        }
    } while ($chapter_id);

    echo 'done';
}
?>
<title>crawler</title>
<form method="get">
    <p>filename:
        <input type="text" name="filename" style="width: 100%"
               value="<?php echo $_REQUEST['filename'] ?: $filename; ?>" title=""/>
    </p>
    <p>url:
        <input type="text" name="url" style="width: 100%"
               value="<?php echo $_REQUEST['url'] ?: 'http://m.zhangyue.com/readbook/10191927/216.html'; ?>" title=""/>
    </p>
    <p>user agent:
        <textarea name="curl_agent" style="width: 100%" title="">User-Agent:Mozilla/5.0 (iPhone; CPU iPhone OS 8_0_2 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) Version/8.0 Mobile/12A366 Safari/600.1.4</textarea>
    </p>
    <p>cookie:
        <textarea name="curl_cookie" style="width: 100%" rows="5" title=""></textarea>
    </p>

    <button type="submit">Submit</button>
</form>

<?php if (file_exists('txt/' . $filename)) { ?>
    <a href="<?php echo 'txt/' . $filename; ?>" id="download" download>download</a>

    <script type="text/javascript">
        // document.getElementById('download').click();
    </script>
<?php } ?>
