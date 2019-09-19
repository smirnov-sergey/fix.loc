<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Плейлист';
?>

<title><?= Html::encode($this->title) ?></title>

<div class="nav nav-pills">
    <a href="<?= Url::to(['playlist/index']); ?>"> <?= Html::submitButton('Все плейлисты', ['class' => 'btn btn-primary']); ?></a>
</div>

<div class="playlist container">
    <h3>Плейлист</h3>
    <div class="form-group">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(['id' => 'playlist-update-form']); ?>
            <?= $form->field($playlist, 'name')->textInput(); ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'login-button']); ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>