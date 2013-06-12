<?php
class CSV { 
	protected $data; 
	private $delimiter;   
	
	/** 
	 * @params array $columns 
	 * @returns void 
	 */ 
	public function __construct($columns, $delimiter = ",") { 
		$this->delimiter = $delimiter;   
		
		foreach($columns as &$column) 
			$column = $this->escapeField($column); 
		$this->data = implode($delimiter, $columns) . "\n"; 
	}   
	
	private static function escapeField($text) { 
		if(strstr($text, '"')) 
			return '"' . str_replace('"', '""', $text) . '"'; 
		return $text; 
	}   
	
	/** 
	 * @params array $row 
	 * @returns void 
	*/ 
	public function addRow($row) { 
		foreach($row as &$column) 
			$column = $this->escapeField($column); 
		$this->data .= implode($this->delimiter, $row) . "\n"; 
	}   
	
	/** 
	 * @returns void 
	 */
	public function export($filename) { 
		if (file_exists($filename . '.csv')) {
			echo "here";
			$fp = fopen($filename . '.csv', 'a');
			var_dump($this->data);
			fputcsv($fp, $this->data, ',');
			fclose($fp);
		} else {
			header('Content-type: text/csv'); 
			header('Content-Disposition: attachment; filename="' . $filename . '.csv"'); 
			echo $this->data; 
		}

		die(); 
	}   
	
	public function __toString() { 
		return $this->data; 
	} 
}

$stats = file_get_contents('http://url');
$e_stats = explode("/", $stats);
$csv = new CSV(array('Date', 'Time', 'Stream', 'Listeners'));
$filename = 'file';
//$fp = fopen($filename, 'w');
foreach ($e_stats as $stat) {
	if (strpos($stat,',,,') !== false) {
		//echo $stat;
		$start = strpos($stat,',,,') + 3;
		$end = strpos($stat,',,',$start);
		$type = substr($stat,0,$start-3);
		$val = substr($stat,$start,($end-$start));
		//$fields = date('m/d/Y') . ',' . date('H:i:s') . ',' . $type . ',' . $val;
		$fields = array(date('m/d/Y'),date('H:i:s'),$type,$val);
		//fputcsv($fp, $fields);
		$csv->addRow($fields);
	}
}
//fclose($fp);
$csv->export($filename);
$string = $csv;
var_dump($string);
?>