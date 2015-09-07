<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $values['page_title'] ?></title>
        
        <?php
        if (isset($values['header_keywords'])) {
            echo '<meta name="keywords" content="' . $values['header_keywords'] . '">';
            echo PHP_EOL;
        }
        if (isset($values['header_description'])) {
            echo '<meta name="description" content="' . $values['header_description'] . '">';
            echo PHP_EOL;
        }
        if (isset($values['header_other_headers'])) {
            echo $values['header_other_headers'];
            echo PHP_EOL;
        }
        ?>
    </head>
    <body>

