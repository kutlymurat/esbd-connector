# esbd-connector
Класс для работы с Единой Страховой Базой Данных (ЕСБД)

Версия 1.0

Реализованы следующие функции:
1) Поиск транспортного средства (ТС) через ID, VIN, гос. номер
2) Поиск клиентов через ИИН, РНН, номер документа и ID
3) Расчет стоимости обязательного страхования
4) Поиск полисов в заданном интервале дат, поиск по глобальному номеру, номеру и ID
5) Вывод информации о ИП/КХ клиента
6) Вывод класса бонус-малус клиента
7) Вывод справочника

Для работы с этим классом, нужны интеграционные ключи от ЕСБД.
Ключи и логин/пароль нужно добавить в EsbdConnect.php в виде ассоциативного массива.

Остальные функции можно подсмотреть в википедии ЕСБД и реализовать: http://wiki.mkb.kz

Тестировался на PHP 5.6 и PHP 7.1

P.S. Я не являюсь сотрудником ЕСБД и не имею отношения к АСБ.