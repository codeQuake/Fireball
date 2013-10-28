<?php
namespace cms\data\news;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;
use wcf\util\UserUtil;
use wcf\system\language\LanguageFactory;

class NewsAction extends AbstractDatabaseObjectAction{

    protected $className = 'cms\data\news\NewsEditor';
    protected $permissionsDelete = array('mod.cms.news.canModerateNews');
    
    public $news = null;
    
    public function create(){
        $data = $this->parameters['data'];
        if (LOG_IP_ADDRESS) {
            // add ip address
            if (!isset($data['ipAddress'])) {
                $data['ipAddress'] = WCF::getSession()->ipAddress;
            }
        }
        else {
            // do not track ip address
            if (isset($data['ipAddress'])) {
                unset($data['ipAddress']);
            }
        }
        
        $news = call_user_func(array($this->className,'create'), $data);
        $newsEditor = new NewsEditor($news);
		
		// handle categories
		$newsEditor->updateCategoryIDs($this->parameters['categoryIDs']);
		$newsEditor->setCategoryIDs($this->parameters['categoryIDs']);
        
        //langID != 0
        $languageID = (!isset($this->parameters['data']['languageID']) || ($this->parameters['data']['languageID'] === null)) ? LanguageFactory::getInstance()->getDefaultLanguageID() : $this->parameters['data']['languageID'];
        
        return $news;
        
    }
    
    public function validateGetIpLog() {
		if (!LOG_IP_ADDRESS) {
			throw new PermissionDeniedException();
		}
		
		if (isset($this->parameters['newsID'])) {
			$this->news = new News($this->parameters['newsID']);
		}
		if ($this->news === null || !$this->news->newsID) {
			throw new UserInputException('newsID');
		}
		
		if (!$this->news->canRead()) {
			throw new PermissionDeniedException();
		}
	}
	
    
    public function getIpLog() {
		// get ip addresses of the author
		$authorIpAddresses = News::getIpAddressByAuthor($this->news->userID, $this->news->username, $this->news->ipAddress);
		
		// resolve hostnames
		$newIpAddresses = array();
		foreach ($authorIpAddresses as $ipAddress) {
			$ipAddress = UserUtil::convertIPv6To4($ipAddress);
			
			$newIpAddresses[] = array(
				'hostname' => @gethostbyaddr($ipAddress),
				'ipAddress' => $ipAddress
			);
		}
		$authorIpAddresses = $newIpAddresses;
		
		// get other users of this ip address
		$otherUsers = array();
		if ($this->news->ipAddress) {
			$otherUsers = News::getAuthorByIpAddress($this->news->ipAddress, $this->news->userID, $this->news->username);
		}
		
		$ipAddress = UserUtil::convertIPv6To4($this->news->ipAddress);
		
		if ($this->news->userID) {
			$sql = "SELECT	registrationIpAddress
				FROM	wcf".WCF_N."_user
				WHERE	userID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(
				$this->news->userID
			));
			$row = $statement->fetchArray();
			
			if ($row !== false && $row['registrationIpAddress']) {
				$registrationIpAddress = UserUtil::convertIPv6To4($row['registrationIpAddress']);
				WCF::getTPL()->assign(array(
					'registrationIpAddress' => array(
						'hostname' => @gethostbyaddr($registrationIpAddress),
						'ipAddress' => $registrationIpAddress
					)
				));
			}
		}
		
		WCF::getTPL()->assign(array(
			'authorIpAddresses' => $authorIpAddresses,
			'ipAddress' => array(
				'hostname' => @gethostbyaddr($ipAddress),
				'ipAddress' => $ipAddress
			),
			'otherUsers' => $otherUsers,
			'news' => $this->news
		));
		
		return array(
			'newsID' => $this->news->newsID,
			'template' => WCF::getTPL()->fetch('newsIpAddress', 'cms')
		);
	}
}