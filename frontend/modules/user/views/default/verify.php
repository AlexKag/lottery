<h2>Верификация</h2>

<div class="white-block white-block--notice">
    <p>
        Если ваш счет еще не&nbsp;верифицирован, вы&nbsp;используете лишь&nbsp;60% возможностей, которых вам представляет <?=Yii::$app->name ?>. Верификация не&nbsp;является обязательной процедурой, но&nbsp;даст возможность воспользоваться полным спектром услуг нашего сервиса.
    </p>
    <p>
        Процедура верификации является достаточно простой, все, что вам необходимо&nbsp;&mdash; загрузить копию вашего паспорта или любого другого документа, подтверждающего вашу личность (на&nbsp;данной странице).
    </p>
</div>

<form class="form-file">
    <label for="document">Загрузить документ</label><input id="document" type="text" value="" name="" placeholder="Ваш файл" disabled>
    <button class="form-file__button" type="submit">Отправить</button>

    <div class="form-file__line"></div>

    <div class="form-file__list">
        Уже загруженный файлы

        <ul>
            <li>moj-pasport-2015.jpg <a href="#" title=""></a></li>
            <li>обратная-сторона-паспорта.jpg <a href="#" title=""></a></li>
        </ul>
    </div>
</form>
