<?php
require_once 'Sudoku.php';
session_start();

if (!isset($_SESSION['sudoku'])) {
    $_SESSION['sudoku'] = new Sudoku();
}

$sudoku = $_SESSION['sudoku'];
$board = $sudoku->generateBoard();
$solution = $sudoku->getSolution();

$errorMessage = '';
$successMessage = '';
$finished = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hasEmptyCells = false;
    $hasErrors = false;
    
    foreach ($board as $i => $row) {
        foreach ($row as $j => $cell) {
            if ($cell == 0) {
                $inputName = "cell_{$i}_{$j}";
                $userInput = isset($_POST[$inputName]) ? trim($_POST[$inputName]) : '';
                
                if ($userInput === '') {
                    $hasEmptyCells = true;
                } elseif (!is_numeric($userInput) || $userInput < 1 || $userInput > 9 || $userInput != $solution[$i][$j]) {
                    $hasErrors = true;
                }
            }
        }
    }
    
    if ($hasEmptyCells) {
        $errorMessage = "Lütfen tüm boş hücreleri doldurun!";
        $finished = false;
    } elseif ($hasErrors) {
        $errorMessage = "Bazı hücrelerde yanlış değerler var!";
        $finished = false;
    }

    if ($finished) {
        $successMessage = "🎉 Tebrikler! Sudoku'yu başarıyla tamamladınız!";
        unset($_SESSION['sudoku']);
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sudoku Oyunu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Sudoku</h1>
    <form method="post">
        <table>
            <?php for ($i = 0; $i < 9; $i++): ?>
                <tr>
                    <?php for ($j = 0; $j < 9; $j++): ?>
                        <?php
                            $inputName = "cell_{$i}_{$j}";
                            $isReadonly = $board[$i][$j] !== 0;
                            $value = $isReadonly ? $board[$i][$j] : (isset($_POST[$inputName]) ? $_POST[$inputName] : '');
                            $isInvalid = !$isReadonly && $value !== '' && $value != $solution[$i][$j];
                        ?>
                        <td class="<?= (floor($i / 3) + (floor($j / 3) * 3) + 1) ?>">
                            <input type="text"
                                   name="<?= $inputName ?>"
                                   maxlength="1"
                                   value="<?= htmlspecialchars($value) ?>"
                                   <?= $isReadonly ? 'disabled' : '' ?>
                                   class="<?= $isInvalid ? 'invalid' : '' ?>">
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </table>
        <br>
        <button type="submit">Kontrol Et</button>
    </form>

    <?php if ($errorMessage): ?>
        <div id="error-message"><?= $errorMessage ?></div>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div id="success-message"><?= $successMessage ?></div>
    <?php endif; ?>
</body>
</html>