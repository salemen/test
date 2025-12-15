<?php
use yii\helpers\Html;
?>

<h2>Доступ к бэкенду</h2>
<p>Введите пароль для доступа:</p>
<form method="get">
    <?= Html::input('password', 'password', null, ['required' => true]) ?>
    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary']) ?>
</form>
