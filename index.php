<?php
session_start();
include("db-operations.php");

$currentFilter = isset($_GET['filter']) ? $_GET['filter'] : 'creation_date';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Do</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

    <?php
        if (!empty($message)) {
            echo "<p>$message</p>";
        }
    ?>

    <div class="container">
        <div class="field">
            <form action="" method="POST">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="filter" value="<?php echo $currentFilter; ?>">
                <div class="field-wrapper">
                    <input type="text" placeholder="Qual a atividade?" name="activity" required>
                </div>
                <label class="centered-label">Para quando é a atividade?</label>
                
                <div class="field-wrapper full-width-input">
                    <input type="date" name="date" required>
                </div>

                <div class="field-wrapper full-width-input">
                    <input type="time" name="time" required>
                </div>

                <div class="field-wrapper"> 
                    <div class="radio-style">
                        <p> Prioridade da atividade: </p>
                        <div class="radio-option">
                            <input type="radio" id="urgent" value="1" name="urgency" required>
                            <label for="urgent">Urgente!!</label>
                        </div>
                        <div class="radio-option">    
                            <input type="radio" id="attention" value="2" name="urgency" required>
                            <label for="attention">Média!</label>
                        </div>
                        <div class="radio-option">    
                            <input type="radio" id="calm" value="3" name="urgency" required>
                            <label for="calm">Baixa</label>
                        </div>
                    </div>
                </div>
                <button class ="button-submit" type="submit">Salvar atividade</button>
            </form>

            <div class="alert"> 
            <?php
            if (isset($_SESSION['message'])) {
                echo $_SESSION['message'];
                unset($_SESSION['message']);
            }
            ?>
            </div>
        </div>

        <main>
            <div class="header"> 
                <h1>ATIVIDADES A FAZER</h1>
                <div class="filter-select">
                    <label for="filter">Ordenar por:</label>
                    <select name="filter" id="filter">
                        <option value="creation-date" <?php echo $currentFilter == 'creation-date' ? 'selected' : ''; ?>>Data de criação</option>
                        <option value="conclusion" <?php echo $currentFilter == 'conclusion' ? 'selected' : ''; ?>>Não concluídos</option> 
                        <option value="priority" <?php echo $currentFilter == 'priority' ? 'selected' : ''; ?>>Prioridade</option>
                        <option value="end-date" <?php echo $currentFilter == 'end-date' ? 'selected' : ''; ?>>Data limite</option>
                    </select>
                </div>
            </div>

            <div class="cards">

            <?php
            $filter = $currentFilter;

            switch ($filter) {
                case 'conclusion':
                    $orderBy = 'isChecked ASC';
                    break;
                case 'priority':
                    $orderBy = 'urgency ASC';
                    break;
                case 'end-date':
                    $orderBy = 'date ASC, time ASC';
                    break;
                case 'creation-date':
                default:
                    $orderBy = 'id ASC';
                    break;
            }
            
            $sql = "SELECT * FROM activities ORDER BY $orderBy";
            $res = $conn->query($sql);

            $numr = $res->num_rows;

            if($numr > 0){
                while($row = $res->fetch_object()){

                    if ($row->urgency == 1) {
                        $bgColor = '#721618';
                        $borderColor = 'border: 3px solid #EA0E0E';
                    } elseif ($row->urgency == 2) {
                        $bgColor = '#805d00';
                        $borderColor = 'border: 3px solid #ffb900';
                    } else {
                        $bgColor = '#1b4b1b';
                        $borderColor = 'border: 3px solid #1DBD1D';
                    }

                    $buttondelete = "<button onclick=\"location.href='?page=create&action=delete&id=".$row->id."&filter=".$currentFilter."';\" class='button-delete' style=' $borderColor'>x</button>";
                                
                    if ($row->isChecked == 1) {
                        
                        $checked = "(Concluido)";
                        $iconchecked = "<svg onclick=\"location.href='?page=create&action=isChecked&id=".$row->id. "&filter=".$currentFilter. "';\" width='50' height='50' class='icon-checked' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M7.50008 9.99999L9.16675 11.6667L12.5001 8.33332M18.3334 9.99999C18.3334 14.6024 14.6025 18.3333 10.0001 18.3333C5.39771 18.3333 1.66675 14.6024 1.66675 9.99999C1.66675 5.39762 5.39771 1.66666 10.0001 1.66666C14.6025 1.66666 18.3334 5.39762 18.3334 9.99999Z' stroke='#f4f4f5' stroke-width='1.25' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";

                    } else {
                        
                        $checked = "";
                        $iconchecked = "<svg onclick=\"location.href='?page=create&action=isChecked&id=".$row->id. "&filter=".$currentFilter. "';\" width='50' height='50' class='Icon-checked' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                        <path d='M8.41664 1.81833C9.46249 1.61593 10.5374 1.61593 11.5833 1.81833M11.5833 18.1817C10.5374 18.3841 9.46249 18.3841 8.41664 18.1817M14.6741 3.10083C15.5587 3.70019 16.3197 4.46406 16.9158 5.35083M1.8183 11.5833C1.6159 10.5375 1.6159 9.46252 1.8183 8.41667M16.8991 14.6742C16.2998 15.5587 15.5359 16.3198 14.6491 16.9158M18.1816 8.41667C18.384 9.46252 18.384 10.5375 18.1816 11.5833M3.1008 5.32583C3.70016 4.44128 4.46403 3.68023 5.3508 3.08417M5.3258 16.8992C4.44124 16.2998 3.6802 15.5359 3.08414 14.6492' stroke='#f4f4f5' stroke-width='1.25' stroke-linecap='round' stroke-linejoin='round'/>
                        </svg>";
                    }

                    $date = date('d/m/Y', strtotime($row->date));
                    $time = date('H:i', strtotime($row->time));
                    
                    echo "
                    <div class='card-bg' style='background-color: $bgColor; $borderColor '>
                    <div class='card-content'>
                        {$iconchecked}
                        <div class='card-text'>
                        <h1>{$row->name} {$checked}</h1>
                        <p>Atividade para a data:</p>
                        <p>{$date} até as {$time} horas.</p>
                        </div>
                    {$buttondelete}
                    </div></div>
                    ";
                }
            } else {
                echo "<p>Nenhuma atividade registrada.</p>";
            }
            ?>
            </div>
        </main>
    </div>    

    <script>
        document.getElementById('filter').addEventListener('change', function() {
            var filter = this.value;
            window.location.href = '?filter=' + filter;
        });

        document.addEventListener("DOMContentLoaded", function() {
            var message = document.querySelector('.session-message');
            if (message) {
                setTimeout(function() {
                    message.style.opacity = '0';
                    setTimeout(function() {
                    message.remove();
                    }, 500); 
                }, 5000);
            }
        });
    </script>

</body>
</html>
