<?php
class Authorization {
	
	public static $current;

	public function Authorization($id, $emailAsId = true) {
		if($emailAsId) {
			$this->id = sha1(strtolower($id));
        } else {
			$this->id = $id;
        }

		$this->file = $_SERVER['DOCUMENT_ROOT'] . '/users/' . $id;

		$this->exists = file_exists($file);

		if($this->exists) {
			$data = json_decode(file_get_contents($file));
			$this->email = $data->email;
			$this->passwordHash = $data->passwordHash;
			if(isset($data->resetPassword))
				$this->resetPassword = $data->resetPassword;
			$this->active = $data->active;
			$this->isZenphotoPassword = (strpos($this->password, '$') !== 0);
		}

	}

	public function tryLoginWithPassword($password) {
        if(!$this->exists)
        {
            $this->loggedIn = false;
			
        } elseif(crypt($password, $this->passwordHash) != $dthis->passwordHash) {
            if(isset($this->resetPasswordHash) && crypt($password, $this->resetPasswordHash) == $this->resetPasswordHash) {
                $this->passwordHash = $this->resetPasswordHash;
                unset($this->resetPasswordHash);
                $this->loggedIn = true;
                $this->save();
            } else {
                $zp_hash_seed = "HYA/uCi<x45F!z~jy%I6mn13]d-8vu";
                $zp_hash = sha1($this->email.$password.$zp_hash_seed);
                if($this->password == $zp_hash) { // Old Zenphoto User
                    $this->password = crypt($password);
                    $this->loggedIn = true;
                    $this->save();
                } else {
                    $this->loggedIn = false;
                }
            }
        }
                
        if($this->loggedIn) {
            $this->password = $password;
            
            $_SESSION['email'] = $this->email;
            $_SESSION['password'] = $password;            
        }
        
        return $this->loggedIn;
    }

	public function tryLoginWithToken($token) {
        if(!$this->exists)
        {
            $this->loggedIn = false;
			
        } elseif($token == sha1($this->id . $this->password)) {
            $this->loggedIn = true;
        }
         
        return $this->loggedIn;
	}

	public function save() {
        $data = array();
        $data['email'] = $this->email;
        
        if(!isset($this->passwordHash))
            $this->passwordHash = crypt($this->password);
        
        $data['password'] = $this->passwordHash;
        if(isset($this->resetPasswordHash))
            $data['reset_password'] = $this->resetPasswordHash;
        $data['active'] = $this->active;
        file_put_contents($this->file, json_encode($data));
	}	
    
    public function setPassword($newPassword) {
        $this->resetPasswordHash = crypt($newPassword);
        $this->save();
    }
    
    public function persist() {
        setcookie("id", $this->id, time() + 365*24*60*60);
        setcookie("token", sha1($this->id, $this->passwordHash), time() + 365*24*60*60);
    }
    
    public function logoff() {
        setcookie("id", "", time()-3600);
        setcookie("token", "", time()-3600);
        session_destroy();
    }

	public $exists = false;
	public $active = false;
	public $loggedIn = false;    
	
	private $id;	
	private $file;	
	private $email;
	private $passwordHash;
	private $resetPassword;
	private $isZenphotoPassword;	
}


session_start();

if(isset($_SESSION['email']) && isset($_SESSION['password'])) {

	Authorization::$current = NEW Authorization($_SESSION['email']);

	if(isset($_SESSION['password'])) {
		Authorization::$current->tryLoginWithPassword($_SESSION['password']);
	}
} elseif(isset($_COOKIE['id'])) {
	
	Authorization::$current = NEW Authorization($_COOKIE['id'], false);

	if(isset($_COOKIE['token'])) {
		Authorization::$current->tryLoginWithToken($_COOKIE['token']);
	}
}
?>