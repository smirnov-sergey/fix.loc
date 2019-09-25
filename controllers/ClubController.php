<?php
/**
 * Created by PhpStorm.
 * User: sergei-sm
 * Date: 17.09.2019
 * Time: 11:54
 */

namespace app\controllers;

use app\models\Club;
use app\models\Genre;
use app\models\Playlist;
use app\models\PlaylistTrack;
use app\models\Track;
use app\models\Visitor;
use app\models\Company;
use app\models\VisitorGenre;
use Yii;
use yii\web\NotFoundHttpException;

class ClubController extends AppController
{
    public function actionIndex()
    {
        $clubs = Club::find()->with('visitor', 'playlist', 'company')->all();

        return $this->render('index', compact('clubs'));
    }

    public function actionView($id)
    {
        $club = $this->findModel($id);

        // 2 способ достать треки (не использую во view
        $tracks = Club::find()
            ->select('track.name')
            ->innerJoin(Playlist::tableName(), 'playlist.id = club.playlist_id')
            ->innerJoin(PlaylistTrack::tableName(), 'playlist.id = playlist_track.playlist_id')
            ->innerJoin(Track::tableName(), 'playlist_track.track_id = track.id')
            ->where(['club.id' => $id])
            ->all();

        $genres = Club::find()
            ->select('genre.name')
            ->innerJoin(Playlist::tableName(), 'playlist.id = club.playlist_id')
            ->innerJoin(PlaylistTrack::tableName(), 'playlist.id = playlist_track.playlist_id')
            ->innerJoin(Track::tableName(), 'playlist_track.track_id = track.id')
            ->innerJoin(Genre::tableName(), 'track.genre_id = genre.id')
            ->where(['club.id' => $id])
            ->all();

        return $this->render('view', compact('club', 'genres', 'tracks'));
    }

    public function actionAdd()
    {
        //TODO: Нужен тест, если плейлист не существует, значение по умолчанию defaultValue('Плейлист пуст'),
        $club = new Club();

        if ($club->load(Yii::$app->request->post()) && $club->validate()) {
            if ($club->save()) {
                return $this->redirect(['club/index']);
            }
        }

        return $this->render('add', compact('club'));
    }

