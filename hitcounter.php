<?

//если не настроен php
if( ! ini_get('date.timezone') )
   	date_default_timezone_set('GMT');


class hitcounter {

	private $id = 0; // id счетчик
	private $ctime = 86400; // пересчитывать раз в сутки
	private $fcache = '';
	private $find = ''; // что ищем
	private $date = ''; // с какого времени считать
	
	function __construct($id){
		$this->id = $id; // id счетчика
	} 

	function time($value) {
		$this->time = $value;
		return $this;
	}

	function find($find, $date = ''){
		
		$this->find = $find;
		
		if ($date == '')
			$date = date('Y-m-d'); //текущая дата

		$this->date = $date;

		$key = md5('clicks'.$find);
	
		$this->fcache = FPCDIR.'clicks'.DIRECTORY_SEPARATOR.md5($key).'.txt'; //опеределяем файл кеша

		return $this;
	}


	//пробуем взять из кеша
	function total_cache() {

		if (file_exists($this->fcache) && time() - $this->ctime < filemtime($this->fcache))
			return file_get_contents($this->fcache);

		return False;
	}


	function total() {

		if ($total = $this->total_cache())
			return $total;

		$total = $this->mailru($this->find, $this->date);
		file_put_contents($this->fcache, $total);


		return $total;


	}


	private function mailru($find, $date = '') {
	
	
		

		//текущий год и месяц
		$curryear = date('Y');
		$currmonth = date('m');

		//дата создания публикации
		$dt = explode(' ', $date);
		$date = explode('-', $dt[0]);
		$year = $date[0];
		$month = $date[1];

		$total = 0;

		$ctx = stream_context_create(array('http'=>
    		array(
        		'timeout' => 30, 
    		)
		));

		for ($y = $year; $y <= $curryear; $y++) {
    	
    		if ($y == $curryear)
    			$maxm = $currmonth;
    		else	
    			$maxm = 12;
    	
    		//считаем по месяцам
    		for ($m = $month; $m <= $maxm; $m++){
    			$url = 'http://top.mail.ru/json/pages?id='.$this->id.'&period=2&date='.$y.'-'.$m.'-01&pp=20&filter_type=0&filter='.$this->find;
    			$value = file_get_contents($url, false, $ctx);
    			if ($value !== FALSE) {
    				$data = json_decode($value, True);
    				if (isset($data['total']))
    					$total += $data['total'];
    			}	
  	   		}

		} 

		return $total;
	
	}


	
	


}


