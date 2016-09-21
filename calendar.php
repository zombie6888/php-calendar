<?php
/**
*  Author: Zhukov Sergey.
*  Email: zom688@gmail.com
*  Website: http://websiteprog.ru *
*
*  Php Calendar
*  Usage:
*
*  Show calendar August, 2016:
*
*      $calendar = new BaseCalendar(2016, 8)
*      echo $calendar->render();
*
*  Show calendar previous month:
*
*      $calendar = new BaseCalendar()
*      echo $calendar->setPreviousMonth()->render();
*
*  Show calendar without instantiate
*
*      //set calendar template
*      $template = __DIR__ . '/default_view.php';
*      //can pass datetime object as parameter
*      $date = new DateTime();
*
*      echo BaseCalendar::renderCalendar(array(
*          'date'=> $date->modify('-2 month'),
*          'template' => $template
*      ));
*/

class BaseCalendar
{
    //TODO remove $_day?
	protected $_day = null;
    protected $_month = null;
    protected $_year = null;
    
    protected $_monthNames;
	protected $_weekNames;
	protected $_events;

	protected $_template = 'default_view.php';

	/**
	 * @param $date
	 * @return $this
	 */
	public function setDate($date)
	{
		 if($date instanceof DateTime)
		 {
			 $this->year = $date->format('Y');
			 $this->month = $date->format('n');
			 $this->day = $date->format('j');
		 }
		 elseif(is_array($date))
		 {
			 $this->year = $date['year'];
			 $this->month = $date['month'];
			 $this->day = $date['day'];
		 }
		 return $this;
	} 
	
	public function __get($name) {
		$name = 'get'. ucfirst($name);
		return $this->$name();
	}

	public function __set($name, $value) {
		$name = 'set'. ucfirst($name);
		$this->$name($value);
	}	

	public function __construct($year = '', $month = '', $day = '', $options = array())
	{
		$this->year = $year ? $year : (int)date('Y');
		$this->month = $month ? $month : (int)date('n');

		if($day) {
		 	$this->day = $day;
		} else {
			//For the current year and month default day of month is current day, otherwise it is the first day of month
			$this->day = ($this->year == (int)date('Y') && $this->month == (int)date('n')) ? (int)date('j') : 1;
		}
		
		if (isset($options['monthNames'])) {
			$this->_monthNames = $options['monthNames'];
		} else {
			$this->_monthNames = array(
				'Январь',
				'Февраль',
				'Март',
				'Апрель',
				'Май',
				'Июнь',
				'Июль',
				'Август',
				'Сентябрь',
				'Октябрь',
				'Ноябрь',
				'Декабрь',
			);
		}
		
		if (isset($options['weekhNames'])) {
			$this->_weekNames = $options['monthNames'];
		} else {
			$this->_weekNames = array(
				'Понедельник',
				'Вторник',
				'Среда',
				'Четверг',
				'Пятница',
				'Суббота',
				'Воскресенье'
			);
		}

		if (isset($options['events'])) {
			$this->_events = $options['events'];
		}
	}
    
	public function getYear()
	{
		return $this->_year;
	}
    
	public function getMonth()
	{
		return $this->_month;
	}
    
	public function getDay()
	{
        return $this->_day;
	}

	public function getMonthNames()
	{
		return $this->_monthNames;
	}

	public function setMonthNames($monthNames)
	{
		$this->_monthNames = $monthNames;
		return $this;
	}

	public function getWeekNames()
	{
		return $this->_weekNames;
	}

	public function setWeekNames($weekNames)
	{
		$this->_weekNames = $weekNames;
	}

	public function getMonthName($month = false)
	{
		if ($month) {
			return $this->_monthNames[$month - 1];
		} else {
			return $this->_monthNames[$this->month - 1];
		}
	}

	public function setYear($value)
	{
        if(is_numeric($value) && $value > 0) {
            $this->_year = $value;
        } else {
            throw new Exception('Неверное занчения для года');
        }        
	}
    
	public function setMonth($value)
	{
        if(is_numeric($value) && $value >= 1 && $value <= 12)
		{
            $this->_month = $value;
            if(!$this->dayIsInCurrentMonth($this->day))
			{
                $this->day = $this->getDaysInCurrentMonth();
            }
         } else {
            throw new Exception('Неверное значение для месяца. Должно быть между 1 и 12.');
         }
	}
    
	public function setDay($value)
	{
         if(is_numeric($value))
		 {
            if($this->dayIsInCurrentMonth($value)) {
                $this->_day = $value;
            } else {
                throw new Exception("Неверное значение дня. Для эттого месяца значение должно быть между 1 и {$this->getDaysInCurrentMonth()}");
            }
        } else {
            throw new Exception('Неверное значение для дня');
        }
	}
	
	public function setEvents($events)
	{
		$this->_events = $events;
	}
	
    
	public function getTimestamp()
	{
		$date = new DateTime("{$this->year}-{$this->month}-{$this->day}");
		return $date->getTimestamp();
	}
    
	public function getFirstDayOfTheWeek()
	{
		$date = new DateTime("{$this->year}-{$this->month}-1");        
		$weekDayNumber = date('w', $date->getTimestamp());		
		$firstDayOfWeek = ($weekDayNumber == 0) ? 7 : $weekDayNumber;	   
		return $firstDayOfWeek;
	}
    
