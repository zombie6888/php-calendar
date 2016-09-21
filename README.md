# PHP Calendar

Календарь на месяц на php

Использование:

  ```
  require_once __DIR__ . '/calendar.php'
  ```

Показать календарь на Август, 2016:

  ```php
  $calendar = new BaseCalendar(2016, 8)
  echo $calendar->render();
  ```

Отобразить календарь за прошлый месяц:

  ```php
  $calendar = new BaseCalendar()
  echo $calendar->setPreviousMonth()->render();
  ```

Показать календарь без создания экземпляра:

  ```php
  //Установить шаблон
  $template = __DIR__ . '/default_view.php';
  //Объект DateTime как параметр
  $date = new DateTime();

  echo BaseCalendar::renderCalendar(array(
      'date'=> $date->modify('-2 month'),
      'template' => $template
  ));
  ```
