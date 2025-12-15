<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<h1>🍎 Управление яблоками</h1>

<?= Html::a('Сгенерировать яблоки', ['generate', 'password' => 'secret123'], ['class' => 'btn btn-success mb-3']) ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
<?php endif; ?>

<?php if (empty($apples)): ?>
    <p>Нет яблок. Нажмите "Сгенерировать яблоки".</p>
<?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Цвет</th>
            <th>Создано</th>
            <th>Статус</th>
            <th>Осталось (%)</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($apples as $apple): ?>
            <tr>
                <td><?= $apple->id ?></td>
                <td><?= Html::encode($apple->color) ?></td>
                <td><?= Yii::$app->formatter->asDatetime($apple->created_at) ?></td>
                <td><?= Html::encode($apple->getStatusLabel()) ?></td>
                <td><?= number_format($apple->getRemainingPercent(), 2) ?>%</td>
                <td>
                    <?php if ($apple->isOnTree()): ?>
                        <?= Html::a('Упасть', ['fall', 'id' => $apple->id, 'password' => 'secret123'], ['class' => 'btn btn-warning btn-sm']) ?>
                    <?php elseif (!$apple->isRotten() && !$apple->isEaten()): ?>
                        <form method="post" action="<?= Url::to(['eat', 'id' => $apple->id, 'password' => 'secret123']) ?>" style="display:inline;">
<!--                             съели в процентах-->
                            <?= Html::input('number', 'percent', 10, [
                                'min' => 1,
                                'max' => $apple->getRemainingPercent(),
                                'step' => 0.1,
                                'style' => 'width:80px'
                            ]) ?>
                            <?= Html::submitButton('Съесть %', ['class' => 'btn btn-info btn-sm']) ?>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
