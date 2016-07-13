<?php
use Entity\UserController;

class UniFi
{
    public $user="";
    public $password="";
    public $site="";
    public $baseurl="";
    public $controller=""; //vesrion
    public $is_loggedin=false;
    private $cookies="/tmp/unifi";
    public $debug=0;

    function __construct(UserController $controller) {
        $this->user = $controller->getUsername();
        $this->password = $controller->getPassword();
        $this->baseurl = $controller->getBaseUrl();
        $this->site = $controller->getSite();
        $this->controller = $controller->getVersion();
    }

    function __destruct() {
        if ($this->is_loggedin) {
            $this->logout();
        }
    }

    function write_array($array) {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

    /*
    Login to Unifi Controller
    */
    public function login() {
        $this->cookies="";
        $ch=$this->get_curl_obj();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        if(intval($this->controller) >= 4) {
            //Controller 4
            curl_setopt($ch, CURLOPT_REFERER, $this->baseurl."/login");
            curl_setopt($ch, CURLOPT_URL, $this->baseurl."/api/login");
            curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode(array("username" => $this->user, "password" => $this->password)).":");
        } else {
            //Controller 3
            curl_setopt($ch, CURLOPT_URL, $this->baseurl."/login");
            curl_setopt($ch, CURLOPT_POSTFIELDS,"login=login&username=".$this->user."&password=".$this->password);
        }
        if ($this->debug == true) {
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        }
        $content=curl_exec($ch);
        if ($this->debug == true) {
            print "<pre>";
            print "-----LOGIN-------------\n";
            print_r (curl_getinfo($ch));
            // print "\n<br>\n---------------------\n<br>\n";
            // print $url."\n<br>\n";
            // print $data."\n<br>\n";
            // print "---------------------\n<br>\n";
            print $content."\n<br>\n";
            print "---------------------\n<br>\n";
            print "</pre>";
        }
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = trim(substr($content, $header_size));
        $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);

        if(curl_exec($ch) === false) {
            error_log('curl error: ' . curl_error($ch));
        }
        curl_close ($ch);
        preg_match_all('|Set-Cookie: (.*);|U', substr($content, 0, $header_size), $results);

