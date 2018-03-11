<?php

define('DB_DRIVER','mysql');
define('DB_HOST','localhost');
define('DB_NAME','tasks');
define('DB_USER','ankostyaeva');
define('DB_PASS','neto1700');

try {
    $connect = DB_DRIVER . ':host='. DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8';
    $db = new PDO($connect, DB_USER,DB_PASS);

    $selectTasks = $db->query("SELECT * FROM tasks");

    $taskName = "addTask";

    if (!empty($_POST['addTask'])) {        
        $addTask = $db->prepare("INSERT INTO `tasks` (description, date_added) VALUES (:descTask, NOW())");
        $addTask->bindParam(':descTask', $_POST['addTask'], PDO::PARAM_STR);
        $addTask->execute();
        header("Location: index.php");
    } 

    if (!empty($_POST['update'])) {            
        $updescTask = $db->prepare("UPDATE `tasks` SET `description` = :description WHERE `tasks`.`id` = :id");  
        $updescTask->execute([':id' => $_GET['id'], ':description' => $_POST['update']]);        
        header("Location: index.php");
    }

    if (!empty($_GET['action'])) { 

        if ($_GET['action'] == 'edite')  {     
            $updateTask = $db->prepare("SELECT * FROM tasks WHERE `tasks`.`id` = :id");
            $updateTask->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $updateTask->execute();
            $descTask = $updateTask->fetch();
            $taskName = "update";
            //header("Location: index.php");
        }

        if ($_GET['action'] == 'completed')  {     
            $completTask = $db->prepare("UPDATE `tasks` SET `is_done` = '1' WHERE `tasks`.`id` = :id");
            $completTask->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $completTask->execute();
            header("Location: index.php");
        }

        if ($_GET['action'] == 'deleted')  {     
            $delTask = $db->prepare("DELETE FROM `tasks` WHERE `tasks`.`id` = :id");
            $delTask->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $delTask->execute();
            header("Location: index.php");
        }        
    } 

    if (isset($_POST['sort_by'])) {
        if ($_POST['sort_by'] == 'date_created') {
            $selectTasks = $db->query("SELECT * FROM `tasks` ORDER BY `tasks`.`date_added` ASC");       
        } 
        elseif ($_POST['sort_by'] == 'status') {
            $selectTasks = $db->query("SELECT * FROM `tasks` ORDER BY `tasks`.`is_done` DESC");        
        } 
        elseif ($_POST['sort_by'] == 'description') {
            $selectTasks = $db->query("SELECT * FROM `tasks` ORDER BY `tasks`.`description` ASC");        
        } 
        else {
            echo "Выберите тип сортировки";
            exit();
        }
    } 

} 
catch (Exception $e) 
{
    die('Error: ' . $e->getMessage() . '<br/>');
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Test</title>
<style>
    table { 
        border-spacing: 0;
        border-collapse: collapse;
    }

    table td, table th {
        border: 1px solid #ccc;
        padding: 5px;
    }
    
    table th {
        background: #eee;
    }
</style>
</head>

<body>

<h1>Список дел на сегодня</h1>
<div style="float: left">
    <form method="POST" action="">
        <input type="text" name="<?= $taskName ?>" placeholder="Описание задачи" value="<?php if (isset($descTask)) echo $descTask['description']; ?>">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="submit" value="Добавить">
    </form>
</div>
<div style="float: left; margin-left: 20px;">
    <form method="POST">
        <label for="sort">Сортировать по:</label>
        <select name="sort_by">
            <option selected disabled>Выберите тип сортировки</option>
            <option value="date_created">Дате добавления</option>
            <option value="status">Статусу</option>
            <option value="description">Описанию</option>
        </select>
        <input type="submit" name="sort" value="Отсортировать">
    </form>
</div>
<div style="clear: both"></div>

<table>
    <thead>
    <tr>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус</th>
        <th></th>
    </tr>
    </thead>
    

    <tbody>
        <tr>
            <?php 
                while ($dataTask = $selectTasks->fetch()) { 
                    if ($dataTask['is_done'] == 1) {
                        $status = "Выполнено";
                        $color = "green";
                        } 
                        else {
                            $status = "В процессе";
                            $color = "red";
                    }
            ?> 
            <td><?php echo $dataTask['description'] ?></td>
            <td><?php echo $dataTask['date_added']?></td>
            <td style="color: <?= $color?>"><?php echo $status?></td>
            <td>
                <a href="?id=<?=$dataTask['id']?>&action=edite">Изменить</a>
                <a href="?id=<?=$dataTask['id']?>&action=completed">Выполнить</a>
                <a href="?id=<?=$dataTask['id']?>&action=deleted">Удалить</a>
            </td>
        </tr>
            <?php 
                } 
            ?>
    </tbody>

</table>

</body>
