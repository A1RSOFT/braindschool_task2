
<form action="" method="POST">
  <label>Errors Count: 
    <input type="text" name="errorsCount" value="<?php if (isset($_REQUEST['errorsCount'])) echo $_REQUEST['errorsCount']; ?>">
  </label>
  <label>Warnings Count: 
    <input type="text" name="warningsCount" value="<?php if (isset($_REQUEST['warningsCount'])) echo $_REQUEST['warningsCount']; ?>">
  </label>
  <input type="submit">
</form>

<?php

 /**
 * Правила:
 * -1E — +1E (исправление одной ошибки за коммит приводит к созданию новой ошибки);
 * -1W — +2W (исправление одного ворнинга за коммит приводит к созданию двух новых ворнингов);
 * -2W — +1E (исправление двух ворнингов за коммит приводит к созданию новой ошибки);
 * -2E — +0E +0W (исправление двух ошибок за коммит не добавляет ни новых ошибок, ни новых ворнингов).
 *
 * Чтобы избавиться от ошибок, их должно быть чётное количество.
 * При наличии хотя бы одного ворнинга мы можем получить любое их количество.
 * Из двух ворнингов получаем одну ошибку. Тогда, при наличии одного ворнинга, можем получить любое число ошибок.
 * Следовательно, от ошибок нельзя избавиться только в случае, когда число ошибок нечётное и нет ворнингов.
 */

  function calculateCommitsCount($errorsCount, $warningsCount) 
  {
    $eCnt = $errorCount;
    $wCnt = $warningsCount;

    // Если количество ворнингов равно нулю и ошибок нечётное количество,
    // то избавиться от всех ошибок и предупреждений невозможно. Возвращаем -1.
    if (($wCnt == 0) && ($eCnt % 2 == 1)) { return -1; }

      // Количество ошибок  после перевода всех ворнингов в ошибки.
      $eCnt = $errorsCount + (int)($wCnt / 2) + $wCnt % 2; 

      // Количество шагов, за которые мы ворнинги перевели в ошибки.
      $commitsCount = (int)($wCnt / 2) + 2 * ($wCnt % 2);

      // Количество шагов с учётом избавление от всех ошибок.
      $commitsCount += (int)($eCnt / 2);

      // Учёт дополнительных шагов в случае, если ошибок нечётное количество. Тогда для
      // последней ошибки необходимо создать 2 новых ворнинга, перевести их в ошибку и избавиться от последних двух ошибок, что
      // происходит за 4 шага.
      if ($eCnt % 2 == 1) { $commitsCount += 4; } 

    return $commitsCount;
  }

  if (
    isset($_REQUEST['errorsCount']) &&
    isset($_REQUEST['warningsCount']) &&
    (0 <= $_REQUEST['errorsCount']) &&
    ($_REQUEST['warningsCount'] <= 1000)
  ) {
      $errorsCount = $_REQUEST['errorsCount'];
      $warningsCount = $_REQUEST['warningsCount'];

      echo "Commits count: " . calculateCommitsCount($errorsCount, $warningsCount);
  }
  
?>