	public function getDaysInCurrentMonth() 
	{
        return $this->getDaysInMonth($this->month, $this->year);
	}
    
	public function getDaysInMonth($month, $year) 
	{
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
	}
    
	public function getPreviousMonth($count=1)
	{
		$date = array();		
		if ($count > 12) 
			throw new Exception('Слишком большое количество предшедствующих месяцев');
		if($this->month - $count <= 0) {
			$date['year'] = $this->year  - 1;
			$date['month'] = 12 - $count + $this->month;
		} else {
			$date['month'] = $this->month - $count;
			$date['year'] = $this->year;
		}
		return $date;
	}
	
	public function getNextMonth($count = 1)
	{
		$date = array();
		if ($count > 12) 
			throw new Exception('Слишком большое количество предшедствующих месяцев');	
		if($this->month + $count > 12) {
			$date['month'] = $count + $this->month - 12;
			$date['year'] = $this->year + 1;
		} else {
			$date['month'] = $this->month + $count;
			$date['year'] = $this->year;
		}
		return $date;
	}    

	public function setPreviousMonth($count = 1, $from_year = '', $from_month = '')
	{
		if ($from_year) {
			$this->year = $from_year;
		}
		if ($from_month) {
			$this->month = $from_month;
		}
			
		$date = $this->getPreviousMonth($count);
		$this->year = $date['year'];
		$this->month = $date['month'];
		return $this;
	}

	public function setNextMonth($count = 1, $from_year = '', $from_month = '')
	{
		if ($from_year) {
			$this->year = $from_year;
		}
		if ($from_month) {
			$this->month = $from_month;
		}
		
		$date = $this->getNextMonth($count);
		$this->year = $date['year'];
		$this->month = $date['month'];
		return $this;
	}   
	
		    
	private function dayIsInCurrentMonth($day) 
	{
        return $day >= 1 && $day <= $this->daysInCurrentMonth;
	}	
		
	public function haveDayEvents($day, $events = array())
	{
		$events = !empty($events) ? $events : $this->_events;
		
		if (!empty($events)) {
			foreach($events as $event)
			{
				if (isset($event['date']))
				{
					$eventDate = $event['date'];					
					if($eventDate['day'] == $day && $eventDate['month'] == $this->month && $eventDate['year'] == $this->year) {
						return true;
					}
				}	
			}
		}	
		return false;
	}
	
	public function isCurrentDay($day)
	{		
		return $day == (int)date('j') && $this->month == (int)date('n') && $this->year == (int)date('Y');
	}		 
		
	public function getClassOfDay($day, $events = array())
	{
		$class = '';
		$isCurrentDay = $this->isCurrentDay($day);
		$haveDayEvents = $this->haveDayEvents($day, $events);

		if($isCurrentDay || $haveDayEvents)
		{
			if($isCurrentDay && $haveDayEvents) {
				$class = 'class="current-day with-events"';
			} elseif($isCurrentDay) {
				$class = 'class="current-day"';
			} else {
				$class = 'class="with-events"';
			}
		}
		return $class;	
	}
	
	public function getEventDay($day)
	{
		//return "<a href='#'>$day</a>";
		return $day;
	}	
	
	public function getTableBody()
	{
		$html = '<tr>';
		$daysStarted = false; 
		$day = 1;		
		
		for($i = 1; $i < $this->daysInCurrentMonth + $this->firstDayOfTheWeek; $i++)
		{
			if(!$daysStarted) {
				$daysStarted = ($i == $this->firstDayOfTheWeek);
			}
				
			$html .=  '<td '. $this->getClassOfDay($day) . ' >';

			if ($daysStarted && $day <= $this->daysInCurrentMonth)
			{
				if ($this->haveDayEvents($day)) {
					$html .= $this->getEventDay($day);
				} else {
					$html .= $day;
				}
				$day++;	
			}

			$html .= '</td>';

			if ($i % 7 == 0) {
				$html .= '</tr><tr>';
			}
		}
		return $html;
	}

	/**
	 * @param string $file
	 * @return string
	 */
	public function render($file = '')
	{
		if($file) {
			$view = is_file($file) ? $file : dirname(__FILE__) .'/'. $file;
		} else {
			$view = dirname(__FILE__) .'/'. $this->_template;
		}

		$out = '';

		//TODO add exception

		if(is_file($view)) {

			ob_start();
			require_once $view;
			$out = ob_get_clean();
		}
		return $out;
	}

	public function setTemplate($view)
	{
		$this->_template = $view;
		return $this;
	}

	public function getTemplate()
	{
		return $this->_template;
	}
	
	public static function renderCalendar($options=array())
	{
		$year = isset($options['year']) ? $options['year'] : (int)date('Y');
		$month = isset($options['month']) ? $options['month'] : (int)date('n');
		$day = isset($options['day']) ? $options['day'] : '';

		$calendar = new self($year, $month, $day, $options);
		$file = isset($options['template']) ? $options['template'] : '';

		if(isset($options['date'])) {
			$calendar->setTemplate($options['date']);
		}

		$html = $calendar->render($file);
		return $html;
	}	
}