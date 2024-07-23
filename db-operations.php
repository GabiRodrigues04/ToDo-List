<?php

include("config.php");

$message = "";

$currentFilter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : 'creation_date';

if (isset($_REQUEST["action"])) {
    switch ($_REQUEST["action"]) {
    
        case 'create':
            $name = $_POST["activity"];
            $date = $_POST["date"];
            $time = $_POST["time"];
            $urgency = $_POST["urgency"];
            $isChecked = 0;

            $sql = "INSERT INTO activities (name, date, time, urgency, isChecked) VALUES ('{$name}','{$date}','{$time}','{$urgency}','{$isChecked}')";
            $res = $conn->query($sql);

            if ($res === true) {
                $_SESSION['message'] = "<div class='session-message'> Atividade criada com sucesso!</div>";
                header("Location: ".$_SERVER['PHP_SELF']."?filter=".$currentFilter);
                exit();
            } else {
                $_SESSION['message'] = "<div class='session-message'> Erro ao criar atividade: " . $conn->error . "</div>";
                header("Location: ".$_SERVER['PHP_SELF']."?filter=".$currentFilter);
                exit();
            }
            break;

        case 'isChecked':
            $id = $_REQUEST["id"];
        
            $sql = "SELECT isChecked FROM activities WHERE id=".$id;
            $res = $conn->query($sql);
        
            $row = $res->fetch_object();
        
            $newisChecked = $row->isChecked == 1 ? 0 : 1;
        
            $sql = "UPDATE activities SET isChecked = '{$newisChecked}' WHERE id = {$id}";
            $conn->query($sql);
        
            if ($res === true) {
                header("Location: ".$_SERVER['PHP_SELF']."?filter=".$currentFilter);
                exit();
            } else {
                header("Location: ".$_SERVER['PHP_SELF']."?filter=".$currentFilter);
                exit();
            }
            break;

        case 'delete':
            $sql = "DELETE FROM activities WHERE id=".$_REQUEST["id"];
            $res = $conn->query($sql);

            if ($res === true) {
                $_SESSION['message'] = "<div class='session-message'> Atividade excluída com sucesso. </div>";
                header("Location: ".$_SERVER['PHP_SELF']."?filter=".$currentFilter);
                exit();
            } else {
                $_SESSION['message'] = "<div class='session-message'> Falha ao excluir atividade " . $conn->error . "</div>";
                header("Location: ".$_SERVER['PHP_SELF']."?filter=".$currentFilter);
                exit();
            }
            break;
    }
}
?>
