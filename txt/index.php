<?php

/**
 * 获取当前TXT文件列表
 * @return array
 */
function getTxtFile()
{
    $dir = './';
    $dirArray = array();
    if (false != ($handle = opendir($dir))) {
        $i = 0;
        while (false !== ($file = readdir($handle))) {
            if (stripos($file, '.txt')) {
                $dirArray[$i] = iconv('gb2312', 'utf-8', $file);
                $i++;
            }
        }
        //关闭句柄
        closedir($handle);
    }
    return $dirArray;
}

?>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>txt list
    </title>
</head>
<ul>
    <?php foreach (getTxtFile() as $value) { ?>
        <li>
            <a href="<?php echo $value; ?>" download><?php echo $value; ?></a>
        </li>
    <?php } ?>
</ul>
