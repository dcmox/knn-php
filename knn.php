<?php 
/* 	
	@author Daniel Moxon 
	@desc 	Classifies X based on k-nearest points 
*/

class kNN
{
	public $k = 3;
	public $pt = array();
	public $data = array();
	public $results = array();
	public $ignore = array();
	
	public function __construct($data, $pt, $k=3, $ignore) 
	{
		$this->k 	= $k;
		$this->data = $data;
		$this->pt 	= $pt;
		if (is_array($ignore))
			$this->ignore = $ignore; // ignore these indexes 
	}

	
	/* Calculate/sort distances, store K results */
	public function calculate()
	{
		$this->results = array();
		foreach ($this->data as $a)
		{
			$this->results[] = array('distance' => $this->euclidianDistance($a, $this->pt), 
									'result' => $a);
		}

		usort($this->results, function($a, $b){
			if ($a['distance'] == $b['distance'])
				return 0;
			return $a['distance'] < $b['distance'] ? -1 : 1;
		});
	
		$this->results = array_slice($this->results, 0, $this->k);
			
		return $this->results;
	}

	/* Classify index of single data point from dataset */
	public function classify($index)
	{
		$tmp = array();

		foreach ($this->results as $result)
		{
			
			if (isset($tmp[$result['result'][$index]]))
				$tmp[$result['result'][$index]]['frequency']++;
			else
				$tmp[$result['result'][$index]] = array('frequency'=>1, 'result'=>$result['result'][$index]);
		}

		usort($tmp, function($a,$b){
			if ($a['frequency'] == $b['frequency'])
				return 0;
			return $a['frequency'] < $b['frequency'] ? 1 : -1;
		});

		return $tmp[0]['result'];
	}
	
	/* used for categorical distance */
	public function hammingDistance($a, $b)
	{
		if (count($a) != count($b))
			return false;
		
		$d = 0;
		
		$i = count($a);
		while($i--)
			if ($a[$i] != $b[$i])
				$d++;
		
		return $d;
	}
	
	/* calculate euclidianDistance to N dimensions */
	public function euclidianDistance($a, $b)
	{
		if (count($a) != count($b))
			return false;
	
		$sum = 0;
		for ($i = 0; $i < count($a); $i++)
		{
			if (in_array($i, $this->ignore))
			{
				
				// dont calculate
			}
			else
				$sum += pow($a[$i]-$b[$i],2);
		}
		
		return sqrt($sum);
	}
	
	/* normalizes the values to be proportional to the data */
	public function standardize()
	{
		for ($i = 0; $i < count($v); $i++)
		{
			$min = min(array_column($this->data, $i));
			$max = max(array_column($this->data, $i));
			
			foreach ($this->data as $k => $v)
			{ 
				if (is_numeric($v))
				{
					$this->data[$k][$i] = ($this->data[$k][$i] - $min) / ($max - $min);
				}
				else
				{
					// do nothing
				}
			}
			if (is_numeric($this->pt[$i]))
				$this->pt[$i] = ($this->pt[$i] - $min) / ($max - $min);
			else
			{
				// do nothing
			}
		}
	}
}
