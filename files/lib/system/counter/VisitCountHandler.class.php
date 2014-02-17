<?php
namespace cms\system\counter;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\system\cache\builder\SpiderCacheBuilder;
/**
 * @author	Jens Krumsieck
 * @copyright	2014 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.fireball
 */
 
class VisitCountHandler extends SingletonFactory{
    public $session = null;
    
    public function init(){
        $this->session = WCF::getSession();
    }
    
    protected function canCount(){
        if($this->session->getVar('counted')) return false;
        return true;
    }

    public function count(){
        if($this->canCount()){
            $userID = WCF::getUser()->userID;
            $time = TIME_NOW;
            $ipAddress = $this->session->ipAddress;
            $spider = $this->getSpiderID($this->session->userAgent);
            if($spider === null) $spider = 0;
            $browser = $this->getBrowser($this->session->userAgent);
            $browserName = $browser['name'];
            $browserVersion = $browser['version'];
            $sql = "INSERT INTO cms".WCF_N."_counter VALUES (".$time.",".$userID.",'".$browserName."','".$browserVersion."','".$ipAddress."',".$spider.")";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute(array());
            $this->session->register('counted', true);
        }
    }
    
    public function getMonthlyVisitors($month = 1, $year=2014,$option="all"){
        $start = mktime(0,0,0,$month,1,$year);
        if(in_array($month, array(1,3,5,7,8,10,12))) $end = mktime(0,0,0, $month,31,$year);
        if($month == 2) $end = mktime(0,0,0, $month,28,$year);
        else $end = mktime(0,0,0, $month,30,$year);
        
        if($option=="all"){
            $sql = "SELECT  COUNT(*) AS amount
                FROM    cms".WCF_N."_counter WHERE time BETWEEN ".$start." AND ".$end."";
        }
        elseif($option=="spiders"){
            $sql = "SELECT  COUNT(*) AS amount
                FROM    cms".WCF_N."_counter WHERE spider <> 0 AND time BETWEEN ".$start." AND ".$end."";
        }
        elseif($option=="registered"){
            $sql = "SELECT  COUNT(*) AS amount
                FROM    cms".WCF_N."_counter WHERE userID <> 0 AND time BETWEEN ".$start." AND ".$end."";
        }
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        return $statement->fetchColumn();
    }
    
    public function getDailyVisitors($day = 10, $month = 2, $year=2014, $option="all"){
        $start = mktime(0,0,0,$month,$day,$year);
        $end = mktime(23,59,59, $month,$day,$year);
        if($option=="all"){
            $sql = "SELECT  COUNT(*) AS amount
                FROM    cms".WCF_N."_counter WHERE time BETWEEN ".$start." AND ".$end."";
        }
        elseif($option=="spiders"){
            $sql = "SELECT  COUNT(*) AS amount
                FROM    cms".WCF_N."_counter WHERE spider <> 0 AND time BETWEEN ".$start." AND ".$end."";
        }
        elseif($option=="registered"){
            $sql = "SELECT  COUNT(*) AS amount
                FROM    cms".WCF_N."_counter WHERE userID <> 0 AND time BETWEEN ".$start." AND ".$end."";
        }
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        
        return $statement->fetchColumn();
    }
    
    public function getWeeklyVisitorArray($option="all"){
        $currentMonth = date("n", TIME_NOW);
        $currentYear = date("Y", TIME_NOW);
        $currentDay = date("j", TIME_NOW);
        
        $visitors = array();
        $year = $currentYear;
        $month = $currentMonth;
        $day = $currentDay;
        
        for($i = 1; $i<=7; $i++){

            $months = array(WCF::getLanguage()->get('wcf.date.month.january'),WCF::getLanguage()->get('wcf.date.month.february'),WCF::getLanguage()->get('wcf.date.month.march'),WCF::getLanguage()->get('wcf.date.month.april'),WCF::getLanguage()->get('wcf.date.month.may'),WCF::getLanguage()->get('wcf.date.month.june'),WCF::getLanguage()->get('wcf.date.month.july'),WCF::getLanguage()->get('wcf.date.month.august'),WCF::getLanguage()->get('wcf.date.month.september'),WCF::getLanguage()->get('wcf.date.month.october'),WCF::getLanguage()->get('wcf.date.month.november'),WCF::getLanguage()->get('wcf.date.month.december'));
            $visitors[$i] = array('string' => $day.'. '.$months[$month-1].' '.$year,
                                'visitors' => $this->getDailyVisitors($day, $month, $year,$option));
            $day--;
            if($day == 0){
                $month--;
                if(in_array($month, array(1,3,5,7,8,10,12))) $day = 31;
                if($month == 2) $day = 28;
                else $day = 30;
            }
            if($month == 0) {
                $month = 12; $year = $currentYear - 1;
            }
        }
        return array_reverse($visitors);
    }
    
    public function getBrowserArray(){
        $sql = "SELECT COUNT(*) AS amount, browser FROM cms".WCF_N."_counter GROUP BY browser"; 
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        
        return $statement->fetchArray();
        
    }
    public function getYearlyVisitorArray($option="all"){
        $currentMonth = date("n", TIME_NOW);
        $currentYear = date("Y", TIME_NOW);
        
        $visitors = array();
        $year = $currentYear;
        $month = $currentMonth;
        
        for($i = 1; $i<=12; $i++){

            $months = array(WCF::getLanguage()->get('wcf.date.month.january'),WCF::getLanguage()->get('wcf.date.month.february'),WCF::getLanguage()->get('wcf.date.month.march'),WCF::getLanguage()->get('wcf.date.month.april'),WCF::getLanguage()->get('wcf.date.month.may'),WCF::getLanguage()->get('wcf.date.month.june'),WCF::getLanguage()->get('wcf.date.month.july'),WCF::getLanguage()->get('wcf.date.month.august'),WCF::getLanguage()->get('wcf.date.month.september'),WCF::getLanguage()->get('wcf.date.month.october'),WCF::getLanguage()->get('wcf.date.month.november'),WCF::getLanguage()->get('wcf.date.month.december'));
            $visitors[$i] = array('string' => $months[$month-1].' '.$year,
                                'visitors' => $this->getMonthlyVisitors($month, $year,$option));
            $month--;
            if($month == 0) {
                $month = 12; $year = $currentYear - 1;
            }
        }
        return array_reverse($visitors);
        
    }
    
    public function getBrowser($u_agent = '') { 
        if($u_agent == '') return array(
                                        'userAgent' => '',
                                        'name'      => 'unknown',
                                        'version'   => '?',
                                        'platform'  => '',
                                        'pattern'    => '');
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
    
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        }
        else{$ub='';}
    
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
    
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= isset($matches['version'][1])? $matches['version'][1] : "";
            }
        }
        else {
            $version= $matches['version'][0];
        }
    
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
    
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }
    
    
    protected function getSpiderID($userAgent) {
		$spiderList = SpiderCacheBuilder::getInstance()->getData();
		$userAgent = strtolower($userAgent);
		
		foreach ($spiderList as $spider) {
			if (strpos($userAgent, $spider->spiderIdentifier) !== false) {
				return $spider->spiderID;
			}
		}
		
		return null;
	}
}