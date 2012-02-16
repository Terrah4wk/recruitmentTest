<?php

@ini_set('memory_limit','128M');
@ini_set('upload_max_filesize','40MB');
@ini_set('max_execution_time','90');

include '../class/autoloader.php';

$autoloader = new autoloader(); // init autoloader
$request = new requestHandler(); // init request
$db = new database(); // init database
$filter = new filter(); // init filter
$data = new dataHandler(); // init dataHandler

$post = $request->getPost();
$file = $request->getFile();
$page = $request->getPage();
$sort = $request->getSorting();

$msg = '';

// If upload
if (isset($post['upload'])):
    
    // Error check
    $errorMsg = array();

    $ext = $filter->isImage($file['image']['name']);
    if ($ext === false):
        $errorMsg['upload'] = '<p class="error">Please insert only png/jpg/jpeg/gif/bmp</p>';
    endif;

    if (isset($file['image']['name']) && $filter->isSize($file['image']['tmp_name'])):
        $errorMsg['upload'] = '<p class="error">Minimum 2MB and 200px x 200px</p>';
    endif;

    // If no errors
    if (count($errorMsg) === 0):
        $data->saveImage($file['image']['tmp_name'], $ext);
        $msg = '<p class="success">Successfully saved.</p>';
    endif;

endif;

// If truncate
if (isset($post['truncate'])):
    $sql = 'TRUNCATE pixel';
    $db->select($sql);
endif;

$sql = 'SELECT pixelID,
               AES_DECRYPT(pixelRGB1, "' . $db->aesKey . '") AS pixelRGB1, 
               AES_DECRYPT(pixelRGB2, "' . $db->aesKey . '") AS pixelRGB2, 
               AES_DECRYPT(pixelRGB3, "' . $db->aesKey . '") AS pixelRGB3,
               pixelX,
               pixelY,
               pixelRank
        FROM pixel ORDER BY pixelID ' . $sort[0]['get'];

$table1 = array();
$table1 = $data->paging($db->select($sql), 20, $page, $sort);

$sql = 'SELECT 
               AES_DECRYPT(pixelRGB1, "' . $db->aesKey . '") AS pixelRGB1, 
               AES_DECRYPT(pixelRGB2, "' . $db->aesKey . '") AS pixelRGB2, 
               AES_DECRYPT(pixelRGB3, "' . $db->aesKey . '") AS pixelRGB3,
               pixelX,
               pixelY,
               pixelRank
        FROM pixel ORDER BY pixelRank ' . $sort[1]['get'];

$table2 = array();
$table2 = $data->paging($db->select($sql), 20, $page, $sort);

// Start html
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Task</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">
    </head>
    <body>

        <div class="span-24 prepend-1">

            <h1>Task</h1>

            <?php echo $msg; ?>

            <div class="span-24 container left">

                <form method="post" enctype="multipart/form-data" action="index.php" class="inline">

                    <fieldset>

                        <legend>Form</legend>

                        <div class="span-8">
                            <?php if (!empty($errorMsg['upload'])) echo $errorMsg['upload']; ?>
                            <input type="file" name="image" ><br />
                        </div>
                        <p>
                            <input type="submit" name="upload" value="Upload" class="button">
                            <input type="submit" name="truncate" value="Truncate" class="button" />
                        </p>

                    </fieldset>

                </form>

            </div>

            <?php if ($table2['data']): $data->getPager($table2, 'index.php', $sort); ?>

                <hr />

                <table border="0" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th class="span-4"><a href="index.php?page=<?php echo $page;?>&sort1=<?php echo $sort[0]['link']; ?>&sort2=<?php echo $sort[1]['get']; ?>">Raw Data (<?php echo $sort[0]['link']; ?>)</th>
                            <th class="span-4 last"><a href="index.php?page=<?php echo $page;?>&sort1=<?php echo $sort[0]['get']; ?>&sort2=<?php echo $sort[1]['link']; ?>">Binary (<?php echo $sort[1]['link']; ?>)</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        
                            $i_max = count($table1['data']);
                            for ($i = 0; $i < $i_max; ++$i): 
                                
                        ?>
                            <tr>
                                <td>
                                    <?php echo $table1['data'][$i]['pixelRGB1']; ?>, 
                                    <?php echo $table1['data'][$i]['pixelRGB2']; ?>,
                                    <?php echo $table1['data'][$i]['pixelRGB3']; ?>
                                </td>
                                <td>
                                    <?php echo $data->dec2bin($table2['data'][$i]['pixelRGB1']); ?>, 
                                    <?php echo $data->dec2bin($table2['data'][$i]['pixelRGB2']); ?>,
                                    <?php echo $data->dec2bin($table2['data'][$i]['pixelRGB3']); ?>
                                </td>
                            </tr>
                        <?php 
                        
                        endfor; 
                        
                        ?>
                    </tbody>
                </table>

                <?php
                
                $data->getPager($table2, 'index.php', $sort);

            else:

                echo '<p class="paging"> No results </p>';

            endif;
            
            ?>
            <hr />

        </div>

    </body>
</html>