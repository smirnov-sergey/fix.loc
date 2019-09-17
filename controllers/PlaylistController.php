<?php
/**
 * Created by PhpStorm.
 * User: sergei-sm
 * Date: 17.09.2019
 * Time: 16:22
 */

namespace app\controllers;

use app\models\Playlist;
use Yii;
use yii\web\NotFoundHttpException;

class PlaylistController extends AppController
{
    public function actionIndex()
    {
        $playlists = Playlist::find()->all();

        return $this->render('index', compact('playlists'));
    }

    public function actionView($id)
    {
        $playlist = $this->findModel($id);

        return $this->render('view', compact('playlist'));
    }

    public function actionAdd()
    {
        $playlist = new Playlist();

        if ($playlist->load(Yii::$app->request->post()) && $playlist->validate()) {
            if ($playlist->save()) {
                return $this->redirect(['playlist/index']);
            }
        }

        return $this->render('add', compact('playlist'));
    }

    public function actionUpdate($id)
    {
        $playlist = $this->findModel($id);

        if ($playlist->load(Yii::$app->request->post()) && $playlist->validate()) {
            if ($playlist->save()) {
                return $this->redirect(['playlist/index']);
            }
        }

        return $this->render('update', compact('playlist'));
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['playlist/index']);
    }

    //поиск записи в таблице
    protected function findModel($id)
    {
        if (($playlist = Playlist::findOne($id)) !== null) {
            return $playlist;
        } else {
            throw new NotFoundHttpException('Данного плейлиста не существует');
        }
    }

    // поиск по названию плейлиста
    public function actionSearch($search)
    {
        if (!$search)
            return $this->render('search');

        $playlist = Playlist::find()->where(['like', 'name', $search])->with(['track'])->all();

        return $this->render('search', compact('playlist', 'search'));
    }
}