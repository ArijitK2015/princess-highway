<?php
class Xigmapro_Jobs_Helper_Data extends Mage_Core_Helper_Abstract {

	static $jobStatus = array(
		0 	=> "Part Time",
		1 	=> "Full Time",
		2	=> "Casual",
		3	=> "Contract"
	);

	public function getJobStatus($id) {
		$status = self::$jobStatus[0];
		if (isset(self::$jobStatus[$id])) {
			$status = self::$jobStatus[$id];
		}
		return $status;
	}
 
}

?>