        if (isset($results[1])) {
            $this->cookies = implode(';', $results[1]);
            if (!empty($body)) {
                if (($code >= 200) && ($code < 400)) {
                    //  if (strpos($this->cookies,"unifises") !== FALSE) {
                    $this->is_loggedin=true;
                    //  }
                }
                if($code === 400) {
                    error_log('we have received an HTTP response status: 400. Probably a controller login failure');
                }
            }
        }
        return $this->is_loggedin;
    }
    /*
    Logout from Unifi Controller
    */
    public function logout() {
        if (!$this->is_loggedin) return false;
        $return=false;
        $content=$this->exec_curl($this->baseurl."/logout");
        $this->is_loggedin=false;
        $this->cookies="";
        return $return;
    }
    /*
    Authorize a MAC address
    parameter <MAC address>,<minutes until expires from now>
    return true on success
    */
    public function authorize_guest($mac,$minutes) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'authorize-guest', 'mac' => $mac, 'minutes' => $minutes));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/stamgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    unauthorize a MAC address
    parameter <MAC address>
    return true on success
    */
    public function unauthorize_guest($mac) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'unauthorize-guest', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/stamgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    reconnect a client
    parameter <MAC address>
    return true on success
    */
    public function reconnect_sta($mac) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'kick-sta', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/stamgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    block a client
    parameter <MAC address>
    return true on success
    */
    public function block_sta($mac) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'block-sta', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/stamgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    unblock a client
    parameter <MAC address>
    return true on success
    */
    public function unblock_sta($mac) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'unblock-sta', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/stamgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    hourly stats method for a site
    parameter <start>
    parameter <end>
    defaults to the past 7*24 hours
    */
    public function stat_hourly_site($start = NULL, $end = NULL) {
        $return=array();
        if (!$this->is_loggedin) return false;
        $return=array();
        $end = is_null($end) ? ((time())*1000) : $end;
        $start = is_null($start) ? $end-604800000 : $start;
        $json=json_encode(array('attrs' => array('bytes', 'num_sta', 'time'), 'start' => $start, 'end' => $end));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/report/hourly.site","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $test) {
                        $return[]=$test;
                    }
                }
            }
        }
        return $return;
    }
    /*
    hourly stats method for all access points
    parameter <start>
    parameter <end>
    defaults to the past 7*24 hours, but unifi controller does not
    keep these stats longer than 5 hours (controller v 4.6.6)
    */
    public function stat_hourly_aps($start = NULL, $end = NULL) {
        $return=array();
        if (!$this->is_loggedin) return false;
        $return=array();
        $end = is_null($end) ? ((time())*1000) : $end;
        $start = is_null($start) ? $end-604800000 : $start;
        $json=json_encode(array('attrs' => array('bytes', 'num_sta', 'time'), 'start' => $start, 'end' => $end));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/report/hourly.ap","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $test) {
                        $return[]=$test;
                    }
                }
            }
        }
        return $return;
    }
    /*
    show all login sessions
    parameter <start>
    parameter <end>
    default start value is 7 days ago
    */
    public function stat_sessions($start = NULL, $end = NULL) {
        $return=array();
        if (!$this->is_loggedin) return false;
        $return=array();
        //$end = is_null($end) ? ((time()-(time() % 3600))) : $end; // why did we need this floor calc?
        $end = is_null($end) ? time() : $end;
        $start = is_null($start) ? $end-604800 : $start;
        $json=json_encode(array('type'=> 'all', 'start' => $start, 'end' => $end));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/session","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $session) {
                        $return[]=$session;
                    }
                }
            }
        }
        return $return;
    }
    /*
    show all authorizations
    parameter <start>
    parameter <end>
    defaults to the past 7*24 hours
    */
    public function stat_auths($start = NULL, $end = NULL) {
        $return=array();
        if (!$this->is_loggedin) return false;
        $return=array();
        //$end = is_null($end) ? ((time()-(time() % 3600))) : $end; // why did we need this floor calc?
        $end = is_null($end) ? time() : $end;
        $start = is_null($start) ? $end-604800 : $start;
        $json=json_encode(array('start' => $start, 'end' => $end));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/authorization","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $auth) {
                        $return[]=$auth;
                    }
                }
            }
        }
        return $return;
    }
    /*
    daily stats method
    parameter <start>
    parameter <end>
    defaults to the past 30*7*24 hours
    */
    public function stat_daily_site($start = NULL, $end = NULL) {
        $return=array();
        if (!$this->is_loggedin) return false;
        $return=array();
        $end = is_null($end) ? ((time()-(time() % 3600))*1000) : $end;
        $start = is_null($start) ? $end-18144000000 : $start;
        $json=json_encode(array('attrs' => array('bytes', 'num_sta', 'time'), 'start' => $start, 'end' => $end));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/report/daily.site","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $test) {
                        $return[]=$test;
                    }
                }
            }
        }
        return $return;
    }

    /*
    get details of all clients ever connected to the site
    json parameters {type: "all", conn: "all", within: "24"}
    Note: within only selects the clients that were online in that period.
    Stats per client remain the same
    */
    public function stat_allusers($historyhours = NULL) {
        $return=array();
        if (!$this->is_loggedin) return false;
        $return=array();
        // default to 1 year of history
        $historyhours = is_null($historyhours) ? 8760 : $historyhours;
        $json=json_encode(array('type' => 'all', 'conn' => 'all', 'within' => $historyhours));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/alluser","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $stats) {
                        $return[]=$stats;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list guests
    returns a array of guest objects
    */
    public function list_guests() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/guest","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $guest) {
                        $return[]=$guest;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list clients
    returns a array of client objects
    */
    public function list_clients() {
        $return=array();
        if (!$this->is_loggedin) {
            echo "not logged in?";
            return $return;
        }
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/sta","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $client) {
                        $return[]=$client;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list health metrics
    returns a array of health metric objects
    */
    public function list_health() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/health","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $health) {
                        $return[]=$health;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list users
    returns a array of known user objects
    */
    public function list_users() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/list/user","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $user) {
                        $return[]=$user;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list access points
    returns a array of known access point objects
    */
    public function list_aps() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/device","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $ap) {
                        $return[]=$ap;
                    }
                }
            }
        }
        return $return;
    }
    public function list_devices($adoptedsite) {
        $return=array();
        if (!$this->is_loggedin) { echo "logged in"; return $return; }
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$adoptedsite."/stat/device","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $ap) {
                        $return[]=$ap;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list rogue access points
    returns a array of known roque access point objects
    json parameter <within> {within: "<hoursback eg 24>"} is optional
    */
    public function list_rogueaps() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array('within' => '24'));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/rogueap","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $rogues) {
                        $return[]=$rogues;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list sites
    returns a list sites hosted on this controller with some details
    */
    public function list_sites() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $content=$this->exec_curl($this->baseurl."/api/self/sites");
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $rogues) {
                        $return[]=$rogues;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list site settings
    returns a array of the site configuration settings
    */
    public function list_settings() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/get/setting","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $setting) {
                        $return[]=$setting;
                    }
                }
            }
        }
        return $return;
    }
    /*
    reboot an access point
    parameter <MAC address>
    return true on success
    */
    public function restart_ap($mac) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'restart', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/devmgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    start flashing LED of an access point for locating purposes
    parameter <MAC address>
    return true on success
    */
    public function set_locate_ap($mac) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'set-locate', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/devmgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }

    public function adopt_ap($mac,$adoptsite) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'adopt', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$adoptsite."/cmd/devmgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    public function upgrade_device($mac,$adoptsite) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'upgrade', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$adoptsite."/cmd/devmgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    set access point radio settings
    parameter <ap_id> <radio>(default=ng) <channel> <ht>(default=20) <tx_power_mode> <tx_power>(default=0)
    return true on success
    */
    public function set_ap_radiosettings($ap_id, $radio, $channel, $ht, $tx_power_mode, $tx_power) {
        if (!$this->is_loggedin) return false;
        $return=false;
        $jsonsettings=json_encode(array('radio' => $radio, 'channel' => $channel, 'ht' => $ht, 'tx_power_mode' => $tx_power_mode, 'tx_power' =>$tx_power));
        $json='{"radio_table": ['.$jsonsettings.']}';
        error_log("de json data die wordt gepost: ".$json);
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/upd/device/".$ap_id,"json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    set guest login settings
    parameters  <portal_enabled> <portal_customized> <redirect_enabled> <redirect_url> <x_password> <expire_number> <expire_unit> <site_id>
    both portal parameters are set to the same value!
    return true on success
    */
    public function set_guestlogin_settings($json) {
        if (!$this->is_loggedin) return false;
        $return=false;
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/set/setting/guest_access","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    rename access point
    parameter <ap_id> <apname>
    return true on success
    */
    public function rename_ap_($ap_id, $apname) {
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('name' => $apname));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/upd/device/".$ap_id,"json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    set wlan settings
    parameter <wlan_id> <name> <x_passphrase>
    return true on success
    */
    public function set_wlansettings($wlan_id, $name, $x_passphrase) {
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('name' => $name, 'x_passphrase' => $x_passphrase));
        error_log("de json data die wordt gepost: ".$json);
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/upd/wlanconf/".$wlan_id,"json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    start flashing LED of an access point for locating purposes
    parameter <MAC address>
    return true on success
    */
    public function unset_locate_ap($mac) {
        $mac=strtolower($mac);
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('cmd' => 'unset-locate', 'mac' => $mac));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/devmgr","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    switch LEDs of all the access points ON
    return true on success
    */
    public function site_ledson() {
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('led_enabled' => true));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/set/setting/mgmt","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }
    /*
    switch LEDs of all the access points OFF
    return true on success
    */
    public function site_ledsoff() {
        if (!$this->is_loggedin) return false;
        $return=false;
        $json=json_encode(array('led_enabled' => false));
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/set/setting/mgmt","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                $return=true;
            }
        }
        return $return;
    }

    /*
    list events
    returns a array of known events
    */
    public function list_events() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/event","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $event) {
                        $return[]=$event;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list wireless settings
    returns a array of wireless networks and settings
    */
    public function list_wlanconf() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/list/wlanconf","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $wlan) {
                        $return[]=$wlan;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list alarms
    returns a array of known alarms
    */
    public function list_alarms() {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/list/alarm","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $alarm) {
                        $return[]=$alarm;
                    }
                }
            }
        }
        return $return;
    }
    /*
    list vouchers
    returns a array of voucher objects
    */
    public function get_vouchers($create_time="") {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $return=array();
        $json=json_encode(array());
        if (trim($create_time) != "") {
            $json=json_encode(array('create_time' => $create_time));
        }
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/stat/voucher","json=".$json);
        $content_decoded=json_decode($content);
        if (isset($content_decoded->meta->rc)) {
            if ($content_decoded->meta->rc == "ok") {
                if (is_array($content_decoded->data)) {
                    foreach ($content_decoded->data as $voucher) {
                        $return[]=$voucher;
                    }
                }
            }
        }
        return $return;
    }
    /*
    create voucher(s)
    parameter <minutes>,<number_of_vouchers_to_create>,<note>,<up>,<down>,<mb>
    returns a array of vouchers codes (Note: without the "-" in the middle)
    */
    public function create_voucher($minutes,$number_of_vouchers_to_create=1,$note="",$up=0,$down=0,$Mbytes=0) {
        $return=array();
        if (!$this->is_loggedin) return $return;
        $json=array('cmd' => 'create-voucher', 'expire' => $minutes, 'n' => $number_of_vouchers_to_create);
        if (trim($note) != "") {
            $json[]=array('note'=>$note);
        }
        if ($up > 0) {
            $json[]=array('up'=>$up);
        }
        if ($down > 0) {
            $json[]=array('down'=>$down);
        }
        if ($Mbytes > 0) {
            $json[]=array('bytes'=>$Mbytes);
        }
        // json_encode here
        $content=$this->exec_curl($this->baseurl."/api/s/".$this->site."/cmd/hotspot","json={".$json."}");
        $content_decoded=json_decode($content);
        if ($content_decoded->meta->rc == "ok") {
            if (is_array($content_decoded->data)) {
                $obj=$content_decoded->data[0];
                foreach ($this->get_vouchers($obj->create_time) as $voucher)  {
                    $return[]=$voucher->code;
                }
            }
        }
        return $return;
    }
    private function exec_curl($url,$data="") {
        $ch=$this->get_curl_obj();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (trim($data) != "") {
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        } else {
            curl_setopt($ch, CURLOPT_POST, FALSE);
        }
        $content=curl_exec($ch);
        if ($this->debug == true) {
            print "<pre>";
            print "---------------------\n<br>\n";
            print_r (curl_getinfo($ch));
            print "\n<br>\n---------------------\n<br>\n";
            print $url."\n<br>\n";
            print $data."\n<br>\n";
            print "---------------------\n<br>\n";
            print $content."\n<br>\n";
            print "---------------------\n<br>\n";
            print "</pre>";
        }
        if(curl_exec($ch) === false) {
            error_log('curl error: ' . curl_error($ch));
        }
        curl_close ($ch);
        return $content;
    }
    private function get_curl_obj() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch , CURLOPT_RETURNTRANSFER, TRUE);
        if ($this->debug == true) {
            curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        }
        if ($this->cookies != "") {
            curl_setopt($ch, CURLOPT_COOKIE,  $this->cookies);
        }
        return $ch;
    }
}