    public function actionUpdate($id)
    {
        $club = $this->findModel($id);

        if ($club->load(Yii::$app->request->post()) && $club->validate()) {
            if ($club->save()) {
                return $this->redirect(['club/index']);
            }
        }

        return $this->render('update', compact('club'));
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['club/index']);
    }

    public function actionDanceFloor($id)
    {
        $club = $this->findModel($id);

        // найти жанр музыки в плейлисте данного клуба
        $genres = Club::find()
            ->select(['genre.name', 'genre.id'])
            ->innerJoin(Playlist::tableName(), 'playlist.id = club.playlist_id')
            ->innerJoin(PlaylistTrack::tableName(), 'playlist.id = playlist_track.playlist_id')
            ->innerJoin(Track::tableName(), 'playlist_track.track_id = track.id')
            ->innerJoin(Genre::tableName(), 'track.genre_id = genre.id')
            ->where(['club.id' => $id])
            ->all();

        //достать genre.id для поиска посетителя с таким жанром, genre.name для дальнейших условий, если жанр не 'romance', тогда танцуют соло
        foreach ($genres as $genre) {
            $genreId[] = $genre->id;
            $genreNames[] = $genre->name;
        }

        // найти всех посетителей, которые танцуют данный жанр
        $dancers = Club::find()
            ->select(['visitor.name', 'genre.id'])
            ->distinct(['visitor.id', 'visitor_genre.genre_id'])
            ->innerJoin(Playlist::tableName(), 'playlist.id = club.playlist_id')
            ->innerJoin(PlaylistTrack::tableName(), 'playlist.id = playlist_track.playlist_id')
            ->innerJoin(Track::tableName(), 'playlist_track.track_id = track.id')
            ->innerJoin(Genre::tableName(), 'track.genre_id = genre.id')
            ->innerJoin(Visitor::tableName(), 'club.id = visitor.club_id')
            ->innerJoin(VisitorGenre::tableName(), 'visitor.id = visitor_genre.visitor_id')
            ->where(['club.id' => $id])
            // обязательно нужен WHERE visitor_genre.genre_id = genre.id || ПОХОЖЕ НЕ РАБОТАЕТ
            ->andWhere(['=', 'visitor_genre.genre_id', $genreId])
            ->all();

        //достать visitor.name для поиска посетителя, visitor_genre.genre_id для дальнейшего сравнения с genre.id
        foreach ($dancers as $dancer) {
            $danceNames[] = $dancer->name;
            $danceGenresId[] = $dancer->id;
        }

        //TODO добавить правило, если genre.name = 'romance' не выводить.
        // танцоры соло
        if (!in_array('romance', $genreNames)) {
            foreach ($danceNames as $danceName) {
                $result[] = $danceName;
            }
        }

        // найти всех посетителей, которые танцуют данный жанр, если посетитель М
        $man = Club::find()
            ->select(['visitor.name'])
            ->distinct(['visitor.id'])
            ->innerJoin(Playlist::tableName(), 'playlist.id = club.playlist_id')
            ->innerJoin(PlaylistTrack::tableName(), 'playlist.id = playlist_track.playlist_id')
            ->innerJoin(Track::tableName(), 'playlist_track.track_id = track.id')
            ->innerJoin(Genre::tableName(), 'track.genre_id = genre.id')
            ->innerJoin(Visitor::tableName(), 'club.id = visitor.club_id')
            ->innerJoin(VisitorGenre::tableName(), 'visitor.id = visitor_genre.visitor_id')
            ->where(['club.id' => $id, 'visitor.gender' => 'мужской'])
            ->all();

        // найти всех посетителей, которые танцуют данный жанр, если посетитель Ж
        $woman = Club::find()
            ->select(['visitor.name'])
            ->distinct(['visitor.id'])
            ->innerJoin(Playlist::tableName(), 'playlist.id = club.playlist_id')
            ->innerJoin(PlaylistTrack::tableName(), 'playlist.id = playlist_track.playlist_id')
            ->innerJoin(Track::tableName(), 'playlist_track.track_id = track.id')
            ->innerJoin(Genre::tableName(), 'track.genre_id = genre.id')
            ->innerJoin(Visitor::tableName(), 'club.id = visitor.club_id')
            ->innerJoin(VisitorGenre::tableName(), 'visitor.id = visitor_genre.visitor_id')
            ->where(['club.id' => $id, 'visitor.gender' => 'женский'])
            ->all();

        foreach ($man as $manName) {
            $manNames[] = $manName->name;
        }

        foreach ($woman as $womanName) {
            $womanNames[] = $womanName->name;
        }

        // создать пары
        //TODO если жанр музыки 'romance' до создаются пары

        for ($i = 0; $i <= min(count($manNames), count($womanNames)); $i++) {
            $couples[] = $manNames[$i] . ' + ' . $womanNames[$i];
        }

        return $this->render('dance-floor', compact('club', 'genres', 'dancers', 'couples'));
    }

    public function actionExitVisitor($visitor_id, $club_id)
    {
        $this->findModel($club_id);

        //TODO: переделать запросы, для защиты от SQL инъекций
        // Выходит Visitor
        Yii::$app->db->createCommand(
            "UPDATE visitor JOIN club ON club.id = visitor.club_id SET visitor.company_id = NULL, visitor.club_id = NULL WHERE visitor.id = $visitor_id;")
            ->execute();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionExitCompany($company_id, $club_id)
    {
        $this->findModel($club_id);

        //TODO: переделать запросы, для защиты от SQL инъекций
        // Выходит Company
        Yii::$app->db->createCommand(
            "UPDATE visitor JOIN club ON club.id = visitor.club_id INNER JOIN company ON visitor.company_id = company.id SET visitor.company_id = NULL, visitor.club_id = NULL WHERE company.id = $company_id;")
            ->execute();

        return $this->redirect(Yii::$app->request->referrer);
    }

    //поиск записи в таблице
    protected function findModel($id)
    {
        if (($club = Club::findOne($id)) !== null) {
            return $club;
        } else {
            throw new NotFoundHttpException('Данного клуба не существует');
        }
    }

    // поиск по названию клуба
    public function actionSearch($search)
    {
        if (!$search)
            return $this->render('search');

        $clubs = Club::find()->where(['like', 'name', $search])->all();

        return $this->render('search', compact('clubs', 'search'));
    }
}