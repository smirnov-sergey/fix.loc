# Тестовое задание: Танцы. 

## Описание
Реализация поиска танцующих гостей клуба.

## Требование
- Создать новый клуб (или несколько)
- В клубе должен быть свой “плейлист” музыки, в который можно добавить/убирать музыку
- В клуб могут входить/выходить посетители
- Посетители могут входить и выходить как группами так и по отдельности
- Посетители умеют танцевать только определенные жанры музыки(один жанр/несколько/ни одного)
- Если начинает играть музыка (клуб включает плейлист), посетители начинают танцевать
- Но только те, кто умеет танцевать этот жанр
- Если включается романтическая музыка, посетители должны найти себе пару (м+ж) и танцевать парами
- Все кому не хватило пары создают группу и танцуют вместе
- Все кто не умеют танцевать - стоят
- Музыку/танцую/не танцую/кто с кем в паре - делать просто через echo/print. 


## Пример использования
- создать клуб
- добавить музыку
- создать/добавить посетителей
- запустить плейлист в клубе
- смотреть вывод


## Что используется
-  Yii2
-  OpenServer
-  DB phpMyAdmin
-  MySQL


## Рекомендации по запуску
-  Для работы понадобится база данных "fix.loc".
-  Использовать веб-интерфейс "phpMyAdmin" и веб-сервер "Open Server".

### Вариант 1.
-  Импортировать БД: БД\fix.loc.sql
-  Перейти на http://localhost:8080/web/.
-  Зарегистрироваться (default):
-  'username' => 'root'
-  'password' => ''

### Вариант 2.
-  В "phpMyAdmin" создать БД "fix.loc"
-  В консоли "open server" под текущим проектом выполнить миграции: yii migrate 
-  Перейти на http://localhost:8080/web/.
-  Зарегистрироваться (default):
-  'username' => 'root'
-  'password' => ''